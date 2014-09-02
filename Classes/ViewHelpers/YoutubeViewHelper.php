<?php
//set_include_path ('/www/infoarchitekt.de/hosting/2376/dcn.de/kunden/cloud/typo3conf/ext/moox_social/Classes/SDK');
//require_once t3lib_extMgm::extPath('moox_social','Classes/SDK/Google/Client.php');
//require_once t3lib_extMgm::extPath('moox_social','Classes/SDK/Google/Service/YouTube.php');

/**
 * YouTube ViewHelper
 *
 * YouTube API Settings.
 *
 * @package moox_social
 */
class Tx_MooxSocial_ViewHelpers_YoutubeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('clientid', 'string', 'OAuth2 Client ID');
		$this->registerArgument('clientsecret', 'string', 'OAuth2 Client Secret');
	}

	/**
	 * Render method
	 * @return var $output
	 */
	public function render() {
            
            session_start();

            $OAUTH2_CLIENT_ID = $this->arguments['clientid'];
            $OAUTH2_CLIENT_SECRET = $this->arguments['clientsecret'];
            
            $client = new Google_Client();
            $client->setClientId($OAUTH2_CLIENT_ID);
            $client->setClientSecret($OAUTH2_CLIENT_SECRET);
            $redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
            $client->setRedirectUri($redirect);
            
            $youtube = new Google_YoutubeService($client);
            
            if (isset($_GET['code'])) {
              if (strval($_SESSION['state']) !== strval($_GET['state'])) {
                die('The session state did not match.');
              }
            
              $client->authenticate();
              $_SESSION['token'] = $client->getAccessToken();
              header('Location: ' . $redirect);
            }
            
            if (isset($_SESSION['token'])) {
              $client->setAccessToken($_SESSION['token']);
            }
            
            if ($client->getAccessToken()) {
              try {
                $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
                  'mine' => 'true',
                ));
            
                $output = '';
                foreach ($channelsResponse['items'] as $channel) {
                  $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];
            
                  $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
                    'playlistId' => $uploadsListId,
                    'maxResults' => 50
                  ));
            
                  $output .= "<h3>Videos in list $uploadsListId</h3><ul>";
                  foreach ($playlistItemsResponse['items'] as $playlistItem) {
                    $output .= sprintf('<li>%s (%s)</li>', $playlistItem['snippet']['title'],
                      $playlistItem['snippet']['resourceId']['videoId']);
                  }
                  $output .= '</ul>';
                }
              } catch (Google_ServiceException $e) {
                $output .= sprintf('<p>A service error occurred: <code>%s</code></p>',
                  htmlspecialchars($e->getMessage()));
              } catch (Google_Exception $e) {
                $output .= sprintf('<p>An client error occurred: <code>%s</code></p>',
                  htmlspecialchars($e->getMessage()));
              }
            
              $_SESSION['token'] = $client->getAccessToken();
            } else {
              $state = mt_rand();
              $client->setState($state);
              $_SESSION['state'] = $state;
            
              $authUrl = $client->createAuthUrl();
              $output = '<h3>Authorization Required</h3><p>You need to <a href="' . $authUrl . '">authorize access</a> before proceeding.<p>';
            }
            
            return $output;
	}     
        
}

?>
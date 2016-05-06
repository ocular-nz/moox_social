<?php
namespace DCNGmbH\MooxSocial\ViewHelpers;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/facebook/facebook.php');

class LoginViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	
	/**
	 * Render the facebook user viewhelper
	 *
	 * @param string $id
	 * @param string $secret
	 * @return var $output
	 * 
	 */
        public function render($id, $secret) {
		
                $fbconfig['baseurl'] = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
                
		$config = array(
			'appId' => $id,
			'secret' => $secret,
			'cookie' => true
		);
		
                $facebook = new Facebook($config);
		$user = $facebook->getUser();
                
                $loginUrl   = $facebook->getLoginUrl(
                        array(
                            'scope'         => 'email,offline_access,publish_stream,user_birthday,user_location,user_work_history,user_about_me,user_hometown',
                            'redirect_uri'  => $fbconfig['baseurl']
                        )
                );
                
                $logoutUrl  = $facebook->getLogoutUrl();
                
                if ($user) {
                    try {
                      // Proceed knowing you have a logged in user who's authenticated.
                      $user_profile = $facebook->api('/me');
                    } catch (FacebookApiException $e) {
                      //you should use error_log($e); instead of printing the info on browser
                      d($e);  // d is a debug function defined at the end of this file
                      $user = null;
                    }
                }
                 
                  
                //if user is logged in and session is valid.
                if ($user){
                    //get user basic description
                    $userInfo = $facebook->api("/$user");
                      
                    //Retriving movies those are user like using graph api
                    try{
                        $movies = $facebook->api("/$user/movies");
                    }
                    catch(Exception $o){
                        d($o);
                    }
                    
                    //update user's status using graph api
                    //http://developers.facebook.com/docs/reference/dialogs/feed/
                    if (isset($_GET['publish'])){
                        try {
                            $publishStream = $facebook->api("/$user/feed", 'post', array(
                                'message' => "I love thinkdiff.net for facebook app development tutorials. :)", 
                                'link'    => 'http://ithinkdiff.net',
                                'picture' => 'http://thinkdiff.net/ithinkdiff.png',
                                'name'    => 'iOS Apps & Games',
                                'description'=> 'Checkout iOS apps and games from iThinkdiff.net. I found some of them are just awesome!'
                                )
                            );
                            //as $_GET['publish'] is set so remove it by redirecting user to the base url 
                        } catch (FacebookApiException $e) {
                            d($e);
                        }
                        $redirectUrl = $fbconfig['baseurl'] . '/index.php?success=1';
                        header("Location: $redirectUrl");
                    }
            
                    //update user's status using graph api
                    //http://developers.facebook.com/docs/reference/dialogs/feed/
                    if (isset($_POST['tt'])){
                        try {
                            $statusUpdate = $facebook->api("/$user/feed", 'post', array('message'=> $_POST['tt']));
                        } catch (FacebookApiException $e) {
                            d($e);
                        }
                    }
            
                    //fql query example using legacy method call and passing parameter
                    try{
                        $fql    =   "select name, hometown_location, sex, pic_square from user where uid=" . $user;
                        $param  =   array(
                            'method'    => 'fql.query',
                            'query'     => $fql,
                            'callback'  => ''
                        );
                        $fqlResult   =   $facebook->api($param);
                    }
                    catch(Exception $o){
                        d($o);
                    }
                }
                
                function d($d){
                    $output = '<pre>' . print_r($d) . '</pre>';
                }
                
                if (!$user) {
                    $login = '<a href="' . $loginUrl . '">Facebook Login</a>' . $userInfo;
                } else {
                    $login = '<a href="' . $logoutUrl . '">Facebook Logout</a>' . $userInfo;
                }
                
                if ($user){
                    $information = '<b>User Information using Graph API</b>' . d($userInfo);
                }
                
                return $login;
	    
        }

}

?>
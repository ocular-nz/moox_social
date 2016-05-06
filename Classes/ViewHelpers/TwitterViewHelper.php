<?php
namespace DCNGmbH\MooxSocial\ViewHelpers;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/twitter/TwitterAPIExchange.php');

/**
 * Twitter ViewHelper
 *
 * Twitter API Settings.
 *
 * @package moox_social
 */
class TwitterViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize arguments
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('oauth-access-token', 'string', 'YOUR_OAUTH_ACCESS_TOKEN');
		$this->registerArgument('oauth-access-token-secret', 'string', 'YOUR_OAUTH_ACCESS_TOKEN_SECRET');
                $this->registerArgument('consumer-key', 'string', 'YOUR_CONSUMER_KEY');
                $this->registerArgument('consumer-secret', 'string', 'YOUR_CONSUMER_SECRET');
	}

	/**
	 * Render method
	 * @return void
	 */
	public function render() {
            
            $settings = array(
                'oauth_access_token' => $this->arguments['oauth-access-token'],
                'oauth_access_token_secret' => $this->arguments['oauth-access-token-secret'],
                'consumer_key' => $this->arguments['consumer-key'],
                'consumer_secret' => $this->arguments['consumer-secret']
            );
    
            $this->viewHelperVariableContainer->addOrUpdate('DCNGmbH\\MooxSocial\\ViewHelpers\\TwitterViewHelper', 'twitter', $settings);

	    return $this->renderChildren();
	}

}

?>

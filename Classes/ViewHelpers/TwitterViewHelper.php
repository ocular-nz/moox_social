<?php
require_once t3lib_extMgm::extPath('moox_social','Classes/SDK/twitter/TwitterAPIExchange.php');

/**
 * Twitter ViewHelper
 *
 * Twitter API Settings.
 *
 * @package moox_social
 */
class Tx_MooxSocial_ViewHelpers_TwitterViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

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
    
            $this->viewHelperVariableContainer->addOrUpdate('Tx_MooxSocial_ViewHelpers_TwitterViewHelper', 'twitter', $settings);

	    return $this->renderChildren();
	}

}

?>

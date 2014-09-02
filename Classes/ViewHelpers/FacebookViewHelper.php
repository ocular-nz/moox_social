<?php
require_once t3lib_extMgm::extPath('moox_social','Classes/SDK/facebook/facebook.php');

/**
 * Facebook ViewHelper
 *
 * Groups Facebook actions.
 *
 * @package moox_social
 */
class Tx_MooxSocial_ViewHelpers_FacebookViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('id', 'string', 'AppId');
		$this->registerArgument('secret', 'string', 'AppSecret');
	}

	/**
	 * Render method
	 * @return void
	 */
	public function render() {
            
            $config = array(
		    'appId' => $this->arguments['id'],
		    'secret' => $this->arguments['secret'],
		    'allowSignedRequest' => false
	    );
		
	    $facebook = new Facebook($config);
            
            $user_id = $facebook->getUser();

	    $this->renderChildren();
	}

}

?>

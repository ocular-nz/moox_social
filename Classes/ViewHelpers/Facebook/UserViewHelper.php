<?php
require_once t3lib_extMgm::extPath('moox_social','Classes/SDK/facebook/facebook.php');

class Tx_MooxSocial_ViewHelpers_Facebook_UserViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
	
	/**
	 * Render the facebook user viewhelper
	 *
	 * @param string $id
	 * @param string $secret
	 * @param string $string 
	 * @return var $output
	 * 
	 */
        public function render($id, $secret, $string) {
		
		$config = array(
			'appId' => $id,
			'secret' => $secret,
			'allowSignedRequest' => false
		);
		
		$facebook = new Facebook($config);
		$user_id = $facebook->getUser();
		  
		if($user_id) {

			  $user_profile = $facebook->api('/me','GET');
			  $output = "Name: " . $user_profile[$string];
		  
		}
		
		return $output;
	    
        }

}

?>
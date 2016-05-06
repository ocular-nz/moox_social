<?php
namespace DCNGmbH\MooxSocial\ViewHelpers;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/facebook/facebook.php');

class UserViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	
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
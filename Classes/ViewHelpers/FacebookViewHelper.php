<?php
namespace DCNGmbH\MooxSocial\ViewHelpers;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/facebook/facebook.php');

/**
 * Facebook ViewHelper
 *
 * Groups Facebook actions.
 *
 * @package moox_social
 */
class FacebookViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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

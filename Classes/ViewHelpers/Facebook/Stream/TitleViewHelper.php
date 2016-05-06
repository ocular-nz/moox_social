<?php
namespace DCNGmbH\MooxSocial\ViewHelpers\Stream;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/facebook/facebook.php');

class TitleViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	
	/**
	 * Render the facebook user viewhelper
	 *
	 * @return void
	 * 
	 */
	
        public function render() {
		
		$config = $this->viewHelperVariableContainer->get('Tx_MooxSocial_ViewHelpers_Facebook_StreamViewHelper', 'post');
		
		$facebook = new Facebook($config);
		
		$pageid = $config['pageid'];
                
                $url = '/' . $pageid . '/feed';
		$rawFeed = $facebook->api($url);
		  
		$feedTitle = array();
                foreach($rawFeed['data'] as $item) {
                        $title = false;
                        if($title === false && isset($item['story']) && !empty($item['story'])) {
                                $title = $item['story'];
                        }
                        if($title === false && isset($item['message']) && !empty($item['message'])) {
                                $title = $item['message'];
                        }
                        
                        $feedTitle[] = array(
                                'title' => $title
                        );
                }
		
		$tester = 'ich bin ein test';
		$this->viewHelperVariableContainer->addOrUpdate('Tx_MooxSocial_ViewHelpers_Facebook_StreamViewHelper', 'post', $tester);
		
		
		foreach($feedTitle as $item) {
                    $fieldTitle .= '<li><div class="moox-facebook-post-title">' . htmlspecialchars($item['title']) . '</div></li>';
                }
		
		$output = $fieldTitle;
                
                return $output;
		
	    
        }

}

?>
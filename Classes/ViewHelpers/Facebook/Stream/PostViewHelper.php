<?php
namespace DCNGmbH\MooxSocial\ViewHelpers\Stream;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/facebook/facebook.php');

class PostViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	
	/**
	 * Render the facebook user viewhelper
	 *
	 * @param string $id
	 * @param string $secret
	 * @param string $pageid
	 * @param string $strings 
	 * @return var $output
	 * 
	 */
        public function render($appid, $secret, $pageid, $strings) {
		
		$config = array(
			'appId' => $appid,
			'secret' => $secret,
			'pageid' => $pageid,
			'allowSignedRequest' => false
		);		
		
		$this->viewHelperVariableContainer->addOrUpdate('Tx_MooxSocial_ViewHelpers_Facebook_StreamViewHelper', 'post', $config);

		
		$facebook = new Facebook($config);
                
                $url = '/' . $pageid . '/feed';
		$rawFeed = $facebook->api($url);
		
		$feed = array();
                foreach($rawFeed['data'] as $item) {
                        $title = false;
                        if($title === false && isset($item['story']) && !empty($item['story'])) {
                                $title = $item['story'];
                        }
                        if($title === false && isset($item['message']) && !empty($item['message'])) {
                                $title = $item['message'];
                        }
                        
                        $image = false;
			if($image === false && isset($item['picture']) && !empty($item['picture'])) {
				$image = $item['picture'];
			}
			
			$link = false;
			if($link === false && isset($item['link']) && !empty($item['link'])) {
				$link = $item['link'];
			}
			
			$shares = false;
			if($shares === false && isset($item['shares']) && !empty($item['shares'])) {
				$link = $item['shares'];
			}
			
			$feed[] = array(
				'title' => $title,
				'link' => $link,
				'image' => $image,
				'shares' => $shares
			);
                }
		
		$output .= '<ul>';
		
		foreach($feed as $item) {
			$output .= '<li>';
			
			if($strings['title'] == '1' && !empty($item['title'])){
			    $output .= '<div class="moox-facebook-post-title">' . htmlspecialchars($item['title']) . '</div>';
			}
			if($strings['picture'] == '1' && !empty($item['image'])){
			    $output .= '<div class="moox-facebook-post-picture"><img src="' . htmlspecialchars($item['image']) . '" /></div>';
			}
			if($strings['link'] == '1' && !empty($item['link'])){
			    $output .= '<div class="moox-facebook-post-link"><a href="' . htmlspecialchars($item['link']) . '">' . htmlspecialchars($item['link']) . '</a></div>';
			}
			if($strings['shares'] == '1' && !empty($item['shares'])){
			    $output .= '<div class="moox-facebook-post-shares">' . htmlspecialchars($item['shares']) . '</div>';
			}
			
			$output .= '</li>';
                }
		
		$output .= '</ul>';
		
		
                return $output;
		
	    
        }

}

?>
<?php
namespace DCNGmbH\MooxSocial\ViewHelpers;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/SDK/slideshare/SSUtil/SSUtil.php');

/**
 * Slideshare ViewHelper
 *
 * Slideshare API Settings.
 *
 * @package moox_social
 */
class SlideshareViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

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
            
		$config = array(
		    'key' => $this->arguments['key'],
		    'secret' => $this->arguments['secret'],
		    'apiurl' => 'http://www.slideshare.net/api/1/'
		);
	    
		$key = $config['key'];
		$secret = $config['secret'];
		$apiurl = $config['apiurl'];
    
		$apiobj=new SSUtil();

		$output .= '#of slides with user variable_orr - '.$apiobj->count_slideUser('variable_orr')."<br/>";
		$output .= '# of slides in the web 2.0 group - '.$apiobj->count_slideGroup('web-20')."<br/>";
		$output .= '# of slides tagged as marketing - '.$apiobj->count_slideTag('marketing')."<br/>";
		
		$output .= $apiobj->get_slideUser('variable_orr',0,50);
		$output .= $apiobj->get_slideInfo(47236);
		
		//RSS utility functions
		$apiobj->get_RSS('http://www.slideshare.net/rss/latest');*/
		$output .= $apiobj->make_RSS('Test feed','Test description','12',$apiobj->get_slideUser('variable_orr',0,50));
	}

}

?>

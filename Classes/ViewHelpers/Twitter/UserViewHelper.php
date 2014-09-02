<?php
require_once t3lib_extMgm::extPath('moox_social','Classes/SDK/twitter/TwitterAPIExchange.php');

/**
 * Twitter ViewHelper
 *
 * Show User Tweets
 *
 * @package moox_social
 */
class Tx_MooxSocial_ViewHelpers_Twitter_UserViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('screen-name', 'string', 'YOUR_OAUTH_ACCESS_TOKEN');
		$this->registerArgument('count', 'string', 'YOUR_OAUTH_ACCESS_TOKEN_SECRET');
	}

	/**
	 * Render method
	 * @return var $output
	 */
	public function render() {
	    
	    $screenName = $this->arguments['screen-name'];
	    $count = $this->arguments['count'];
                
            $settings = $this->viewHelperVariableContainer->get('Tx_MooxSocial_ViewHelpers_TwitterViewHelper', 'twitter');
            
            $twitter = new TwitterAPIExchange($settings);
            
            $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
            $requestMethod = "GET";
            
            $getfield = '?screen_name='.$screenName.'&count='.$count;
            
            $string = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(),$assoc = TRUE);

            if($string["errors"][0]["message"] != "") {
                $output = "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";exit();
            }else{
                foreach($string as $items)
                {
                    $output .= "Time and Date of Tweet: ".$items['created_at']."<br />";
                    $output .= "Tweet: ". $items['text']."<br />";
                    $output .= "Tweeted by: ". $items['user']['name']."<br />";
                    $output .= "Screen name: ". $items['user']['screen_name']."<br />";
                    $output .= "Followers: ". $items['user']['followers_count']."<br />";
                    $output .= "Friends: ". $items['user']['friends_count']."<br />";
                    $output .= "Listed: ". $items['user']['listed_count']."<br /><hr />";
                }   
	    }
            
            return $output;

	}

}

?>

<?php
namespace TYPO3\MooxSocial\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Dominic Martin <dm@dcn.de>, DCN GmbH
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TwitterController extends \TYPO3\MooxSocial\Controller\PostController {

	/**
	 * twitterRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\TwitterRepository
	 * @inject
	 */
	protected $twitterRepository;	

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		
		if($this->settings['use_ajax']){
			$this->settings['limit'] = $this->settings['ajax_limit'];
		}
		
		if(!$this->settings['sort_by']){
			$this->settings['sort_by'] = "updated";
		}
		
		if(!$this->settings['sort_direction']){
			$this->settings['sort_direction'] = "DESC";
		}
		
		if(!$this->settings['limit']){
			$this->settings['limit'] = 25;
		}
		
		if($this->settings['source']=="api"){
			
			// Get the extensions's configuration
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);
			if($this->settings['api_oauth_access_token']==""){
				$this->settings['api_oauth_access_token'] = $extConf['fallbackTwitterOauthAccessToken'];
			}
			if($this->settings['api_oauth_access_token_secret']==""){
				$this->settings['api_oauth_access_token_secret'] = $extConf['fallbackTwitterOauthAccessTokenSecret'];
			}
			if($this->settings['api_consumer_key']==""){
				$this->settings['api_consumer_key'] = $extConf['fallbackTwitterConsumerKey'];
			}
			if($this->settings['api_consumer_key_secret']==""){
				$this->settings['api_consumer_key_secret'] = $extConf['fallbackTwitterConsumerKeySecret'];
			}
			
			if($this->settings['api_oauth_access_token']!="" && $this->settings['api_oauth_access_token_secret']!="" && $this->settings['api_consumer_key']!="" && $this->settings['api_consumer_key_secret']!=""){
				
				$posts = $this->twitterRepository->requestAllBySettings($this->settings);				
			}
			
		} else {
		
			$count 	= $this->twitterRepository->findAll($this->settings['screen_name'])->count();

			$posts 	= $this->twitterRepository->findAllBySettings($this->settings);
		}
		
		$this->view->assign('currentpid', $GLOBALS['TSFE']->id);
		$this->view->assign('posts', $posts);
		$this->view->assign('count', $count);
		$pages = array();
		for($i=1;$i<=ceil($count/$this->settings['limit']);$i++){
			$pages[] = array("id" => $i, "offset" => (($i-1)*$this->settings['limit']));
		}
		$this->view->assign('pages', $pages);
		$this->view->assign('pagesCount', count($pages));
		$this->view->assign('maxOffset', (($i-2)*$this->settings['limit']));
		
	}
	
	/**
	 * action listAjax
	 *
	 * @return void
	 */
	public function listAjaxAction() {
				
		if(!$this->settings['sort_by']){
			$this->settings['sort_by'] = "updated";
		}
		
		if(!$this->settings['sort_direction']){
			$this->settings['sort_direction'] = "DESC";
		}
		
		if(!$this->settings['limit']){
			$this->settings['limit'] = 25;
		}
		
		if($this->request->hasArgument('perrequest')){			
			$this->settings['limit'] = $this->request->getArgument('perrequest');				
		}
		
		if($this->request->hasArgument('offset')){			
			$this->settings['offset'] = $this->request->getArgument('offset');			
		}
		
		if($this->request->hasArgument('source')){			
			$this->settings['source'] = $this->request->getArgument('source');				
		}	

		if($this->request->hasArgument('page')){			
			$this->settings['api_page_id'] = $this->request->getArgument('page');				
		}
				
		if($this->settings['source']=="api"){
			
			// Get the extensions's configuration
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);		
			if($this->settings['api_oauth_access_token']==""){
				$this->settings['api_oauth_access_token'] = $extConf['fallbackTwitterOauthAccessToken'];
			}
			if($this->settings['api_oauth_access_token_secret']==""){
				$this->settings['api_oauth_access_token_secret'] = $extConf['fallbackTwitterOauthAccessTokenSecret'];
			}
			if($this->settings['api_consumer_key']==""){
				$this->settings['api_consumer_key'] = $extConf['fallbackTwitterConsumerKey'];
			}
			if($this->settings['api_consumer_key_secret']==""){
				$this->settings['api_consumer_key_secret'] = $extConf['fallbackTwitterConsumerKeySecret'];
			}
			
			if($this->settings['api_oauth_access_token']!="" && $this->settings['api_oauth_access_token_secret']!="" && $this->settings['api_consumer_key']!="" && $this->settings['api_consumer_key_secret']!=""){
				
				$posts = $this->twitterRepository->requestAllBySettings($this->settings);				
			}								
						
		} else {
		
			$posts 	= $this->twitterRepository->findAllBySettings($this->settings);
		}
		
		$this->view->assign('currentpid', $GLOBALS['TSFE']->id);
		$this->view->assign('posts', $posts);		
	}

	/**
	 * action show
	 *
	 * @param \TYPO3\MooxSocial\Domain\Model\Twitter $twitter
	 * @return void
	 */
	public function showAction(\TYPO3\MooxSocial\Domain\Model\Twitter $twitter = NULL) {				
		
		if(!$twitter && $this->settings['source']!="api"){
			$twitter	= $this->twitterRepository->findRandomOne($this->settings['screen_name']);
			$this->view->assign('israndom', TRUE);			
		}
		
		$this->view->assign('twitter', $twitter);
	}
	
	/**
	 * execute twitter api request
	 *
	 * @param string $oauthAccessToken
	 * @param string $oauthAccessTokenSecret
	 * @param string $consumerKey
	 * @param string $consumerKeySecret
	 * @param string $screenName
	 * @param string $request
	 * @return array feedData
	 */
	public function twitter($oauthAccessToken,$oauthAccessTokenSecret,$consumerKey,$consumerKeySecret,$screenName, $request = "") {
		$rawFeed = array();
		
		// Get the extensions's configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);		
		if($oauthAccessToken==""){
			$oauthAccessToken = $extConf['fallbackTwitterOauthAccessToken'];
		}
		if($oauthAccessTokenSecret==""){
			$oauthAccessTokenSecret = $extConf['fallbackTwitterOauthAccessTokenSecret'];
		}
		if($consumerKey==""){
			$consumerKey = $extConf['fallbackTwitterConsumerKey'];
		}
		if($consumerKeySecret==""){
			$consumerKeySecret = $extConf['fallbackTwitterConsumerKeySecret'];
		}

		if($oauthAccessToken!="" && $oauthAccessTokenSecret!="" && $consumerKey!="" && $consumerKeySecret!="" && $screenName!=""){
			
			$config = array(
				'consumer_key' => $consumerKey,
				'consumer_secret' => $consumerKeySecret,
				'oauth_access_token' => $oauthAccessToken,
				'oauth_access_token_secret' => $oauthAccessTokenSecret,
				'screenName' => $screenName,
				'allowSignedRequest' => false
			);
				
			$twitter = new \TYPO3\MooxSocial\Twitter\TwitterAPIExchange($config);
			
			if($request=="init"){
				$request="&count=200";
			} elseif($request==""){
				$request="&count=25";
			}
			
			$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
			$requestMethod = "GET";
			$getfield = '?screen_name=' . $screenName . $request;
			
			$rawFeed = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(),$assoc = TRUE);
		}
		return $rawFeed;
	}

	/**
	 * prepare twitter post
	 *
	 * @param array $item	
	 * @return array post
	 */
	public function twitterPost($item) {
		
		if(is_array($item)){
			
			$post = array();
			
			if($item['retweeted_status']){
				$post['created'] 	= strtotime($item['retweeted_status']['created_at']);
				$post['type'] 		= "shared_status";
				$post['text'] 		= $item['retweeted_status']['text'];
				//$post['sharedUrl'] 	= $item['retweeted_status']['entities']['urls']['expanded_url'];
				//$post['sharedTitle'] 	= $item['retweeted_status']['entities']['urls']['display_url'];
				//$post['sharedCaption'] 	= $item['retweeted_status']['entities']['hashtags'];
				$post['author'] 	= $item['retweeted_status']['user']['name'];
				$post['authorId'] 	= $item['retweeted_status']['user']['id_str'];
				$post['linkName'] 	= $item['retweeted_status']['user']['screen_name'];
				$post['url'] 		= $item['retweeted_status']['user']['url'];
				$post['imageUrl'] 	= $item['retweeted_status']['entities']['media']['0']['media_url'];
				$post['likes'] 		= $item['retweeted_status']['favorite_count'];
				$post['shares'] 	= $item['retweeted_status']['retweet_count'];
				$post['imageEmbedcode'] = $item['retweeted_status']['user']['profile_image_url'];
				//$post['comments'] 	= $item['retweeted_status']['entities']['user_mentions'];
			} else {
				$post['created'] 	= strtotime($item['created_at']);
				$post['type'] 		= "status";
				$post['text'] 		= $item['text'];
				//$post['sharedUrl'] 	= $item['entities']['urls']['expanded_url'];
				//$post['sharedTitle'] 	= $item['entities']['urls']['display_url'];
				//$post['sharedCaption'] 	= $item['entities']['hashtags'];
				$post['author'] 	= $item['user']['name'];
				$post['authorId'] 	= $item['user']['id_str'];
				$post['linkName'] 	= $item['user']['screen_name'];
				$post['url'] 		= $item['user']['url'];
				$post['imageUrl'] 	= $item['entities']['media']['0']['media_url'];
				$post['likes'] 		= $item['favorite_count'];
				$post['shares'] 	= $item['retweet_count'];
				$post['imageEmbedcode'] = $item['user']['profile_image_url'];
				//$post['comments'] 	= $item['entities']['user_mentions'];
			}	
					
			$post['pid'] 				= $item['id_str'];
			//$post['created'] 			= strtotime($item['created_at']);
			$post['updated'] 			= strtotime($item['created_at']);
			//$post['type'] 			= "status";
			$post['statusType'] 			= $item['source'];	// no match
			$post['page'] 				= $item['user']['screen_name'];
			$post['action'] 			= "";	// no match				
			$post['summary'] 			= "";	// no match
			$post['title'] 				= "";	// no match
			$post['description'] 			= "";	// no match
			$post['caption'] 			= "";	// no match
			//$post['text'] 			= $item['text'];
			//$post['sharedUrl'] 			= $item['entities']['urls']['expanded_url'];
			//$post['sharedTitle'] 			= $item['entities']['urls']['display_url'];
			//$post['sharedCaption'] 		= $item['entities']['hashtags'];
			//$post['author'] 			= $item['user']['name'];
			//$post['authorId'] 			= $item['user']['id_str'];
			//$post['url'] 				= $item['user']['url'];
			//$post['linkName'] 			= "";	// no match
			$post['linkUrl'] 			= "";	// no match
			//$post['imageUrl'] 			= $item['entities']['media']['media_url'];
			//$post['imageEmbedcode'] 		= $item['user']['profile_image_url'];
			$post['videoUrl'] 			= "";	// no match
			$post['videoEmbedcode'] 		= "";	// no match				
			//$post['likes'] 			= count($item['favorite_count']);
			//$post['shares'] 			= count($item['retweet_count']);
			//$post['comments'] 			= $item['entities']['user_mentions'];
			$post['apiUid'] 			= $item['id'];			
			$post['apiHash'] 			= md5(print_r($post,TRUE));
						
			return $post;
			
		} else {
		
			return false;
		}
	}
}
?>
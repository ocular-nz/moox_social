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
class FlickrController extends \TYPO3\MooxSocial\Controller\PostController {

	/**
	 * flickrRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\FlickrRepository
	 * @inject
	 */
	protected $flickrRepository;

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
			if($this->settings['api_flickr_key']==""){
				$this->settings['api_flickr_key'] = $extConf['fallbackFlickrApiKey'];
			}
			if($this->settings['api_flickr_secret_key']==""){
				$this->settings['api_flickr_secret_key'] = $extConf['fallbackFlickrApiSecretKey'];
			}
			
			if($this->settings['api_flickr_key']!="" && $this->settings['api_flickr_secret_key']!=""){
				
				$posts = $this->flickrRepository->requestAllBySettings($this->settings);				
			}
			
		} else {
		
			$count 	= $this->flickrRepository->findAll($this->settings['user_id'])->count();

			$posts 	= $this->flickrRepository->findAllBySettings($this->settings);
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
			if($this->settings['api_flickr_key']==""){
				$this->settings['api_flickr_key'] = $extConf['fallbackFlickrApiKey'];
			}
			if($this->settings['api_flickr_secret_key']==""){
				$this->settings['api_flickr_secret_key'] = $extConf['fallbackFlickrApiSecretKey'];
			}
                        
			if($this->settings['api_flickr_key']!="" && $this->settings['api_flickr_secret_key']!=""){
				
				$posts = $this->flickrRepository->requestAllBySettings($this->settings);				
			}								
						
		} else {
		
			$posts 	= $this->flickrRepository->findAllBySettings($this->settings);
		}
		
		$this->view->assign('currentpid', $GLOBALS['TSFE']->id);
		$this->view->assign('posts', $posts);		
	}

	/**
	 * action show
	 *
	 * @param \TYPO3\MooxSocial\Domain\Model\Flickr $flickr
	 * @return void
	 */
	public function showAction(\TYPO3\MooxSocial\Domain\Model\Flickr $flickr = NULL) {				
		
		if(!$flickr && $this->settings['source']!="api"){
			$flickr	= $this->flickrRepository->findRandomOne($this->settings['user_id']);
			$this->view->assign('israndom', TRUE);			
		}
		
		$this->view->assign('flickr', $flickr);
	}
	
	/**
	 * execute flickr api request
	 *
	 * @param string $apiKey
	 * @param string $apiSecretKey
	 * @param string $userId
	 * @param string $request
	 * @return array feedData
	 */
	public function flickr($apiKey,$apiSecretKey,$userId) {
		$rawFeed = array();
		
		// Get the extensions's configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);
		if($apiKey==""){
			$apiKey = $extConf['fallbackFlickrApiKey'];
		}
		if($apiSecretKey==""){
			$apiSecretKey = $extConf['fallbackFlickrApiSecretKey'];
		}

		if($apiKey!="" && $apiSecretKey!="" && $userId!=""){
			
			$config = array(
				'api_key' => $apiKey,
				'api_secret_key' => $apiSecretKey,
				'user_id' => $userId,
				'allowSignedRequest' => false
			);
                        
			$feedUrl = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key='.$apiKey.'&user_id='.$userId.'&format=rest';
			$feedXml = simplexml_load_file($feedUrl);
			
			$flickrFeed = array();
			
			foreach($feedXml->photosets[0]->photoset as $item) {				
				$flickrFeed[] = array(
					'id' => (string)$item->attributes()->id,
					'primary' => (string)$item->attributes()->primary,
					'secret' => (string)$item->attributes()->secret,
					'server' => (string)$item->attributes()->server,
					'farm' => (string)$item->attributes()->farm,
					'photos' => (string)$item->attributes()->photos,
					'videos' => (string)$item->attributes()->videos,
					'needs_interstitial' => (string)$item->attributes()->needs_interstitial,
					'visibility_can_see_set' => (string)$item->attributes()->visibility_can_see_set,
					'count_views' => (string)$item->attributes()->count_views,
					'count_comments' => (string)$item->attributes()->count_comments,
					'can_comment' => (string)$item->attributes()->can_comment,
					'date_create' => date('r', (string)$item->attributes()->date_create),
					'date_update' => date('r', (string)$item->attributes()->date_update),
					'title' => (string)$item->title,
					'description' => (string)$item->description
				);
			}
			
			$rawFeed = $flickrFeed;
		}
		return $rawFeed;
	}

	/**
	 * prepare flickr post
	 *
	 * @param array $item	
	 * @return array post
	 */
	public function flickrPost($item) {
		
		if(is_array($item)){
			
			$post = array();	
					
			$post['pid'] 				= $item['pid'];
			$post['created'] 			= strtotime($item['date_create']);
			$post['updated'] 			= strtotime($item['date_update']);
			$post['type'] 				= "image";
			$post['statusType'] 			= "";	// no match
			$post['page'] 				= $item['userId'];
			$post['action'] 			= "";	// no match				
			$post['summary'] 			= "";	// no match
			$post['title'] 				= $item['title'];
			$post['description'] 			= $item['description'];
			$post['caption'] 			= $item['photos'];
			$post['text'] 				= "";	// no match
			$post['sharedUrl'] 			= "";	// no match
			$post['sharedTitle'] 			= "";	// no match
			$post['sharedCaption'] 			= "";	// no match
			$post['author'] 			= "";	// no match
			$post['authorId'] 			= "";	// no match
			$post['url'] 				= "https://www.flickr.com/photos/".$item['userId']."/sets/".$item['id']."/";
			$post['linkName'] 			= "";	// no match
			$post['linkUrl'] 			= "";	// no match
			$post['imageUrl'] 			= "http://farm".$item['farm'].".staticflickr.com/".$item['server']."/".$item['primary']."_".$item['secret'].".jpg";
			$post['imageEmbedcode'] 		= "";	// no match
			$post['videoUrl'] 			= "";	// no match
			$post['videoEmbedcode'] 		= "";	// no match				
			$post['likes'] 				= $item['count_views'];
			$post['shares'] 			= ""; 	// no match
			$post['comments'] 			= $item['count_comments'];
			$post['apiUid'] 			= $item['id'];		
			$post['apiHash'] 			= md5(print_r($post,TRUE));
						
			return $post;
			
		} else {
		
			return false;
		}
	}
}
?>
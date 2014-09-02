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
class YoutubeController extends \TYPO3\MooxSocial\Controller\PostController {

	/**
	 * youtubeRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\YoutubeRepository
	 * @inject
	 */
	protected $youtubeRepository;

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
			if($this->settings['api_youtube_channel']==""){
				$this->settings['api_youtube_channel'] = $extConf['fallbackYoutubeYoutubeChannel'];
			}
			
			if($this->settings['api_youtube_channel']!=""){
				
				$posts = $this->youtubeRepository->requestAllBySettings($this->settings);				
			}
			
		} else {
		
			$count 	= $this->youtubeRepository->findAll($this->settings['youtube_channel'])->count();

			$posts 	= $this->youtubeRepository->findAllBySettings($this->settings);
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
			if($this->settings['api_youtube_channel']==""){
				$this->settings['api_youtube_channel'] = $extConf['fallbackYoutubeYoutubeChannel'];
			}
                        
			if($this->settings['api_youtube_channel']!=""){
				
				$posts = $this->youtubeRepository->requestAllBySettings($this->settings);				
			}								
						
		} else {
		
			$posts 	= $this->youtubeRepository->findAllBySettings($this->settings);
		}
		
		$this->view->assign('currentpid', $GLOBALS['TSFE']->id);
		$this->view->assign('posts', $posts);		
	}

	/**
	 * action show
	 *
	 * @param \TYPO3\MooxSocial\Domain\Model\Youtube $youtube
	 * @return void
	 */
	public function showAction(\TYPO3\MooxSocial\Domain\Model\Youtube $youtube = NULL) {				
		
		if(!$youtube && $this->settings['source']!="api"){
			$youtube	= $this->youtubeRepository->findRandomOne($this->settings['youtube_channel']);
			$this->view->assign('israndom', TRUE);			
		}
		
		$this->view->assign('youtube', $youtube);
	}
	
	/**
	 * execute youtube api request
	 *
	 * @param string $youtubeChannel
	 * @param string $request
	 * @return array feedData
	 */
	public function youtube($youtubeChannel) {
		$rawFeed = array();
		
		// Get the extensions's configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);		

		if($youtubeChannel!=""){
			
			$config = array(
                            'youtube_channel' => $youtubeChannel,
                            'allowSignedRequest' => false
			);
			$feedUrl = 'http://gdata.youtube.com/feeds/api/users/'.$youtubeChannel.'/uploads?max-results=50&alt=rss&lr=de&orderby=published&format=1,5,6';
			$feedXml = simplexml_load_file(rawurlencode($feedUrl));
			
			$youtubeFeed = array();
			
			foreach($feedXml->channel[0]->item as $item) {
				$atom = $item->children('http://www.w3.org/2005/Atom');
				$media = $item->children('http://search.yahoo.com/mrss/');
				$yt = $item->children('http://gdata.youtube.com/schemas/2007');
				$gd = $item->children('http://schemas.google.com/g/2005');
				$videoId = explode('/',(string) $item->guid);
				
				$youtubeFeed[] = array(
					'id' => $videoId[6],
					'pubDate' => date('d.m.Y H:i', strtotime((string) $item->pubDate)),
					'updateDate' => date('d.m.Y H:i', strtotime((string) $item->children('atom',true)->updated)),
					'title' => (string) $item->title,
					'category' => (string) $media->group->category,
					'author' => (string) $item->author,
					'description' => (string) $media->group->description,
					'link' => (string) $item->link,
					'imageUrl' => (string) $media->group->thumbnail->attributes()->url,
					'favoriteCount' => (string) $yt->statistics->attributes()->favoriteCount,
					'viewCount' => (string) $yt->statistics->attributes()->viewCount
				);
			}
			
			$rawFeed = $youtubeFeed;

		}
		return $rawFeed;
	}

	/**
	 * prepare youtube post
	 *
	 * @param array $item	
	 * @return array post
	 */
	public function youtubePost($item) {
		
		if(is_array($item)){
			
			$post = array();	
					
			$post['pid'] 				= $item['pid'];
			$post['created'] 			= strtotime($item['pubDate']);
			$post['updated'] 			= strtotime($item['updateDate']);
			$post['type'] 				= "video";
			$post['statusType'] 			= "";	// no match
			$post['page'] 				= $item['youtubeChannel'];
			$post['action'] 			= "";	// no match				
			$post['summary'] 			= "";	// no match
			$post['title'] 				= $item['title'];
			$post['description'] 			= $item['description'];
			$post['caption'] 			= "";	// no match
			$post['text'] 				= "";	// no match
			$post['sharedUrl'] 			= "";	// no match
			$post['sharedTitle'] 			= "";	// no match
			$post['sharedCaption'] 			= "";	// no match
			$post['author'] 			= $item['author'];
			$post['authorId'] 			= "";	// no match
			$post['url'] 				= "";	// no match
			$post['linkName'] 			= "";	// no match
			$post['linkUrl'] 			= "";	// no match
			$post['imageUrl'] 			= $item['imageUrl'];
			$post['imageEmbedcode'] 		= "";	// no match
			$post['videoUrl'] 			= $item['link'];
			$post['videoEmbedcode'] 		= "";	// no match				
			$post['likes'] 				= $item['favoriteCount'];
			$post['shares'] 			= $item['viewCount'];
			$post['comments'] 			= "";	// no match
			$post['apiUid'] 			= $item['id'];		
			$post['apiHash'] 			= md5(print_r($post,TRUE));
						
			return $post;
			
		} else {
		
			return false;
		}
	}
}
?>
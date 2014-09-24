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
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * youtubeRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\YoutubeRepository
	 * @inject
	 */
	protected $youtubeRepository;
	
	/**
	 * action index
	 *
	 * @return void
	 */
	public function indexAction() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'tx_scheduler_task',
			'WHERE' => '1=1'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		$tasks = array();
		foreach($res AS $task){
			
			$youtubetask = unserialize($task['serialized_task_object']);
			if($youtubetask instanceof \TYPO3\MooxSocial\Tasks\YoutubeGetTask){
				$addTask = array();				
				$addTask['pid'] 			= $youtubetask->getPid();
				$addTask['youtubeChannel'] 	= $youtubetask->getYoutubeChannel();
				$addTask['taskUid'] 		= $youtubetask->getTaskUid();
				$tasks[] = $addTask;
			}
		}
		
		$this->view->assign('tasks', $tasks);		
		
	}
	
	/**
	 * action reinit
	 *
	 * @param string $youtubeChannel
	 * @param integer $storagePid
	 * @return void
	 */
	public function reinitAction($youtubeChannel,$storagePid) {	
		if($youtubeChannel!=""){
			
			$this->youtubeRepository->removeByPageId($youtubeChannel,$storagePid);
			
			$rawFeed 	= self::youtube($youtubeChannel,'init');			
			
			$posts 		= array();			
			$postIds 	= array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['id'],$postIds)){
					
					$postIds[] 		= $item['id'];					
					$postId 		= $item['id'];	
					
					$item['id'] 			= $postId;
					$item['youtubeChannel'] 	= $youtubeChannel;
					$item['pid'] 			= $storagePid;
					
					$post 			= self::youtubePost($item);					
					
					if(is_array($post)){
						$posts[] 	= $post;
					}
				}
				
			}			
			
			if(count($posts)){
				
				$insertCnt = 0;
				
				foreach($posts AS $post){				
										
					$youtubePost = new \TYPO3\MooxSocial\Domain\Model\Youtube;
					
					$youtubePost->setPid($post['pid']);
					$youtubePost->setCreated($post['created']);					
					$youtubePost->setUpdated($post['updated']);
					$youtubePost->setModel("youtube");
					$youtubePost->setType($post['type']);
					$youtubePost->setStatusType($post['statusType']);
					$youtubePost->setPage($post['page']);
					$youtubePost->setAction($post['action']);
					$youtubePost->setTitle($post['title']);
					$youtubePost->setSummary($post['summary']);
					$youtubePost->setText($post['text']);
					$youtubePost->setAuthor($post['author']);
					$youtubePost->setAuthorId($post['authorId']);
					$youtubePost->setDescription($post['description']);
					$youtubePost->setCaption($post['caption']);
					$youtubePost->setUrl($post['url']);
					$youtubePost->setLinkName($post['linkName']);
					$youtubePost->setLinkUrl($post['linkUrl']);
					$youtubePost->setImageUrl($post['imageUrl']);
					$youtubePost->setImageEmbedcode($post['imageEmbedcode']);
					$youtubePost->setVideoUrl($post['videoUrl']);
					$youtubePost->setVideoEmbedcode($post['videoEmbedcode']);
					$youtubePost->setSharedUrl($post['sharedUrl']);
					$youtubePost->setSharedTitle($post['sharedTitle']);
					$youtubePost->setSharedDescription($post['sharedDescription']);
					$youtubePost->setSharedCaption($post['sharedCaption']);				
					$youtubePost->setLikes($post['likes']);
					$youtubePost->setShares($post['shares']);
					$youtubePost->setComments($post['comments']);
					$youtubePost->setApiUid($post['apiUid']);					
					$youtubePost->setApiHash($post['apiHash']);
					
					$this->youtubeRepository->add($youtubePost);
					
					$insertCnt++;
					
				}	
				
				$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$this->objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Videos geladen",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			}
		}
		
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.youtube.listing.reinit.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('index');
	}
	
	/**
	 * action truncate
	 *
	 * @param string $youtubeChannel
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateAction($youtubeChannel,$storagePid) {
		if($youtubeChannel!=""){
			$this->youtubeRepository->removeByPageId($youtubeChannel,$storagePid);
		}
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.youtube.listing.truncate.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('index');
	}
	
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
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
 * Include Facebook API Tools
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Facebook/facebook.php'); 
 
/**
 *
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FacebookController extends \TYPO3\MooxSocial\Controller\PostController {

	/**
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * facebookRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\FacebookRepository
	 * @inject
	 */
	protected $facebookRepository;
	
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
		
		$res 	= $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		$tasks 	= array();
		
		foreach($res AS $task){
			
			$facebooktask = unserialize($task['serialized_task_object']);
			
			if($facebooktask instanceof \TYPO3\MooxSocial\Tasks\FacebookGetTask){
				$addTask = array();				
				$addTask['pid'] 	= $facebooktask->getPid();
				$addTask['appId'] 	= $facebooktask->getAppId();
				$addTask['secret'] 	= $facebooktask->getSecret();
				$addTask['pageId'] 	= $facebooktask->getPageId();
				$addTask['taskUid'] = $facebooktask->getTaskUid();
				$tasks[] = $addTask;
			}
		}
		
		$this->view->assign('tasks', $tasks);				
	}
	
	/**
	 * action reinit
	 *
	 * @param string $pageId
	 * @param integer $storagePid
	 * @param string $appId
	 * @param string $secret
	 * @return void
	 */
	public function reinitAction($pageId,$storagePid,$appId,$secret) {	
		if($pageId!=""){
			
			$this->facebookRepository->removeByPageId($pageId,$storagePid);
			
			$rawFeed 	= self::facebook($appId,$secret,$pageId,'init');			
			
			$posts 		= array();			
			$postIds 	= array();
			
			foreach($rawFeed['data'] as $item) {
				
				if(!in_array($item['id'],$postIds) && $item['status_type']!=""){
					
					$postIds[] 		= $item['id'];					
					$postId 		= explode("_",$item['id']);
					$postId 		= $postId[1];
					
					$item['postId'] = $postId;
					$item['pageId'] = $pageId;
					$item['pid'] 	= $storagePid;
					
					$post 			= self::facebookPost($item);					
					
					if(is_array($post)){
						$posts[] 	= $post;
					}
				}
				
			}			
			
			if(count($posts)){
				
				$insertCnt = 0;
				
				foreach($posts AS $post){				
										
					$facebookPost = new \TYPO3\MooxSocial\Domain\Model\Facebook;
					
					$facebookPost->setPid($post['pid']);
					$facebookPost->setCreated($post['created']);					
					$facebookPost->setUpdated($post['updated']);
					$facebookPost->setModel("facebook");
					$facebookPost->setType($post['type']);
					$facebookPost->setStatusType($post['statusType']);
					$facebookPost->setPage($post['page']);
					$facebookPost->setAction($post['action']);
					$facebookPost->setTitle($post['title']);
					$facebookPost->setSummary($post['summary']);
					$facebookPost->setText($post['text']);
					$facebookPost->setAuthor($post['author']);
					$facebookPost->setAuthorId($post['authorId']);
					$facebookPost->setDescription($post['description']);
					$facebookPost->setCaption($post['caption']);
					$facebookPost->setUrl($post['url']);
					$facebookPost->setLinkName($post['linkName']);
					$facebookPost->setLinkUrl($post['linkUrl']);
					$facebookPost->setImageUrl($post['imageUrl']);
					$facebookPost->setImageEmbedcode($post['imageEmbedcode']);
					$facebookPost->setVideoUrl($post['videoUrl']);
					$facebookPost->setVideoEmbedcode($post['videoEmbedcode']);
					$facebookPost->setSharedUrl($post['sharedUrl']);
					$facebookPost->setSharedTitle($post['sharedTitle']);
					$facebookPost->setSharedDescription($post['sharedDescription']);
					$facebookPost->setSharedCaption($post['sharedCaption']);				
					$facebookPost->setLikes($post['likes']);
					$facebookPost->setShares($post['shares']);
					$facebookPost->setComments($post['comments']);
					$facebookPost->setApiUid($post['apiUid']);					
					$facebookPost->setApiHash($post['apiHash']);
					
					$this->facebookRepository->add($facebookPost);
					
					$insertCnt++;
					
				}	
				
				$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$this->objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				if($insertCnt>0){
					\TYPO3\MooxSocial\Controller\AdministrationController::clearCache("mooxsocial_pi1",array());
				}
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Posts geladen",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			}
		}
		
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.facebook.listing.reinit.success', $this->extensionName ),
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
	 * @param string $pageId
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateAction($pageId,$storagePid) {
		if($pageId!=""){
			$this->facebookRepository->removeByPageId($pageId,$storagePid);
		}
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.facebook.listing.truncate.success', $this->extensionName ),
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
			if($this->settings['api_app_id']==""){
				$this->settings['api_app_id'] = $extConf['fallbackFacebookAppId'];
			}		
			if($this->settings['api_secret']==""){
				$this->settings['api_secret'] = $extConf['fallbackFacebookSecret'];
			}
			
			if($this->settings['api_app_id']!="" && $this->settings['api_secret']!="" && $this->settings['api_page_id']!=""){				
				$posts = $this->facebookRepository->requestAllBySettings($this->settings);				
			}
			
		} else {
		
			$count 	= $this->facebookRepository->findAll($this->settings['page_id'])->count();

			$posts 	= $this->facebookRepository->findAllBySettings($this->settings);
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
			if($this->settings['api_app_id']==""){
				$this->settings['api_app_id'] = $extConf['fallbackFacebookAppId'];
			}		
			if($this->settings['api_secret']==""){
				$this->settings['api_secret'] = $extConf['fallbackFacebookSecret'];
			}			
			if($this->settings['api_app_id']!="" && $this->settings['api_secret']!="" && $this->settings['api_page_id']!=""){				
				$posts = $this->facebookRepository->requestAllBySettings($this->settings);								
			}
			
		} else {
		
			$posts 	= $this->facebookRepository->findAllBySettings($this->settings);
		}
		
		$this->view->assign('currentpid', $GLOBALS['TSFE']->id);
		$this->view->assign('posts', $posts);		
	}

	/**
	 * action show
	 *
	 * @param \TYPO3\MooxSocial\Domain\Model\Facebook $facebook
	 * @return void
	 */
	public function showAction(\TYPO3\MooxSocial\Domain\Model\Facebook $facebook = NULL) {				
		
		if(!$facebook && $this->settings['source']!="api"){
			$facebook	= $this->facebookRepository->findRandomOne($this->settings['page_id']);
			$this->view->assign('israndom', TRUE);			
		}
		
		$this->view->assign('facebook', $facebook);
	}
	
	/**
	 * execute facebook api request
	 *
	 * @param string $appId
	 * @param string $secret
	 * @param string $pageId
	 * @param string $request
	 * @return array feedData
	 */
	public function facebook($appId,$secret,$pageId, $request = "") {
		$rawFeed = array();
		
		// Get the extensions's configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);		
		if($appId==""){
			$appId = $extConf['fallbackFacebookAppId'];
		}		
		if($secret==""){
			$secret = $extConf['fallbackFacebookSecret'];
		}

		if($appId!="" && $secret!="" && $pageId!=""){
						
			$config = array(
				'appId'				 	=> $appId,
				'secret'				=> $secret,
				'pageid' 				=> $pageId,
				'allowSignedRequest' 	=> false
			);
			
			$facebook = new \TYPO3\MooxSocial\Facebook\Facebook($config);
			if($request=="fullinit"){
				$repeats = 999;
				$request = "init";
			} else {
				$repeats = 10;
			}
			if($request=="init"){
				$rawFeedData 	= array();
				$rawFeedIds 	= array();
				$until 			= time();				
				for($i=1;$i<=$repeats;$i++){
					$since = $until-(604800*16);
					$request="posts?limit=250&since=".$since."&until=".$until; //."&limit=10000"; limit > 250 is not allowed
					$url = '/' . $pageId . '/'.$request;
					$rawFeedTmp = $facebook->api($url);
					if(count($rawFeedTmp['data'])>0){
						foreach($rawFeedTmp['data'] AS $data){
							if(!in_array($data['id'],$rawFeedIds)){
								$rawFeedData[] 	= $data;
								$rawFeedIds[] 	= $data['id'];
							}
						}
					} else {
						break;
					}
					$until = $since;
				}
				$rawFeed = array("data" => $rawFeedData);
			} else {
				$request="posts?limit=25";
				$url = '/' . $pageId . '/'.$request;
				$rawFeed = $facebook->api($url);
			}			
		}
		
		return $rawFeed;
	}

	/**
	 * prepare facebook post
	 *
	 * @param array $item	
	 * @return array post
	 */
	public function facebookPost($item) {
		
		if(is_array($item)){
			
			$post = array();
			
			if(strpos($item['status_type'],"shared_")!== false){
				$post['title'] 				= ""; 						// no match
				$post['description'] 		= "";
				$post['caption'] 			= "";
				$post['sharedUrl'] 			= $item['sharedUrl']; 		// no match
				$post['sharedTitle'] 		= $item['sharedTitle']; 	// no match
				$post['sharedDescription'] 	= $item['description'];
				$post['sharedCaption'] 		= $item['caption'];
			} else {
				$post['title'] 				= $item['title']; 			// no match
				$post['description'] 		= $item['description'];
				$post['caption'] 			= $item['caption'];
				$post['sharedUrl'] 			= ""; 						// no match
				$post['sharedTitle'] 		= ""; 						// no match
				$post['sharedDescription'] 	= "";
				$post['sharedCaption'] 		= "";
			}
			if($item['type']=="photo" && $item['object_id']>0){
				$item['picture'] = "https://graph.facebook.com/".$item['object_id']."/picture?type=normal";
			} elseif(strpos($item['picture'], "safe_image.php")!==false){	
				$safeImageUrl = urldecode($item['picture']);
				$safeImageUrl = explode("&url=",$safeImageUrl);
				$safeImageUrl = $safeImageUrl[1];
				$safeImageUrl = explode("&",$safeImageUrl);
				$safeImageUrl = $safeImageUrl[0];
				$check = get_headers($safeImageUrl);
				if(strpos($check[0], "200")!==false && strpos(strtolower($check[0]), "ok")!==false){
					$item['picture'] = utf8_encode($safeImageUrl);
				} else {
					$item['picture'] = "";
				}				
			}
			$post['pid'] 					= $item['pid'];
			$post['created'] 				= strtotime($item['created_time']);
			$post['updated'] 				= strtotime($item['created_time']);
			$post['type'] 					= $item['type'];
			$post['statusType'] 			= $item['status_type'];
			$post['page'] 					= $item['pageId'];
			$post['action'] 				= $item['story'];				
			$post['summary'] 				= $item['summary']; 			// no match
			$post['text'] 					= $item['message'];
			$post['author'] 				= $item['from']['name'];
			$post['authorId'] 				= $item['from']['id'];
			$post['url'] 					= "https://www.facebook.com/".$item['pageId']."/posts/".$item['postId'];
			$post['linkName'] 				= $item['name'];
			$post['linkUrl'] 				= $item['link'];
			$post['imageUrl'] 				= $item['picture'];
			$post['imageEmbedcode'] 		= $item['imageEmbedcode']; 		// no match
			$post['videoUrl'] 				= $item['source'];
			$post['videoEmbedcode'] 		= $item['videoEmbedcode']; 		// no match				
			$post['likes'] 					= count($item['likes']['data']);
			$post['shares'] 				= count($item['shares']['data']);
			$post['comments'] 				= count($item['comments']['data']);
			$post['apiUid'] 				= $item['id'];				
			$post['apiHash'] 				= md5(print_r($post,TRUE));
						
			return $post;
			
		} else {
		
			return false;
		}
	}
}
?>
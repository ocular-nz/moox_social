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
class AdministrationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * facebookRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\FacebookRepository
	 * @inject
	 */
	protected $facebookRepository;
	
	/**
	 * twitterRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\TwitterRepository
	 * @inject
	 */
	protected $twitterRepository;

	/**
	 * youtubeRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\YoutubeRepository
	 * @inject
	 */
	protected $youtubeRepository;
	
	/**
	 * flickrRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\FlickrRepository
	 * @inject
	 */
	protected $flickrRepository;
	
	/**
	 * slideshareRepository
	 *
	 * @var \TYPO3\MooxSocial\Domain\Repository\SlideshareRepository
	 * @inject
	 */
	protected $slideshareRepository;
	
	/**
	 * action overview facebook
	 *
	 * @return void
	 */
	public function overviewFacebookAction() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'tx_scheduler_task',
			'WHERE' => '1=1'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		$tasks = array();
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
	 * action overview twitter
	 *
	 * @return void
	 */
	public function overviewTwitterAction() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'tx_scheduler_task',
			'WHERE' => '1=1'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		$tasks = array();
		foreach($res AS $task){
			
			$twittertask = unserialize($task['serialized_task_object']);
			if($twittertask instanceof \TYPO3\MooxSocial\Tasks\TwitterGetTask){
				$addTask = array();				
				$addTask['pid'] 			= $twittertask->getPid();
				$addTask['oauthAccessToken'] 		= $twittertask->getOauthAccessToken();
				$addTask['oauthAccessTokenSecret'] 	= $twittertask->getOauthAccessTokenSecret();
				$addTask['consumerKey']			= $twittertask->getConsumerKey();
				$addTask['consumerKeySecret'] 		= $twittertask->getConsumerKeySecret();
				$addTask['screenName'] 			= $twittertask->getScreenName();
				$addTask['taskUid'] 			= $twittertask->getTaskUid();
				$tasks[] = $addTask;
			}
		}
		
		$this->view->assign('tasks', $tasks);		
		
	}
	
	/**
	 * action overview youtube
	 *
	 * @return void
	 */
	public function overviewYoutubeAction() {
		
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
				$addTask['youtubeChannel'] 		= $youtubetask->getYoutubeChannel();
				$addTask['taskUid'] 			= $youtubetask->getTaskUid();
				$tasks[] = $addTask;
			}
		}
		
		$this->view->assign('tasks', $tasks);		
		
	}
	
	/**
	 * action overview flickr
	 *
	 * @return void
	 */
	public function overviewFlickrAction() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'tx_scheduler_task',
			'WHERE' => '1=1'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		$tasks = array();
		foreach($res AS $task){
			
			$flickrtask = unserialize($task['serialized_task_object']);
			if($flickrtask instanceof \TYPO3\MooxSocial\Tasks\FlickrGetTask){
				$addTask = array();				
				$addTask['pid'] 			= $flickrtask->getPid();
				$addTask['apiKey'] 			= $flickrtask->getApiKey();
				$addTask['apiSecretKey'] 		= $flickrtask->getApiSecretKey();
				$addTask['userId'] 			= $flickrtask->getUserId();
				$addTask['taskUid'] 			= $flickrtask->getTaskUid();
				$tasks[] = $addTask;
			}
		}
		
		$this->view->assign('tasks', $tasks);		
		
	}
	
	/**
	 * action overview slideshare
	 *
	 * @return void
	 */
	public function overviewSlideshareAction() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'tx_scheduler_task',
			'WHERE' => '1=1'
		);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		$tasks = array();
		foreach($res AS $task){
			
			$slidesharetask = unserialize($task['serialized_task_object']);
			if($slidesharetask instanceof \TYPO3\MooxSocial\Tasks\SlideshareGetTask){
				$addTask = array();				
				$addTask['pid'] 			= $slidesharetask->getPid();
				$addTask['apiKey'] 			= $slidesharetask->getApiKey();
				$addTask['apiSecretKey'] 		= $slidesharetask->getApiSecretKey();
				$addTask['userId'] 			= $slidesharetask->getUserId();
				$addTask['taskUid'] 			= $slidesharetask->getTaskUid();
				$tasks[] = $addTask;
			}
		}
		
		$this->view->assign('tasks', $tasks);		
		
	}
	
	/**
	 * action overview folders
	 *
	 * @return void
	 */
	public function overviewFoldersAction() {
		
		$folders = array();
		
		$addFolder = array();				
		$addFolder['uid'] 		= 0;
		$addFolder['title'] 	= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.folder.listing.default_storage', $this->extensionName );
		$addFolder['countFacebook'] = $this->facebookRepository->findAllByStoragePid(0)->count();
		$addFolder['countTwitter'] = $this->twitterRepository->findAllByStoragePid(0)->count();
		$addFolder['countYoutube'] = $this->youtubeRepository->findAllByStoragePid(0)->count();
		$addFolder['countFlickr'] = $this->flickrRepository->findAllByStoragePid(0)->count();
		$addFolder['count']   = $addFolder['countFacebook']+$addFolder['countTwitter']+$addFolder['countYoutube']+$addFolder['countFlickr'];	
		
		$folders[] = $addFolder;
		
		$result = $this->getSocialFolders();
		
		foreach($result AS $folder){
						
			$addFolder = array();				
			$addFolder['uid'] 		= $folder['uid'];
			$addFolder['title'] 	= $folder['title'];
			$addFolder['countFacebook'] = $this->facebookRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countTwitter'] = $this->twitterRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countYoutube'] = $this->youtubeRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countFlickr'] = $this->flickrRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['count']   = $addFolder['countFacebook']+$addFolder['countTwitter']+$addFolder['countYoutube']+$addFolder['countFlickr'];	
			$folders[] = $addFolder;			
		}
		
		$this->view->assign('folders', $folders);		
		
	}
	
	/**
	 * action reinit facebook
	 *
	 * @param string $pageId
	 * @param integer $storagePid
	 * @param string $appId
	 * @param string $secret
	 * @return void
	 */
	public function reinitFacebookAction($pageId,$storagePid,$appId,$secret) {	
		if($pageId!=""){
			
			$this->facebookRepository->removeByPageId($pageId,$storagePid);
			
			$rawFeed 	= \TYPO3\MooxSocial\Controller\FacebookController::facebook($appId,$secret,$pageId,'init');			
			
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
					
					$post 			= \TYPO3\MooxSocial\Controller\FacebookController::facebookPost($item);					
					
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
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
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
		$this->redirect('overviewFacebook');
	}
	
	/**
	 * action reinit twitter
	 *
	 * @param string $screenName
	 * @param integer $storagePid
	 * @param string $oauthAccessToken
	 * @param string $oauthAccessTokenSecret
	 * @param string $consumerKey
	 * @param string $consumerKeySecret
	 * @return void
	 */
	public function reinitTwitterAction($screenName,$storagePid,$oauthAccessToken,$oauthAccessTokenSecret,$consumerKey,$consumerKeySecret) {	
		if($screenName!=""){
			
			$this->twitterRepository->removeByPageId($screenName,$storagePid);
			
			$rawFeed 	= \TYPO3\MooxSocial\Controller\TwitterController::twitter($oauthAccessToken,$oauthAccessTokenSecret,$consumerKey,$consumerKeySecret,$screenName,'init');			
			
			$posts 		= array();			
			$postIds 	= array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['id'],$postIds)){
					
					$postIds[] 		= $item['id'];					
					$postId 		= $item['id'];	
					
					$item['id'] 			= $postId;
					$item['user']['screen_name'] 	= $screenName;
					$item['id_str'] 			= $storagePid;
					
					$post 			= \TYPO3\MooxSocial\Controller\TwitterController::twitterPost($item);					
					
					if(is_array($post)){
						$posts[] 	= $post;
					}
				}
				
			}			
			
			if(count($posts)){
				
				$insertCnt = 0;
				
				foreach($posts AS $post){				
										
					$twitterPost = new \TYPO3\MooxSocial\Domain\Model\Twitter;
					
					$twitterPost->setPid($post['pid']);
					$twitterPost->setCreated($post['created']);					
					$twitterPost->setUpdated($post['updated']);
					$twitterPost->setModel("twitter");
					$twitterPost->setType($post['type']);
					$twitterPost->setStatusType($post['statusType']);
					$twitterPost->setPage($post['page']);
					$twitterPost->setAction($post['action']);
					$twitterPost->setTitle($post['title']);
					$twitterPost->setSummary($post['summary']);
					$twitterPost->setText($post['text']);
					$twitterPost->setAuthor($post['author']);
					$twitterPost->setAuthorId($post['authorId']);
					$twitterPost->setDescription($post['description']);
					$twitterPost->setCaption($post['caption']);
					$twitterPost->setUrl($post['url']);
					$twitterPost->setLinkName($post['linkName']);
					$twitterPost->setLinkUrl($post['linkUrl']);
					$twitterPost->setImageUrl($post['imageUrl']);
					$twitterPost->setImageEmbedcode($post['imageEmbedcode']);
					$twitterPost->setVideoUrl($post['videoUrl']);
					$twitterPost->setVideoEmbedcode($post['videoEmbedcode']);
					$twitterPost->setSharedUrl($post['sharedUrl']);
					$twitterPost->setSharedTitle($post['sharedTitle']);
					$twitterPost->setSharedDescription($post['sharedDescription']);
					$twitterPost->setSharedCaption($post['sharedCaption']);				
					$twitterPost->setLikes($post['likes']);
					$twitterPost->setShares($post['shares']);
					$twitterPost->setComments($post['comments']);
					$twitterPost->setApiUid($post['apiUid']);					
					$twitterPost->setApiHash($post['apiHash']);
					
					$this->twitterRepository->add($twitterPost);
					
					$insertCnt++;
					
				}	
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Tweets geladen",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			}
		}
		
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.twitter.listing.reinit.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewTwitter');
	}
	
	/**
	 * action reinit youtube
	 *
	 * @param string $youtubeChannel
	 * @param integer $storagePid
	 * @return void
	 */
	public function reinitYoutubeAction($youtubeChannel,$storagePid) {	
		if($youtubeChannel!=""){
			
			$this->youtubeRepository->removeByPageId($youtubeChannel,$storagePid);
			
			$rawFeed 	= \TYPO3\MooxSocial\Controller\YoutubeController::youtube($youtubeChannel,'init');			
			
			$posts 		= array();			
			$postIds 	= array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['id'],$postIds)){
					
					$postIds[] 		= $item['id'];					
					$postId 		= $item['id'];	
					
					$item['id'] 			= $postId;
					$item['youtubeChannel'] 	= $youtubeChannel;
					$item['pid'] 			= $storagePid;
					
					$post 			= \TYPO3\MooxSocial\Controller\YoutubeController::youtubePost($item);					
					
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
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
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
		$this->redirect('overviewYoutube');
	}
	
	/**
	 * action reinit flickr
	 *
	 * @param string $apiKey
	 * @param string $apiSecretKey
	 * @param string $userId
	 * @param integer $storagePid
	 * @return void
	 */
	public function reinitFlickrAction($apiKey,$apiSecretKey,$userId,$storagePid) {	
		if($apiKey!="" && $apiSecretKey!="" && $userId!=""){
			
			$this->flickrRepository->removeByPageId($userId,$storagePid);
			
			$rawFeed 	= \TYPO3\MooxSocial\Controller\FlickrController::flickr($apiKey,$apiSecretKey,$userId,'init');			
			
			$posts 		= array();			
			$postIds 	= array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['id'],$postIds)){
					
					$postIds[] 		= $item['id'];					
					$postId 		= $item['id'];	
					
					$item['id'] 		= $postId;
					$item['userId']		= $userId;
					$item['pid'] 		= $storagePid;
					
					$post 			= \TYPO3\MooxSocial\Controller\FlickrController::flickrPost($item);					
					
					if(is_array($post)){
						$posts[] 	= $post;
					}
				}
				
			}			
			
			if(count($posts)){
				
				$insertCnt = 0;
				
				foreach($posts AS $post){				
										
					$flickrPost = new \TYPO3\MooxSocial\Domain\Model\Flickr;
					
					$flickrPost->setPid($post['pid']);
					$flickrPost->setCreated($post['created']);					
					$flickrPost->setUpdated($post['updated']);
					$flickrPost->setModel("flickr");
					$flickrPost->setType($post['type']);
					$flickrPost->setStatusType($post['statusType']);
					$flickrPost->setPage($post['page']);
					$flickrPost->setAction($post['action']);
					$flickrPost->setTitle($post['title']);
					$flickrPost->setSummary($post['summary']);
					$flickrPost->setText($post['text']);
					$flickrPost->setAuthor($post['author']);
					$flickrPost->setAuthorId($post['authorId']);
					$flickrPost->setDescription($post['description']);
					$flickrPost->setCaption($post['caption']);
					$flickrPost->setUrl($post['url']);
					$flickrPost->setLinkName($post['linkName']);
					$flickrPost->setLinkUrl($post['linkUrl']);
					$flickrPost->setImageUrl($post['imageUrl']);
					$flickrPost->setImageEmbedcode($post['imageEmbedcode']);
					$flickrPost->setVideoUrl($post['videoUrl']);
					$flickrPost->setVideoEmbedcode($post['videoEmbedcode']);
					$flickrPost->setSharedUrl($post['sharedUrl']);
					$flickrPost->setSharedTitle($post['sharedTitle']);
					$flickrPost->setSharedDescription($post['sharedDescription']);
					$flickrPost->setSharedCaption($post['sharedCaption']);				
					$flickrPost->setLikes($post['likes']);
					$flickrPost->setShares($post['shares']);
					$flickrPost->setComments($post['comments']);
					$flickrPost->setApiUid($post['apiUid']);					
					$flickrPost->setApiHash($post['apiHash']);
					
					$this->flickrRepository->add($flickrPost);
					
					$insertCnt++;
					
				}	
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Bilder geladen",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			}
		}
		
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.flickr.listing.reinit.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewFlickr');
	}
	
	/**
	 * action reinit slideshare
	 *
	 * @param string $apiKey
	 * @param string $apiSecretKey
	 * @param string $userId
	 * @param integer $storagePid
	 * @return void
	 */
	public function reinitSlideshareAction($apiKey,$apiSecretKey,$userId,$storagePid) {	
		if($apiKey!="" && $apiSecretKey!="" && $userId!=""){
			
			$this->slideshareRepository->removeByPageId($userId,$storagePid);
			
			$rawFeed 	= \TYPO3\MooxSocial\Controller\SlideshareController::slideshare($apiKey,$apiSecretKey,$userId,'init');			
			
			$posts 		= array();			
			$postIds 	= array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['ID'],$postIds)){
					
					$postIds[] 		= $item['ID'];					
					$postId 		= $item['ID'];	
					
					$item['id'] 		= $postId;
					$item['userId']		= $userId;
					$item['pid'] 		= $storagePid;
					
					$post 			= \TYPO3\MooxSocial\Controller\SlideshareController::slidesharePost($item);					
					
					if(is_array($post)){
						$posts[] 	= $post;
					}
				}
				
			}			
			
			if(count($posts)){
				
				$insertCnt = 0;
				
				foreach($posts AS $post){				
										
					$slidesharePost = new \TYPO3\MooxSocial\Domain\Model\Slideshare;
					
					$slidesharePost->setPid($post['pid']);
					$slidesharePost->setCreated($post['created']);					
					$slidesharePost->setUpdated($post['updated']);
					$slidesharePost->setModel("slideshare");
					$slidesharePost->setType($post['type']);
					$slidesharePost->setStatusType($post['statusType']);
					$slidesharePost->setPage($post['page']);
					$slidesharePost->setAction($post['action']);
					$slidesharePost->setTitle($post['title']);
					$slidesharePost->setSummary($post['summary']);
					$slidesharePost->setText($post['text']);
					$slidesharePost->setAuthor($post['author']);
					$slidesharePost->setAuthorId($post['authorId']);
					$slidesharePost->setDescription($post['description']);
					$slidesharePost->setCaption($post['caption']);
					$slidesharePost->setUrl($post['url']);
					$slidesharePost->setLinkName($post['linkName']);
					$slidesharePost->setLinkUrl($post['linkUrl']);
					$slidesharePost->setImageUrl($post['imageUrl']);
					$slidesharePost->setImageEmbedcode($post['imageEmbedcode']);
					$slidesharePost->setVideoUrl($post['videoUrl']);
					$slidesharePost->setVideoEmbedcode($post['videoEmbedcode']);
					$slidesharePost->setSharedUrl($post['sharedUrl']);
					$slidesharePost->setSharedTitle($post['sharedTitle']);
					$slidesharePost->setSharedDescription($post['sharedDescription']);
					$slidesharePost->setSharedCaption($post['sharedCaption']);				
					$slidesharePost->setLikes($post['likes']);
					$slidesharePost->setShares($post['shares']);
					$slidesharePost->setComments($post['comments']);
					$slidesharePost->setApiUid($post['apiUid']);					
					$slidesharePost->setApiHash($post['apiHash']);
					
					$this->slideshareRepository->add($slidesharePost);
					
					$insertCnt++;
					
				}	
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Praesentationen geladen",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			}
		}
		
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.slideshare.listing.reinit.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewSlideshare');
	}
	
	/**
	 * action truncate facebook
	 *
	 * @param string $pageId
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateFacebookAction($pageId,$storagePid) {
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
		$this->redirect('overviewFacebook');
	}
	
	/**
	 * action truncate twitter
	 *
	 * @param string $screenName
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateTwitterAction($screenName,$storagePid) {
		if($screenName!=""){
			$this->twitterRepository->removeByPageId($screenName,$storagePid);
		}
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.twitter.listing.truncate.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewTwitter');
	}
	
	/**
	 * action truncate youtube
	 *
	 * @param string $youtubeChannel
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateYoutubeAction($youtubeChannel,$storagePid) {
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
		$this->redirect('overviewYoutube');
	}
	
	/**
	 * action truncate flickr
	 *
	 * @param string $userId
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateFlickrAction($userId,$storagePid) {
		if($userId!=""){
			$this->flickrRepository->removeByPageId($userId,$storagePid);
		}
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.flickr.listing.truncate.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewFlickr');
	}
	
	/**
	 * action truncate slideshare
	 *
	 * @param string $userId
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateSlideshareAction($userId,$storagePid) {
		if($userId!=""){
			$this->slideshareRepository->removeByPageId($userId,$storagePid);
		}
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.slideshare.listing.truncate.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewSlideshare');
	}
	
	/**
	 * action truncate folder
	 *	
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateFolderAction($storagePid) {
		$this->facebookRepository->removeByStoragePid($storagePid);		
		$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.folder.listing.truncate.success', $this->extensionName ),
			 '', // the header is optional
			 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
			 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
		);
		\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
		$this->redirect('overviewFolders');
	}
	
	/**
	 * Get array of folders with social module	
	 *	
	 * @return	array	folders with social module	
	 */
	public function getSocialFolders() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'pages',
			'WHERE' => "deleted=0 AND doktype=254 AND module='social'"
		);
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		return $pages;		
	}

}
?>
<?php
namespace TYPO3\MooxSocial\Tasks;

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
 * Include Flickr API Tools
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Flickr/phpFlickr.php'); 


/**
 * Include Flickr Repository
 */
//require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Domain/Repository/Flickr.php'); 

/**
 * Get Flickr videos
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FlickrGetTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {		
	
	/**
	 * Sicherheitszeitraum für Zeitüberschneidungen während der zyklischen Ausführung des Tasks
	 *
	 * @var integer
	 */
	public $intervalBuffer = 300;
	
	/**
	 * PID der Seite/Ordner in dem die Posts dieses Tasks gespeichert werden sollen
	 *
	 * @var integer
	 */
	public $pid;
	
	/**
	 * Flickr api key
	 *
	 * @var string
	 */
	public $apiKey;
	
	/**
	 * Flickr api secret key
	 *
	 * @var string
	 */
	public $apiSecretKey;
	
	/**
	 * Flickr user ID
	 *
	 * @var string
	 */
	public $userId;
	
	/**
	 * Works through the indexing queue and indexes the queued items into Solr.
	 *
	 * @return	boolean	Returns TRUE on success, FALSE if no items were indexed or none were found.
	 * @see	typo3/sysext/scheduler/tx_scheduler_Task#execute()
	 */
	public function execute() {						
		
		// Get the extensions's configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['moox_social']);		
		if($extConf['debugEmailSenderName']==""){
			$extConf['debugEmailSenderName'] = $extConf['debugEmailSenderAddress'];
		}		
		if($this->email==""){
			$this->email = $extConf['debugEmailReceiverAddress'];			
		}
		
		$executionSucceeded = FALSE;
		
		if(!$this->pid){
			$this->pid = 0;
		}
		
		if($this->apiKey!="" && $this->apiSecretKey!="" && $this->userId!=""){
			
			$execution 	= $this->getExecution();
			$interval 	= $execution->getInterval();
			$time 		= time();
			$to		= $time;
			$from		= ($time-$interval-$this->intervalBuffer);			
			
			try {			
				$rawFeed = \TYPO3\MooxSocial\Controller\FlickrController::flickr($this->apiKey,$this->apiSecretKey,$this->userId);				
				/*print "<pre>";
                                print_r($rawFeed);
                                print "</pre>";
				exit();*/
				$executionSucceeded = TRUE;
			} catch (\Exception $e) {				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_execution_error')." [". $e->getMessage()."]",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
				if($this->email && $extConf['debugEmailSenderAddress']){				
					$lockfile = $_SERVER['DOCUMENT_ROOT']."/typo3temp/.lock-email-task-".md5($this->apiKey.$this->apiSecretKey.$this->userId);
					if(file_exists($lockfile)){
						$lockfiletime = filemtime($lockfile);
						if($lockfiletime<(time()-86400)){
							unlink($lockfile);
						}
					}
					if(!file_exists($lockfile)){						
						$message = (new \TYPO3\CMS\Core\Mail\MailMessage())
									->setFrom(array($extConf['debugEmailSenderAddress'] => $extConf['debugEmailSenderName']))
									->setTo(array($this->email => $this->email))
									->setSubject($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_error_mailsubject'))
									->setBody('ERROR: while requesting [api key: '.$this->apiKey.' | api secret key: '.$this->apiSecretKey.' | user id: '.$this->userId."]");
									$message->send();
						touch($lockfile);
					}
				}
			}	
			
			
			
			$posts = array();
			
			$postIds = array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['id'],$postIds)){
					
					$postIds[] 		= $item['id'];					
					$postId 		= $item['id'];
					
					$item['id'] 		= $postId;
					$item['userId'] 	= $this->userId;
					$item['pid'] 		= $this->pid;
					
					$post 			= \TYPO3\MooxSocial\Controller\FlickrController::flickrPost($item);					
					
					if(is_array($post)){
						$posts[] 		= $post;
					}
				}
				
			}
			
			if(count($posts)){
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$flickrRepository = $objectManager->get('\TYPO3\MooxSocial\Domain\Repository\FlickrRepository');       
				
				$insertCnt = 0;
				$updateCnt = 0;
				
				foreach($posts AS $post){				
										
					$flickrPost		= $flickrRepository->findOneByApiUid($post['apiUid'],$this->pid);
					
					if(!($flickrPost instanceof \TYPO3\MooxSocial\Domain\Model\Flickr)){
						$flickrPost = new \TYPO3\MooxSocial\Domain\Model\Flickr;
						$action	= "insert";						
					}
					
					if($action=="insert"){
						$flickrPost->setPid($post['pid']);
						$flickrPost->setCreated($post['created']);
					}
					
					$flickrPost->setUpdated($post['updated']);
					$flickrPost->setType($post['type']);
					$flickrPost->setStatusType($post['statusType']);
					
					if($action=="insert"){
						$flickrPost->setPage($post['page']);
						$flickrPost->setModel("flickr");
					}
					
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
					
					if($action=="insert"){
						$flickrPost->setApiUid($post['apiUid']);
					}
					
					$flickrPost->setApiHash($post['apiHash']);
					
					if($action=="insert"){
						$flickrRepository->add($flickrPost);
						$insertCnt++;
					} else {
						$flickrRepository->update($flickrPost);
						$updateCnt++;
					}
				}	
				
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Bilder geladen | ".$updateCnt." bestehende Bilder aktualisiert",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			} else {
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					 "Keine neuen oder aktualisierten Bilder gefunden",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			}
		} 				

		return $executionSucceeded;
	}
	
	/**
	 * This method returns the sleep duration as additional information
	 *
	 * @return string Information to display
	 */
	public function getAdditionalInformation() {
		$info = $GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.pid_label') . ': ' . $this->pid;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_key_label') . ': ' . $this->apiKey;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_secret_key_label') . ': ' . $this->apiSecretKey;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.user_id_label') . ': ' . $this->userId;
		if($this->email){
			$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.email_label') . ': ' . $this->email;
		}
		return $info;
	}
	
	/**
	 * Returns the pid
	 *
	 * @return integer
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * Set the pid
	 *
	 * @param integer $pid pid
	 * @return void
	 */
	public function setPid($pid) {
		$this->pid = $pid;
	}
	
	/**
	 * Returns the api key
	 *
	 * @return integer
	 */
	public function getApiKey() {
		return $this->apiKey;
	}

	/**
	 * Set the api key
	 *
	 * @param integer $apiKey api key
	 * @return void
	 */
	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
	}
	
	/**
	 * Returns the api secret key
	 *
	 * @return integer
	 */
	public function getApiSecretKey() {
		return $this->apiSecretKey;
	}

	/**
	 * Set the api secret key
	 *
	 * @param integer $apiKey api secret key
	 * @return void
	 */
	public function setApiSecretKey($apiSecretKey) {
		$this->apiSecretKey = $apiSecretKey;
	}
	
	/**
	 * Returns the user id
	 *
	 * @return integer
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * Set the user id
	 *
	 * @param integer $userId user id
	 * @return void
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}
	
	/**
	 * Returns the page id
	 *
	 * @return integer
	 */
	public function getPageId() {
		return $this->pageId;
	}

	/**
	 * Set the page id
	 *
	 * @param integer $pageId page id
	 * @return void
	 */
	public function setPageId($pageId) {
		$this->pageId = $pageId;
	}
}
?>
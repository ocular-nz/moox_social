<?php
namespace DCNGmbH\MooxSocial\Tasks;

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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;

 /**
 * Include Slideshare API Tools
 */
//require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Slideshare/SSUtil.php'); 


/**
 * Include Slideshare Repository
 */
//require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Domain/Repository/Slideshare.php'); 

/**
 * Get Slideshare slideshows
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class SlideshareGetTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {		
	
	/**
	 * Sicherheitszeitraum f�r Zeit�berschneidungen w�hrend der zyklischen Ausf�hrung des Tasks
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
	 * Slideshare api key
	 *
	 * @var string
	 */
	public $apiKey;
	
	/**
	 * Slideshare api secret key
	 *
	 * @var string
	 */
	public $apiSecretKey;
	
	/**
	 * Slideshare user ID
	 *
	 * @var string
	 */
	public $userId;

	/**
	 * flash message service
	 *
	 * @var \TYPO3\CMS\Core\Messaging\FlashMessageService
	 */
	public $flashMessageService;
	
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
				$rawFeed = \DCNGmbH\MooxSocial\Controller\SlideshareController::slideshare($this->apiKey,$this->apiSecretKey,$this->userId);
				/*print "<pre>";
                                print_r($rawFeed);
                                print "</pre>";
				exit();*/
				$executionSucceeded = TRUE;
			} catch (\Exception $e) {				
				$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.api_execution_error')." [". $e->getMessage()."]",
					 '',
					 FlashMessage::ERROR,
					 TRUE
				);
				$flashMessageQueue->addMessage($message);
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
									->setSubject($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.api_error_mailsubject'))
									->setBody('ERROR: while requesting [api key: '.$this->apiKey.' | api secret key: '.$this->apiSecretKey.' | user id: '.$this->userId."]");
									$message->send();
						touch($lockfile);
					}
				}
			}	
			
			
			
			$posts = array();
			
			$postIds = array();
			
			foreach($rawFeed as $item) {
				
				if(!in_array($item['ID'],$postIds)){
					
					$postIds[] 		= $item['ID'];					
					$postId 		= $item['ID'];
					
					$item['id'] 		= $postId;
					$item['userId'] 	= $this->userId;
					$item['pid'] 		= $this->pid;
					
					$post 			= \DCNGmbH\MooxSocial\Controller\SlideshareController::slidesharePost($item);
					
					if(is_array($post)){
						$posts[] 		= $post;
					}
				}
				
			}
			
			if(count($posts)){
				
				$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$slideshareRepository = $objectManager->get('DCNGmbH\\MooxSocial\\Domain\\Repository\\SlideshareRepository');
				
				$insertCnt = 0;
				$updateCnt = 0;
				
				foreach($posts AS $post){				
										
					$slidesharePost		= $slideshareRepository->findOneByApiUid($post['apiUid'],$this->pid);
					
					if(!($slidesharePost instanceof \DCNGmbH\MooxSocial\Domain\Model\Slideshare)){
						$slidesharePost = new \DCNGmbH\MooxSocial\Domain\Model\Slideshare;
						$action	= "insert";						
					}
					
					if($action=="insert"){
						$slidesharePost->setPid($post['pid']);
						$slidesharePost->setCreated($post['created']);
					}
					
					$slidesharePost->setUpdated($post['updated']);
					$slidesharePost->setType($post['type']);
					$slidesharePost->setStatusType($post['statusType']);
					
					if($action=="insert"){
						$slidesharePost->setPage($post['page']);
						$slidesharePost->setModel("slideshare");
					}
					
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
					
					if($action=="insert"){
						$slidesharePost->setApiUid($post['apiUid']);
					}
					
					$slidesharePost->setApiHash($post['apiHash']);
					
					if($action=="insert"){
						$slideshareRepository->add($slidesharePost);
						$insertCnt++;
					} else {
						$slideshareRepository->update($slidesharePost);
						$updateCnt++;
					}
				}	
				
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Praesentationen geladen | ".$updateCnt." bestehende Praesentationen aktualisiert",
					 '',
					 FlashMessage::OK,
					 TRUE
				);
				$flashMessageQueue->addMessage($message);
			} else {
				$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					 "Keine neuen oder aktualisierten Praesentationen gefunden",
					 '',
					 FlashMessage::OK,
					 TRUE
				);
				$flashMessageQueue->addMessage($message);
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
		$info = $GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.pid_label') . ': ' . $this->pid;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.api_key_label') . ': ' . $this->apiKey;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.api_secret_key_label') . ': ' . $this->apiSecretKey;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.user_id_label') . ': ' . $this->userId;
		if($this->email){
			$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.email_label') . ': ' . $this->email;
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
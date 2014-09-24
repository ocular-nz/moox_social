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
 * Include Facebook API Tools
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Facebook/facebook.php'); 

/**
 * Include Facebook Repository
 */
//require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Domain/Repository/Facebook.php'); 

/**
 * Get Facebook posts
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FacebookGetTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {		
	
	/**
	 * Sicherheitszeitraum für Zeitüberschneidungen während der zyklischen Ausführung des Tasks
	 *
	 * @var integer
	 */
	public static $intervalBuffer = 86400;
	
	/**
	 * limit of facebook api request
	 *
	 * @var integer
	 */
	public static $limit = 250;
	
	/**
	 * PID der Seite/Ordner in dem die Posts dieses Tasks gespeichert werden sollen
	 *
	 * @var integer
	 */
	public $pid;
	
	/**
	 * ID Ihrer Facebook Anwendung
	 *
	 * @var string
	 */
	public $appId;
	
	/**
	 * Secret Ihrer Facebook Anwendung
	 *
	 * @var string
	 */
	public $secert;
	
	/**
	 * ID Ihrer Facebook Seite
	 *
	 * @var string
	 */
	public $pageId;
	
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
		
		if($this->appId!="" && $this->secret!="" && $this->pageId!=""){
			
			$execution 	= $this->getExecution();
			$interval 	= $execution->getInterval();
			$time 		= time();
			$to			= $time;
			$from		= ($time-$interval-self::$intervalBuffer);									
			
			try {			
				$rawFeed = \TYPO3\MooxSocial\Controller\FacebookController::facebook($this->appId,$this->secret,$this->pageId,'posts?since='.$from.'&until='.$to.'&limit='.self::$limit);				
				$executionSucceeded = TRUE;
			} catch (\TYPO3\MooxSocial\Facebook\FacebookApiException $e) {				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.api_execution_error')." [". $e->getMessage()."]",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
				if($this->email && $extConf['debugEmailSenderAddress']){				
					$lockfile = $_SERVER['DOCUMENT_ROOT']."/typo3temp/.lock-email-task-".md5($this->appId.$this->secret.$this->pageId);
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
									->setSubject($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.api_error_mailsubject'))
									->setBody('ERROR: while requesting [app id: '.$this->appId.' | secret: '.$this->secret.' | page id: '.$this->pageId."]");
									$message->send();
						touch($lockfile);
					}
				}
			}	
			
			
			
			$posts = array();
			
			$postIds = array();
			
			foreach($rawFeed['data'] as $item) {
								
				if(!in_array($item['id'],$postIds) && $item['status_type']!=""){
					
					$postIds[] 		= $item['id'];					
					$postId 		= explode("_",$item['id']);
					$postId 		= $postId[1];
					
					$item['postId'] = $postId;
					$item['pageId'] = $this->pageId;
					$item['pid'] 	= $this->pid;
					
					$post 			= \TYPO3\MooxSocial\Controller\FacebookController::facebookPost($item);					
					
					if(is_array($post)){
						$posts[] 		= $post;
					}
				}				
			}	

			if(count($posts)){
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$facebookRepository = $objectManager->get('\TYPO3\MooxSocial\Domain\Repository\FacebookRepository');       
				
				$insertCnt = 0;
				$updateCnt = 0;
				
				foreach($posts AS $post){				
										
					$facebookPost		= $facebookRepository->findOneByApiUid($post['apiUid'],$this->pid);
					
					if(!($facebookPost instanceof \TYPO3\MooxSocial\Domain\Model\Facebook)){
						$facebookPost = new \TYPO3\MooxSocial\Domain\Model\Facebook;
						$action	= "insert";
					}
					
					if($action=="insert"){
						$facebookPost->setPid($post['pid']);
						$facebookPost->setCreated($post['created']);
					}
					
					$facebookPost->setUpdated($post['updated']);
					$facebookPost->setType($post['type']);
					$facebookPost->setStatusType($post['statusType']);
					
					if($action=="insert"){
						$facebookPost->setPage($post['page']);
						$facebookPost->setModel("facebook");
					}
					
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
					
					if($action=="insert"){
						$facebookPost->setApiUid($post['apiUid']);
					}
					
					$facebookPost->setApiHash($post['apiHash']);
					
					if($action=="insert"){
						$facebookRepository->add($facebookPost);
						$insertCnt++;
					} else {
						$facebookRepository->update($facebookPost);
						$updateCnt++;
					}
				}	
				
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				if($insertCnt>0 || $updateCnt>0){
					\TYPO3\MooxSocial\Controller\AdministrationController::clearCache("mooxsocial_pi1");
				}
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Posts geladen | ".$updateCnt." bestehende Posts aktualisiert",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			} else {
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					 "Keine neuen oder aktualisierten Posts gefunden",
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
		$info = $GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.pid_label') . ': ' . $this->pid;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.app_id_label') . ': ' . $this->appId;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.secret_label') . ': ' . $this->secret;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.page_id_label') . ': ' . $this->pageId;
		if($this->email){
			$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.email_label') . ': ' . $this->email;
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
	 * Returns the app id
	 *
	 * @return integer
	 */
	public function getAppId() {
		return $this->appId;
	}

	/**
	 * Set the app id
	 *
	 * @param integer $appId app id
	 * @return void
	 */
	public function setAppId($appId) {
		$this->appId = $appId;
	}
	
	/**
	 * Returns the secret
	 *
	 * @return integer
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * Set the secret
	 *
	 * @param integer $secret secret
	 * @return void
	 */
	public function setSecret($secret) {
		$this->secret = $secret;
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
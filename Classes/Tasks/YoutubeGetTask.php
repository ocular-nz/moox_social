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
 * Include Youtube Repository
 */
//require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Domain/Repository/Youtube.php'); 

/**
 * Get Youtube videos
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class YoutubeGetTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {		
	
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
	 * YOUTUBE_CHANNEL Ihrer Youtube Timeline
	 *
	 * @var string
	 */
	public $youtubeChannel;

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
		
		if($this->youtubeChannel!=""){
			
			$execution 	= $this->getExecution();
			$interval 	= $execution->getInterval();
			$time 		= time();
			$to			= $time;
			$from		= ($time-$interval-$this->intervalBuffer);			
			
			try {			
				$rawFeed = \DCNGmbH\MooxSocial\Controller\YoutubeController::youtube($this->youtubeChannel);
				/*print "<pre>"; 
                                print_r($rawFeed);
                                print "</pre>"; 
				exit();*/
				$executionSucceeded = TRUE;
			} catch (\Exception $e) {				
				$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.api_execution_error')." [". $e->getMessage()."]",
					 '',
					 FlashMessage::ERROR,
					 TRUE
				);
				$flashMessageQueue->addMessage($message);
				if($this->email && $extConf['debugEmailSenderAddress']){				
					$lockfile = $_SERVER['DOCUMENT_ROOT']."/typo3temp/.lock-email-task-".md5($this->youtubeChannel);
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
									->setSubject($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.api_error_mailsubject'))
									->setBody('ERROR: while requesting [youtube channel: '.$this->youtubeChannel."]");
									$message->send();
						touch($lockfile);
					}
				}
			}	
			
			
			
			$posts = array();
			
			$postIds = array();
			
			foreach($rawFeed as $item) {
				
				//if(1 || !in_array($item['type'],array("status"))){
				//if(!in_array($item['id'],$postIds) && $item['status_type']!=""){
				if(!in_array($item['id'],$postIds)){
					
					$postIds[] 		= $item['id'];					
					$postId 		= $item['id'];
					
					$item['id'] 		= $postId;
					$item['youtubeChannel'] 		= $this->youtubeChannel;
					$item['pid'] 			= $this->pid;
					
					$post 			= \DCNGmbH\MooxSocial\Controller\YoutubeController::youtubePost($item);
					
					if(is_array($post)){
						$posts[] 		= $post;
					}
				}
				
			}
			
			if(count($posts)){
				
				$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$youtubeRepository = $objectManager->get('DCNGmbH\\MooxSocial\\Domain\\Repository\\YoutubeRepository');
				
				$insertCnt = 0;
				$updateCnt = 0;
				
				foreach($posts AS $post){				
										
					$youtubePost		= $youtubeRepository->findOneByApiUid($post['apiUid'],$this->pid);
					
					if(!($youtubePost instanceof \DCNGmbH\MooxSocial\Domain\Model\Youtube)){
						$youtubePost = new \DCNGmbH\MooxSocial\Domain\Model\Youtube;
						$action	= "insert";						
					}
					
					if($action=="insert"){
						$youtubePost->setPid($post['pid']);
						$youtubePost->setCreated($post['created']);
					}
					
					$youtubePost->setUpdated($post['updated']);
					$youtubePost->setType($post['type']);
					$youtubePost->setStatusType($post['statusType']);
					
					if($action=="insert"){
						$youtubePost->setPage($post['page']);
						$youtubePost->setModel("youtube");
					}
					
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
					
					if($action=="insert"){
						$youtubePost->setApiUid($post['apiUid']);
					}
					
					$youtubePost->setApiHash($post['apiHash']);
					
					if($action=="insert"){
						$youtubeRepository->add($youtubePost);
						$insertCnt++;
					} else {
						$youtubeRepository->update($youtubePost);
						$updateCnt++;
					}
				}	
				
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Videos geladen | ".$updateCnt." bestehende Videos aktualisiert",
					 '',
					 FlashMessage::OK,
					 TRUE
				);
				$flashMessageQueue->addMessage($message);
			} else {
				$message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					 "Keine neuen oder aktualisierten Videos gefunden",
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
		$info = $GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.pid_label') . ': ' . $this->pid;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.youtube_channel_label') . ': ' . $this->youtubeChannel;
		if($this->email){
			$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.email_label') . ': ' . $this->email;
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
	 * Returns the youtube channel
	 *
	 * @return integer
	 */
	public function getYoutubeChannel() {
		return $this->youtubeChannel;
	}

	/**
	 * Set the youtube channel
	 *
	 * @param integer $youtubeChannel youtube channel
	 * @return void
	 */
	public function setYoutubeChannel($youtubeChannel) {
		$this->youtubeChannel = $youtubeChannel;
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
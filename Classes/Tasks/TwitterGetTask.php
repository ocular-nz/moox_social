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
 * Include Twitter API Tools
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Twitter/TwitterAPIExchange.php'); 

/**
 * Include Twitter Repository
 */
//require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Domain/Repository/Twitter.php'); 

/**
 * Get Twitter posts
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TwitterGetTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {		
	
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
	 * OAUTH_ACCESS_TOKEN Ihrer Twitter Anwendung
	 *
	 * @var string
	 */
	public $oauthAccessToken;
	
	/**
	 * OAUTH_ACCESS_TOKEN_SECRET Ihrer Twitter Anwendung
	 *
	 * @var string
	 */
	public $oauthAccessTokenSecret;
	
	/**
	 * CONSUMER_KEY Ihrer Twitter Anwendung
	 *
	 * @var string
	 */
	public $consumerKey;
	
	/**
	 * CONSUMER_KEY_SECRET Ihrer Twitter Anwendung
	 *
	 * @var string
	 */
	public $consumerKeySecret;
	
	/**
	 * SCREEN_NAME Ihrer Twitter Timeline
	 *
	 * @var string
	 */
	public $screenName;
	
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
		
		if($this->oauthAccessToken!="" && $this->oauthAccessTokenSecret!="" && $this->consumerKey!="" && $this->consumerKeySecret!="" && $this->screenName!=""){
			
			$execution 	= $this->getExecution();
			$interval 	= $execution->getInterval();
			$time 		= time();
			$to			= $time;
			$from		= ($time-$interval-$this->intervalBuffer);			
			
			try {			
				$rawFeed = \TYPO3\MooxSocial\Controller\TwitterController::twitter($this->oauthAccessToken,$this->oauthAccessTokenSecret,$this->consumerKey,$this->consumerKeySecret,$this->screenName,'&count=200');				
				/*print "<pre>"; 
                                print_r($rawFeed);
                                print "</pre>"; 
				exit();*/
				$executionSucceeded = TRUE;
			} catch (\Exception $e) {				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.api_execution_error')." [". $e->getMessage()."]",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
				if($this->email && $extConf['debugEmailSenderAddress']){				
					$lockfile = $_SERVER['DOCUMENT_ROOT']."/typo3temp/.lock-email-task-".md5($this->oauthAccessToken.$this->oauthAccessTokenSecret.$this->consumerKey.$this->consumerKeySecret.$this->screenName);
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
									->setSubject($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.api_error_mailsubject'))
									->setBody('ERROR: while requesting [oauth access token: '.$this->oauthAccessToken.' | oauth access token secret: '.$this->oauthAccessTokenSecret.' | consumer key: '.$this->consumerKey.' | consumer key secret: '.$this->consumerKeySecret.' | screen_name: '.$this->screenName."]");
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
					
					$item['postId'] 		= $postId;
					$item['pageId'] 		= $this->pageId;
					$item['pid'] 			= $this->pid;
					
					$post 			= \TYPO3\MooxSocial\Controller\TwitterController::twitterPost($item);					
					
					if(is_array($post)){
						$posts[] 		= $post;
					}
				}
				
			}
			
			if(count($posts)){
				
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$twitterRepository = $objectManager->get('\TYPO3\MooxSocial\Domain\Repository\TwitterRepository');       
				
				$insertCnt = 0;
				$updateCnt = 0;
				
				foreach($posts AS $post){				
										
					$twitterPost		= $twitterRepository->findOneByApiUid($post['apiUid'],$this->pid);
					
					if(!($twitterPost instanceof \TYPO3\MooxSocial\Domain\Model\Twitter)){
						$twitterPost = new \TYPO3\MooxSocial\Domain\Model\Twitter;
						$action	= "insert";						
					}
					
					if($action=="insert"){
						$twitterPost->setPid($post['pid']);
						$twitterPost->setCreated($post['created']);
					}
					
					$twitterPost->setUpdated($post['updated']);
					$twitterPost->setType($post['type']);
					$twitterPost->setStatusType($post['statusType']);
					
					if($action=="insert"){
						$twitterPost->setPage($post['page']);
						$twitterPost->setModel("twitter");
					}
					
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
					
					if($action=="insert"){
						$twitterPost->setApiUid($post['apiUid']);
					}
					
					$twitterPost->setApiHash($post['apiHash']);
					
					if($action=="insert"){
						$twitterRepository->add($twitterPost);
						$insertCnt++;
					} else {
						$twitterRepository->update($twitterPost);
						$updateCnt++;
					}
				}	
				
				$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$insertCnt." neue Tweets geladen | ".$updateCnt." bestehende Tweets aktualisiert",
					 '', // the header is optional
					 \TYPO3\CMS\Core\Messaging\FlashMessage::OK, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
					 TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($message);
			} else {
				$message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					 "Keine neuen oder aktualisierten Tweets gefunden",
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
		$info = $GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.pid_label') . ': ' . $this->pid;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.screen_name_label') . ': ' . $this->screenName;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.oauth_access_token_label') . ': ' . $this->oauthAccessToken;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.oauth_access_token_secret_label') . ': ' . $this->oauthAccessTokenSecret;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.consumer_key_label') . ': ' . $this->consumerKey;
		$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.consumer_key_secret_label') . ': ' . $this->consumerKeySecret;
		if($this->email){
			$info .= " | ".$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.email_label') . ': ' . $this->email;
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
	 * Returns the oauth access token
	 *
	 * @return integer
	 */
	public function getOauthAccessToken() {
		return $this->oauthAccessToken;
	}

	/**
	 * Set the oauth access token
	 *
	 * @param integer $oauthAccessToken oauth access token
	 * @return void
	 */
	public function setOauthAccessToken($oauthAccessToken) {
		$this->oauthAccessToken = $oauthAccessToken;
	}
	
	/**
	 * Returns the oauth access token secret
	 *
	 * @return integer
	 */
	public function getOauthAccessTokenSecret() {
		return $this->oauthAccessTokenSecret;
	}

	/**
	 * Set the oauth access token secret
	 *
	 * @param integer $oauthAccessTokenSecret oauth access token secret
	 * @return void
	 */
	public function setOauthAccessTokenSecret($oauthAccessTokenSecret) {
		$this->oauthAccessTokenSecret = $oauthAccessTokenSecret;
	}
	
	/**
	 * Returns the consumer key
	 *
	 * @return integer
	 */
	public function getConsumerKey() {
		return $this->consumerKey;
	}

	/**
	 * Set the consumer key
	 *
	 * @param integer $consumerKey consumer key
	 * @return void
	 */
	public function setConsumerKey($consumerKey) {
		$this->consumerKey = $consumerKey;
	}
	
	/**
	 * Returns the consumer key secret
	 *
	 * @return integer
	 */
	public function getConsumerKeySecret() {
		return $this->consumerKeySecret;
	}

	/**
	 * Set the consumer key secret
	 *
	 * @param integer $consumerKeySecret consumer key secret
	 * @return void
	 */
	public function setConsumerKeySecret($consumerKeySecret) {
		$this->consumerKeySecret = $consumerKeySecret;
	}
	
	/**
	 * Returns the screen name
	 *
	 * @return integer
	 */
	public function getScreenName() {
		return $this->screenName;
	}

	/**
	 * Set the screen name
	 *
	 * @param integer $screenName screen name
	 * @return void
	 */
	public function setScreenName($screenName) {
		$this->screenName = $screenName;
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
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

/**
 * Include Administration Controller
 */
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('moox_social','Classes/Controller/AdministrationController.php'); 
 
/**
 * Additional field provider for the Twitter get task
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TwitterGetTaskAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

	/**
	 * This method is used to define new fields for adding or editing a task
	 * In this case, it adds an pid field
	 *
	 * @param array $taskInfo Reference to the array containing the info used in the add/edit form
	 * @param object $task When editing, reference to the current task object. Null when adding.
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return array	Array containing all the information pertaining to the additional fields
	 */
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		
		// Initialize extra field value
		if (empty($taskInfo['pid'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['pid'] = 0;
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['pid'] = $task->pid;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['pid'] = 0;
			}
		}
		
		if (empty($taskInfo['oauthAccessToken'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['oauthAccessToken'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['oauthAccessToken'] = $task->oauthAccessToken;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['oauthAccessToken'] = '';
			}
		}
		
		if (empty($taskInfo['oauthAccessTokenSecret'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['oauthAccessTokenSecret'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['oauthAccessTokenSecret'] = $task->oauthAccessTokenSecret;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['oauthAccessTokenSecret'] = '';
			}
		}
		
		if (empty($taskInfo['consumerKey'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['consumerKey'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['consumerKey'] = $task->consumerKey;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['consumerKey'] = '';
			}
		}
		
		if (empty($taskInfo['consumerKeySecret'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['consumerKeySecret'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['consumerKeySecret'] = $task->consumerKeySecret;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['consumerKeySecret'] = '';
			}
		}
		
		if (empty($taskInfo['screenName'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['screenName'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['screenName'] = $task->screenName;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['screenName'] = '';
			}
		}
		
		if (empty($taskInfo['email'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default pid
				$taskInfo['email'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['email'] = $task->email;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['email'] = '';
			}
		}
		
		$additionalFields = array();
		
		// Write the code for the field
		$fieldID = 'task_pid';
		$fieldCode = $this->getSocialFoldersSelector('tx_scheduler[pid]',$taskInfo['pid']);		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[TYPO3]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.pid_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_oauthAccessToken';		
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[oauthAccessToken]" id="' . $fieldID . '" value="' . $taskInfo['oauthAccessToken'] . '" size="10" />';	
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Twitter]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.oauth_access_token_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_oauthAccessTokenSecret';		
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[oauthAccessTokenSecret]" id="' . $fieldID . '" value="' . $taskInfo['oauthAccessTokenSecret'] . '" size="10" />';	
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Twitter]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.oauth_access_token_secret_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_consumerKey';
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[consumerKey]" id="' . $fieldID . '" value="' . $taskInfo['consumerKey'] . '" size="10" />';		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Twitter]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.consumer_key_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_consumerKeySecret';
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[consumerKeySecret]" id="' . $fieldID . '" value="' . $taskInfo['consumerKeySecret'] . '" size="10" />';		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Twitter]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.consumer_key_secret_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_screenName';
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[screenName]" id="' . $fieldID . '" value="' . $taskInfo['screenName'] . '" size="10" />';		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Twitter]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.screen_name_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_email';
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[email]" id="' . $fieldID . '" value="' . $taskInfo['email'] . '" size="10" />';		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Twitter]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.email_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		return $additionalFields;
	}

	/**
	 * This method checks any additional data that is relevant to the specific task
	 * If the task class is not relevant, the method is expected to return TRUE
	 *
	 * @param array $submittedData Reference to the array containing the data submitted by the user
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		
		$result = TRUE;
		
		$submittedData['pid'] = intval($submittedData['pid']);						
		
		if ($submittedData['oauthAccessToken']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.oauth_access_token_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} 
		
		if ($submittedData['oauthAccessTokenSecret']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.oauth_access_token_secret_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} 
		
		if ($submittedData['consumerKey']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.consumer_key_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		}

		if ($submittedData['consumerKeySecret']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.consumer_key_secret_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} 
		
		if ($submittedData['screenName']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.screen_name_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} 	
		
		if ($submittedData['email']!="" && !\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($submittedData['email'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.email_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} 
		
		if($result){
			$config = array(
				'consumer_key' 				=> $submittedData['consumerKey'],
				'consumer_secret' 			=> $submittedData['consumerKeySecret'],
				'oauth_access_token' 		=> $submittedData['oauthAccessToken'],
				'oauth_access_token_secret' => $submittedData['oauthAccessTokenSecret'],
				'screenName' 				=> $submittedData['screenName'],
				'allowSignedRequest' 		=> false
			);
				
			$twitter 		= new \DCNGmbH\MooxSocial\Twitter\TwitterAPIExchange($config);
			$url 			= "https://api.twitter.com/1.1/statuses/user_timeline.json";
			$requestMethod 	= "GET";
			$getfield 		= '?screen_name=' . $submittedData['screenName'];
			
			try {			
				$rawFeed = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(),$assoc = TRUE);
			} catch (\Exception $e) {
				$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.api_error')." [". $e->getMessage()."]", \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
				$result = FALSE;				
			}						
		}
		
		return $result;
	}

	/**
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param array $submittedData Array containing the data submitted by the user
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the current task object
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$task->pid 						= $submittedData['pid'];
		$task->oauthAccessToken 		= $submittedData['oauthAccessToken'];
		$task->oauthAccessTokenSecret 	= $submittedData['oauthAccessTokenSecret'];
		$task->consumerKey 				= $submittedData['consumerKey'];
		$task->consumerKeySecret 		= $submittedData['consumerKeySecret'];
		$task->screenName 				= $submittedData['screenName'];
		$task->email 					= $submittedData['email'];
	}
	
	/**
	 * Get select box of folders with social module	
	 *
	 * @param integer $pid current storage pid	
	 * @return	string	Folder selector HTML code
	 */
	public function getSocialFoldersSelector($selectorName,$pid = 0) {
		
		$folders = \DCNGmbH\MooxSocial\Controller\AdministrationController::getSocialFolders();
		
		$selector = '<select name="' . $selectorName . '">';
		
		$selector .= '<option value="0">'.$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.default_storage').' [0]</option>';
		
		foreach ($folders as $folder) {
			$selectedAttribute = '';
			if ($folder['uid'] == $pid) {
				$selectedAttribute = ' selected="selected"';
			}

			$selector .= '<option value="' . $folder['uid'] . '"' . $selectedAttribute . '>'
				. $folder['title'] . ' ['.$folder['uid'].']'
				. '</option>';
		}

		$selector .= '</select>';

		return $selector;
	}
}

?>
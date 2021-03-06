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
 * Additional field provider for the Flickr get task
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FlickrGetTaskAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

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
				$taskInfo['pid'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['pid'] = $task->pid;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['pid'] = '';
			}
		}
		
		if (empty($taskInfo['apiKey'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default user id
				$taskInfo['apiKey'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['apiKey'] = $task->apiKey;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['apiKey'] = '';
			}
		}
		
		if (empty($taskInfo['apiSecretKey'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default user id
				$taskInfo['apiSecretKey'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['apiSecretKey'] = $task->apiSecretKey;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['apiSecretKey'] = '';
			}
		}
		
		if (empty($taskInfo['userId'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default user id
				$taskInfo['userId'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['userId'] = $task->userId;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['userId'] = '';
			}
		}
		
		if (empty($taskInfo['email'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default email
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
		//$fieldCode = '<input type="text" name="tx_scheduler[pid]" id="' . $fieldID . '" value="' . $taskInfo['pid'] . '" size="10" />';	
		$fieldCode = $this->getSocialFoldersSelector('tx_scheduler[pid]',$taskInfo['pid']);
		//$fieldCode .= '<div style="display: block">'.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_label', 'moox_social' ).'</div>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[TYPO3]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.pid_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_apiKey';		
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[apiKey]" id="' . $fieldID . '" value="' . $taskInfo['apiKey'] . '" size="10" />';	
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Flickr]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_key_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_apiSecretKey';		
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[apiSecretKey]" id="' . $fieldID . '" value="' . $taskInfo['apiSecretKey'] . '" size="10" />';	
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Flickr]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_secret_key_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_userId';		
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[userId]" id="' . $fieldID . '" value="' . $taskInfo['userId'] . '" size="10" />';	
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Flickr]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.user_id_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_email';
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[email]" id="' . $fieldID . '" value="' . $taskInfo['email'] . '" size="10" />';		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Flickr]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.email_label', 'moox_social' ),
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
		$submittedData['pid'] = intval($submittedData['pid']);				
		if ($submittedData['pid']<0) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.pid_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if ($submittedData['apiKey']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_key_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if ($submittedData['apiSecretKey']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_secret_key_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if ($submittedData['userId']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.user_id_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if ($submittedData['email']!="" && !\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($submittedData['email'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.email_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if($result){
			$config = array(
				'api_key' => $submittedData['apiKey'],
				'api_secret_key' => $submittedData['apiSecretKey'],
				'user_id' => $submittedData['userId'],
				'allowSignedRequest' => false
			);
			
			$feedUrl = 'https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key='.$submittedData['apiKey'].'&user_id='.$submittedData['userId'].'&format=rest';
			$feedXml = simplexml_load_file($feedUrl);
			
			$flickrFeed = array();
			
			foreach($feedXml->photosets[0]->photoset as $item) {				
				$flickrFeed[] = array(
					'id' => (string)$item->attributes()->id,
					'primary' => (string)$item->attributes()->primary,
					'secret' => (string)$item->attributes()->secret,
					'server' => (string)$item->attributes()->server,
					'farm' => (string)$item->attributes()->farm,
					'photos' => (string)$item->attributes()->photos,
					'videos' => (string)$item->attributes()->videos,
					'needs_interstitial' => (string)$item->attributes()->needs_interstitial,
					'visibility_can_see_set' => (string)$item->attributes()->visibility_can_see_set,
					'count_views' => (string)$item->attributes()->count_views,
					'count_comments' => (string)$item->attributes()->count_comments,
					'can_comment' => (string)$item->attributes()->can_comment,
					'date_create' => date('r', (string)$item->attributes()->date_create),
					'date_update' => date('r', (string)$item->attributes()->date_update),
					'title' => (string)$item->title,
					'description' => (string)$item->description
				);
			}   		
						
			try {			
				$rawFeed = $flickrFeed;
			} catch (\Exception $e) {
				$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.api_error')." [". $e->getMessage()."]", \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
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
		$task->pid = $submittedData['pid'];
		$task->apiKey = $submittedData['apiKey'];
		$task->apiSecretKey = $submittedData['apiSecretKey'];
		$task->userId = $submittedData['userId'];
		$task->email = $submittedData['email'];
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
		
		$selector .= '<option value="0">'.$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.default_storage').' [0]</option>';
		
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
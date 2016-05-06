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
 * Additional field provider for the Youtube get task
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class YoutubeGetTaskAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

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
		
		if (empty($taskInfo['youtubeChannel'])) {
			if ($parentObject->CMD == 'add') {
				// In case of new task and if field is empty, set default youtube channel
				$taskInfo['youtubeChannel'] = '';
			} elseif ($parentObject->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo['youtubeChannel'] = $task->youtubeChannel;
			} else {
				// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['youtubeChannel'] = '';
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
		//$fieldCode .= '<div style="display: block">'.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.api_label', 'moox_social' ).'</div>';
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[TYPO3]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.pid_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_youtubeChannel';		
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[youtubeChannel]" id="' . $fieldID . '" value="' . $taskInfo['youtubeChannel'] . '" size="10" />';	
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Youtube]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.youtube_channel_label', 'moox_social' ),
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);
		
		// Write the code for the field
		$fieldID = 'task_email';
		$fieldCode = '<input type="text" size="30" name="tx_scheduler[email]" id="' . $fieldID . '" value="' . $taskInfo['email'] . '" size="10" />';		
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => '<strong style="width: 80px;display: inline-block">[Youtube]</strong> '.\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.email_label', 'moox_social' ),
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
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.pid_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if ($submittedData['youtubeChannel']=="") {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.youtube_channel_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if ($submittedData['email']!="" && !\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($submittedData['email'])) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.email_error'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			$result = FALSE;
		} else {
			$result = TRUE;
		}
		
		if($result){
			$config = array(
				'youtube_channel' => $submittedData['youtubeChannel'],
				'allowSignedRequest' => false
			);
			
			$youtubeChannel = $submittedData['youtubeChannel'];
			$feedUrl = 'https://www.youtube.com/feeds/videos.xml?user='.$youtubeChannel;
			$feedXml = simplexml_load_file($feedUrl);
			
			$youtubeFeed = array();

			foreach($feedXml->entry as $item) {
				$atom = $item->children('http://www.w3.org/2005/Atom');
				$media = $item->children('http://search.yahoo.com/mrss/');
				$yt = $item->children('http://www.youtube.com/xml/schemas/2015');

				$youtubeFeed[] = array(
					'id' => (string) $yt->videoId,
					'pubDate' => date('d.m.Y H:i', strtotime((string) $item->published)),
					'updateDate' => date('d.m.Y H:i', strtotime((string) $item->updated)),
					'title' => (string) $item->title,
					'author' => (string) $item->author->name,
					'description' => (string) $media->group->description,
					'link' => (string) $item->link->attributes()->href,
					'imageUrl' => (string) $media->group->thumbnail->attributes()->url,
					'favoriteCount' => (string) $media->group->community->starRating->attributes()->count,
					'viewCount' => (string) $media->group->community->statistics->attributes()->views
				);
			}
			
						
			try {			
				$rawFeed = $youtubeFeed;
			} catch (\Exception $e) {
				$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.api_error')." [". $e->getMessage()."]", \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
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
		$task->youtubeChannel = $submittedData['youtubeChannel'];
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
		
		$selector .= '<option value="0">'.$GLOBALS['LANG']->sL('LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.default_storage').' [0]</option>';
		
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

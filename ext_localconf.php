<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'DCNGmbH.' . $_EXTKEY,
	'Pi1',
	array(
		'Facebook' => 'list,listAjax,show',
	),
	// non-cacheable actions
	array(
		'Facebook' => 'listAjax',	
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'DCNGmbH.' . $_EXTKEY,
	'Pi2',
	array(		
		'Twitter' => 'list,listAjax,show',		
	),
	// non-cacheable actions
	array(		
		'Twitter' => 'listAjax',		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'DCNGmbH.' . $_EXTKEY,
	'Pi3',
	array(		
		'Youtube' => 'list,listAjax,show',		
	),
	// non-cacheable actions
	array(		
		'Youtube' => 'listAjax',		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'DCNGmbH.' . $_EXTKEY,
	'Pi4',
	array(		
		'Flickr' => 'list,listAjax,show',		
	),
	// non-cacheable actions
	array(		
		'Flickr' => 'listAjax',		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'DCNGmbH.' . $_EXTKEY,
	'Pi5',
	array(		
		'Slideshare' => 'list,listAjax,show',		
	),
	// non-cacheable actions
	array(		
		'Slideshare' => 'listAjax',		
	)
);

// Get the extensions's configuration
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

// If  tasks should be shown, register information for the tasks
if (!empty($extConf['showTasks'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DCNGmbH\\MooxSocial\\Tasks\\FacebookGetTask'] = array(
		'extension' => $_EXTKEY,
		'title'            => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.title',
		'description'      => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_facebookgettask.description',
		'additionalFields' => 'DCNGmbH\\MooxSocial\\Tasks\\FacebookGetTaskAdditionalFieldProvider'
	);
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DCNGmbH\\MooxSocial\\Tasks\\TwitterGetTask'] = array(
		'extension' => $_EXTKEY,
		'title'            => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.title',
		'description'      => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_twittergettask.description',
		'additionalFields' => 'DCNGmbH\\MooxSocial\\Tasks\\TwitterGetTaskAdditionalFieldProvider'
	);
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DCNGmbH\\MooxSocial\\Tasks\\YoutubeGetTask'] = array(
		'extension' => $_EXTKEY,
		'title'            => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.title',
		'description'      => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_youtubegettask.description',
		'additionalFields' => 'DCNGmbH\\MooxSocial\\Tasks\\YoutubeGetTaskAdditionalFieldProvider'
	);
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DCNGmbH\\MooxSocial\\Tasks\\FlickrGetTask'] = array(
		'extension' => $_EXTKEY,
		'title'            => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.title',
		'description'      => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_flickrgettask.description',
		'additionalFields' => 'DCNGmbH\\MooxSocial\\Tasks\\FlickrGetTaskAdditionalFieldProvider'
	);
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['DCNGmbH\\MooxSocial\\Tasks\\SlideshareGetTask'] = array(
		'extension' => $_EXTKEY,
		'title'            => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.title',
		'description'      => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_scheduler.xlf:tx_mooxsocial_tasks_slidesharegettask.description',
		'additionalFields' => 'DCNGmbH\\MooxSocial\\Tasks\\SlideshareGetTaskAdditionalFieldProvider'
	);
}
	
?>
<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'MOOX-Social-Facebook'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_pi1';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_pi1.xml');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi2',
	'MOOX-Social-Twitter'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_pi2';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_pi2.xml');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi3',
	'MOOX-Social-Youtube'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_pi3';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_pi3.xml');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi4',
	'MOOX-Social-Flickr'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_pi4';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_pi4.xml');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi5',
	'MOOX-Social-Slideshare'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_pi5';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_pi5.xml');


/* ===========================================================================
 	Register BE-Module for Administration
=========================================================================== */

if (TYPO3_MODE === 'BE') {

    /***************
     * Register Main Module
     */
	$mainModuleName = "moox";
	if (!isset($TBE_MODULES[$mainModuleName])) {
        $temp_TBE_MODULES = array();
        foreach ($TBE_MODULES as $key => $val) {
            if ($key == 'web') {
                $temp_TBE_MODULES[$key] = $val;
                $temp_TBE_MODULES[$mainModuleName] = '';
            } else {
                $temp_TBE_MODULES[$key] = $val;
            }
        }
        $TBE_MODULES = $temp_TBE_MODULES;
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('moox_core')) {
			$mainModuleKey 		= "DCNGmbH.moox_core";
			$mainModuleIcon 	= 'EXT:moox_core/ext_icon32.png';
			$mainModuleLabels 	= 'LLL:EXT:moox_core/Resources/Private/Language/MainModule.xlf';			
		} else {
			$mainModuleKey 		= "TYPO3.".$_EXTKEY;
			$mainModuleIcon 	= 'EXT:'.$_EXTKEY.'/Resources/Public/Moox/MainModuleExtIcon.png';
			$mainModuleLabels 	= 'LLL:EXT:'.$_EXTKEY.'/Resources/Public/Moox/MainModule.xlf';
		}
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
			$mainModuleKey,
			$mainModuleName,
			'',
			'',
			array(),
			array(
				'access' => 'user,group',
				'icon'   => $mainModuleIcon,
				'labels' => $mainModuleLabels,
			)
		);
    } 

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'DCNGmbH.'.$_EXTKEY,
		$mainModuleName,	// Make module a submodule of 'moox'
		'socialmanagement',		// Submodule key
		'after:MooxNewsTxMooxnewsM2',					// Position
		array(
			'Administration' => 'index,truncateFolder',
			'Facebook' => 'index,reinit,truncate',
			'Twitter' => 'index,reinit,truncate',
			'Youtube' => 'index,reinit,truncate',
			'Flickr' => 'index,reinit,truncate',
			'Slideshare' => 'index,reinit,truncate'
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon32.png',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_init.xlf',
		)
	);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'MOOX social');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mooxsocial_domain_model_facebook', 'EXT:moox_social/Resources/Private/Language/locallang_csh_tx_mooxsocial_domain_model_facebook.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mooxsocial_domain_model_facebook');
$TCA['tx_mooxsocial_domain_model_facebook'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_facebook',
		'label' => 'api_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),		
		'searchFields' => 'title,api_uid,summary,text,description,caption,shared_title,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Facebook.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_mooxsocial_domain_model_facebook.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mooxsocial_domain_model_twitter', 'EXT:moox_social/Resources/Private/Language/locallang_csh_tx_mooxsocial_domain_model_twitter.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mooxsocial_domain_model_twitter');
$TCA['tx_mooxsocial_domain_model_twitter'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_twitter',
		'label' => 'api_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),		
		'searchFields' => 'title,api_uid,summary,text,description,caption,shared_title,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Twitter.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_mooxsocial_domain_model_twitter.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mooxsocial_domain_model_youtube', 'EXT:moox_social/Resources/Private/Language/locallang_csh_tx_mooxsocial_domain_model_youtube.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mooxsocial_domain_model_youtube');
$TCA['tx_mooxsocial_domain_model_youtube'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube',
		'label' => 'api_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),		
		'searchFields' => 'title,api_uid,summary,text,description,caption,shared_title,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Youtube.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_mooxsocial_domain_model_youtube.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mooxsocial_domain_model_flickr', 'EXT:moox_social/Resources/Private/Language/locallang_csh_tx_mooxsocial_domain_model_flickr.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mooxsocial_domain_model_flickr');
$TCA['tx_mooxsocial_domain_model_flickr'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_flickr',
		'label' => 'api_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),		
		'searchFields' => 'title,summary,text,description,caption,shared_title,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Flickr.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_mooxsocial_domain_model_flickr.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_mooxsocial_domain_model_slideshare', 'EXT:moox_social/Resources/Private/Language/locallang_csh_tx_mooxsocial_domain_model_slideshare.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mooxsocial_domain_model_slideshare');
$TCA['tx_mooxsocial_domain_model_slideshare'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_slideshare',
		'label' => 'api_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),		
		'searchFields' => 'title,api_uid,summary,text,description,caption,shared_title,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Slideshare.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_mooxsocial_domain_model_slideshare.gif'
	),
);


/***************
 * Wizard
 */
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName);
if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses'][$pluginSignature . '_wizicon'] =
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Resources/Private/Php/class.' . $pluginSignature . '_wizicon.php';
}

/***************
 * Icon in page tree
 */
$TCA['pages']['columns']['module']['config']['items'][] = array('MOOX-Social', 'mxsocial', 'EXT:moox_social/ext_icon.gif');
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('pages', 'contains-mxsocial', '../typo3conf/ext/moox_social/ext_icon.gif');
?>
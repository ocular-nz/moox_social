<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_mooxsocial_domain_model_youtube'] = array(
	'ctrl' => $TCA['tx_mooxsocial_domain_model_youtube']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, created, updated, type, status_type, page, action, title, summary, text, author, author_id, description, caption, url, link_name, link_url, image_url, video_url, shared_url, shared_title, shared_description, shared_caption, likes, shares, comments',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, created, updated, type, status_type, page, action, title, summary, text, author, author_id, description, caption, url, link_name, link_url, image_url, video_url, shared_url, shared_title, shared_description, shared_caption, likes, shares, comments,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,hidden, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_mooxsocial_domain_model_youtube',
				'foreign_table_where' => 'AND tx_mooxsocial_domain_model_youtube.pid=###CURRENT_PID### AND tx_mooxsocial_domain_model_youtube.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'pid' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'created' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.created',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'updated' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.updated',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'model' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.model',
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.type',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'status_type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.status_type',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'page' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.page',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'action' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.action',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.title',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'summary' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.summary',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'text' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.text',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'author' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.author',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'author_id' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.author_id',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'caption' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.caption',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.url',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'link_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.link_name',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'link_url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.link_url',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'image_url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.image_url',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'image_embedcode' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.image_embedcode',
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'video_url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.video_url',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'video_embedcode' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.image_embedcode',
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'shared_url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.shared_url',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '256',
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'shared_title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.shared_title',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
		'shared_description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.shared_description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'shared_caption' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.shared_caption',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim'
			),
		),
		'likes' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.likes',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'shares' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.shares',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'comments' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.comments',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'api_uid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.api_uid',
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'api_hash' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:moox_social/Resources/Private/Language/locallang_db.xlf:tx_mooxsocial_domain_model_youtube.api_hash',
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);

?>
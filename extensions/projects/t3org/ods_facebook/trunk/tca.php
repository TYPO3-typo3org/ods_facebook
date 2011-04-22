<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_odsfacebook_auth'] = array (
	'ctrl' => $TCA['tx_odsfacebook_auth']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,title,api_key,client_id,client_secret,access_token'
	),
	'feInterface' => $TCA['tx_odsfacebook_auth']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ods_facebook/locallang_db.xml:tx_odsfacebook_auth.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'api_key' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ods_facebook/locallang_db.xml:tx_odsfacebook_auth.api_key',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'client_id' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ods_facebook/locallang_db.xml:tx_odsfacebook_auth.client_id',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'client_secret' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ods_facebook/locallang_db.xml:tx_odsfacebook_auth.client_secret',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'access_token' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:ods_facebook/locallang_db.xml:tx_odsfacebook_auth.access_token',		
			'config' => array (
				'type' => 'input',
				'size' => '100',
				'eval' => 'trim',
				'wizards' => Array(
					'_PADDING' => 2,
					'0' => Array(
						'type' => 'popup',
						'title' => 'Get Access token',
						'script' => 'EXT:ods_facebook/wizard/index.php',
						'icon' => 'EXT:ods_facebook/wizard/icon.png',
						'JSopenParams' => ',width=600,height=400,status=0,menubar=0,scrollbars=0',
					)
				),
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, api_key;;;;3-3-3, client_id, client_secret, access_token')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>
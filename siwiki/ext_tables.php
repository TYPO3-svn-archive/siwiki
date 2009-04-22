<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
 * @package TYPO3
 * @subpackage tx_siwiki
 */


//-------------------------------------------------------------------
// Backend
//-------------------------------------------------------------------
$TCA["tx_siwiki_articles"] = array (
    "ctrl" => array (
        'title'     => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles',        
        'label'     => 'title',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'title',    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon_article.png',
    ),
    "feInterface" => array (
        "fe_admin_fieldList" => "hidden, title, namespace, article, creator, editor, version",
    )
);


$TCA["tx_siwiki_namespaces"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_namespaces',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon_namespace.png',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, name",
	)
);

require_once(t3lib_extMgm::extPath('div', 'class.tx_div.php'));
t3lib_extMgm::addStaticFile($_EXTKEY, './configurations', 'siWiki');                              // (extKey, path, label)
t3lib_extMgm::addPlugin(Array('LLL:EXT:siwiki/locallang_db.php:pluginLabel', 'tx_siwiki'));       // array(title, pluginKey)
t3lib_extMgm::addPiFlexFormValue('tx_siwiki', 'FILE:EXT:siwiki/configurations/flexform.xml');     // (pluginKey, path)
$TCA['tt_content']['types']['list']['subtypes_excludelist']['tx_siwiki']='layout,select_key,pages,recurs';
$TCA['tt_content']['types']['list']['subtypes_addlist']['tx_siwiki']='pi_flexform';

//-------------------------------------------------------------------
// Frontend
//-------------------------------------------------------------------


?>

<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

/**
 * TCA
 * @package TYPO3
 * @subpackage tx_siwiki
 */
$TCA['tx_siwiki_articles'] = array (
    'ctrl' => $TCA['tx_siwiki_articles']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden, title, namespace, article, creator, editor, version',
    ),
    'feInterface' => $TCA['tx_siwiki_articles']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'title' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles.title',        
            'config' => Array (
                'type' => 'input',
                'size' => '80',    
                'eval' => 'required'
            ),
        ),
        'namespace' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles.namespace',        
            'config' => Array (
                'type' => 'select',
                'foreign_table' => 'tx_siwiki_namespaces',
                'foreign_table_where' => 'AND tx_siwiki_namespaces.pid = ###CURRENT_PID###',
            )
        ),
        'article' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles.article',        
            'config' => Array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => Array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php',
                    ),
                ),
            )
        ),
        'creator' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles.creator',        
            'config' => Array (
                'type' => 'select',    
                'foreign_table' => 'fe_users',
                'foreign_table_where' => 'ORDER BY fe_users.username',
                'foreign_label' => 'username',
                'default' => '',
            )
        ),
        'editor' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles.editor',        
            'config' => Array (
                'type' => 'select',    
                'foreign_table' => 'fe_users',
                'foreign_table_where' => 'ORDER BY fe_users.username',
                'size' => '1',
                'minitems' => 0,
                'maxitems' => 1,
                'default' => '',
            )
        ),
        'version' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_articles.version',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title, namespace, article;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image], creator, editor, version')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);


$TCA['tx_siwiki_namespaces'] = array (
    'ctrl' => $TCA['tx_siwiki_namespaces']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden, name, description',
    ),
    'feInterface' => $TCA['tx_siwiki_namespaces']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_namespaces.name',        
            'config' => Array (
                'type' => 'input',
                'size' => '80',    
		'eval' => 'required',
            )
        ),
        'description' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:siwiki/locallang_db.xml:tx_siwiki_namespaces.description',        
            'config' => Array (
                'type' => 'text',
                'cols' => '40',    
                'rows' => '2',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, name, description')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);
?>

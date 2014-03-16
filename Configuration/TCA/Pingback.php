<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_t3blog_pingback'] = array (
    'ctrl' => $TCA['tx_t3blog_pingback']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,starttime,endtime,title,url,date,text'
    ),
    'feInterface' => $TCA['tx_t3blog_pingback']['feInterface'],
    'columns' => array (
        'hidden' => array (
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'starttime' => array (
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config'  => array (
                'type'     => 'input',
                'size'     => '8',
                'max'      => '20',
                'eval'     => 'date',
                'default'  => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => array (
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config'  => array (
                'type'     => 'input',
                'size'     => '8',
                'max'      => '20',
                'eval'     => 'date',
                'checkbox' => '0',
                'default'  => '0',
                'range'    => array (
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
                )
            )
        ),
        'title' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.title',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
            )
        ),
        'url' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.url',
            'config' => Array (
                'type' => 'input',
                'size' => '30',
            )
        ),
        'date' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.date',
            'config' => Array (
                'type'     => 'input',
                'size'     => '12',
                'max'      => '20',
                'eval'     => 'datetime',
                'checkbox' => '0',
                'default'  => '0'
            )
        ),
        'text' => Array (
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.text',
            'config' => Array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, url;;;;3-3-3, date, text')
    ),
    'palettes' => array (
        '1' => array('showitem' => 'starttime, endtime')
    )
);

?>
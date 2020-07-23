<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'ORDER BY crdate DESC',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'extensions-t3extblog-subscriber',
        ],
        'searchFields' => 'email',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                ],
                'default' => 0,
            ],
        ],
        'crdate' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tstamp' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'deleted' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'email' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.email',
            'config' => [
                'type' => 'input',
                'eval' => 'required',
            ],
        ],
        'lastsent' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.lastsent',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'default' => 0,
            ],
        ],
        'code' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.code',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
                'size' => 30,
            ],
        ],
        'privacy_policy_accepted' => [
            'label' => '...',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
			sys_language_uid, email, hidden,
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.tabs.meta,
				lastsent, code, privacy_policy_accepted'],
    ],
];

<?php

defined('TYPO3_MODE') || die();

call_user_func(function()
{
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'webfonts',
        'setup',
        "@import 'EXT:webfonts/Configuration/TypoScript/setup.typoscript'"
    );


    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'tx-webfonts-times-solid', // Icon-Identifier, e.g. tx-myext-action-preview
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:webfonts/Resources/Public/Icons/times-solid.svg']
    );


    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'WEBFONTS.webfonts',
        'WebfontsPlugin',
        [
            \WEBFONTS\Webfonts\Controller\AutoSetupController::class => 'autoSetup',
        ],
        // non-cacheable actions
        [
            \WEBFONTS\Webfonts\Controller\AutoSetupController::class => 'autoSetup',
        ]
    );
});

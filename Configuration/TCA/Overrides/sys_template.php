<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

// Add static template configuration
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'pb_rel_nofollow',
    'Configuration/TypoScript',
    'Rel nofollow'
);

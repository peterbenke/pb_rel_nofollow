<?php

/**
 * TYPO3
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

// Add static template configuration
ExtensionManagementUtility::addStaticFile(
    'pb_rel_nofollow',
    'Configuration/TypoScript',
    'Rel nofollow'
);

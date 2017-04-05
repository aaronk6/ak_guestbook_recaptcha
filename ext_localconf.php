<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Hook into ve_guestbook: Add CAPTCHA
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ve_guestbook']['extraItemMarkerHook'][]
	= Ak\GuestbookCaptcha\tx_akguestbookrecaptcha::class;

// Hook into ve_guestbook: Verify result
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ve_guestbook']['onErrorHook'][]
	= Ak\GuestbookCaptcha\tx_akguestbookrecaptcha::class;

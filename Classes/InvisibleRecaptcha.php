<?php
namespace Ak\GuestbookCaptcha;

use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class tx_akguestbookrecaptcha extends AbstractPlugin {

	public $prefixId = 'tx_akguestbookrecaptcha';
	public $extKey = 'ak_guestbook_recaptcha';

	const API_JS_URL = 'https://www.google.com/recaptcha/api.js';
	const API_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	public function __construct() {

		// get plugin configuration
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		$conf = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $conf["plugin."][$this->prefixId . "."]["settings."];
	}


	/**
	 * Function extraItemMarkerProcessor is called when form and guestbook entries gets rendered
	 *
	 * @param array &$markerArray Array with markers
	 * @param array $row Field values
	 * @param array $config Configuration
	 * @param object &$obj Parent object
	 * @return array $markerArray
	 */
	public function extraItemMarkerProcessor(&$markerArray, $row, $config, &$obj) {

		// make sure we’re on the form
		if(empty($markerArray['###ACTION_URL###'])) {
			return $markerArray;
		}

		$site_key_esc = json_encode($this->settings['site_key']);
		$query_string = http_build_query([ 'render' => 'explicit', 'onload' => $this->extKey . '_onload' ]);

		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_api']
			= '<script src="' . self::API_JS_URL . '?' . $query_string . '"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_render']
			= "<script>RECAPTCHA_SITE_KEY = $site_key_esc; </script>";

		return $markerArray;
	}

	/**
	 * Function onErrorProcessor is called from a guestbook hook
	 * and gives the chance to force an error when the challenge wasn’t solved
	 *
	 * @param array $error Error(s) that have occurred (if any)
	 * @param object &$obj Parent object
	 */
	public function onErrorProcessor(&$error, &$obj) {

		$postvars = GeneralUtility::_POST('tx_veguestbook_pi1');

		if (empty($postvars['token'])) {
			$error = LocalizationUtility::translate('errorCaptchaTokenMissing', $this->extKey);
			return;
		}

		if (!$this->verifyToken($postvars['token'])) {
			$error = LocalizationUtility::translate('errorCaptchaNotCorrect', $this->extKey);
			return;
		}
	}

	protected function verifyToken($token) {

		$fields = [
			'secret' => $this->settings['secret_key'],
			'response' => $token
		];

		$opts = ['http' => [
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => http_build_query($fields)
		]];

		$res = file_get_contents(self::API_VERIFY_URL, false, stream_context_create($opts));
		$obj = @json_decode($res);

		if(empty($obj)) {
			$this->logInternalError("Received invalid response from reCAPTCHA API");
			return false;
		}

		return !empty($obj->success) && $obj->success === true;
	}

	protected function logInternalError($msg) {
		error_log($this->extKey . ": $msg");
	}

}

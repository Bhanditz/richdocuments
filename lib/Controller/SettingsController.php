<?php
/**
 * ownCloud - Richdocuments App
 *
 * @author Victor Dubiniuk
 * @copyright 2014 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

namespace OCA\Richdocuments\Controller;

use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\IL10N;
use \OCP\AppFramework\Http\TemplateResponse;

use \OCA\Richdocuments\AppConfig;

class SettingsController extends Controller {
	private $userId;
	private $l10n;
	private $appConfig;

	public function __construct($appName, IRequest $request, IL10N $l10n, AppConfig $appConfig, $userId) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->l10n = $l10n;
		$this->appConfig = $appConfig;
	}

	/**
	 * @NoAdminRequired
	 */
	public function getSettings() {
		return [
			'doc_format' => $this->appConfig->getAppValue('doc_format'),
			'wopi_url' => $this->appConfig->getAppValue('wopi_url'),
			'test_wopi_url' => $this->appConfig->getAppValue('test_wopi_url'),
			'test_server_groups' => $this->appConfig->getAppValue('test_server_groups')
		];
	}

	/**
	 * @NoCSRFRequired
	 */
	public function settingsIndex() {
		return new TemplateResponse(
			$this->appName,
			'settings',
			'blank'
		);
	}

	public function adminIndex() {
		return new TemplateResponse(
			$this->appName,
			'admin',
			[
				'wopi_url' => $this->appConfig->getAppValue('wopi_url'),
				'edit_groups' => $this->appConfig->getAppValue('edit_groups'),
				'doc_format' => $this->appConfig->getAppValue('doc_format'),
				'test_wopi_url' => $this->appConfig->getAppValue('test_wopi_url'),
				'test_server_groups' => $this->appConfig->getAppValue('test_server_groups'),
				'external_apps' => $this->appConfig->getAppValue('external_apps'),
				'canonical_webroot' => $this->appConfig->getAppValue('canonical_webroot'),
				'menu_option' => $this->appConfig->getAppValue('menu_option')
			],
			'blank'
		);
	}

	public function setSettings($wopi_url, $edit_groups, $doc_format, $test_wopi_url, $test_server_groups, $external_apps, $canonical_webroot, $menu_option) {
		$message = $this->l10n->t('Saved');

		if ($wopi_url !== null) {
			$this->appConfig->setAppValue('wopi_url', $wopi_url);

			$colon = \strpos($wopi_url, ':', 0);
			if (\OC::$server->getRequest()->getServerProtocol() !== \substr($wopi_url, 0, $colon)) {
				$message = $this->l10n->t('Saved with error: Collabora Online should use the same protocol as the server installation.');
			}
		}

		if ($edit_groups !== null) {
			$this->appConfig->setAppValue('edit_groups', $edit_groups);
		}

		if ($doc_format !== null) {
			$this->appConfig->setAppValue('doc_format', $doc_format);
		}

		if ($test_wopi_url !== null) {
			$this->appConfig->setAppValue('test_wopi_url', $test_wopi_url);
		}

		if ($test_server_groups !== null) {
			$this->appConfig->setAppValue('test_server_groups', $test_server_groups);
		}

		if ($external_apps !== null) {
			$this->appConfig->setAppValue('external_apps', $external_apps);
		}

		if ($canonical_webroot !== null) {
			$this->appConfig->setAppValue('canonical_webroot', $canonical_webroot);
		}

		if ($menu_option !== null) {
			$this->appConfig->setAppValue('menu_option', $menu_option);
		}

		$richMemCache = \OC::$server->getMemCacheFactory()->create('richdocuments');
		$richMemCache->clear('discovery.xml');

		$response = [
			'status' => 'success',
			'data' => ['message' => (string) $message]
		];

		return $response;
	}
}

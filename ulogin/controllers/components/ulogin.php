<?php
class UloginComponent extends Object {
	public $emailActivation = true;

	private $agregator = 'ulogin'; //'loginza'

	private $uloginAuthUrl = 'http://ulogin.ru/token.php?token=';
	private $loginzaAuthUrl = 'http://loginza.ru/api/authinfo?token=';

	public function initialize(&$controller) {
		$this->controller =& $controller;
		if ($this->agregator === 'loginza') {
			$this->loginzaIds = Configure::read('Loginza.ids');
			$this->loginzaSigs = Configure::read('Loginza.sigs');
		}
	}


	/**
	 * @param $token
	 *
	 * @return mixed
	 */
	public function getAuthData($token) {
		$authData = null;
		if ($this->agregator === 'loginza') {
			$authData = json_decode(file_get_contents($this->loginzaAuthUrl . $token . '&id=' . $this->loginzaIds[$_SERVER['SERVER_NAME']] . '&sig=' . md5($token . $this->loginzaSigs[$_SERVER['SERVER_NAME']])), true);
		} else if ($this->agregator === 'ulogin') {
			$authData = json_decode(file_get_contents($this->uloginAuthUrl . $token . '&host=' . $_SERVER['HTTP_HOST']), true);
			$authData['provider'] = $authData['network'];
		}

		return $authData;
	}

	/**
	 * @param $token
	 *
	 * @return bool
	 */
	public function auth($token) {
		if (empty($token)) {
			return false;
		}
		$authData = $this->getAuthData($token);

		return (isset($authData['error_type'])) ? false : $authData;
	}
}

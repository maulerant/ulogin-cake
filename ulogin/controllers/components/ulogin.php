<?php
class UloginComponent extends Object {
    var $emailActivation = true ;

    private $agregator = 'ulogin' ; //'loginza'

    private $uloginAuthUrl = 'http://ulogin.ru/token.php?token=';
    private $loginzaAuthUrl = 'http://loginza.ru/api/authinfo?token=';

    function initialize(&$controller) {
        // сохранение ссылки на контроллер для последующего использования
        $this->controller =& $controller;
        $this->User = ClassRegistry::init('User') ;
        if ($this->agregator === 'loginza') {
            $this->loginzaIds = Configure::read('Loginza.ids');
            $this->loginzaSigs = Configure::read('Loginza.sigs');
        }
    }


    public function getAuthData($token) {
        if ($this->agregator === 'loginza') {
            $authData = json_decode(file_get_contents($this->loginzaAuthUrl.$token.'&id='.$this->loginzaIds[$_SERVER['SERVER_NAME']].'&sig='.md5($token.$this->loginzaSigs[$_SERVER['SERVER_NAME']]),true);
        } else if ($this->agregator === 'ulogin') {
            $authData = json_decode(file_get_contents($this->uloginAuthUrl.$this->token.'&host='.$_SERVER['HTTP_HOST']),true);
            $authData['provider'] = $authData['network'] ;
        }

        return $authData ;
    }

/*
 * возвращает соответстующую запись из модели User или false в случае ошибки
 */

    function auth($token) {
        if(!empty($token)) {
            $authData = $this->getAuthData($token);
            
            if(isset($authData['error_type'])) {
                return false ;
            }

            $SocConnectionModel = ClassRegistry::init('Ulogin.SocConnection');
            $currentConnection = $SocConnectionModel->find('first', array(
                'conditions' => array(
                    'SocConnection.provider' => $authData['provider'],
                    'SocConnection.uid' => $authData['uid'],
                )
            ));
            if($currentConnection) {
                $user = $this->User->find('first', array(
                    'conditions' => array(
                        'User.id' => $currentConnection['SocConnection']['user_id'],
                    )
                ));
                return $user ;
            }

            if(empty($authData['email'])) {
                return false ;
            }

            $user = $this->User->find('first', array(
                'conditions' => array(
                    'User.email' => $authData['email'],
                )
            ));

            if(!$user) {
                $user = $this->__register($authData, $referral);
                if($user) {
                    $activationData = array(
                        'User' => $user,
                    );
                    $this->__doActivation($activationData);
                    $user = $this->User->find('first', array(
                        'conditions' => array(
                            'User.id' => $user['id'],
                        )
                    ));
                }
            }
        }
    }

    function soc_connects() {
        $socConnects = ClassRegistry::init('Ulogin.SocConnection')->find('all', array(
            'conditions' => array(
                'SocConnection.user_id' => $this->Auth->user('id'),
            )
        ));
        $this->set('socConnects', $socConnects);
    }

    function del_connection($connectionId = null) {
        if($connectionId) {
            $SocConnectionModel = ClassRegistry::init('Ulogin.SocConnection');
            $currentConnection = $SocConnectionModel->find('first', array(
                'conditions' => array(
                    'SocConnection.user_id' => $this->Auth->user('id'),
                    'SocConnection.id' => $connectionId,
                )
            ));

            if($currentConnection) {
                if($SocConnectionModel->delete($currentConnection['SocConnection']['id'])) {
                    return true ;
                }
            }
        }

        return false ;
    }


    private function __doActivation($userData, $emailOnly = false) {
        $this->Email->template = 'activation';
        $this->Email->to = $userData['User']['email'];
        $this->Email->subject = __('USERS_REGISTRATION',true);
        if($emailOnly) {
            $this->Email->template = 'email_activation';
            $this->Email->subject = __('USERS_NEW_EMAIL_ACTIVATION_TOPIC', true);
        }
        $this->Email->sendAs = 'both';
        $this->set('user', $userData);
        $this->Email->send();
    }

    private function __genpassword($length = 10) {
		srand((double)microtime()*1000000);
		$password = '';
		$vowels = array("a", "e", "i", "o", "u");
		$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "v", "w", "z", "tr",
							"cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
		for($i = 0; $i < $length; $i++){
			$password .= $cons[mt_rand(0, 31)] . $vowels[mt_rand(0, 4)];
		}
		return substr($password, 0, $length);
	}

    private function __register($userData, $referral = false) {
        $password = $this->__genpassword();


        $data = array(
            'username' => $userData['email'],
            'email' => $userData['email'],
            'password' => $password,
            'passwd' => $password,
            'sex' => 'male',
        );

        if(isset($userData['gender'])) {
            if($userData['gender'] == 'M') {
                $data['sex'] = 'male';
            } else {
                $data['sex'] = 'female';
            }
        }

        if($referral) {
            $data['ref_id'] = $referral;
        }

        if($this->User->save($data, false)) {
            $data['id'] = $this->User->getLastInsertId();

            $socData = array(
                'user_id' => $data['id'],
                'uid' => $userData['uid'],
                'provider' => $userData['provider'],
            );
            $SocConnectionModel = ClassRegistry::init('Ulogin.SocConnection');
            $SocConnectionModel->create();
            $SocConnectionModel->save($socData) ;

            return $data;
        }
        
        return false;
    }
}

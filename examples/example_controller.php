<?php
class UsersController extends AppController {
    var $name = 'Users';
    var $components = array(
        'Ulogin.Ulogin'
    );
    var $helpers = array(
        'Html',
        'Session',
        'Ulogin.Ulogin'
	);

    function login() {
        if (isset($this->data['token'])) {
            $user = $this->Ulogin->auth($this->data['token']) ;
            if (! empty($user)) {
                $this->Auth->login($user) ;
                $this->redirect(array('controller'=>'users', 'action'=> 'main_page'));
            } else {
                $this->Session->setFlash('Authorisation error','flash_error') ;
            }

        } else {
            $this->Session->setFlash('Token error','flash_error') ;
        }
    }
}

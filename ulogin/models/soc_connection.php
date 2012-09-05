<?php
class SocConnection extends AppModel {
    var $name = 'SocConnection';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
    );

    function checkConnect($uid, $provider) {
        $check = $this->find('count', array(
            'conditions' => array(
                'SocConnection.uid' => $uid,
                'SocConnection.provider' => $provider,
            )
        ));
        return (boolean)$check;
    }
}
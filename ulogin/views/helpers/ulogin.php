<?php

class UloginHelper extends Helper
{
    var $helpers = array('Html') ;
    //параметры по-умолчанию
    var $uloginParams = array(
        'display'       =>  'panel',
        'fields'        =>  'first_name,last_name,email,nickname, bdate, sex, phone, photo, photo_big, city, country',
        'providers'     =>  'vkontakte,odnoklassniki,google,facebook',
//        'hidden'        =>  'twitter,mailru,google,yandex,livejournal,openid,lastfm,linkedin,liveid,soundcloud,steam',
        'hidden'        =>  '',
        'redirect'      =>  'http://www.vir-city.com/users/loginza_auth',
    );

    public function widget()
    {
        $output = '' ;
        //подключаем JS скрипт
        $output .= $this->Html->script('http://ulogin.ru/js/ulogin.js');
        $output .= '<div id="uLogin" x-ulogin-params="display='.$this->uloginParams['display'].
                                                        ';fields='.$this->uloginParams['fields'].
                                                        ';providers='.$this->uloginParams['providers'].
                                                        ';hidden='.$this->uloginParams['hidden'].
                                                        ';redirect_uri='.urlencode($this->uloginParams['redirect']).'">
                    </div>' ;
        return $output ;
    }

    public function setUloginParams($uloginParams)
    {
        $this->uloginParams = array_merge($this->uloginParams, $uloginParams);
    }
}

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
        'redirect'      =>  '',
    );

    public function widget($provider_images = array(), $redirect = null)
    {
        $output = '' ;
        //подключаем JS скрипт
        $redirect = $redirect?$redirect:$this->uloginParams['redirect'] ;
        $display = empty($provider_images)?$this->uloginParams['display']:'buttons' ;
        $output .= $this->Html->script('http://ulogin.ru/js/ulogin.js');
        $output .= '<div id="uLogin" x-ulogin-params="display='.$display.
                                                        ';fields='.$this->uloginParams['fields'].
                                                        ';providers='.$this->uloginParams['providers'].
                                                        ';hidden='.$this->uloginParams['hidden'].
                                                        ';redirect_uri='.urlencode($redirect).'">';
        foreach($provider_images as $provider=>$image) {
            $output .= '<img src="'.$image.'" x-ulogin-button = "'.$provider.'"/>' ;
        }

        $output .= '</div>' ;
        return $output ;
    }

    public function setUloginParams($uloginParams)
    {
        $this->uloginParams = array_merge($this->uloginParams, $uloginParams);
    }
}

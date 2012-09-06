<html>
<body>
<?= $this->Ulogin->widget(array('vkontakte' => "/img/icon-vk.png",'odnoklassniki' => "/img/icon-od.png",'facebook' => "/img/icon-fb.png",),Router::url(array('controller'=>'users', 'action'=>'auth'), true)) ;?>
</body>
<html>

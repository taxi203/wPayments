<?php

define ('IMG_SCRIPT_URL', 'images/');

require_once $_SERVER['DOCUMENT_ROOT'].'/libs/wPayments/ver2/PayConfig.php';

PaySystem::init($pay_conf['rem-mastera.ru'] );
//foreach(PaySystem::$psList as $ps)  dump($ps);

if (isset($_POST['PaySystem'])) {
  $up = new UserPay($_SESSION['login']['u_id']);
  //_POST['idOrd'] = $up->NewPay($_POST['amount'], 1, $ps['ps'], array('valuta'=>$ps['valuta']));
  $_POST['idOrd'] = 'uu-245';
  $_POST['comment'] = 'не известно за что';
  return PaySystem::generateNewPay($_POST);
}
elseif (isset($_GET['res'])){
  return PaySystem::PayComplite();
}
else {
  return PaySystem::PayMethodsForm(array('addit'=>array('phone'=>array('default'=>'12345')), 'FixAmount__'=>270));
}

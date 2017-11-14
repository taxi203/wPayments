<?php

class rbkPay extends PaySystem{
  public $code = 2;
  public $valuta = array(
    1 => array('code'=>"rbkmoney", 'name'=>"RBK Money", 'img'=>"RBK_money.png", 'status'=>1, 'description'=>'оплата с кошелька в платежной системе RBK'),
    2 => array('code'=>"euroset", 'name'=>"Евросеть", 'img'=>"euroset.png", 'status'=>1, 'description'=>'оплата в салонах Евросети, через кассира или через терминал'),
    3 => array('code'=>"svyaznoy", 'name'=>"Связной", 'img'=>"svyaznoy.png", 'status'=>1, 'description'=>'оплата в салонах Связного, через кассира или через терминал'),
    4 => array('code'=>"mts", 'name'=>"салоны и терминалы МТС", 'img'=>"mts.png", 'status'=>0),
    5 => array('code'=>"contact", 'name'=>"Contact", 'img'=>"contact.jpg", 'status'=>1, 'description'=>"Оплата в банках, работающих с системой денежных переводов Contact.<br/> <i>при оплате обязательно требуется документ, удостоверяющий личность.</i>"),
    6 => array('name'=>"банковский платеж", 'code'=>"sberbank", 'img'=>'sberbank.jpg', 'status'=>1, 'description'=>"перевод средств в отделениях Сбербанка без открытия счета по указанным в квитанции реквизитам"),
   );
  function NewPay($dat){
    $action = "https://rbkmoney.ru/acceptpurchase.aspx";
    //$eshopId = 2011258; //такси-инфор
    //$eshopId = 2013601; // leb
    $params = array(
      'recipientAmount'=>$dat['amount'], 
      'recipientCurrency'=>"RUR", 
      'preference'=>$this->valuta[$dat['PaySystem']]['code'], 
      'eshopId'=>$this->idShop, 
      'orderId'=>$dat['IdOrd'], 
      'serviceName'=>$dat['comment'],
    );
    $params['user_email'] = empty($dat['email']) ? "payment@flowers-tlt.ru" : $dat['email']; 
    return SmartyProcess($iframe_post_tpl, array('action'=>$action, 'target'=>"myiframe", 'params'=>$params))."<a href='/'><b> << на главую </b></a>";
  }
  function verification(){
    $str = "$_POST[eshopId]::$_POST[orderId]::$_POST[serviceName]::$_POST[eshopAccount]::$_POST[recipientAmount]::$_POST[recipientCurrency]::$_POST[paymentStatus]::$_POST[userName]::$_POST[userEmail]::$_POST[paymentData]::".$this->secret;
    if ($_POST['hash'] ===  md5($str))
      return array('ordId'=>$_POST['orderId'], 'sum'=>$_POST['merchantPaymentAmount']);
  }
  function ReturnCode(){ }  
}

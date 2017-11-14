<?php

class yadPay extends PaySystem{
  public $code = 4;
  public $valuta = array(
    1 => array('name'=>"Яндекс.деньги", 'code'=>'PC', 'img'=>"Yandex_dengi.png", 'status'=>1, 'description'=>"Вы будете перемещены на платежную страницу сайта Яндекс денег, где сможете произвести оплату со своего ЯД счета.<br /> <i>кстати, пополнить счет ЯД без коммисий можно через салоны Евросети</i>" ),
    2 => array('name'=>"банковские карты", 'code'=>'AC', 'img'=>"cards.png", 'status'=>1, 'description'=>"оплата банковскими картами через платежный шлюз Яндекса"),
    //3 => array('name'=>'с баланса мобильного', 'code'=>'MC', 'status'=>1),
    //4 => array('name'=>'Оплата наличными через терминалы', 'code'=>'GP', 'status'=>1),
    
  );
  function NewPay($dat){
    
    $action = "https://money.yandex.ru/quickpay/confirm.xml";
    $params = array(
      'receiver'	=> $this->idShop, 
      'label'		=> $dat['IdOrd'],
      'FormComment'	=> $this->NameShop, 
      'short-dest'	=> $dat['comment'],
      'writable-targets'=> 'false', 
      'writable-sum'	=> 'true', 
      'comment-needed'	=> 'false', 
      'quickpay-form'	=> 'shop', 
      'targets'		=> $dat['comment'].'.', 
      'sum'		=> $dat['amount'],
      'submit-button'	=> 'Оплатить',
      'paymentType'	=> $this->valuta[$dat['PaySystem']['valuta']]['code'],
      'successURL'	=> urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?result=success'),
      'failURL'		=> urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?result=fail'),
    );
    
    return SmartyProcess(self::$iframe_post_tpl, array('action'=>$action, 'target'=>'_top', 'params'=>$params));
  }
  function verification(){    
    $str = "$_POST[notification_type]&$_POST[operation_id]&$_POST[amount]&$_POST[currency]&$_POST[datetime]&$_POST[sender]&$_POST[codepro]&" .$this->secret ."&$_POST[label]";
    if ($_POST['sha1_hash'] === sha1($str))
      return array('ordId'=>$_POST['label'], 'sum'=>$_POST['amount']);
  }
  function ReturnCode(){
    return 'OK';
  }
}

<?php

class qiwiPay extends PaySystem{
  public $code = 3;
  public $valuta = array(
    1 => array(
      'code'=>"QIWI", 
      'name'=>"платежные терминалы QIWI", 
      'img'=>"qiwi.png", 
      'AdditParams'=>array('phone' => array('label'=>'телефон для выписки счета', 'remark'=>'некорректный номер телефона')),
      'status'=>1, 
      'description'=>'на указанный Вами номер телефона, будет выписан счет, который Вы сможете оплатить через терминалы, либо со своего мобильного QIWI кошелька. При оплате через терминалы возможна дополнительная комиссия, утанавливаемая владельцем терминала.'
    ),
  );
  function NewPay($dat){
    require_once "PhoneFormat.php";
    $phone = substr(PhoneCorrect($dat['phone']), 1);
    if (empty($phone))
      throw new exception('дайте нормальный телефон');
    /*  
    require_once "lib/qiwi.php";
    require_once "lib/qiwi-config.php";
    global $qiwiConfig;
    if (!empty($this->idShop) && !empty($this->secret)){
      $qiwiConfig['shopID'] = $this->idShop;
      $qiwiConfig['password'] = $this->secret;
    }
    $q = QIWI::getInstance($qiwiConfig);
    $q->createBill(array('phone'=>$phone, 'amount'=>$dat['amount'], 'comment'=>$dat['comment'], 'txn-id'=>$dat['IdOrd']));
    $t = "<div style='border:solid 1px; padding=24px;'>Счет для оплаты в системе QIWI создан</div> <div style='text-align:right;'><a href='/'> << вернуться на главную </a></div>";
    return $t. SmartyProcess(dirname(__FILE__)."/qiwi_instruction.html", array('ImgsUrl'=>IMG_SCRIPT_URL."qiwi_instruction/"));
    */
    
    $data = array(      
      'user' => "tel:+7$phone",
      'amount' => $dat['amount'],
      'ccy' => 'RUB',
      'comment' => $dat['comment'],
      'lifetime' => "2018-01-30T15:35:00",
      'pay_source' => "qw",
      'prv_name' => "Хороший магазин"
    );
    dump($data);
    dump(http_build_query($data));
        
    $ch = curl_init('https://api.qiwi.com/api/v2/prv/'.$this->idShop."/bills/$dat[IdOrd]");
    
    dump( curl_version() );
    return;
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $this->RestId.':'.$this->RestPwd);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Accept: application/json"));
      
    $results = curl_exec ($ch) or die(curl_error($ch));
    dump($results);
    //dump(curl_error($ch));
    curl_close ($ch);

    $successUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?result=success");
    $failUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?result=fail");
    $tr = urlencode($dat['IdOrd']);
    $url = 'https://qiwi.com/order/external/main.action?shop='.$this->idShop."&transaction=$tr&successUrl=$successUrl&failUrl=$failUrl&qiwi_phone=7$phone";
   
    echo "<a href='$url'>Ссылка переадресации для оплаты счета</a>";       
    // header("location: $url");
    
  }
  function hexToStr($hex){
    $string='';
    for ($i = 0; $i < strlen($hex)-1; $i += 2)
      $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    return $string;    
  }
  
  function checkSign(){ //генерация подписи по ключу и строке параметров
    return base64_encode($this->hexToStr(hash_hmac("sha1", $this->getReqParams(), $this->NotifyPwd)));
  }
  
  function getReqParams(){ //возвращает упорядоченную строку значений параметров POST-запроса
    $reqparams = '';
    ksort($_POST);
    foreach ($_POST as $param => $valuep)
      $reqparams = "$reqparams|$valuep";
    return substr($reqparams,1);    
  }
  
  function getSign(){ //Извлечение цифровой подписи из заголовков запроса
    foreach (getallheaders() as $header => $value) 
      if ($header == 'X-Api-Signature')
	return $value;	
  }
  
  function verification(){
    if ($this->checkSign() == $this->getSign())
      return array('IdOrd'=>$_POST['bill_id'], 'sum'=>$_POST['amount']);
  }
  
  function ReturnCode(){
    $error = 0;    
    header('Content-Type: text/xml');
    $xmlres = "<?xml version='1.0'?><result><result_code>$error</result_code></result>";
    return $xmlres;
  }
}

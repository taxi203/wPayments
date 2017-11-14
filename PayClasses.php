<?php
//require_once "WinchUtilsLib.php";

abstract class PaySystem{
  static $iframe_post_tpl;
  static $psList = array();
  static $js_urls = array();
  //protected $idShop, $secret, $NameShop;
  function __construct(array $conf){
    foreach($conf as $k=>$v)
      $this->$k = $v;
    
    self::$psList[$this->code] = $this;
    self::$iframe_post_tpl = __DIR__."/../ifarame_post.html";
  }  
  static function init($ps_conf){
    foreach ($ps_conf['pss'] as $ps=>$pConf){
      if (file_exists(__DIR__."/PaySystems/$ps.php"))
	require_once "PaySystems/$ps.php";
      $p = new $ps($pConf['params']);
      if (isset($pConf['states']) && is_array($pConf['states']))
	foreach($pConf['states'] as $k=>$val)
	  $p->valuta[$k]['status'] = $val;
    }
    if (isset($ps_conf['js_urls']))
      self::$js_urls = $ps_conf['js_urls'];
  }

  function ValutaCode($Valuta){
    return $this->code*256 + $Valuta;
  }
  function PaySystemDecode($val){
    //dump($val);
    return array('ps'=>(int)($val / 256), 'valuta'=>$val % 256);
  }
  
  static function PayMethodsForm($params=null){
    $pss = array();
    foreach(self::$psList as $ps){
      foreach($ps->valuta as $k=>$v){
	$pss[$ps->ValutaCode($k)] = $v;
      }    
    }
    return SmartyProcess(dirname(__FILE__)."/PayMethodsForm.html", array(
      'ImgPath'=>IMG_SCRIPT_URL."PaySystemsLogos/", 
      'pss'=>$pss, 
      'js_urls'=>self::$js_urls, 
      'params'=>$params));
  }
  
  static function generateNewPay($dat){
    $dat['PaySystem'] = self::PaySystemDecode($dat['PaySystem']);
    if (!$ps = PaySystem::$psList[$dat['PaySystem']['ps']])
      throw new Exception('несуществующая платежная система');
    if (!isset($ps->valuta[$dat['PaySystem']['valuta']]))
      throw new Exception('несуществующая валюта');
    return $ps->NewPay($dat);
  }
  
  static function PayComplite(){
    if ($_GET['result']=='success')
      $text = 'оплата успешно произведена';
    elseif($_GET['result']=='fail')
      $text = 'во время оплаты произошла ошибка';
    return SmartyProcess(__DIR__.'/WindowCloser.html', array('MesText'=>$text));    
  }
  
  //abstract public function NewPay($valuta, $IdOrd, $sum, $PayComment, $AdditionalData);
  abstract public function NewPay($dat);
  abstract public function verification();
  abstract public function ReturnCode();
  private function WriteLog(){
    $f = fopen(dirname(__FILE__)."/payments.log",'w');  
    fwrite($f, " ip: ".$_SERVER['REMOTE_ADDR']."\n agent: ".$_SERVER['HTTP_USER_AGENT']."\n query: ".$_SERVER['QUERY_STRING']."\n");
    fwrite($f, "post: ".vd($_POST));
    //fwrite($f, "\n".RBK_verification($_POST));
    fclose($f); 
  }
}

class cashPay extends PaySystem{
  public $code = 1;
  public $valuta = array(1=> array('code'=>"cash", 'name'=>"наличные при получении", 'img'=>"cash.png", 'status'=>1 ));
  function NewPay($dat){
    //return 'оплата наличными';
    return SmartyProcess(__DIR__.'/WindowCloser.html', array('MesText'=>"Вы выбрали: оплата наличными при получении или в магазине.<br/> Благодарим за Ваш заказ."));
  }
  function verification(){}
  function ReturnCode(){}
}

<?php
ini_set('display_errors', 1);
//ini_set('log_errors', 1);

require_once 'PayConfig.php';
require_once __DIR__.'/../../WinchUtilsLib.php';

$dbh = new PDO("mysql:host=localhost;dbname=$DbLog_Name", $DbLog_User, $DbLog_Passwd);
$dbh->exec('set names utf8');

$dbh->prepare("insert into pay_notify_log (ip,agent,query,post) values(inet_aton(?), ?, ?, ? )")->
  execute( array($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['QUERY_STRING'], json_encode($_POST)) );  
$id = $dbh->lastInsertId();

require_once 'ErrorSupervisor.php';
new ErrorSupervisor(function($err) use ($dbh, $id){
  $dbh->prepare('update pay_notify_log set err=? where id=?')->execute(array(json_encode($err), $id));
});

ExecAction($pay_conf['vashvizit.ru'], 'vu-', 563, 10);
die;

//$f = fopen(__DIR__."/payments.log",'w');
//dump($f);
//fwrite($f, " ip: ".$_SERVER['REMOTE_ADDR']."\n agent: ".$_SERVER['HTTP_USER_AGENT']."\n query: ".$_SERVER['QUERY_STRING']."\n");
//fwrite($f, "\nheaders: ".json_encode(getallheaders(), JSON_PRETTY_PRINT));
//fwrite($f, "\nget: ".json_encode($_GET, JSON_PRETTY_PRINT));
//fwrite($f, "\npost: ".json_encode($_POST, JSON_PRETTY_PRINT));


$CurrentPS = $_GET['PayCallback'];

if (class_exists($CurrentPS) && is_subclass_of($CurrentPS, 'PaySystem') ) {  
  $ps_conf = $pay_conf[$_SERVER['SERVER_NAME']];
  $ps = new $CurrentPS($ps_conf['pss'][$CurrentPS]);
  
  //dump($ps);  
  fwrite ($f, "\nverification: ". json_encode($ps->verification($_POST), JSON_PRETTY_PRINT));
  if ($res = $ps->verification($_POST)){
    
    ExecAction($ps_conf, substr($res['OrdId'], 0, 3), substr($res['OrdId'], 3), $res['sum']);
    echo $ps->ReturnCode();
  }
}

function ExecAction(array $conf, $preff, $ord, $sum){
  require_once $conf['preffs'][$preff]['module'];
  PayConfirma($ord, $sum);
}

//fclose($f);

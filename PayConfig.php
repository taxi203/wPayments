<?php
/* 
 * статусы валют:
 * 0 - не активна
 * 1 - активна
 * 2 - скрыта
 */

$ShopName = 'магазин "цветы Тольятти"';

// $MyYadPay_demo = array('idShop' => 4100321641617, 'secret' => '', 'NameShop' => $ShopName);
$MyYadPay_params = array('idShop' => 410014400000, 'secret' => '***', 'NameShop' => $ShopName);
$MyQiwiPay_params = array('idShop' => 2000000, 'RestId' => 69099978 , 'RestPwd' => '***', 'NotifyPwd' => '***', 'NameShop' => $ShopName);

$vv_NameShop = array('NameShop'=>"сайт посуточной аренды квартир");

$DbLog_Name = '';
$DbLog_User = '';
$DbLog_Passwd = '';

$pay_conf = array(
  'vashvizit.ru' => array(
    'js_urls' => array(
      '/visit/js/jquery.maskedinput.js',
    ),
    'pss' => array(
      'yadPay' => array(
	'params' => array_merge($MyYadPay_params, $vv_NameShop),
      ),
      'qiwiPay' => array(
	'params' => array_merge($MyQiwiPay_params, $vv_NameShop),
      ),
    ),
    'preffs' => array(
      'vu-' => array('module' => "$_SERVER[DOCUMENT_ROOT]/visit/UserRoom/Payments.php"),
    )
  ),
  'flowers-tlt.ru' => array(
    'pss' => array(
      'yadPay' => array(
	'params' => $MyYadPay_params,
      ),
      'qiwiPay' => array(
	'params' => $MyQiwiPay_params,
	'states' => array(1=>0),
      ),
      'cashPay' => array('params'=>array()),
    ),    
    'preffs' => array(
      'vu-' => array('module' => "$_SERVER[DOCUMENT_ROOT]/visit/UserRoom/Payments.php"),
      'fs-' => array('module' => "$_SERVER[DOCUMENT_ROOT]/../drupal-7.9/sites/flowers-tlt.ru/modules/FlowerShop/w_checkout.php"),      
    )
  ),
  
  'rem-mastera.ru' => array(
    'js_urls' => array(
      '/media/js/jquery/jquery-1.11.1.min.js',
      '/media/js/jquery.maskedinput.js'
    ),    
    'pss' => array(      
      'yadPay' => array(
	'params' => $MyYadPay_params,
	'states' => array(2=>2),
      ),
      'qiwiPay' => array(
	'params' => $MyQiwiPay_params,
	'states' => array(1=>1),
      ),
      //'rbkPay' => array(2010000, '***', $ShopName), 
      'cashPay' => array('params'=>array()),
    ),
    'preffs' => array(
    )
  ),
  
  'tltbuket.ru' => array(
    'RetURL' => '...',
    'pss' => array(
      'yadPay' => array(
	'params' => array('idShop' => 410014400000, 'secret' => '', 'NameShop' => $ShopName),	
      ),
      'qiwiPay' => array(
	'params' => array(),
	'states' => array(1=>0),
      ),      
      'cashPay' => array('params'=>array()),
    ),
    'preffs' => array(
      'fs-' => array('module' => "$_SERVER[DOCUMENT_ROOT]/../drupal-7.9/sites/flowers-tlt.ru/modules/FlowerShop/w_checkout.php"),
    )
  ),
);

require_once 'PayClasses.php';

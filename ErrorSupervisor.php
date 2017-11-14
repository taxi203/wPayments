<?php

function DefaultErrorHandler($err) {
  dump($err);
};

class ErrorSupervisor {        
  public function __construct($FatalErrorHandler=DefaultErrorHandler, $OtherErrorHandler=DefaultErrorHandler){
    $this->FatalErrorHandler = $FatalErrorHandler;
    $this->OtherErrorHandler = $OtherErrorHandler;
    set_error_handler(array($this, 'OtherErrorCatcher'));		// регистрация ошибок
    register_shutdown_function(array($this, 'FatalErrorCatcher'));	// перехват критических ошибок
    ob_start();		// создание буфера вывода
  }
  public function FatalErrorCatcher() {
    $err = error_get_last();
    if (isset($err) && ($err['type']==E_ERROR || $err['type']==E_PARSE || $err['type']==E_COMPILE_ERROR || $err['type']==E_CORE_ERROR) ){
	ob_end_clean();
	header('HTTP/1.1 500 Internal Server Error');
	call_user_func($this->FatalErrorHandler, $err);
	//dump($err);
    }
    else
      ob_end_flush();
  }
  public function OtherErrorCatcher($errno, $errstr) {
    call_user_func($this->OtherErrorHandler, array('errno'=>$errno, 'errstr'=>$errstr) );
    return false; // выводить ли  Warning
  }  
}

return;

// this exampe 

new ErrorSupervisor(
  function($err){
    print_r($err);    
  },
  function($err){
    echo '<pre>' ;
    print_r($err);
    echo '</pre>' ;
  }
);
echo "генерация простейшего контента";

//require 'null';
include 'null';
//require 'err.php';
<?php
require_once 'HPFExceptions.php';
$my_exception_handler=new HPFExceptions();
set_exception_handler(array($my_exception_handler,"exception_handler"));
set_error_handler(array($my_exception_handler,"error_handler"));
register_shutdown_function(array($my_exception_handler,"shutdown_handler"));
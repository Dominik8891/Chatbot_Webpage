<?php
include	'class/PdoConnect';
include 'class/User';
include 'assets/inc/system.php';
include 'controller/login_system.php';
include 'controller/signup_system.php';

$pdo_instance = new PdoConnect();

session_start();
print_r($GLOBALS);
if (g('act') != null)
{
    $func_name = "act_". g('act');
    call_user_func($func_name);
}
else
{
    home();
}
<?php
include	'class/PdoConnect';
include 'class/User';
include 'class/ChatLog';
include 'assets/inc/system.php';
include 'controller/login_system.php';
include 'controller/signup_system.php';
include 'controller/user_system.php';
include 'controller/message_system.php';

$pdo_instance = new PdoConnect();

session_start();
set_time_limit(300);
#print_r($GLOBALS);
if (g('act') != null)
{
    $func_name = "act_". g('act');
    call_user_func($func_name);
}
else
{
    home();
}
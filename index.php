<?php
include	'class/PdoConnect';
include 'class/User';
include 'assets/inc/system.php';

session_start();

if (g('act') != null)
{
    $func_name = "act_". g('act');
    call_user_func($func_name);
}
else
{
    home();
}
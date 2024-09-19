<?php

#################################### admin bereich ############################# 

function act_login()
{
    if( g('username') != null)
    {
        $user = new User();
        $pwd = md5(g('pwd'));
        $user_id = $user->login(g('username'), $pwd);

        if($user_id > 0)
        {
            $_SESSION['user_id'] = $user->get_id();
            $_SESSION['user_role'] = $user->get_usertype();
        }
    }

    if(!isset($_SESSION['user_id']))
    {
        $html_output = file_get_contents("assets/html/login.html");
        output($html_output);
    }
    output("Sie sind angemeldet!");
}

function act_logout()
{
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    session_destroy();
    output('Sie sind abgemeldet!');
}

############################################################################### 

################################### user bereich ############################## 

function act_goto_login()
{
    if(!isset($_SESSION['user_id']))
    {
        $out = file_get_contents("assets/html/frontend/login.html");
        output_fe($out);
    }
    act_goto_chat();
}


################################################################## 
##  error nachrichten für falsche logindaten einbauen sowohl php als auch js?
################################################################## 
function act_login_fe()
{
    if(g('username') != null && g('pwd') != null)
    {
        $user = new User();
        $pwd = md5(g('pwd'));
        $user_id = $user->login(g('username'), $pwd);

        if($user_id > 0)
        {
            $_SESSION['user_id'] = $user->get_id();
        }
    }
    if(!isset($_SESSION['user_id']))
    {
        $out = file_get_contents("assets/html/frontend/login.html");
        output_fe($out);
    }
    act_goto_chat();
}

function act_logout_fe()
{
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    session_destroy();
    home();
}
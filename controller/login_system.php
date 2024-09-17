<?php

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

    output('Sie sind abgemeldet!');
}
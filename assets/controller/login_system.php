<?php

function act_login()
{
    if( g('username') != null)
    {
        $user = new User();
        $pwd = md5(g('pwd'));
        $user_id = $user->login(g('username'), $pwd);
    }
}
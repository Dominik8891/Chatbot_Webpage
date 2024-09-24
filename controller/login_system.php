<?php

#################################### admin bereich ############################# 

function act_login()
{
    if( g('username') != null)
    {
        $user = new User();
        $username = htmlspecialchars(g('username'));
        $pwd = htmlspecialchars(g('pwd'));
        $user_id = $user->login($username, pwd_decrypt($pwd));

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
    //if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['pwd']))
    {
        if(g('username') != null && g('pwd') != null)
        {
            $user = new User();
            $username = htmlspecialchars(g('username'));
            $pwd = htmlspecialchars(g('pwd'));
            $user_id = $user->login($username, $pwd);

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
        if(!isset($_SESSION['chat_history']))
        {
            $history = new ChatLog();
            $history->set_user_id($_SESSION['user_id']);
            $chat_history = $history->get_history_as_array();
            if(!isset($_SESSION['chat_history']))
            foreach($chat_history as $row)
            {
                $_SESSION['chat_history'][] = array(
                    'role' => $row[1],
                    'content' => $row[2],
                    'timestamp' => $row[3]
                );
            }
            send_greeting(g('username'));
        } 
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

function pwd_decrypt($in_pwd)
{
    $config = include 'config/config.php';
    $pepper = $config['pepper'];
    $pwd = $in_pwd;
    $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
    return $pwd_peppered;
}


<?php

#################################### admin bereich ############################# 

function act_login()
{
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['pwd']))
    {
        $error = "";
        if( g('username') != null)
        {
            $user = new User();
            $username = htmlspecialchars(g('username'));
            $pwd = htmlspecialchars(g('pwd'));
            $user_id = $user->login($username, $pwd);

            if($user_id > 0)
            {
                $_SESSION['user_id'] = $user->get_id();
                $_SESSION['role_id'] = $user->get_type_id();
            }
            else
            {
                $error = "Ungültiger Benutzername oder Passwort.";
                die;
            }
        }

        if(!isset($_SESSION['user_id']) && !isset($_SESSION['role_id']))
        {
            $html_output = file_get_contents("assets/html/login.html");
            $out = str_replace("###LOGIN_ERROR###", $error, $html_output);
            output($out);
            die();
        }
        elseif($_SESSION['role_id'] < 3)
        {
            act_admin("Keine Berechtigung");
        }
        else
        {
            act_admin("Sie sind nun eingelogt!");
        }
        
    }
    else
    {
        header("Location: index.php");
        die;
    } 
}

function act_login_page()
{
    $html_output = file_get_contents("assets/html/login.html");
    $out = str_replace("###LOGIN_ERROR###", "", $html_output);
    output($out);
}

function act_logout()
{
    unset($_SESSION['user_id']);
    unset($_SESSION['role_id']);
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
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['pwd']))
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
                $_SESSION['role_id'] = $user->get_type_id();
            }
            else
            {
                $_SESSION['login_error'] = "Ungültiger Benutzername oder Passwort";
                header("Location: index.php?act=login_fe");
                exit;
            }
        }
        if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] < 2)
        {
            $out = file_get_contents("assets/html/frontend/login.html");
            output_fe($out);
            exit;
        }
        if(!isset($_SESSION['chat_history']))
        {
            get_chat_history();
        } 
    }
    else
    {
        header("Location: index.php");
        exit;
    } 
    act_goto_chat();
    exit;
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

function get_chat_history()
{
    $history = new ChatLog();
    $history->set_user_id($_SESSION['user_id']);
    $chat_history_array = $history->get_history_as_array();
    if(!isset($_SESSION['chat_history']))
    {
        if($chat_history_array != null)
        {
            generate_chat_history($chat_history_array);
        } 
        send_greeting(g('username'));
    }
}

function generate_chat_history($in_chat_history_array)
{
    foreach($in_chat_history_array as $row)
    {
        $originalDate = substr($row[3], 0, 10);
        $time         = substr($row[3], 11);
        $dateObject   = new DateTime($originalDate);
        $_SESSION['chat_history'][] = array(
            'role' => $row[1],
            'content' => $row[2],
            'timestamp' => $dateObject->format('d.m.Y') . ' ' . $time
        );
    }
}


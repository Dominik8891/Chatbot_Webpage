<?php

#################################################################################################### 
################################## signup backend ################################################## 


function act_signup()
{
    if(g('username') != null)
    {
        $username = htmlspecialchars(g('username'));
        $email = htmlspecialchars(g('email'));
        $pwd = htmlspecialchars(g('pwd'));
        $user = new User();
        if(!$user->check_if_username_exists($username))
        {
            output("index.php?act=signup_page?error=username");
            die();
        }
        if(!$user->check_if_email_exists($email))
        {
            output("index.php?act=signup_page?error=email");
            die();
        }
            $user->set_username($username);
            $user->set_email($email);
            $user->set_pwd(pwd_encrypt($pwd));
            $user->save();
    }
    act_goto_chat();
}

function act_goto_signup()
{
    if(isset($_SESSION['user_id']))
    {
        act_goto_chat();
    }
    $out  = "";
    if(g('error') != null)
    {
        $out = check_signup_error(g('error'));
    }
    $html = file_get_contents("assets/html/frontend/signup.html");
    $out = str_replace("###MESSAGE###", $out, $html);
    output_fe($out);
}

function act_signup_fe()
{
    if(g('username') != null)
    {
        $username = htmlspecialchars(g('username'));
        $email = htmlspecialchars(g('email'));
        $pwd = htmlspecialchars(g('pwd'));
        $user = new User();
        if(!$user->check_if_username_exists($username))
        {
            output("index.php?act=signup_page?error=username");
            die();
        }
        if(!$user->check_if_email_exists($email))
        {
            output("index.php?act=signup_page?error=email");
            die();
        }
            $user->set_username($username);
            $user->set_email($email);
            $user->set_pwd(pwd_encrypt($pwd));
            $user->save();
    }
    act_goto_chat();
}

function check_signup_error($in_error)
{
    if($in_error == "username")
    {
        return "Der Benutzername existiert bereits. Bitte einen anderen wählen";
    }
    elseif($in_error == "email")
    {
        return "Diese Email Adresse wird bereits verwendet!";
    }
    else
    {
        return "Bitte ein anderes Password wählen";
    }
}

/*
error messages in php oder js einbauen für falsche
*/

function pwd_encrypt($in_pwd)
{
    $config = include 'config/config.php';
    $pepper = $config['pepper'];
    $pwd = $in_pwd;
    $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
    $pwd_hashed = password_hash($pwd_peppered, PASSWORD_ARGON2I);
    return $pwd_hashed;
}
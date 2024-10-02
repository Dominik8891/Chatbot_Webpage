<?php

#################################################################################################### 
################################## signup backend ################################################## 


function act_signup()
{
    if(g('username') != null)
    {
        $role     = htmlspecialchars(g('type_id'));
        $username = htmlspecialchars(g('username'));
        $email    = htmlspecialchars(g('email'));
        $pwd      = htmlspecialchars(g('pwd'));
        $pwd_scnd = htmlspecialchars(g('pwd_scnd'));
        $user = new User();
        if($user->check_if_username_exists($username))
        {
            header("Location: index.php?act=signup_page&error=username");
            #output("index.php?act=signup_page?error=username");
            die;
        }
        if($user->check_if_email_exists($email))
        {
            header("Location: index.php?act=signup_page&error=email");
            #output("index.php?act=signup_page?error=email");
            die;
        }
        if($pwd == $pwd_scnd)
        {
            $user->set_type_id($role);
            $user->set_username($username);
            $user->set_email($email);
            $user->set_pwd(pwd_encrypt($pwd));
            $user->save();
        } 
        else
        {
            header("Location: index.php?act=signup_page&error=pw");
            #output("index.php?act=signup_page?error=pw");
            die;
        }

    }
    act_goto_chat();
}

function act_signup_page()
{
    $out = file_get_contents("assets/html/signup.html");
    $error = "";
    if(g('error') !== null)
    {
        $script = file_get_contents("assets/js/signup_error.js");
        $error = g('error');
        $error = str_replace("###ERROR###", $error, $script);
        $error = "<script>" . $error . "</script>";
    }
    $out = str_replace("###ERROR###", $error, $out);
    output($out);
}

function pwd_encrypt($in_pwd)
{
    $config = include 'config/config.php';
    $pepper = $config['pepper'];
    $pwd_peppered = hash_hmac("sha256", $in_pwd, $pepper);
    $pwd_hashed = password_hash($pwd_peppered, PASSWORD_ARGON2I);
    return $pwd_hashed;
}
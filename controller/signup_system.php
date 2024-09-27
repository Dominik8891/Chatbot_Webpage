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

function act_signup_page()
{
    $out = file_get_contents("assets/html/signup.html");
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
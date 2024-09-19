<?php

#################################################################################################### 
################################## signup backend ################################################## 


function act_signup()
{
    if(g('username') != null)
    {
        $user = new User();
        if(!$user->check_if_username_exists(g('username')))
        {
            output("index.php?act=signup_page?error=username");
            die();
        }
        if(!$user->check_if_email_exists(g('email')))
        {
            output("index.php?act=signup_page?error=email");
            die();
        }
            $user->set_username(g('username'));
            $user->set_email(g('email'));
            $user->set_pwd(md5(g('pwd')));
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
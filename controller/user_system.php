<?php

function act_manage_user()
{

    // wenn user nicht eingelogt ist oder nur umschüler ist zurück auf die startseite
    if(!isset($_SESSION['user_id']) || isset($_SESSION['user_role']) && $_SESSION['user_role'] == "Umschüler")
    {
        home();
    }

    $out = file_get_contents("assets/html/manage_user.html");
    $tmp_user = new User(intval(g('user_id')));

    $user_info = " neu anlegen";
    $status = "";
    // user mit GET id laden
    if(g('user_id') != null && g('send') == null)
    {
        $status = get_status($tmp_user);
        $user_info = $tmp_user->get_id() . " (" . $tmp_user->get_username() . ")" . " bearbeiten ";
    }
    // Userdaten aus Formular speichern
    elseif(g('send') != null)
    {
        $sel_user = new User(g('id'));
        $sel_user->set_id(g('id'));
        if(g('status')   != null) $sel_user->set_status(g('status'));
        if(g('role')     != null) $sel_user->set_type_id(g('role') + 1);  
        if(g('username') != null) $sel_user->set_username(g('username'));
        if(g('email')    != null) $sel_user->set_email(g('email'));

        if(g('pwd') != null)
        {
            $sel_user->set_pwd(pwd_encrypt(g('pwd')));
        }

        $sel_user->save();

        act_list_user();
    }
    
    $role_out = get_role_options();

    $out = str_replace("###ID###"       , $tmp_user->get_id()       , $out);
    $out = str_replace("###STATUS###"   , $status                   , $out);
    $out = str_replace("###ROLE###"     , $role_out                 , $out);
    $out = str_replace("###USERNAME###" , $tmp_user->get_username() , $out);
    $out = str_replace("###EMAIL###"    , $tmp_user->get_email()    , $out);
    $out = str_replace("###PASSWORD###" , ""                        , $out);
    $out = str_replace("###USER_INFO###", $user_info                , $out);

    output($out);
}

function act_list_user()
{
    // wenn nicht eingelogt dann auf home seite
    if(!isset($_SESSION['user_id']) || isset($_SESSION['user_role']) && $_SESSION['user_role'] == "Umschüler")
    {
        home();
    }

    $user = new User($_SESSION['user_id']);
    $all_user_ids = $user->getAll();

    $table_html = file_get_contents("assets/html/list_user.html");

    $all_rows = generate_user_rows($user, $all_user_ids);
    
    $out = str_replace("###USER_ROWS###", $all_rows, $table_html);

    output($out);
}

function get_action($in_user, $in_current_user)
{
    if($in_user->get_id() == $in_current_user->get_id())
    {
        $action   = 'aktueller Benutzer | <a href="index.php?act=manage_user&user_id=' . $in_current_user->get_id() .'">Ändern</a>';
    }
    elseif($in_user->get_usertype() == "Admin")
    {
        $action   = '<a href="index.php?act=manage_user&user_id=' . $in_current_user->get_id() .'">Ändern</a> | 
                        <a href="#" onclick="del(\'index.php?act=delete_user&user_id='. $in_current_user->get_id() .'\')">Löschen</a>';
    }
    elseif($in_user->get_type_id() <= $in_current_user->get_type_id())
    {
        $action  = "keine Berechtigung";
    }
    else
    {
        $action   = '<a href="index.php?act=manage_user&user_id=' . $in_current_user->get_id() .'">Ändern</a> | 
                        <a href="#" onclick="del(\'index.php?act=delete_user&user_id=' . $in_current_user->get_id() .'\')">Löschen</a>';
    }
    return $action;
}

function act_delete_user()
{
    $tmp_user = new User(g('user_id'));
    $tmp_user->del_it();

    act_list_user();
}

function get_status($in_user)
{
    $status_arr = ["Inaktiv","Aktiv"];
    $status_out = gen_html_options($status_arr, $in_user->get_status(), false);
    $get_status = "<label>Status:</label> <select name='status'> ###GET_STATUS### </select>";
    $status = str_replace("###GET_STATUS###", $status_out, $get_status);
    return $status;
}

function get_role_options()
{
    $tmp_user = new User();
    $user_roles = $tmp_user->get_all_user_types();
    $tmp_arr = [];
    foreach($user_roles as $row)
    {
        if($_SESSION['role_id'] > intval($row[0]))
        {
            array_push($tmp_arr, $row[1]);
        }
        elseif(intval($_SESSION['role_id']) == 4)
        {
            array_push($tmp_arr, $row[1]);
        }  
    }
    $role_out = gen_html_options($tmp_arr, $tmp_user->get_usertype(), false);
    return $role_out;
}

function generate_user_rows($in_user, $in_user_ids)
{
    $row_html = file_get_contents("assets/html/list_user_row.html");

    $all_rows = "";

    foreach($in_user_ids as $one_user_id)
    {
        $tmp_user = new User($one_user_id);
        $status = "Inaktiv";
        if($tmp_user->get_status() == 1)
        {
            $status = "Aktiv";
        }
        $action = get_action($in_user, $tmp_user);

        $tmp_row = str_replace("###ID###"       , $tmp_user->get_id()       , $row_html);
        $tmp_row = str_replace("###STATUS###"   , $status                   , $tmp_row);
        $tmp_row = str_replace("###ROLE###"     , $tmp_user->get_usertype() , $tmp_row);
        $tmp_row = str_replace("###USERNAME###" , $tmp_user->get_username() , $tmp_row);
        $tmp_row = str_replace("###EMAIL###"    , $tmp_user->get_email()    , $tmp_row);
        $tmp_row = str_replace("###ACTION###"   , $action                   , $tmp_row);

        $all_rows .= $tmp_row;
    }
    return $all_rows;
}
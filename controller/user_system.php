<?php

function act_manage_user()
{

    // wenn user nicht eingelogt ist oder nur umschüler ist zurück auf die startseite
    if(!isset($_SESSION['user_id']) || isset($_SESSION['user_role']) && $_SESSION['user_role'] == "Umschüler")
    {
        home();
    }
    check_if_authorized();

    $out = file_get_contents("assets/html/manage_user.html");

    // wird noch benötigt für berechtigung zur bearbeitung bzw auswahl von benutzertypen
    $user = new User(intval($_SESSION['user_id']));
    //
    $tmp_user = new User(intval(g('user_id')));

    $user_info = " neu anlegen";
    $status = "";
    // user mit GET id laden
    if(g('user_id') != null && g('send') == null)
    {
        $status_arr = ["Inaktiv","Aktiv"];
        $status_out = gen_html_options($status_arr, $tmp_user->get_status(), false);
        $user_info = "bearbeiten (" . $tmp_user->get_id() . ")";
        $get_status = "<label>Status:</label> <select name='status'> ###GET_STATUS### </select>";
        $status = str_replace("###GET_STATUS###", $status_out, $get_status);
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
    check_if_authorized();

    $user = new User();
    $all_user_ids = $user->getAll();

    $table_html = file_get_contents("assets/html/list_user.html");
    $row_html = file_get_contents("assets/html/list_user_row.html");

    $all_rows = "";

    foreach($all_user_ids as $one_user_id)
    {
        $tmp_user = new User($one_user_id);
        $status = "Inaktiv";
        if($tmp_user->get_status() == 1)
        {
            $status = "Aktiv";
        }

        $tmp_row = str_replace("###ID###"       , $tmp_user->get_id()       , $row_html);
        $tmp_row = str_replace("###STATUS###"   , $status                   , $tmp_row);
        $tmp_row = str_replace("###ROLE###"     , $tmp_user->get_usertype() , $tmp_row);
        $tmp_row = str_replace("###USERNAME###" , $tmp_user->get_username() , $tmp_row);
        $tmp_row = str_replace("###EMAIL###"    , $tmp_user->get_email()    , $tmp_row);

        $all_rows .= $tmp_row;
    }
    $out = str_replace("###USER_ROWS###", $all_rows, $table_html);

    output($out);
}

function act_delete_user()
{
    check_if_authorized();

    $tmp_user = new User(g('user_id'));
    $tmp_user->del_it();

    act_list_user();
}

function check_if_authorized()
{
    if(!isset($_SESSION['user_id']))
    {
        home();
    }
    if(!isset($_SESSION['user_role']))
    {
        $user = new User($_SESSION['user_id']);
        $_SESSION['user_role'] = $user->get_usertype();
    }
    if($_SESSION['user_role'] == "Umschüler")
    {
        home();
    }
}
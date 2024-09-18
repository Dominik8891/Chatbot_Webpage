<?php

function act_manage_user()
{

    // wenn user nicht eingelogt ist oder nur umschüler ist zurück auf die startseite
    /*if(!isset($_SESSION['user_id']) || isset($_SESSION['user_role']) && $_SESSION['user_role'] == "Umschüler")
    {
        home();
    }*/
    check_if_authorized();

    $out = file_get_contents("assets/html/manage_user.html");

    $tmp_user = new User(intval(g('user_id')));

    $user_info = " neu anlegen";

    // user mit GET id laden
    if(g('user_id') != null && g('send') == null)
    {
        $user_info = "bearbeiten (" . $tmp_user->get_id() . ")";
    }
    // Userdaten aus Formular speichern
    elseif(g('send') != null)
    {
        $tmp_user->set_username(g('username'));
        
        // falls ein passwort eingegeben wurde wied das alte überschrieben
        if(g('pwd') != '')
        {
            $tmp_user->set_pwd(md5(g('pwd')));
        }
        $tmp_user->save();

        act_list_user();
    }

    $out = str_replace("###ID###", $tmp_user->get_id(), $out);
    $out = str_replace("###STATUS###", $tmp_user->get_status(), $out);
    $out = str_replace("###ROLE###", $tmp_user->get_usertype(), $out);
    $out = str_replace("###USERNAME###", $tmp_user->get_username(), $out);
    $out = str_replace("###EMAIL###", $tmp_user->get_email(), $out);
    $out = str_replace("###PASSWORD###", "", $out);
    $out = str_replace("###USER_INFO###", $user_info, $out);

    output($out);
}

function act_list_user()
{
    // wenn nicht eingelogt dann auf home seite
    /*if(!isset($_SESSION['user_id']) || isset($_SESSION['user_role']) && $_SESSION['user_role'] == "Umschüler")
    {
        home();
    }*/
    check_if_authorized();

    $user = new User();
    $all_user_ids = $user->getAll();

    $table_html = file_get_contents("assets/html/list_user.html");
    $row_html = file_get_contents("assets/html/list_user_row.html");

    $all_rows = "";

    foreach($all_user_ids as $one_user_id)
    {
        $tmp_user = new User($one_user_id);

        $tmp_row = str_replace("###ID###", $tmp_user->get_id(), $row_html);
        $tmp_row = str_replace("###STATUS###", $tmp_user->get_status(), $tmp_row);
        $tmp_row = str_replace("###ROLE###", $tmp_user->get_usertype(), $tmp_row);
        $tmp_row = str_replace("###USERNAME###", $tmp_user->get_username(), $tmp_row);
        $tmp_row = str_replace("###EMAIL###", $tmp_user->get_email(), $tmp_row);

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
    if(!isset($_SESSION['user_id']) || isset($_SESSION['user_role']) && $_SESSION['user_role'] == "Umschüler")
    {
        home();
    }
}
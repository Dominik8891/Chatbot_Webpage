<?php

function act_get_name_for_greeting()
{
    if (isset($_POST['login'])) {
        $user = new User($_SESSION['user_id']);
        $response = [
            'username' => $user->get_username()
        ];
        echo json_encode($response);
    }
}

function act_process_message()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    
        $json_data = array(
            'message' => $_POST['message'],
            'user_id' => $_SESSION['user_id']
        );

        $original_dir = getcwd();
        chdir('python');
        $command = 'python main.py ' . base64_encode(json_encode($json_data));
        $output = shell_exec(escapeshellcmd($command));
        chdir($original_dir);

        $response = json_decode($output);
        $history_date = date("d.m.Y H:i");
    
        write_message_in_db($_SESSION['user_id'], $_POST['message'], 'user');
        $_SESSION['bla'] = 'bla';
        write_message_in_db($_SESSION['user_id'], $response, 'bot');

        echo json_encode(array(
            'user_message' => htmlspecialchars($_POST['message']),
            'bot_message' => htmlspecialchars($response),
            'time' => $history_date,
            'now' => date('m.d.Y')
        ));
    }
}

function show_chatbot()
{
    $chat_history = file_get_contents("assets/html/frontend/chatbot.html");
    $bot_history = file_get_contents("assets/html/frontend/history_bot.html");
    $user_history = file_get_contents("assets/html/frontend/history_user.html");
    $tmp_history = "";
    if(isset($_SESSION['chat_history']))
    {
        foreach ($_SESSION['chat_history'] as $message) {
            $time = $message['time'];
            if (substr($message['time'], 0, 10) == date('d.m.Y')) {
                $time = "today" . substr($message['time'], 10);
            }
            if ($message['role'] === 'user') {
                $tmp_hist = str_replace("###MESSAGE###", $message['content'], $user_history);
                $tmp_hist = str_replace("###TIME###", $time, $tmp_hist);
            } elseif ($message['role'] === 'assistant') {
                $tmp_hist = str_replace("###MESSAGE###", $message['content'], $bot_history);
                $tmp_hist = str_replace("###TIME###", $time, $tmp_hist);
            }
            $tmp_history .= $tmp_hist;
        }
    }
    $chat_history = str_replace("###HISTORY###", $tmp_history, $chat_history);
    return $chat_history;
}

function act_goto_chat()
{
    if(isset($_SESSION['user_id']))
    {
        $out = show_chatbot();
        output_fe($out);
    }
    home();
}

function send_greeting($in_username)
{
    $command = 'python python/main1.py ' . base64_encode(json_encode($in_username));
    $output = shell_exec(escapeshellcmd($command));

    $response = json_decode($output);
    $history_date = date("d.m.Y H:i");

    write_message_in_db($_SESSION['user_id'], $response, 'bot');

    echo json_encode(array(
        'bot_message' => htmlspecialchars($response),
        'time' => $history_date,
        'now' => date('m.d.Y')
    ));
}

function write_message_in_db($in_user_id, $in_msg, $in_msg_type)
{
    $chat = new ChatLog();
    $chat->set_user_id($_SESSION['user_id']);
    //$chat->set_user_id($in_user_id);
    $chat->set_msg($in_msg);
    $chat->set_msg_type($in_msg_type);
    $chat->save();
}
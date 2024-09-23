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
    
        $user_msg = htmlspecialchars($_POST['message']);

        $json_data = array(
            'message' => $user_msg,
            'user_id' => $_SESSION['user_id']
        );

        $original_dir = getcwd();
        chdir('python');
        $command = 'python main.py ' . base64_encode(json_encode($json_data));
        $output = shell_exec(escapeshellcmd($command));
        chdir($original_dir);

        $response = json_decode($output);
        $history_date = date("d.m.Y H:i");

        if($response == null || $response == "")
        {
            $chat = new ChatLog();
            $chat->set_user_id($_SESSION['user_id']);
            $response = $chat->get_last_answer();
        }

        $_SESSION['chat_history'][] = array(
            'role' => 'user',
            'content' => htmlspecialchars($user_msg),
            'timestamp' => $history_date
        );

        $_SESSION['chat_history'][] = array(
            'role' => 'bot',
            'content' => $response,
            'timestamp' => $history_date
        );

        echo json_encode(array(
            'user_message' => htmlspecialchars($user_msg),
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
            $time = $message['timestamp'];
            if (substr($message['timestamp'], 0, 10) == date('Y-m-d')) {
                $time = "today" . substr($message['timestamp'], 10);
            }
            if ($message['role'] === 'user') {
                $tmp_hist = str_replace("###MESSAGE###", $message['content'], $user_history);
                $tmp_hist = str_replace("###TIME###", $time, $tmp_hist);
            } elseif ($message['role'] === 'bot') {
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
    $greeting = "Hallo " . $in_username . " was kann ich für dich tun?";
    $history_date = date("Y-m-d H:i");

    $_SESSION['chat_history'][] = array(
        'role' => 'bot',
        'content' => $greeting,
        'timestamp' => $history_date
    );

    echo json_encode(array(
        'bot_message' => htmlspecialchars($greeting),
        'time' => $history_date,
        'now' => date('Y-m-d')
    ));
}

function write_message_in_db($in_msg, $in_msg_type)
{
    $chat = new ChatLog();
    $chat->set_user_id($_SESSION['user_id']);
    $chat->set_msg($in_msg);
    $chat->set_msg_type($in_msg_type);
    $chat->save();
}
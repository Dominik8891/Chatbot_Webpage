<?php

function act_login_message()
{
    if (isset($_POST['login'])) {
        $user = new User($_SESSION['user_id']);
        $response = [
            'username' => $user->get_username(),
            'time' => date("H:i")
        ];
        echo json_encode($response);
    }
}

function act_process_message()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
        $history_filename = 'history' . '.txt';
        $folder = 'userdata/user' . $_SESSION['user_id'] . '/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $history_file = $folder . $history_filename;
    
        $json_data = array(
            'message' => $_POST['message'],
            'user_id' => $_SESSION['user_id']
        );

        $history_json = json_encode(array(
            'chat_history' => $_SESSION['chat_history']
        ));
        file_put_contents($history_file, $history_json);
    
        $command = 'python main1.py ' . base64_encode(json_encode($json_data));
        $output = shell_exec(escapeshellcmd($command));
    
        $response = json_decode($output);
        $history_date = date("d.m.Y H:i");
    
        $_SESSION['chat_history'][] = array(
            'role' => 'user',
            'content' => htmlspecialchars($_POST['message']),
            'time' => $history_date
        );
        if (isset($response)) {
            $_SESSION['chat_history'][] = array(
                'role' => 'assistant',
                'content' => htmlspecialchars($response),
                'time' => $history_date
            );
        }
        
        $ai_command = json_decode($output);
        shell_exec(escapeshellcmd($ai_command));

        $history_json = json_encode(array(
            'chat_history' => $_SESSION['chat_history']
        ));
        file_put_contents($history_file, $history_json);

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
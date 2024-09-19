$(document).ready(function () {
    var greetingMessage = '<div class="message bot"><div class="content"><strong>Bot:</strong><br>Willkommen!</div><div class="time">today <?php echo date("H:i"); ?></div></div>';
    $('#chat-history').append(greetingMessage);
    $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
});
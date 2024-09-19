$(document).ready(function () {

    $('#chat-form').submit(function (e) {
        e.preventDefault();
        var message = $('#message').val();

        var formattedTime = "recently ";
            
        $('#chat-history').append(
            '<div class="message user">' +
                '<div class="header"><strong>You:</strong></div>' +
                '<div class="content">' + message + '</div>' +
                '<div class="time" id="user_time">' + formattedTime + '</div>' +
            '</div>'
        );

        $('#chat-history').append(
            '<div class="message bot">' +
                '<div class="content" id="bot_msg"><strong>Bot:</strong><br>' + '<div id="dots" style="display: none;"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>' + '</div>' +
                '<div class="time" id="bot_time">' + formattedTime + '</div>' +
            '</div>'
        );
        $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
        const dots = document.getElementById('dots');
        dots.style.display = 'flex';
        $('#message').val('');

        $.ajax({
            type: 'POST',
            url: 'index.php?act=process_message',
            data: { message: message },
            dataType: 'json',
            timeout: 600000,
            success: function (response) {
                if (response.error) {
                    alert(response.error);
                } else {

                    var currentDate = new Date(response.now);
                    var currDay = currentDate.getDate().toString().padStart(2, '0');
                    var currMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0');
                    var currYear = currentDate.getFullYear();
                    var currDate = currDay + "." + currMonth + "." + currYear;

                    var formattedTime = currDate === response.time.slice(0, 10)
                        ? "today " + response.time.slice(-5)
                        : response.time;
                    
                    document.getElementById("bot_msg").innerHTML = "<strong>Bot:</strong><br>" + response.bot_message.replace("\n","<br>");
                    document.getElementById("bot_msg").id = "";

                    document.getElementById("user_time").innerHTML = formattedTime;
                    document.getElementById("user_time").id = "";

                    document.getElementById("bot_time").innerHTML = formattedTime;
                    document.getElementById("bot_time").id = "";

                    $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Fehler: " + error);
                alert('An error occurred. Please try again.');
            }
        });
    });
});
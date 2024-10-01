$(document).ready(function () {
    
    // Funktion zum Senden der Nachricht
    function sendMessage() {
        var message = $('#message').val();

        // Initialisiere "recently"
        var formattedTime = "recently ";

        // Nachricht des Nutzers zum Chat-Verlauf hinzufügen
        $('#chat-history').append(
            '<div class="message user">' +
                '<div class="header"><strong>You:</strong></div>' +
                '<div class="content">' + message + '</div>' +
                '<div class="time" id="user_time">' + formattedTime + '</div>' +
            '</div>'
        );

        // Bot-Nachricht Platzhalter zum Chat-Verlauf hinzufügen
        $('#chat-history').append(
            '<div class="message bot">' +
                '<div class="content" id="bot_msg"><strong>Bot:</strong><br>' + '<div id="dots" style="display: none;"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>' + '</div>' +
                '<div class="time" id="bot_time">' + formattedTime + '</div>' +
            '</div>'
        );

        // Scroll zum Ende des Chat-Verlaufs
        $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);

        // Zeige die animierten Punkte an
        const dots = document.getElementById('dots');
        dots.style.display = 'flex';

        // Nachrichtentextfeld leeren
        $('#message').val('');

        // AJAX-Request an den Server
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
                    // Formatierung der Zeit
                    var currentDate = new Date(response.now);
                    var currDay = currentDate.getDate().toString().padStart(2, '0');
                    var currMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0');
                    var currYear = currentDate.getFullYear();
                    var currDate = currDay + "." + currMonth + "." + currYear;

                    var formattedTime = currDate === response.time.slice(0, 10)
                        ? "today " + response.time.slice(-5)
                        : response.time;

                    // Bot-Nachricht anzeigen
                    document.getElementById("bot_msg").innerHTML = "<strong>Bot:</strong><br>" + response.bot_message.replace("\n", "<br>");
                    document.getElementById("bot_msg").id = "";

                    // Zeit der Benutzernachricht aktualisieren
                    document.getElementById("user_time").innerHTML = formattedTime;
                    document.getElementById("user_time").id = "";

                    // Zeit der Bot-Nachricht aktualisieren
                    document.getElementById("bot_time").innerHTML = formattedTime;
                    document.getElementById("bot_time").id = "";

                    // Scroll wieder ans Ende
                    $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Fehler: " + error);
                alert('An error occurred. Please try again.');
            }
        });
    }

    // Senden-Button und Enter-Taste für das Absenden der Nachricht
    $('#chat-form').submit(function (e) {
        e.preventDefault();
        sendMessage();
    });

    // Drücken der Enter-Taste ohne Shift zum Senden der Nachricht
    $('#message').keydown(function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // Verhindert Zeilenumbruch
            sendMessage(); // Nachricht senden
        }
    });
});

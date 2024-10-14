$(document).ready(function () {
    
    // Funktion zum Senden der Nachricht
    function sendMessage() {
        var message = $('#message').val();

        // Initialisiere "recently" für Zeitstempel
        var formattedTime = "kürzlich ";

        // Benutzer-Nachricht in den Chat-Verlauf einfügen
        $('#chat-history').append(
            '<div class="message user">' +
                '<div class="header"><strong>Du:</strong></div>' +
                '<div class="content">' + message + '</div>' +
                '<div class="time" id="user_time">' + formattedTime + '</div>' +
            '</div>'
        );

        // Platzhalter für Bot-Nachricht in den Chat-Verlauf einfügen
        $('#chat-history').append(
            '<div class="message bot">' +
                '<div class="content" id="bot_msg"><strong>Bot:</strong><br>' +
                '<div id="dots" style="display: none;"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>' +
                '</div>' +
                '<div class="time" id="bot_time">' + formattedTime + '</div>' +
            '</div>'
        );

        // Automatisches Scrollen zum Ende des Chat-Verlaufs
        $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);

        // Zeige die animierten Punkte (Ladeanimation)
        const dots = document.getElementById('dots');
        dots.style.display = 'flex';

        // Leere das Eingabefeld
        $('#message').val('');

        // AJAX-Request an den Server, um die Bot-Nachricht zu erhalten
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
                    // Zeitstempel formatieren (heute oder spezifisches Datum)
                    var currentDate = new Date(response.now);
                    var currDay = currentDate.getDate().toString().padStart(2, '0');
                    var currMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0');
                    var currYear = currentDate.getFullYear();
                    var currDate = currDay + "." + currMonth + "." + currYear;

                    var formattedTime = currDate === response.time.slice(0, 10)
                        ? "heute " + response.time.slice(-5)
                        : response.time;

                    // Bot-Nachricht aktualisieren und anzeigen
                    document.getElementById("bot_msg").innerHTML = "<strong>Bot:</strong><br>" + response.bot_message.replace("\n", "<br>");
                    document.getElementById("bot_msg").id = "";

                    // Zeitstempel der Benutzernachricht aktualisieren
                    document.getElementById("user_time").innerHTML = formattedTime;
                    document.getElementById("user_time").id = "";

                    // Zeitstempel der Bot-Nachricht aktualisieren
                    document.getElementById("bot_time").innerHTML = formattedTime;
                    document.getElementById("bot_time").id = "";

                    // Automatisches Scrollen zum Ende des Chat-Verlaufs nach der Antwort
                    $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Fehler: " + error);
                alert('An error occurred. Please try again.');
            }
        });
    }

    // Nachricht senden bei Formular-Submit
    $('#chat-form').submit(function (e) {
        e.preventDefault(); // Verhindert das Neuladen der Seite
        sendMessage(); // Nachricht senden
    });

    // Nachricht senden bei Enter-Taste ohne Shift
    $('#message').keydown(function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // Verhindert den Zeilenumbruch
            sendMessage(); // Nachricht senden
        }
    });
});

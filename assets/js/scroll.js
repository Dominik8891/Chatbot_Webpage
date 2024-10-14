// Wartet, bis das Dokument vollständig geladen ist, bevor das Skript ausgeführt wird.
$(document).ready(function () {

    // Scrollt das Chat-Fenster automatisch an das Ende des Verlaufs.
    $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
});

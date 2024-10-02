document.addEventListener("DOMContentLoaded", function() {
    var error = "###ERROR###"; // PHP setzt hier den Fehlercode ein
    
    // Fehler auswerten und entsprechende Nachricht anzeigen
    if (error) {
        if (error === "username") {
            alert("Username already exists. Please choose another one.");
        } else if (error === "email") {
            alert("Email already in use. Please choose another one.");
        } else if (error === "pw") {
            alert("Passwords do not match. Please try again.");
        } else {
            alert("An unknown error occurred. Please try again.");
        }
    }
});
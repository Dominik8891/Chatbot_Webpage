from dotenv import load_dotenv
import sys
import json
import requests
import base64
from db_connect import get_history, insert_msg, get_msg

model = "llama3.1"
# Laden der Umgebungsvariablen aus der .env Datei
load_dotenv()

# Verarbeitet die eingehende Nachricht des Benutzers
def handle_message(data):
    # System-Kontext, der festgelegte Informationen bereitstellt
    content = """   
    """
    context = [{"role": "system", "content": content}]

    # Extrahiere Nachricht und Benutzer-ID aus den JSON-Daten
    user_input = data.get('message', '')
    user_id = data.get('user_id', '')

    # Chat-Verlauf des Benutzers abrufen und zum Kontext hinzufügen
    history_data = get_history(user_id)
    context.extend(history_data)

    # Benutzer-Nachricht in der Datenbank speichern
    insert_msg(user_id, user_input, 'user')

    try:
        # Nachricht zusammen mit Kontext an das Modell senden
        messages = context + [{"role": "user", "content": user_input}]
        r = requests.post(
            "http://127.0.0.1:11434/api/chat",
            json={"model": model, "messages": messages, "stream": True},
            stream=True
        )
        r.raise_for_status()

        output = ""

        # Verarbeite den Antwort-Stream vom Modell
        for line in r.iter_lines():
            body = json.loads(line)
            if "error" in body:
                raise Exception(body["error"])
            if body.get("done") is False:
                message = body.get("message", "")
                content = message.get("content", "")
                output += content

            # Wenn das Modell die Antwort abgeschlossen hat, speichere die Nachricht
            if body.get("done", False):
                message["content"] = output

        # Antwort des Bots in der Datenbank speichern
        insert_msg(user_id, message['content'], 'bot')

    except requests.exceptions.RequestException as e:
        # Fehler bei der Anfrage verarbeiten
        raise SystemExit(e)

if __name__ == "__main__":
    # Lese die JSON-Daten von der Standard-Eingabe (Base64 kodiert)
    input_data = base64.b64decode(sys.argv[1])

    try:
        # Lade die JSON-Daten und verarbeite die Nachricht
        data = json.loads(input_data)
        handle_message(data)

    except json.JSONDecodeError as e:
        # Fehler beim JSON-Parsing behandeln
        error_response = {
            "error": "Invalid JSON: " + str(e)
        }
        print(json.dumps(error_response))

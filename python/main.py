from dotenv import load_dotenv
import sys
import json
import requests
import base64
from db_connect import get_history, insert_msg, get_msg

model = "llama3.1"
# Laden der Umgebungsvariablen aus der .env Datei
load_dotenv()



def handle_message(data):
    content = f"""        
    """
    context = [{"role": "system", "content": content}]
    
    # Extrahiere Nachricht und Session-ID aus den JSON-Daten
    user_input = data.get('message', '')
    user_id = data.get('user_id', '')
    
    history_data = get_history(user_id)
    
    #context.extend(history_data)
    insert_msg(user_id, user_input, 'user')
    try:
        messages= context + [{"role": "user", "content": user_input}]
        r = requests.post(
            "http://127.0.0.1:11434/api/chat",
            json={"model":model, "messages": messages, "stream":True},
            stream=True
        )
        r.raise_for_status()
        output = ""

        for line in r.iter_lines():
            body = json.loads(line)
            if "error" in body:
                raise Exception(body["error"])
            if body.get("done") is False:
                message = body.get("message", "")
                content = message.get("content", "")
                output += content
                # the response streams one token at a time, print that as we receive it

            if body.get("done", False):
                message["content"] = output
               
        insert_msg(user_id, message['content'], 'bot')       
        # Extrahiere die Antwort des Bots aus der API-Antwort
        #print(json.dumps(message['content'], indent=2, ensure_ascii=True))
        with open("test.txt", "w", encoding="utf-8") as f:
            #f.write(msg)
            f.write(message['content'])

    except requests.exceptions.RequestException as e:
        # Handle errors
        raise SystemExit(e)


if __name__ == "__main__":
    # Lese die JSON-Daten von der Standard-Eingabe
    input_data = base64.b64decode(sys.argv[1])

    try:
        # Lade die JSON-Daten
        data = json.loads(input_data)

        # Verarbeite die Nachricht und behandle sie
        handle_message(data)

    except json.JSONDecodeError as e:
        # Handle JSON decode error
        error_response = {
            "error": "Invalid JSON: " + str(e)
        }
        print(json.dumps(error_response))

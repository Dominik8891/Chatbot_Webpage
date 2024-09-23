import os
import json
from dotenv import load_dotenv
import mysql.connector
from mysql.connector import Error

dotenv_path = os.path.join(os.path.dirname(__file__), '..',  'config', '.env')
load_dotenv(dotenv_path=dotenv_path)

host = os.getenv('HOST')
port = os.getenv('PORT')
user = os.getenv('DBUSERNAME')
pwd = os.getenv('PASSWORD')
dbname = os.getenv('DBNAME')

print(f"Host: {host}, Port: {port}, User: {user}, Passwort: {pwd}, DB: {dbname}")

def create_connection():
    connection = None
    try:
        connection = mysql.connector.connect(
            host=host,
            port=port,
            user=user,
            passwd=pwd,
            database=dbname
        )
        print("Erfolgreich mit der MySQL-Datenbank verbunden!")
    except Error as e:
        print(f"Fehler bei der Verbindung: {e}")

    return connection


def execute_query(connection, query, params = None):
    cursor = connection.cursor()
    try:
        cursor.execute(query, params)
        connection.commit() # Notwendig für INSER, UPDATE oder DELETE
        print("Abfrage erfolgreich ausgeführt")
    except Error as e:
        print(f"Fehler bei der Abfrage: {e}")
    finally:
        cursor.close()


def fetch_query_results(connection, query, params=None):
    cursor = connection.cursor()
    result = None
    try:
        cursor.execute(query, params)
        result = cursor.fetchall()
        return result
    except Error as e:
        print(f"Fehler beim Abrufen der Daten: {e}")
    finally:
        cursor.close()


def close_connection(connection):
    if connection.is_connected():
        connection.close()
        print("Die Verbindung zur Datenbank wurde geschlossen.")



def get_history(id, limit=5):
    connection = create_connection()
    query = """WITH last_user_msg AS (
                    SELECT *
                    FROM chatlog
                    WHERE msg_type = 'user' AND user_id = %s AND deleted = 0
                    ORDER BY timestamp DESC
                    LIMIT %s
                  ),
                  first_user_timestamp AS (
                    SELECT MIN(timestamp) AS min_user_timestamp
                    FROM last_user_msg
                  )
                  
                  SELECT *
                  FROM chatlog
                  WHERE msg_type = 'bot'
                  AND timestamp >= (SELECT min_user_timestamp FROM first_user_timestamp)
                  
                  UNION ALL
                  
                  SELECT * 
                  FROM last_user_msg
                  WHERE user_id = %s 
                  AND deleted = 0
                  ORDER BY timestamp ASC;"""
    
    params = (id, limit, id)  # Verwende ein Tuple hier
    results = fetch_query_results(connection, query, params)
    json_output = json.dumps(results, default=str)
    close_connection(connection)
    return json_output

def get_msg(id):
    connection = create_connection()
    query = """SELECT msg FROM chatlog WHERE user_id = %s ORDER BY timestamp DESC LIMIT 1
            """
    params = (id,)
    results = fetch_query_results(connection, query, params)
    #json_output = json.dumps(results, default=str)
    close_connection(connection)
    return results[0][0]

def insert_msg(id, msg, msg_type):
    connection = create_connection()
    query = """INSERT INTO chatlog (user_id, msg, msg_type)
                            VALUES (%s, %s, %s);
    """
    params = (id, msg, msg_type)  # Verwende ein Tuple hier
    execute_query(connection, query, params)

    
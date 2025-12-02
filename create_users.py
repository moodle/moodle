import csv
import os
import requests
import random
import logging
from dotenv import load_dotenv

# CONFIG
REMOVE_USERS = False # Set to True to delete users
DEFAULT_PASSWORD = "AustIsTheBest123!"

# SETUP
load_dotenv()
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

MOODLE_URL = os.getenv('MOODLE_URL')
MOODLE_TOKEN = os.getenv('MOODLE_TOKEN')

if not MOODLE_URL or not MOODLE_TOKEN:
    # Try to find .env in parent directory if not found
    if os.path.exists(os.path.join(os.path.dirname(__file__), '.env')):
        load_dotenv(os.path.join(os.path.dirname(__file__), '.env'))
        MOODLE_URL = os.getenv('MOODLE_URL')
        MOODLE_TOKEN = os.getenv('MOODLE_TOKEN')

if not MOODLE_URL or not MOODLE_TOKEN:
    logging.error("MOODLE_URL and MOODLE_TOKEN must be set in .env")
    exit(1)

API_ENDPOINT = f"{MOODLE_URL.rstrip('/')}/webservice/rest/server.php"

def flatten_params(d, parent_key='', sep=''):
    items = []
    for k, v in d.items():
        new_key = f"{parent_key}[{k}]" if parent_key else k
        if isinstance(v, dict):
            items.extend(flatten_params(v, new_key, sep=sep).items())
        elif isinstance(v, list):
            for i, val in enumerate(v):
                list_key = f"{new_key}[{i}]"
                if isinstance(val, dict):
                    items.extend(flatten_params(val, list_key, sep=sep).items())
                else:
                    items.append((list_key, val))
        else:
            items.append((new_key, v))
    return dict(items)

def call_moodle(func, params=None):
    if not params: params = {}
    url_params = {
        'wstoken': MOODLE_TOKEN,
        'wsfunction': func,
        'moodlewsrestformat': 'json'
    }
    
    flat_params = flatten_params(params)
    try:
        r = requests.post(API_ENDPOINT, params=url_params, data=flat_params, timeout=30)
        r.raise_for_status()
        return r.json()
    except Exception as e:
        logging.error(f"API Request Error: {e}")
        return {'exception': 'APIError', 'message': str(e)}

def process_users():
    csv_path = os.path.join(os.path.dirname(__file__), 'users.csv')
    if not os.path.exists(csv_path):
        logging.error(f"users.csv not found at {csv_path}")
        return

    with open(csv_path, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        users_to_process = []
        
        for row in reader:
            email = row.get('email', '').strip()
            if not email: continue
            
            full_name = row.get('full_name', '').strip()
            parts = full_name.split(' ', 1)
            firstname = parts[0]
            lastname = parts[1] if len(parts) > 1 else 'User'
            
            dept = row.get('department_full_name', '').strip()
            job = row.get('job_title', '').strip()
            
            users_to_process.append({
                'username': email.lower(),
                'email': email,
                'firstname': firstname,
                'lastname': lastname,
                'department': dept,
                'institution': job, # Mapping job title to institution
                'lang': random.choice(['en', 'es_mx']),
                'auth': 'manual',
                'password': DEFAULT_PASSWORD
            })

    if REMOVE_USERS:
        delete_users(users_to_process)
    else:
        create_users(users_to_process)

def create_users(users):
    logging.info(f"Creating {len(users)} users...")
    
    for user in users:
        try:
            logging.info(f"Creating {user['username']}...")
            res = call_moodle('core_user_create_users', {'users': [user]})
            if isinstance(res, dict) and 'exception' in res:
                if 'Username already exists' in res['message']:
                     logging.info(f"User {user['username']} already exists. Updating...")
                     # Update user to ensure dept/job are correct
                     # Need ID first
                     get_res = call_moodle('core_user_get_users', {'criteria': [{'key': 'username', 'value': user['username']}]})
                     if isinstance(get_res, dict) and 'users' in get_res and len(get_res['users']) > 0:
                         existing_user = get_res['users'][0]
                         update_payload = {
                             'id': existing_user['id'],
                             'department': user['department'],
                             'institution': user['institution']
                         }
                         call_moodle('core_user_update_users', {'users': [update_payload]})
                         logging.info(f"Updated {user['username']}")
                else:
                    logging.warning(f"Failed to create {user['username']}: {res['message']}")
            else:
                logging.info(f"Created {user['username']}")
        except Exception as e:
            logging.error(f"Error processing {user['username']}: {e}")

def delete_users(users):
    logging.info(f"Deleting {len(users)} users...")
    
    for user in users:
        try:
            # Get user by username
            res = call_moodle('core_user_get_users', {'criteria': [{'key': 'username', 'value': user['username']}]})
            if isinstance(res, dict) and 'users' in res and len(res['users']) > 0:
                userid = res['users'][0]['id']
                logging.info(f"Deleting {user['username']} (ID: {userid})...")
                del_res = call_moodle('core_user_delete_users', {'userids': [userid]})
                if del_res is None or (isinstance(del_res, list) and not del_res): 
                     logging.info(f"Deleted {user['username']}")
                elif isinstance(del_res, dict) and 'exception' in del_res:
                     logging.warning(f"Failed to delete {user['username']}: {del_res['message']}")
            else:
                logging.warning(f"User {user['username']} not found.")
        except Exception as e:
            logging.error(f"Error deleting {user['username']}: {e}")

if __name__ == "__main__":
    process_users()

import os
import logging
import json
import requests
import time
from dotenv import load_dotenv

# --- CONFIGURATION ---
load_dotenv()

# LOGGING
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - THE_CLEANER - %(levelname)s - %(message)s'
)

MOODLE_URL = os.getenv('MOODLE_URL')
MOODLE_TOKEN = os.getenv('MOODLE_TOKEN')
API_ENDPOINT = f"{MOODLE_URL.rstrip('/')}/webservice/rest/server.php"

# BATCH SIZE (Prevent Timeouts)
DELETE_BATCH_SIZE = 1

# --- HELPERS (Reused from your code) ---

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
        # TIMEOUT INCREASED TO 300 SECONDS (5 MINUTES)
        r = requests.post(API_ENDPOINT, params=url_params, data=flat_params, timeout=300)
        r.raise_for_status()
        return r.json()
    except requests.exceptions.ReadTimeout:
        # If it still times out, assume Moodle is still chugging along.
        logging.warning(f"Request timed out, but Moodle is likely still deleting in the background.")
        return {}
    except Exception as e:
        logging.error(f"API Connection Failed: {e}")
        raise

# --- CORE LOGIC ---

def get_all_courses():
    logging.info("Fetching all courses...")
    # Return all courses (no filtering params needed to get everything)
    courses = call_moodle('core_course_get_courses')
    
    if isinstance(courses, dict) and 'exception' in courses:
        raise Exception(f"Fetch Failed: {courses['message']}")
        
    return courses

def delete_batch(course_ids):
    if not course_ids:
        return

    logging.info(f"Deleting batch of {len(course_ids)} courses: {course_ids}")
    
    payload = {'courseids': course_ids}
    
    # core_course_delete_courses usually returns an object with warnings if any
    res = call_moodle('core_course_delete_courses', payload)
    
    if isinstance(res, dict) and 'exception' in res:
        logging.error(f"DELETE FAILED: {res['message']}")
    elif isinstance(res, dict) and 'warnings' in res and res['warnings']:
         for w in res['warnings']:
             logging.warning(f"Moodle Warning: {w['message']}")
    else:
        logging.info("Batch deleted successfully.")

def main():
    print("!!! DANGER ZONE !!!")
    print("This script will delete ALL courses in the Moodle instance.")
    print(f"Target: {MOODLE_URL}")
    confirm = input("Type 'DELETE EVERYTHING' to proceed: ")
    
    if confirm != "DELETE EVERYTHING":
        print("Aborted.")
        return

    try:
        courses = get_all_courses()
        
        # FILTER: Never delete Course ID 1 (Site Home)
        target_ids = [c['id'] for c in courses if c['id'] != 1]
        
        total = len(target_ids)
        logging.info(f"Found {len(courses)} courses. {total} targeted for deletion.")

        if total == 0:
            logging.info("No courses to delete. System is clean.")
            return

        # PROCESS IN BATCHES
        for i in range(0, total, DELETE_BATCH_SIZE):
            batch = target_ids[i:i + DELETE_BATCH_SIZE]
            delete_batch(batch)
            # Small sleep to let DB catch breath
            time.sleep(1) 

        logging.info("--- CLEANUP COMPLETE ---")

    except Exception as e:
        logging.error(f"Fatal Error: {e}")

if __name__ == "__main__":
    main()
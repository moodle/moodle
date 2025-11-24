import os
import re
import logging
import json
import base64
import requests
import psycopg2
import psycopg2.extras
from dotenv import load_dotenv
from typing import Dict, List, Optional
import urllib.parse

# Import your provided alerter
import job_alerter

# --- 1. CONFIGURATION ---

load_dotenv()

# LOGGING
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - GIGA_MASTER - %(levelname)s - %(message)s'
)
logging.getLogger("urllib3").setLevel(logging.WARNING)

# --- CONFIG ---
PLM_TARGETS = [
    {
        'description': 'Manufacturing Instructions (MI)',
        'table_name': 'Itemsdmrdocuments',
        'db_filter': '%_MI_%', 
        'workspace_id': '193',
        'category_id': 1 # MASTER: Verify this Category ID exists
    }
]

# FLAGS
STRICT_PDF_ONLY = True 
DOWNLOAD_DIR = "temp_staging_area"

# CREDENTIALS
MOODLE_URL = os.getenv('MOODLE_URL')
MOODLE_TOKEN = os.getenv('MOODLE_TOKEN')

# API ENDPOINTS
# We return to the standard server.php. It is the only way for 'core_' functions.
API_BASE = MOODLE_URL.rstrip('/') 
API_ENDPOINT = f"{API_BASE}/webservice/rest/server.php"

PG_HOST = os.getenv('PG_HOST')
PG_PORT = os.getenv('PG_PORT', "5432")
PG_DB = os.getenv('PG_DBNAME_FUSION', "autodesk-fusion")
PG_USER = os.getenv('PG_USER')
PG_PASSWORD = os.getenv('PG_PASSWORD')

AUTODESK_CLIENT_ID = os.getenv('AUTODESK_CLIENT_ID')
AUTODESK_CLIENT_SECRET = os.getenv('AUTODESK_CLIENT_SECRET')
AUTODESK_HOST = os.getenv('AUTODESK_HOST')
AUTODESK_USER_EMAIL = os.getenv('AUTODESK_USER_EMAIL')
BASE_API_URL = f"https://{AUTODESK_HOST}/api/v2"

# GLOBAL CACHE
access_token = None
COURSE_CACHE = {} 

# --- 2. DATABASE ---

def get_db_connection():
    try:
        conn = psycopg2.connect(
            host=PG_HOST, port=PG_PORT, dbname=PG_DB, user=PG_USER, password=PG_PASSWORD
        )
        return conn
    except Exception as e:
        logging.error(f"FATAL: Database connection failed: {e}")
        raise

def fetch_targets_from_db(conn, table_name: str, filter_str: str) -> List[Dict]:
    sql = f'SELECT i.* FROM public."{table_name}" AS i WHERE i.descriptor LIKE %s'
    try:
        with conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor) as cur:
            cur.execute(sql, (filter_str,))
            rows = cur.fetchall()
        return rows
    except Exception as e:
        logging.error(f"DB Query Failed on table {table_name}: {e}")
        raise

# --- 3. AUTODESK V2 API ---

def get_autodesk_token():
    global access_token
    logging.info("Refreshing Autodesk Token...")
    try:
        b64 = base64.b64encode(f"{AUTODESK_CLIENT_ID}:{AUTODESK_CLIENT_SECRET}".encode()).decode()
        token_url = 'https://developer.api.autodesk.com/authentication/v2/token'
        headers = {'Authorization': f"Basic {b64}", 'Content-Type': 'application/x-www-form-urlencoded'}
        data = {'grant_type': 'client_credentials', 'scope': 'data:read'}
        resp = requests.post(token_url, headers=headers, data=data, timeout=15)
        resp.raise_for_status()
        access_token = resp.json()['access_token']
        return True
    except Exception as e:
        logging.error(f"Autodesk Auth Failed: {e}")
        return False

def make_api_request_v2(method, url, **kwargs):
    global access_token
    if not access_token:
        if not get_autodesk_token(): raise Exception("Auth Failed")
    
    headers = kwargs.setdefault('headers', {})
    headers['Authorization'] = f"Bearer {access_token}"
    headers['X-user-id'] = AUTODESK_USER_EMAIL
    
    try:
        resp = requests.request(method, url, **kwargs)
        if resp.status_code == 401:
            logging.warning("401 Token Expired. Retrying...")
            if get_autodesk_token():
                headers['Authorization'] = f"Bearer {access_token}"
                resp = requests.request(method, url, **kwargs)
            else:
                raise Exception("Token Refresh Failed")
        resp.raise_for_status()
        return resp
    except Exception as e:
        logging.error(f"API Request Failed: {e}")
        raise

def download_pdf_logic(workspace_id, item_id, descriptor):
    url = f"{BASE_API_URL}/workspaces/{workspace_id}/items/{item_id}/files"
    try:
        resp = make_api_request_v2('GET', url)
        data = resp.json()
        
        files = data.get('elements', []) if isinstance(data, dict) else (data if isinstance(data, list) else [])
        if isinstance(data, dict) and 'id' in data: files = [data]

        target = None
        for f in files:
            if f.get('fileName', '').lower().endswith('.pdf'):
                target = f
                break
        
        if not target: return None

        fid = target.get('id')
        fname = target.get('fileName')
        dl_url = f"{BASE_API_URL}/workspaces/{workspace_id}/items/{item_id}/files/{fid}"
        
        safe_name = re.sub(r'[^a-zA-Z0-9_\-\.]', '_', fname)
        save_path = os.path.join(DOWNLOAD_DIR, safe_name)
        if not os.path.exists(DOWNLOAD_DIR): os.makedirs(DOWNLOAD_DIR)

        logging.info(f"Downloading PDF: {safe_name}...")
        with make_api_request_v2('GET', dl_url, headers={'Accept': 'application/octet-stream'}, stream=True) as r:
            with open(save_path, 'wb') as f:
                for chunk in r.iter_content(chunk_size=32768):
                    if chunk: f.write(chunk)
        return save_path
    except Exception as e:
        logging.error(f"Download Error: {e}")
        return None

# --- 4. MOODLE CONSTRUCTION ---

def call_moodle(func, params=None, files=None):
    if not params: params = {}
    payload = {'wstoken': MOODLE_TOKEN, 'wsfunction': func, 'moodlewsrestformat': 'json'}
    payload.update(params)
    r = requests.post(API_ENDPOINT, data=payload, files=files, timeout=300)
    r.raise_for_status()
    try:
        res = r.json()
        if isinstance(res, dict) and 'exception' in res:
             # Just pass it through for logic handling, or raise if critical
             pass
        return res
    except Exception:
        raise Exception(f"Moodle returned non-JSON: {r.text}")

def ensure_specific_course_exists(shortname: str, fullname: str, category_id: int):
    if shortname in COURSE_CACHE: return COURSE_CACHE[shortname]

    logging.info(f"Checking for course: {shortname}")
    all_courses = call_moodle('core_course_get_courses')
    if isinstance(all_courses, list):
        for c in all_courses:
            if c.get('shortname') == shortname:
                COURSE_CACHE[shortname] = c['id']
                logging.info(f"Found Existing Course: {c['id']}")
                return c['id']

    logging.info(f"Creating New Course: {shortname}")
    res = call_moodle('core_course_create_courses', {
        'courses[0][fullname]': fullname,
        'courses[0][shortname]': shortname,
        'courses[0][categoryid]': category_id,
        'courses[0][format]': 'topics'
    })

    if isinstance(res, dict) and 'exception' in res:
        if 'shortnametaken' in res.get('errorcode', '') or 'already used' in res.get('message', ''):
            logging.warning("Course exists (race condition). Retrying fetch...")
            retry_courses = call_moodle('core_course_get_courses')
            for c in retry_courses:
                if c.get('shortname') == shortname:
                    COURSE_CACHE[shortname] = c['id']
                    return c['id']
        raise Exception(f"Course Creation Failed: {res['message']}")

    new_id = res[0]['id']
    COURSE_CACHE[shortname] = new_id
    return new_id

def upload_file_to_moodle(file_path):
    """
    FIXED STRATEGY: 
    1. Use core_files_upload (Official API).
    2. Put ALL parameters in the 'data' (Body).
    3. FORCE all parameters to be STRINGS.
    4. Pass the file in 'files'.
    """
    logging.info(f"Uploading {os.path.basename(file_path)}...")
    
    # STRICT STRING TYPING FOR MULTIPART
    data = {
        'wstoken': MOODLE_TOKEN,
        'wsfunction': 'core_files_upload',
        'moodlewsrestformat': 'json',
        'component': 'user',
        'filearea': 'draft',
        'itemid': '0',   # MUST BE STRING '0'
        'filepath': '/',
        'filename': os.path.basename(file_path)
    }

    with open(file_path, 'rb') as f:
        files = {'file_1': f}
        
        # Sending data + files = multipart/form-data
        response = requests.post(API_ENDPOINT, data=data, files=files, timeout=300)
    
    try:
        res = response.json()
    except json.JSONDecodeError:
        raise Exception(f"Moodle Upload Crash: {response.text}")

    if isinstance(res, dict) and 'exception' in res:
        raise Exception(f"Moodle Upload Error: {res['message']} (Debug: {res.get('debuginfo', 'N/A')})")

    # Success = List of file objects
    if isinstance(res, list):
        item_id = res[0]['itemid']
        logging.info(f"Upload Success. Draft ID: {item_id}")
        return item_id
    
    raise Exception(f"Unexpected upload response: {res}")

def build_module_infrastructure(course_id, base_name, version, pdf_path=None):
    """Build quiz module infrastructure for the course"""
    quiz_params = {
        'modules[0][modulename]': 'quiz',
        'modules[0][section]': 1, 
        'modules[0][name]': f"TRAINING: {base_name}",
        'modules[0][intro]': "I acknowledge I have read and understood the documentation.", 
        'modules[0][introformat]': 1,
    }
    
    settings = [
        ('preferredbehaviour', 'deferredfeedback'), ('grade', '10'), ('grademethod', '1'),
        ('reviewattempt', '69904'), ('reviewcorrectness', '69904'), ('attempts', '0'),
        ('overduehandling', 'autosubmit')
    ]
    for i, (k, v) in enumerate(settings):
        quiz_params[f'modules[0][moduleinfo][{i}][name]'] = k
        quiz_params[f'modules[0][moduleinfo][{i}][value]'] = v

    call_moodle('core_course_create_modules', quiz_params)

# --- 5. MAIN LOGIC ---

def get_latest_clean_version(items: List[Dict]) -> Optional[Dict]:
    clean = [x for x in items if x.get('version') and re.match(r'^\.[A-Z]$', x['version'])]
    if not clean: return None
    clean.sort(key=lambda x: x['version'], reverse=True)
    return clean[0]

def extract_id(item):
    if item.get('id'): return item['id']
    match = re.search(r'\.(\d+)$', item.get('urn', ''))
    return match.group(1) if match else None

def main():
    conn = get_db_connection()
    try:
        for target in PLM_TARGETS:
            logging.info(f"--- BATCH: {target['description']} ---")
            rows = fetch_targets_from_db(conn, target['table_name'], target['db_filter'])
            
            grouped = {}
            for row in rows:
                desc = row.get('descriptor', 'Unknown')
                match = re.match(r'([A-Z]+-[A-Z]+-\d+)', desc)
                if match:
                    base = match.group(1)
                    if base not in grouped: grouped[base] = []
                    grouped[base].append(row)
            
            logging.info(f"Found {len(grouped)} unique documents.")

            for base_id, items in grouped.items():
                alpha = get_latest_clean_version(items)
                if not alpha: continue

                item_id = extract_id(alpha)
                if not item_id: continue

                logging.info(f"Processing {base_id} -> Version {alpha['version']}")

                # 1. Download
                pdf = download_pdf_logic(target['workspace_id'], item_id, base_id)
                if not pdf:
                    if STRICT_PDF_ONLY:
                        logging.warning(f"Skipping {base_id} (No PDF)")
                        continue
                    else:
                        logging.warning(f"Building {base_id} (No PDF)")

                try:
                    full_title = alpha.get('descriptor', base_id)
                    
                    course_id = ensure_specific_course_exists(
                        shortname=base_id,
                        fullname=f"Training: {full_title}",
                        category_id=target['category_id']
                    )

                    build_module_infrastructure(course_id, base_id, alpha['version'], pdf)
                    
                    logging.info(f"SUCCESS: {base_id} deployed.")
                    if pdf: os.remove(pdf)

                except Exception as e:
                    logging.error(f"FAILURE on {base_id}: {e}")

    except Exception as e:
        job_alerter.send_failure_alert(
            job_name="Master PLM-Moodle Builder",
            summary="Critical Failure",
            error=e,
            recipients=["aust_admin@example.com"]
        )
        raise
    finally:
        conn.close()
        logging.info("--- COMPLETE ---")

if __name__ == "__main__":
    main()
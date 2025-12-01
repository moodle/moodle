import os
import re
import logging
import json
import requests
import base64
import psycopg2
import psycopg2.extras
import time
from dotenv import load_dotenv
from typing import Dict, List, Optional

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
        'category_id': 1 
    }
]

# FLAGS
STRICT_PDF_ONLY = True 
DOWNLOAD_DIR = "temp_staging_area"

# CREDENTIALS
MOODLE_URL = os.getenv('MOODLE_URL')
MOODLE_TOKEN = os.getenv('MOODLE_TOKEN')

# API ENDPOINTS
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
USER_ID = None

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

# --- 4. MOODLE LOGIC ---

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

def call_moodle_json(func, params=None):
    """
    Sends request as Form-Encoded Data (Flattened).
    Moodle REST API typically requires this over JSON body.
    """
    if not params: params = {}
    
    # Auth in URL
    url_params = {
        'wstoken': MOODLE_TOKEN,
        'wsfunction': func,
        'moodlewsrestformat': 'json'
    }
    
    # Flatten params for Moodle's PHP-style array handling
    flat_params = flatten_params(params)
    
    # Send as Form Data
    r = requests.post(API_ENDPOINT, params=url_params, data=flat_params, timeout=300)
    
    if r.status_code != 200:
        logging.error(f"HTTP Error {r.status_code}: {r.text}")
        r.raise_for_status()
        
    try:
        res = r.json()
        return res
    except Exception:
        raise Exception(f"Moodle returned non-JSON: {r.text}")

def get_userid():
    global USER_ID
    if USER_ID: return USER_ID
    
    logging.info("Fetching User ID...")
    res = call_moodle_json('core_webservice_get_site_info')
    if isinstance(res, dict) and 'userid' in res:
        USER_ID = res['userid']
        return USER_ID
    raise Exception(f"Could not fetch User ID: {res}")

def upload_file_json_base64(file_path):
    """
    UPLOADS AS JSON + BASE64.
    This is the only way to satisfy Strict Integer Type Checking on 'itemid'.
    """
    logging.info(f"Uploading {os.path.basename(file_path)}...")
    
    base = os.path.basename(file_path)
    clean_name = re.sub(r'[^a-zA-Z0-9]', '', base.split('.')[0])
    clean_filename = f"{clean_name[-20:]}.pdf"

    # Encode file to Base64 String
    with open(file_path, "rb") as f:
        b64_content = base64.b64encode(f.read()).decode('utf-8')

    userid = get_userid()

    # Construct JSON Payload
    # Note: itemid is an INTEGER here (0), not a string ("0")
    payload = {
        'contextlevel': 'user',
        'instanceid': userid,
        'component': 'user',
        'filearea': 'draft',
        'itemid': 0, 
        'filepath': '/',
        'filename': clean_filename,
        'filecontent': b64_content
    }

    # Send using the JSON wrapper
    res = call_moodle_json('core_files_upload', payload)

    if isinstance(res, dict) and 'exception' in res:
        logging.error(f"UPLOAD FAIL DEBUG: {json.dumps(res)}")
        raise Exception(f"Moodle Upload Error: {res['message']}")

    if isinstance(res, list):
        item_id = res[0]['itemid']
        logging.info(f"Upload Success. Draft ID: {item_id}")
        return item_id, clean_filename
    elif isinstance(res, dict) and 'itemid' in res:
        return res['itemid'], clean_filename
    
    raise Exception(f"Unexpected upload response: {res}")

def ensure_specific_course_exists(shortname: str, fullname: str, category_id: int):
    if shortname in COURSE_CACHE: return COURSE_CACHE[shortname]

    logging.info(f"Checking for course: {shortname}")
    
    # JSON Search
    search_payload = {'field': 'shortname', 'value': shortname}
    search_res = call_moodle_json('core_course_get_courses_by_field', search_payload)
    
    if isinstance(search_res, dict) and 'courses' in search_res:
        search_res = search_res['courses']
        
    if isinstance(search_res, list) and len(search_res) > 0:
        existing = search_res[0]
        if existing.get('shortname') == shortname:
            logging.info(f"Found Existing Course: {existing['id']}")
            COURSE_CACHE[shortname] = existing['id']
            return existing['id']

    logging.info(f"Creating New Course: {shortname}")
    
    # JSON Create
    create_payload = {
        'courses': [{
            'fullname': fullname,
            'shortname': shortname,
            'categoryid': int(category_id),
            'format': 'topics'
        }]
    }
    
    res = call_moodle_json('core_course_create_courses', create_payload)

    if isinstance(res, dict) and 'exception' in res:
        if 'shortnametaken' in res.get('errorcode', '') or 'already used' in res.get('message', ''):
            logging.warning("Course exists (race condition). Retrying fetch...")
            search_res_retry = call_moodle_json('core_course_get_courses_by_field', search_payload)
            if isinstance(search_res_retry, dict) and 'courses' in search_res_retry: search_res_retry = search_res_retry['courses']
            
            if isinstance(search_res_retry, list) and len(search_res_retry) > 0:
                 existing = search_res_retry[0]
                 if existing.get('shortname') == shortname:
                     COURSE_CACHE[shortname] = existing['id']
                     return existing['id']
             
        logging.error(f"COURSE CREATE FATAL: {json.dumps(res)}")
        raise Exception(f"Course Creation Failed: {res['message']}")

    new_id = res[0]['id']
    COURSE_CACHE[shortname] = new_id
    return new_id

def build_module_infrastructure(course_id, base_name, version, pdf_path=None):
    """
    Creates Resource and Quiz using YOUR CUSTOM API SCHEMA + JSON.
    """

    # --- STEP 1: CREATE PDF RESOURCE ---
    if pdf_path:
        draft_id, clean_fname = upload_file_json_base64(pdf_path)
        
        # JSON Payload with Custom moduleinfo
        resource_payload = {
            'modules': [{
                'modulename': 'resource',
                'courseid': int(course_id), 
                'section': 1,
                'name': f"{base_name} - PDF Document",
                'intro': "Please review this document.",
                'introformat': 1,
                'visible': 1,
                'moduleinfo': [
                    {'name': 'module', 'value': '18'},  # Hardcoded ID from user
                    {'name': 'course', 'value': str(course_id)},
                    {'name': 'files', 'value': str(draft_id)},
                    {'name': 'showsize', 'value': '1'},
                    {'name': 'display', 'value': '0'},
                    {'name': 'printintro', 'value': '1'},
                    {'name': 'tobemigrated', 'value': '0'},
                    {'name': 'legacyfiles', 'value': '0'},
                    {'name': 'legacyfileslast', 'value': '0'},
                    {'name': 'filterfiles', 'value': '0'},
                    {'name': 'revision', 'value': '1'},
                    {'name': 'timemodified', 'value': str(int(time.time()))}
                ]
            }]
        }
        
        logging.info(f"Creating File Resource for {base_name}...")
        res = call_moodle_json('core_course_create_modules', resource_payload)
        
        if isinstance(res, dict) and 'exception' in res:
             logging.error(f"RESOURCE FAIL DEBUG: {json.dumps(res)}")
             raise Exception(f"Resource Creation Failed: {res['message']}")

    # --- STEP 2: CREATE QUIZ MODULE ---
    logging.info(f"Creating Quiz infrastructure for {base_name}...")
    
    # Custom PHP 'moduleinfo' structure
    quiz_payload = {
        'modules': [{
            'modulename': 'quiz',
            'courseid': int(course_id),
            'section': 1,
            'name': f"TRAINING: {base_name}",
            'intro': "I acknowledge I have read and understood the documentation. / Reconozco que he leído y comprendido la documentación.",
            'introformat': 1,
            'visible': 1,
            'moduleinfo': [
                {'name': 'module', 'value': '17'}, # Hardcoded ID from user
                {'name': 'course', 'value': str(course_id)},
                {'name': 'preferredbehaviour', 'value': 'deferredfeedback'},
                {'name': 'quizpassword', 'value': ''},
                {'name': 'grade', 'value': '10'},
                {'name': 'grademethod', 'value': '1'},
                {'name': 'attempts', 'value': '0'}, # Unlimited
                {'name': 'overduehandling', 'value': 'autosubmit'},
                {'name': 'browsersecurity', 'value': '-'},
                {'name': 'completion', 'value': '1'},
                {'name': 'questionsperpage', 'value': '1'},
                {'name': 'shuffleanswers', 'value': '1'}
            ]
        }]
    }

    res = call_moodle_json('core_course_create_modules', quiz_payload)
    if isinstance(res, dict) and 'exception' in res:
        logging.error(f"QUIZ FAIL DEBUG: {json.dumps(res)}")
        raise Exception(f"Quiz Creation Failed: {res['message']}")
    
    course_module_id = res[0]['id']
    logging.info(f"Quiz Course Module Created (cmid: {course_module_id}).")
    
    # Get the actual quiz instance ID from course contents
    # Retry with delay to handle caching/timing issues
    logging.info("Fetching quiz instance ID...")
    quiz_instance_id = None
    max_retries = 3
    
    for attempt in range(max_retries):
        if attempt > 0:
            logging.info(f"Retry {attempt}/{max_retries-1} after 2 second delay...")
            time.sleep(2)
        
        contents = call_moodle_json('core_course_get_contents', {'courseid': course_id})
        for section in contents:
            if 'modules' in section:
                for mod in section['modules']:
                    if mod.get('id') == course_module_id:
                        quiz_instance_id = mod.get('instance')
                        break
            if quiz_instance_id:
                break
        
        if quiz_instance_id:
            break
    
    if not quiz_instance_id:
        raise Exception(f"Could not find quiz instance ID for course_module {course_module_id}")
    
    logging.info(f"Quiz Instance ID: {quiz_instance_id}")

    # --- STEP 3: ADD QUESTION VIA LOCAL PLUGIN ---
    logging.info("Adding Bilingual True/False Question...")
    
    question_text = "Have you completed the training? / ¿Ha completado el entrenamiento?"
    
    q_payload = {
        'quizid': int(quiz_instance_id),
        'questionname': 'Training Completion / Completado',
        'questiontext': question_text,
        'correctanswer': 1
    }
    
    try:
        q_res = call_moodle_json('local_masterbuilder_create_question', q_payload)
        if isinstance(q_res, dict) and 'exception' in q_res:
             logging.error(f"QUESTION FAIL DEBUG: {json.dumps(q_res)}")
             # Don't fail the whole job, just log it
             logging.warning("Failed to create question, but Quiz exists.")
        else:
             logging.info(f"Question Created Successfully (ID: {q_res.get('questionid')})")
    except Exception as e:
        logging.error(f"Question Creation Error: {e}")

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
                    if pdf and os.path.exists(pdf): os.remove(pdf)

                except Exception as e:
                    logging.error(f"FAILURE on {base_id}: {e}")
                    # Continue

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
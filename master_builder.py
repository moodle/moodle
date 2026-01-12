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
# Exact titles to process from FactItems table
# Add the full title strings you want to process
EXACT_TITLES = [
    # Example titles - replace with your actual titles:
    "F20-MI-000713 - F20 Manufacturing Instruction - End Cut, Attach, & Finish - ",
    "ZDN-MI-000805 - ZDN Catheter Manufacturing Procedure - ",
]

PLM_CONFIG = {
    'workspace_id': '193',
    'category_id': 1
}

# FLAGS
STRICT_PDF_ONLY = True 
DOWNLOAD_DIR = "temp_staging_area"
NUKE_VERSION_TABLE = False # Set to True to wipe the DB state on run

# CREDENTIALS
MOODLE_URL = os.getenv('MOODLE_URL')
MOODLE_TOKEN = os.getenv('MOODLE_TOKEN')

# API ENDPOINTS
API_BASE = MOODLE_URL.rstrip('/') 
API_ENDPOINT = f"{API_BASE}/webservice/rest/server.php"

PG_HOST = os.getenv('PG_HOST')
PG_PORT = os.getenv('PG_PORT')
PG_DBNAME = os.getenv('PG_DBNAME_FUSION')
PG_USER = os.getenv('PG_USER')
PG_PASSWORD = os.getenv('PG_PASSWORD')

AUTODESK_CLIENT_ID = os.getenv('AUTODESK_CLIENT_ID')
AUTODESK_CLIENT_SECRET = os.getenv('AUTODESK_CLIENT_SECRET')
AUTODESK_HOST = os.getenv('AUTODESK_HOST')
AUTODESK_USER_EMAIL = os.getenv('AUTODESK_USER_EMAIL')

# Construct BASE_API_URL correctly
if AUTODESK_HOST:
    if not AUTODESK_HOST.startswith('http'):
        BASE_API_URL = f"https://{AUTODESK_HOST}/api/v2"
    else:
         BASE_API_URL = f"{AUTODESK_HOST}/api/v2"
else:
    BASE_API_URL = None

# GLOBALS
COURSE_CACHE = {}
USER_ID = None
AUTODESK_TOKEN = None
TOKEN_EXPIRY = 0

# --- 2. AUTODESK AUTH ---

def get_autodesk_token():
    global AUTODESK_TOKEN, TOKEN_EXPIRY
    if AUTODESK_TOKEN and time.time() < TOKEN_EXPIRY:
        return AUTODESK_TOKEN

    logging.info("Refreshing Autodesk Token...")
    url = "https://developer.api.autodesk.com/authentication/v2/token"
    payload = {
        'client_id': AUTODESK_CLIENT_ID,
        'client_secret': AUTODESK_CLIENT_SECRET,
        'grant_type': 'client_credentials',
        'scope': 'data:read'
    }
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    
    resp = requests.post(url, data=payload, headers=headers)
    if resp.status_code != 200:
        raise Exception(f"Auth Failed: {resp.text}")
    
    data = resp.json()
    AUTODESK_TOKEN = data['access_token']
    TOKEN_EXPIRY = time.time() + data['expires_in'] - 60
    return AUTODESK_TOKEN

def make_api_request_v2(method, url, **kwargs):
    token = get_autodesk_token()
    headers = kwargs.get('headers', {})
    headers['Authorization'] = f"Bearer {token}"
    if AUTODESK_USER_EMAIL:
        headers['X-user-id'] = AUTODESK_USER_EMAIL
    kwargs['headers'] = headers
    
    resp = requests.request(method, url, **kwargs)
    if resp.status_code == 401:
        logging.warning("Token expired. Retrying...")
        global AUTODESK_TOKEN
        AUTODESK_TOKEN = None
        token = get_autodesk_token()
        headers['Authorization'] = f"Bearer {token}"
        resp = requests.request(method, url, **kwargs)
        
    if resp.status_code not in [200, 201, 202]:
        logging.error(f"API Error {resp.status_code}: {resp.text}")
        # Don't raise immediately, let caller handle
    return resp

# --- 3. DATABASE ---

def get_db_connection():
    return psycopg2.connect(
        host=PG_HOST,
        port=PG_PORT,
        dbname=PG_DBNAME,
        user=PG_USER,
        password=PG_PASSWORD
    )

def ensure_version_table_exists(conn):
    """Creates the moodle_course_versions table if it doesn't exist."""
    query = """
        CREATE TABLE IF NOT EXISTS public.moodle_course_versions (
            id SERIAL PRIMARY KEY,
            item_title VARCHAR(500) NOT NULL,
            course_shortname VARCHAR(100) NOT NULL,
            course_type VARCHAR(20) NOT NULL,
            deployed_version VARCHAR(20),
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(item_title, course_type)
        );
    """
    with conn.cursor() as cur:
        cur.execute(query)
    conn.commit()
    logging.info("Version tracking table ensured.")

def get_deployed_version(conn, item_title: str, course_type: str) -> Optional[str]:
    """Gets the currently deployed version for an item/course type."""
    query = """
        SELECT deployed_version FROM public.moodle_course_versions
        WHERE item_title = %s AND course_type = %s
    """
    with conn.cursor() as cur:
        cur.execute(query, (item_title, course_type))
        row = cur.fetchone()
        return row[0] if row else None

def update_deployed_version(conn, item_title: str, course_shortname: str, 
                            course_type: str, version: str):
    """Updates or inserts the deployed version for an item."""
    query = """
        INSERT INTO public.moodle_course_versions 
            (item_title, course_shortname, course_type, deployed_version, last_updated)
        VALUES (%s, %s, %s, %s, CURRENT_TIMESTAMP)
        ON CONFLICT (item_title, course_type) 
        DO UPDATE SET 
            deployed_version = EXCLUDED.deployed_version,
            course_shortname = EXCLUDED.course_shortname,
            last_updated = CURRENT_TIMESTAMP
    """
    with conn.cursor() as cur:
        cur.execute(query, (item_title, course_shortname, course_type, version))
    conn.commit()

def fetch_items_by_exact_titles(conn, titles: List[str]):
    """
    Fetches items from FactItems table by exact title match.
    Excludes working versions (.w, w).
    """
    if not titles:
        return []
    
    query = """
        SELECT * FROM public."FactItems"
        WHERE title = ANY(%s)
          AND version NOT LIKE '%%.w'
          AND version_id != 'w'
    """
    with conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor) as cur:
        cur.execute(query, (titles,))
        return cur.fetchall()

def parse_version_number(version_str: str) -> tuple:
    """
    Parses version string for sorting.
    Examples: '.s1' -> ('s', 1), '.2' -> ('', 2), '.C' -> ('C', 0)
    """
    if not version_str:
        return ('', 0)
    
    # Remove leading dot
    v = version_str.lstrip('.')
    
    # Check for letter+number pattern (s1, s2) or just number (1, 2, 3)
    match = re.match(r'^([a-zA-Z]*)(\d*)$', v)
    if match:
        letter = match.group(1) or ''
        num = int(match.group(2)) if match.group(2) else 0
        return (letter, num)
    
    return (v, 0)

def get_latest_released_version(items: List[Dict]) -> Optional[Dict]:
    """
    Returns the item with the highest non-working version.
    Sorts by version to find the latest.
    """
    if not items:
        return None
    
    # Filter out working versions
    released = [x for x in items if x.get('version') and 
                not x['version'].endswith('w') and 
                x.get('version_id', '') != 'w']
    
    if not released:
        return None
    
    # Sort by parsed version (descending)
    released.sort(key=lambda x: parse_version_number(x.get('version', '')), reverse=True)
    return released[0]

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
    if shortname in COURSE_CACHE: return COURSE_CACHE[shortname], False

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
            return existing['id'], False

    logging.info(f"Creating New Course: {shortname}")
    
    # JSON Create
    create_payload = {
        'courses': [{
            'fullname': fullname,
            'shortname': shortname,
            'categoryid': int(category_id),
            'format': 'topics',
            'startdate': int(time.time() - 86400), # Start yesterday to appear in "In Progress"
            'enddate': 0,                            # Disable end date
            'visible': 1
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
                     return existing['id'], False
             
        logging.error(f"COURSE CREATE FATAL: {json.dumps(res)}")
        raise Exception(f"Course Creation Failed: {res['message']}")

    new_id = res[0]['id']
    COURSE_CACHE[shortname] = new_id
    return new_id, True

def check_quiz_exists(course_id):
    """
    Checks if a quiz module already exists in the course.
    """
    try:
        contents = call_moodle_json('core_course_get_contents', {'courseid': course_id})
        for section in contents:
            if 'modules' in section:
                for mod in section['modules']:
                    if mod.get('modname') == 'quiz':
                        return True
    except Exception as e:
        logging.error(f"Error checking for quiz: {e}")
    return False

def post_announcement(course_id, subject, message, message_format=1):
    """
    Posts an announcement to the course 'News' forum.
    """
    try:
        # 1. Find the News forum
        forums_res = call_moodle_json('mod_forum_get_forums_by_courses', {'courseids': [course_id]})
        news_forum_id = None
        
        # Log what we found to help debug
        if isinstance(forums_res, list):
            logging.info(f"Forums found in course {course_id}: {[f.get('name', 'Unknown') + ' (' + f.get('type', '?') + ')' for f in forums_res]}")
            
            for forum in forums_res:
                # Check for type 'news' OR name 'Announcements'/'Avisos'
                if forum.get('type') == 'news' or forum.get('name') in ['Announcements', 'Avisos', 'News']:
                    news_forum_id = forum.get('id')
                    break
        
        if not news_forum_id:
            logging.warning(f"No News forum found for course {course_id}. Creating one...")
            # Attempt to create one if missing
            try:
                forum_payload = {
                    'modules': [{
                        'modulename': 'forum',
                        'courseid': int(course_id),
                        'section': 0,
                        'name': 'Announcements',
                        'intro': 'General news and announcements',
                        'introformat': 1,
                        'visible': 1,
                        'moduleinfo': [
                            {'name': 'type', 'value': 'news'},
                            {'name': 'forcesubscribe', 'value': '1'},
                            {'name': 'trackingtype', 'value': '1'}
                        ]
                    }]
                }
                res = call_moodle_json('core_course_create_modules', forum_payload)
                if isinstance(res, list) and len(res) > 0:
                     # We need the instance ID, not the CMID
                     cmid = res[0]['id']
                     # Fetch instance id
                     time.sleep(1)
                     contents = call_moodle_json('core_course_get_contents', {'courseid': course_id})
                     for section in contents:
                        for mod in section.get('modules', []):
                            if mod.get('id') == cmid:
                                news_forum_id = mod.get('instance')
                                logging.info(f"Created new Announcements forum: {news_forum_id}")
                                break
                        if news_forum_id: break
            except Exception as e:
                logging.error(f"Failed to create forum: {e}")

        if not news_forum_id:
            logging.warning("Still no forum found. Aborting announcement.")
            return

        # 2. Post discussion
        payload = {
            'forumid': news_forum_id,
            'subject': subject,
            'message': message
        }
        
        res = call_moodle_json('mod_forum_add_discussion', payload)
        if isinstance(res, dict) and 'discussionid' in res:
            logging.info(f"Announcement posted: {subject}")
        else:
            logging.error(f"Failed to post announcement: {res}")

    except Exception as e:
        logging.error(f"Announcement Error: {e}")

def build_pdf_course(course_id, base_name, pdf_path):
    """
    Creates a course with only the PDF resource (no quiz).
    """
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
    
    logging.info(f"Creating PDF Resource for {base_name}...")
    res = call_moodle_json('core_course_create_modules', resource_payload)
    
    if isinstance(res, dict) and 'exception' in res:
         logging.error(f"RESOURCE FAIL DEBUG: {json.dumps(res)}")
         raise Exception(f"Resource Creation Failed: {res['message']}")
    
    logging.info(f"PDF Resource created successfully for course {course_id}")

def build_quiz_course(course_id, base_name):
    """
    Creates a course with only the quiz (no PDF).
    """
    logging.info(f"Creating Quiz infrastructure for {base_name}...")
    
    # Custom PHP 'moduleinfo' structure
    quiz_payload = {
        'modules': [{
            'modulename': 'quiz',
            'courseid': int(course_id),
            'section': 1,
            'name': f"COMPETENCY: {base_name}",
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

    # --- ADD QUESTION VIA LOCAL PLUGIN ---
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

def extract_id(item):
    """Extract item ID from FactItems row (using dms_id)."""
    if item.get('dms_id'):
        return item['dms_id']
    # Fallback to URN parsing
    match = re.search(r'\.(\d+)$', item.get('item_urn', ''))
    return match.group(1) if match else None

def main():
    conn = get_db_connection()
    try:
        # Ensure version tracking table exists
        ensure_version_table_exists(conn)
        
        if not EXACT_TITLES:
            logging.warning("No exact titles configured in EXACT_TITLES. Nothing to process.")
            return
        
        logging.info(f"--- Fetching items for {len(EXACT_TITLES)} configured titles ---")
        all_items = fetch_items_by_exact_titles(conn, EXACT_TITLES)
        logging.info(f"Found {len(all_items)} total item records (all versions).")
        
        # Group by title to find latest version per title
        grouped = {}
        for item in all_items:
            title = item.get('title')
            if title not in grouped:
                grouped[title] = []
            grouped[title].append(item)
        
        logging.info(f"Processing {len(grouped)} unique documents.")

        for title, items in grouped.items():
            latest = get_latest_released_version(items)
            if not latest:
                logging.warning(f"No released version for: {title}")
                continue
            
            item_id = extract_id(latest)
            current_version = latest.get('version', '')
            
            if not item_id:
                logging.warning(f"Skipping {title} - no item ID")
                continue

            # Extract base ID for shortname (e.g., "F20-MI-000713")
            match = re.match(r'([A-Z0-9]+-[A-Z]+-\d+)', title)
            base_id = match.group(1) if match else title[:20].replace(' ', '_').replace('-', '_')
            
            logging.info(f"Processing: {title} (Version: {current_version})")

            # ========================================
            # --- PDF Course (Read and Understand) ---
            # ========================================
            pdf_shortname = f"{base_id}-RU"
            pdf_fullname = f"{title} Read and Understand"
            
            deployed_pdf_version = get_deployed_version(conn, title, 'pdf')
            
            if deployed_pdf_version != current_version:
                logging.info(f"PDF update needed: {base_id} ({deployed_pdf_version} -> {current_version})")
                
                pdf_path = download_pdf_logic(PLM_CONFIG['workspace_id'], item_id, base_id)
                if pdf_path:
                    try:
                        course_id, is_new = ensure_specific_course_exists(
                            shortname=pdf_shortname,
                            fullname=pdf_fullname,
                            category_id=PLM_CONFIG['category_id']
                        )
                        build_pdf_course(course_id, title, pdf_path)
                        update_deployed_version(conn, title, pdf_shortname, 'pdf', current_version)
                        
                        # Reset course progress if it's an update (not a new course)
                        if not is_new:
                            logging.info(f"Resetting course progress for {pdf_shortname}...")
                            try:
                                reset_res = call_moodle_json('local_masterbuilder_reset_course_progress', {'courseid': course_id})
                                logging.info(f"Reset result: {reset_res}")
                            except Exception as e:
                                logging.error(f"Failed to reset course progress: {e}")
                        
                        # Post announcement
                        subject = "Document Updated / Documento Actualizado"
                        message = f"A new version ({current_version}) of '{base_id}' is available. Please review it. / Una nueva versión ({current_version}) de '{base_id}' está disponible. Por favor revíselo."
                        post_announcement(course_id, subject, message)
                        
                        logging.info(f"PDF course updated: {pdf_shortname}")
                    except Exception as e:
                        logging.error(f"Error creating PDF course for {title}: {e}")
                    finally:
                        if os.path.exists(pdf_path):
                            os.remove(pdf_path)
                else:
                    if STRICT_PDF_ONLY:
                        logging.warning(f"Skipping PDF course for {base_id} (No PDF available)")
            else:
                logging.info(f"PDF up to date: {pdf_shortname} (version {current_version})")

            # ========================================
            # --- Quiz Course (Competency) ---
            # ========================================
            quiz_shortname = f"{base_id}-CMP"
            quiz_fullname = f"{title} Competency"
            
            try:
                course_id, is_new = ensure_specific_course_exists(
                    shortname=quiz_shortname,
                    fullname=quiz_fullname,
                    category_id=PLM_CONFIG['category_id']
                )
                
                if not check_quiz_exists(course_id):
                    build_quiz_course(course_id, title)
                    update_deployed_version(conn, title, quiz_shortname, 'quiz', current_version)
                    
                    # Post announcement for new quiz
                    subject = "Competency Assessment Available / Evaluación de Competencia Disponible"
                    message = f"Complete this assessment to demonstrate competency for {base_id}. / Complete esta evaluación para demostrar competencia en {base_id}."
                    post_announcement(course_id, subject, message)
                    
                    logging.info(f"Quiz course created: {quiz_shortname}")
                else:
                    logging.info(f"Quiz already exists: {quiz_shortname}")
                    
            except Exception as e:
                logging.error(f"Error creating Quiz course for {title}: {e}")

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
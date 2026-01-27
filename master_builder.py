import os
import re
import logging
import json
import requests
import base64
import psycopg2
import psycopg2.extras
import time
import csv
from dotenv import load_dotenv
from typing import Dict, List, Optional
from difflib import SequenceMatcher

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
# Moodle Categories - ID to Name mapping for fuzzy matching
MOODLE_CATEGORIES = {
    10: "All Company",
    11: "All Company Compliance",
    12: "SOP Training",
    13: "Human Resources",
    29: "AGI",
    19: "BLN",
    35: "F20",
    1: "Category 1",
    30: "F20",
    24: "BLN",
    25: "CTI",
    44: "SPX",
    15: "Technical",
    31: "Sub Assembly",
    26: "BMD",
    20: "CTI",
    21: "BMD",
    14: "Engineering",
    2: "Production",
    7: "New Hire Orientation",
    8: "Direct Labor",
    9: "Indirect Labor",
    3: "FPC-AuST",
    4: "FPC-CP",
    5: "Train the Trainer",
    6: "Production Leadership",
    36: "CTI",
    16: "SSPC-AuST",
    17: "SSPC-CP",
    22: "MP3",
    38: "FPC",
    23: "SMG",
    32: "SMG",
    18: "SMG",
    27: "MP3",
    41: "Dragonfly",
    37: "BMD",
    33: "BLN",
    28: "Agile-AuST",
    46: "FST",
    34: "AGI",
    39: "APV",
    40: "Low Volume",
    43: "NVS",
    42: "VPU",
    45: "BFC",
    47: "ACF",
    48: "AUD",
}

# Reverse lookup: name to IDs (multiple IDs may have same name)
CATEGORY_NAME_TO_IDS = {}
for cat_id, cat_name in MOODLE_CATEGORIES.items():
    name_lower = cat_name.lower()
    if name_lower not in CATEGORY_NAME_TO_IDS:
        CATEGORY_NAME_TO_IDS[name_lower] = []
    CATEGORY_NAME_TO_IDS[name_lower].append(cat_id)

PLM_CONFIG = {
    'workspace_id': '193',
    'category_id': 15  # Default to Technical (ID 15) as fallback
}

# Path to the Document List CSV
DOCUMENT_LIST_CSV = os.path.join(os.path.dirname(__file__), "Document List.csv")

# FLAGS
STRICT_PDF_ONLY = True 
DOWNLOAD_DIR = "temp_staging_area"
NUKE_VERSION_TABLE = False # Set to True to wipe the DB state on run

def load_document_list() -> List[Dict]:
    """
    Loads the Document List CSV and returns a list of document configurations.
    """
    documents = []
    if not os.path.exists(DOCUMENT_LIST_CSV):
        logging.error(f"Document List CSV not found: {DOCUMENT_LIST_CSV}")
        return documents
    
    with open(DOCUMENT_LIST_CSV, 'r', encoding='utf-8-sig') as f:
        reader = csv.DictReader(f)
        for row in reader:
            # Normalize keys by stripping whitespace (handles "2 Courses " -> "2 Courses")
            normalized_row = {k.strip(): v for k, v in row.items()}
            
            # Skip empty rows
            if not normalized_row.get('Document', '').strip():
                continue
            
            # Clean up the document ID (remove tabs and whitespace)
            doc_id = normalized_row.get('Document', '').strip().replace('\t', '')
            
            # Get the "2 Courses" value (may have trailing space in original header)
            two_courses_value = normalized_row.get('2 Courses', '').strip().lower()
            
            doc_config = {
                'highest_category': normalized_row.get('Highest category', '').strip(),
                'subcategory_1': normalized_row.get('subcategory 1', '').strip(),
                'subcategory_2': normalized_row.get('subcategroy 2', '').strip(),  # Note: typo in CSV header
                'document_id': doc_id,
                'full_name': normalized_row.get('Full name', '').strip(),
                'course_name': normalized_row.get('Course Name', '').strip(),
                'create_quiz': two_courses_value == 'yes'
            }
            documents.append(doc_config)
    
    logging.info(f"Loaded {len(documents)} documents from CSV")
    return documents

def find_best_category_match(category_name: str) -> Optional[int]:
    """
    Finds the best matching Moodle category ID for a given category name.
    Uses case-insensitive fuzzy matching.
    """
    if not category_name:
        return None
    
    search_name = category_name.lower().strip()
    
    # Exact match first
    if search_name in CATEGORY_NAME_TO_IDS:
        return CATEGORY_NAME_TO_IDS[search_name][0]
    
    # Fuzzy match - find best similarity ratio
    best_match = None
    best_ratio = 0.0
    
    for cat_name_lower, cat_ids in CATEGORY_NAME_TO_IDS.items():
        # Check substring match
        if search_name in cat_name_lower or cat_name_lower in search_name:
            # Substring match found - prefer this
            ratio = 0.9 if len(search_name) > len(cat_name_lower) else 0.95
            if ratio > best_ratio:
                best_ratio = ratio
                best_match = cat_ids[0]
        else:
            # Use sequence matcher for similarity
            ratio = SequenceMatcher(None, search_name, cat_name_lower).ratio()
            if ratio > best_ratio and ratio > 0.6:  # Minimum 60% match
                best_ratio = ratio
                best_match = cat_ids[0]
    
    if best_match:
        logging.debug(f"Category match: '{category_name}' -> ID {best_match} (ratio: {best_ratio:.2f})")
    else:
        logging.warning(f"No category match found for: '{category_name}'")
    
    return best_match

def get_or_create_category(name: str, parent_id: int = 0) -> Optional[int]:
    """
    Gets or creates a Moodle category by name under the specified parent.
    Returns the category ID.
    """
    if not name:
        return None
    
    # First, try to find existing category
    try:
        categories = call_moodle_json('core_course_get_categories', {
            'criteria': [{'key': 'name', 'value': name}]
        })
        
        if isinstance(categories, list):
            for cat in categories:
                if cat.get('name', '').lower() == name.lower():
                    # Check parent matches if specified
                    if parent_id == 0 or cat.get('parent', 0) == parent_id:
                        logging.debug(f"Found existing category: {name} (ID: {cat['id']})")
                        return cat['id']
        
        # Find by fuzzy match in existing categories
        matched_id = find_best_category_match(name)
        if matched_id:
            return matched_id
        
        # Category not found, create it
        logging.info(f"Creating category: {name} (parent: {parent_id})")
        result = call_moodle_json('core_course_create_categories', {
            'categories': [{
                'name': name,
                'parent': parent_id,
                'description': f'Auto-created by Master Builder',
                'descriptionformat': 1
            }]
        })
        
        if isinstance(result, list) and len(result) > 0:
            new_id = result[0].get('id')
            logging.info(f"Created category: {name} (ID: {new_id})")
            return new_id
        elif isinstance(result, dict) and 'exception' in result:
            logging.error(f"Failed to create category '{name}': {result.get('message')}")
            
    except Exception as e:
        logging.error(f"Error getting/creating category '{name}': {e}")
    
    return None

def ensure_category_hierarchy(highest: str, sub1: str, sub2: str) -> int:
    """
    Ensures the full category hierarchy exists and returns the leaf category ID.
    Creates categories as needed: highest -> sub1 -> sub2
    """
    # Start with default Technical category
    final_category_id = PLM_CONFIG['category_id']
    
    # Try to find/create highest level category
    if highest:
        top_id = get_or_create_category(highest, 0)
        if top_id:
            final_category_id = top_id
            
            # Try sub1 under top
            if sub1:
                sub1_id = get_or_create_category(sub1, top_id)
                if sub1_id:
                    final_category_id = sub1_id
                    
                    # Try sub2 under sub1
                    if sub2:
                        sub2_id = get_or_create_category(sub2, sub1_id)
                        if sub2_id:
                            final_category_id = sub2_id
    
    return final_category_id


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

def fetch_items_by_document_prefix(conn, document_id: str, workspace_id: str = '193'):
    """
    Fetches items from FactItems table where title starts with the document ID.
    This matches on the first part before the ' - ' in the title.
    Excludes working versions (.w, w).
    
    Args:
        document_id: The document ID prefix to match (e.g., 'DFE-MI-001249')
        workspace_id: The Fusion workspace ID to filter by
    
    Returns:
        List of matching items
    """
    if not document_id:
        return []
    
    # Use LIKE to match titles starting with the document ID
    search_pattern = f"{document_id}%"
    
    query = """
        SELECT * FROM public."FactItems"
        WHERE title LIKE %s
          AND workspace_id = %s
          AND version NOT LIKE '%%.w'
          AND version_id != 'w'
    """
    with conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor) as cur:
        cur.execute(query, (search_pattern, workspace_id))
        return cur.fetchall()

def is_superseded_version(version_str: str) -> bool:
    """
    Checks if a version is superseded (starts with 's' after the dot).
    Examples: '.sA', '.s1', '.sC' are superseded.
    '.A', '.B', '.F' are NOT superseded (current releases).
    """
    if not version_str:
        return False
    v = version_str.lstrip('.')
    return v.lower().startswith('s')

def parse_version_for_sorting(version_str: str) -> tuple:
    """
    Parses version string for sorting released (non-superseded) versions.
    
    Examples:
    - '.A' -> (0, 'A')  (first letter = A)
    - '.B' -> (0, 'B')  
    - '.F' -> (0, 'F')  (latest)
    - '.1' -> (1, '')   (numeric)
    - '.10' -> (10, '') 
    
    We sort by: (numeric, letter) so '.F' > '.B' > '.A'
    """
    if not version_str:
        return (-999, '')
    
    v = version_str.lstrip('.')
    
    # Pure letter version (A, B, C, etc.)
    if len(v) == 1 and v.isalpha():
        return (0, v.upper())
    
    # Numeric version (1, 2, 10, etc.)
    if v.isdigit():
        return (int(v), '')
    
    # Letter + number combo (unlikely for releases but handle it)
    match = re.match(r'^([a-zA-Z]+)(\d*)$', v)
    if match:
        letter = match.group(1).upper()
        num = int(match.group(2)) if match.group(2) else 0
        return (num, letter)
    
    return (-999, v)

def get_latest_released_version(items: List[Dict]) -> Optional[Dict]:
    """
    Returns the item with the highest released (non-superseded) version.
    
    Version logic:
    - Superseded versions start with 's' (e.g., .sA, .sB, .s1) - EXCLUDE these
    - Working versions end with 'w' - EXCLUDE these
    - Released versions are single letters (.A, .B, .F) or numbers (.1, .2)
    - Sort alphabetically for letters: .F > .E > .D > .C > .B > .A
    """
    if not items:
        return None
    
    # Filter out working versions and superseded versions
    released = [
        x for x in items 
        if x.get('version') and 
           not x['version'].endswith('w') and 
           x.get('version_id', '') != 'w' and
           not is_superseded_version(x.get('version', ''))
    ]
    
    if not released:
        logging.warning("No released (non-superseded) versions found.")
        return None
    
    # Log available versions for debugging
    versions = [x.get('version', '') for x in released]
    logging.info(f"Available released versions: {versions}")
    
    # Sort by parsed version (descending)
    released.sort(key=lambda x: parse_version_for_sorting(x.get('version', '')), reverse=True)
    
    latest = released[0]
    logging.info(f"Selected latest version: {latest.get('version', '')}")
    return latest

def reset_quiz_grades(course_id: int):
    """
    Resets all quiz attempts and grades for all users in a course.
    This is called when a new PDF version is detected to force re-training.
    """
    try:
        # Get all quizzes in the course
        contents = call_moodle_json('core_course_get_contents', {'courseid': course_id})
        quiz_ids = []
        
        for section in contents:
            for mod in section.get('modules', []):
                if mod.get('modname') == 'quiz':
                    quiz_ids.append(mod.get('instance'))
        
        if not quiz_ids:
            logging.info(f"No quizzes found in course {course_id}")
            return
        
        # Use local plugin to reset quiz attempts if available
        for quiz_id in quiz_ids:
            try:
                res = call_moodle_json('local_masterbuilder_reset_quiz_attempts', {
                    'quizid': quiz_id
                })
                logging.info(f"Reset quiz {quiz_id} attempts: {res}")
            except Exception as e:
                logging.warning(f"Could not reset quiz {quiz_id}: {e}")
                
    except Exception as e:
        logging.error(f"Error resetting quiz grades for course {course_id}: {e}")

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

    new_id = res[0]['id']
    COURSE_CACHE[shortname] = new_id
    return new_id, True

def get_quiz_id(course_id):
    """
    Checks if a quiz module already exists in the course.
    Returns the quiz instance ID if found, else None.
    """
    try:
        contents = call_moodle_json('core_course_get_contents', {'courseid': course_id})
        for section in contents:
            if 'modules' in section:
                for mod in section['modules']:
                    if mod.get('modname') == 'quiz':
                        return mod.get('instance')
    except Exception as e:
        logging.error(f"Error checking for quiz: {e}")
    return None

def configure_pdf_completion(course_id):
    """
    Configures completion for PDF course (Activity only, no grade).
    """
    logging.info(f"Enforcing completion settings for PDF course {course_id}...")
    try:
        completion_res = call_moodle_json('local_masterbuilder_configure_course_completion', {
            'courseid': int(course_id),
            'requiregrade': 0,
            'requireactivity': 1
        })
        if isinstance(completion_res, dict) and completion_res.get('success'):
            logging.info(f"Completion configured: {completion_res.get('message')}")
        else:
            logging.warning(f"Completion config response: {completion_res}")
    except Exception as e:
        logging.error(f"Failed to enforce PDF completion: {e}")

def configure_quiz_full(course_id, quiz_id):
    """
    Configures Quiz settings and Course Completion.
    """
    logging.info(f"Enforcing settings for Quiz {quiz_id} in course {course_id}...")
    
    # 1. Quiz Settings
    try:
        quiz_conf_res = call_moodle_json('local_masterbuilder_configure_quiz_settings', {
            'quizid': int(quiz_id),
            'gradetopass': 100.0
        })
        if isinstance(quiz_conf_res, dict) and quiz_conf_res.get('success'):
            logging.info(f"Quiz settings updated: {quiz_conf_res.get('message')}")
        else:
            logging.warning(f"Quiz config failure: {quiz_conf_res}")
    except Exception as e:
        logging.error(f"Failed to configure quiz settings: {e}")

    # 2. Course Completion
    try:
        completion_res = call_moodle_json('local_masterbuilder_configure_course_completion', {
            'courseid': int(course_id),
            'requiregrade': 0, # Require passing grade (which is enforced by quiz settings)
            'requireactivity': 1
        })
        if isinstance(completion_res, dict) and completion_res.get('success'):
            logging.info(f"Course completion configured: {completion_res.get('message')}")
        else:
            logging.warning(f"Completion config failure: {completion_res}")
    except Exception as e:
        logging.error(f"Failed to configure course completion: {e}")

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
    Configures course completion to require activity completion + 100% grade.
    """
    draft_id, clean_fname = upload_file_json_base64(pdf_path)
    
    # JSON Payload with Custom moduleinfo
    # completion=1 means students must manually mark the activity as complete
    resource_payload = {
        'modules': [{
            'modulename': 'resource',
            'courseid': int(course_id), 
            'section': 1,
            'name': f"{base_name} - PDF Document",
            'intro': "Please review this document and mark it as done when complete. / Por favor revise este documento y márquelo como completado.",
            'introformat': 1,
            'visible': 1,
            'completion': 1,  # Students must manually mark as complete
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
                {'name': 'timemodified', 'value': str(int(time.time()))},
                {'name': 'completion', 'value': '1'}  # Manual completion
            ]
        }]
    }
    
    logging.info(f"Creating PDF Resource for {base_name}...")
    res = call_moodle_json('core_course_create_modules', resource_payload)
    
    if isinstance(res, dict) and 'exception' in res:
         logging.error(f"RESOURCE FAIL DEBUG: {json.dumps(res)}")
         raise Exception(f"Resource Creation Failed: {res['message']}")
    
    logging.info(f"PDF Resource created successfully for course {course_id}")
    
    # Configure course completion settings.
    configure_pdf_completion(course_id)

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
                {'name': 'completion', 'value': '2'}, # Auto completion
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

    except Exception as e:
        logging.error(f"Question Creation Error: {e}")

    # --- CONFIGURE SETTINGS ---
    configure_quiz_full(course_id, quiz_instance_id)
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
    failed_documents = []  # Track documents that couldn't be found
    
    try:
        # Ensure version tracking table exists
        ensure_version_table_exists(conn)
        
        # Load documents from CSV
        document_list = load_document_list()
        
        if not document_list:
            logging.warning("No documents found in Document List CSV. Nothing to process.")
            return
        
        logging.info(f"--- Processing {len(document_list)} documents from CSV ---")
        
        for doc_config in document_list:
            document_id = doc_config['document_id']
            course_name = doc_config['course_name']
            full_name = doc_config['full_name']
            create_quiz = doc_config['create_quiz']
            
            logging.info(f"Looking for document: {document_id}")
            
            # Fetch items matching the document ID prefix
            items = fetch_items_by_document_prefix(conn, document_id, PLM_CONFIG['workspace_id'])
            
            if not items:
                logging.warning(f"FAILED: No items found for document ID: {document_id}")
                failed_documents.append({
                    'document_id': document_id,
                    'course_name': course_name,
                    'reason': 'No items found in Fusion workspace 193'
                })
                continue
            
            # Get latest released version
            latest = get_latest_released_version(items)
            if not latest:
                logging.warning(f"FAILED: No released version for: {document_id}")
                failed_documents.append({
                    'document_id': document_id,
                    'course_name': course_name,
                    'reason': 'No released (non-superseded) version found'
                })
                continue
            
            item_id = extract_id(latest)
            current_version = latest.get('version', '')
            title = latest.get('title', document_id)
            
            if not item_id:
                logging.warning(f"FAILED: No item ID for: {document_id}")
                failed_documents.append({
                    'document_id': document_id,
                    'course_name': course_name,
                    'reason': 'Could not extract item ID'
                })
                continue
            
            # Get or create the category hierarchy
            category_id = ensure_category_hierarchy(
                doc_config['highest_category'],
                doc_config['subcategory_1'],
                doc_config['subcategory_2']
            )
            
            logging.info(f"Processing: {document_id} -> {course_name} (Version: {current_version}, Category: {category_id})")

            # ========================================
            # --- PDF Course (Read and Understand) ---
            # ========================================
            pdf_shortname = f"{course_name}-RU"
            pdf_fullname = f"{course_name} Read and Understand"
            
            deployed_pdf_version = get_deployed_version(conn, document_id, 'pdf')
            
            if deployed_pdf_version != current_version:
                logging.info(f"PDF update needed: {course_name} ({deployed_pdf_version} -> {current_version})")
                
                pdf_path = download_pdf_logic(PLM_CONFIG['workspace_id'], item_id, course_name)
                if pdf_path:
                    try:
                        course_id, is_new = ensure_specific_course_exists(
                            shortname=pdf_shortname,
                            fullname=pdf_fullname,
                            category_id=category_id
                        )
                        build_pdf_course(course_id, course_name, pdf_path)
                        update_deployed_version(conn, document_id, pdf_shortname, 'pdf', current_version)
                        
                        # Reset course progress if it's an update (not a new course)
                        if not is_new:
                            logging.info(f"Resetting course progress for {pdf_shortname}...")
                            try:
                                reset_res = call_moodle_json('local_masterbuilder_reset_course_progress', {'courseid': course_id})
                                logging.info(f"Reset result: {reset_res}")
                            except Exception as e:
                                logging.error(f"Failed to reset course progress: {e}")
                            
                            # Also reset quiz grades for the related competency course
                            if create_quiz:
                                cmp_shortname = f"{course_name}-CMP"
                                logging.info(f"Resetting quiz grades for competency course {cmp_shortname}...")
                                try:
                                    # Find the quiz course
                                    search_res = call_moodle_json('core_course_get_courses_by_field', 
                                        {'field': 'shortname', 'value': cmp_shortname})
                                    if isinstance(search_res, dict) and 'courses' in search_res:
                                        search_res = search_res['courses']
                                    if isinstance(search_res, list) and len(search_res) > 0:
                                        quiz_course_id = search_res[0]['id']
                                        reset_quiz_grades(quiz_course_id)
                                        logging.info(f"Quiz grades reset for course {quiz_course_id}")
                                except Exception as e:
                                    logging.error(f"Failed to reset quiz grades: {e}")
                        
                        # Post announcement
                        subject = "Document Updated / Documento Actualizado"
                        message = f"A new version ({current_version}) of '{course_name}' is available. Please review it and retake the competency assessment. / Una nueva versión ({current_version}) de '{course_name}' está disponible. Por favor revíselo y vuelva a realizar la evaluación de competencia."
                        post_announcement(course_id, subject, message)
                        
                        logging.info(f"PDF course updated: {pdf_shortname}")
                    except Exception as e:
                        logging.error(f"Error creating PDF course for {document_id}: {e}")
                    finally:
                        if os.path.exists(pdf_path):
                            os.remove(pdf_path)
                else:
                    if STRICT_PDF_ONLY:
                        logging.warning(f"Skipping PDF course for {course_name} (No PDF available)")
            else:
                logging.info(f"PDF up to date: {pdf_shortname} (version {current_version})")
                # Enforce completion settings even if up to date
                search_res = call_moodle_json('core_course_get_courses_by_field', {'field': 'shortname', 'value': pdf_shortname})
                if isinstance(search_res, dict) and 'courses' in search_res and search_res['courses']:
                    pdf_cid = search_res['courses'][0]['id']
                    configure_pdf_completion(pdf_cid)

            # ========================================
            # --- Quiz Course (Competency) ---
            # ========================================
            if create_quiz:
                quiz_shortname = f"{course_name}-CMP"
                quiz_fullname = f"{course_name} Competency"
                
                try:
                    course_id, is_new = ensure_specific_course_exists(
                        shortname=quiz_shortname,
                        fullname=quiz_fullname,
                        category_id=category_id
                    )
                    
                    quiz_instance_id = get_quiz_id(course_id)
                    if not quiz_instance_id:
                        build_quiz_course(course_id, course_name)
                        update_deployed_version(conn, document_id, quiz_shortname, 'quiz', current_version)
                        
                        # Post announcement for new quiz
                        subject = "Competency Assessment Available / Evaluación de Competencia Disponible"
                        message = f"Complete this assessment to demonstrate competency for {course_name}. / Complete esta evaluación para demostrar competencia en {course_name}."
                        post_announcement(course_id, subject, message)
                        
                        logging.info(f"Quiz course created: {quiz_shortname}")
                    else:
                        logging.info(f"Quiz already exists: {quiz_shortname} (ID: {quiz_instance_id})")
                        # Enforce configuration
                        configure_quiz_full(course_id, quiz_instance_id)
                        
                except Exception as e:
                    logging.error(f"Error creating Quiz course for {document_id}: {e}")
            else:
                logging.info(f"Skipping quiz creation for {course_name} (2 Courses = No)")
        
        # Report failed documents
        if failed_documents:
            logging.error("=" * 60)
            logging.error(f"FAILED DOCUMENTS SUMMARY: {len(failed_documents)} documents could not be processed")
            logging.error("=" * 60)
            for failed in failed_documents:
                logging.error(f"  - {failed['document_id']} ({failed['course_name']}): {failed['reason']}")
            logging.error("=" * 60)
        else:
            logging.info("All documents processed successfully!")

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
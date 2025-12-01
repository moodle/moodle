import os
import msal
import requests
import json
import traceback
import base64
from dotenv import load_dotenv
from typing import List, Optional

# --- 1. INITIALIZATION & CONFIGURATION ---
# Load variables from .env file AT THE START
load_dotenv()

# Load credentials from environment
TENANT_ID = os.environ.get("TENANT_ID")
CLIENT_ID = os.environ.get("CLIENT_ID")
CLIENT_SECRET = os.environ.get("CLIENT_SECRET")

# Module-level constants
AUTHORITY = f"https://login.microsoftonline.com/{TENANT_ID}"
GRAPH_ENDPOINT = "https://graph.microsoft.com/v1.0"
SCOPE = ["https://graph.microsoft.com/.default"]
SENDER_EMAIL = "alerting@aust-mfg.com"

# Robust path to the logo file, relative to THIS module
# This ensures it's found regardless of where the job script is run from
try:
    LOGO_PATH = os.path.join(os.path.dirname(__file__), "AuST-Group_Logo_MAIN.png")
except NameError:
    # Fallback for interactive/scripting environments where __file__ isn't defined
    LOGO_PATH = "AuST-Group_Logo_MAIN.png"


def _get_graph_token() -> Optional[str]:
    """
    (Internal) Acquires an OAuth token from Azure AD.
    """
    app = msal.ConfidentialClientApplication(
        CLIENT_ID,
        authority=AUTHORITY,
        client_credential=CLIENT_SECRET
    )
    result = app.acquire_token_silent(SCOPE, account=None)
    
    if not result:
        print("No token in cache. Acquiring new token from Azure AD...")
        result = app.acquire_token_for_client(scopes=SCOPE)
        
    if "access_token" in result:
        print("Access token acquired.")
        return result['access_token']
    else:
        print("FATAL: Failed to acquire access token.")
        print(result.get("error"))
        print(result.get("error_description"))
        return None

def _encode_image_to_base64(image_path: str) -> Optional[str]:
    """
    (Internal) Reads and Base64-encodes the logo image.
    """
    try:
        with open(image_path, "rb") as image_file:
            return base64.b64encode(image_file.read()).decode('utf-8')
    except FileNotFoundError:
        print(f"WARNING: Logo file not found at {image_path}. Email will send without logo.")
        return None
    except Exception as e:
        print(f"WARNING: Could not encode image. Error: {e}")
        return None

def _build_html_body(job_name: str, summary: str, error: Exception, tb_str: str, logo_base64: Optional[str], attachment_name: Optional[str]) -> str:
    """
    (Internal) Constructs the branded HTML email body.
    """
    logo_html = f'<img src="data:image/png;base64,{logo_base64}" alt="AuST Group Logo" style="max-width:250px;">' if logo_base64 else ''
    
    # Clean exception and summary text for HTML
    error_class = str(error.__class__.__name__)
    error_message = str(error).replace("<", "&lt;").replace(">", "&gt;")
    summary_html = str(summary).replace("\n", "<br>")

    # Clean the traceback for HTML display
    tb_html = tb_str.replace("<", "&lt;").replace(">", "&gt;").replace("\n", "<br>").replace(" ", "&nbsp;")
    
    attachment_note = ""
    if attachment_name:
        attachment_note = f"""
        <p style="background-color: #FDB913; padding: 10px; border-radius: 4px;">
            <strong>Attachment:</strong> A file named <strong>{attachment_name}</strong> is attached to this email for analysis.
        </p>
        """
    # --- END MODIFICATION ---

    return f"""
    <html lang="en">
    <head>
        <style>
            body {{ font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }}
            .container {{ 
                border: 5px solid #FDB913; 
                padding: 20px; 
                max-width: 800px; 
                margin: 20px; 
                border-radius: 8px; 
            }}
            .header {{ 
                font-size: 24px; 
                font-weight: 600; 
                color: #004A91; 
                margin-bottom: 10px;
            }}
            .error-box {{ 
                background-color: #f8f8f8; 
                border: 1px solid #ddd;
                padding: 15px; 
                font-family: 'Courier New', Courier, monospace;
                font-size: 14px;
                white-space: pre-wrap;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }}
            p {{ font-size: 16px; line-height: 1.5; }}
            .summary {{ background-color: #f0f4f9; padding: 10px; border-left: 4px solid #004A91; }}
        </style>
    </head>
    <body>
        <div class="container">
            {logo_html}
            <h1 class="header">JOB FAILED: {job_name}</h1>
            <p>Admins,</p>
            <p>Your automated job, <strong>{job_name}</strong>, has failed.</p>
            
            <p class="summary"><strong>Job Summary:</strong><br>{summary_html}</p>
            
            {attachment_note} 

            <h2 style="color: #004A91;">Error Details</h2>
            <div class="error-box">
                <strong>{error_class}:</strong> {error_message}
            </div>
            
            <h2 style="color: #004A91;">Full Traceback</h2>
            <div class="error-box">
                {tb_html}
            </div>
        </div>
    </body>
    </html>
    """

def _send_graph_email(access_token: str, to_email_list: List[str], subject: str, html_body_content: str, attachment_path: Optional[str] = None):
    """
    (Internal) Sends the email using the Graph API. Now supports one attachment.
    """
    send_mail_url = f"{GRAPH_ENDPOINT}/users/{SENDER_EMAIL}/sendMail"
    
    to_recipients_json = [{"emailAddress": {"address": email}} for email in to_email_list]

    attachments_json = []
    if attachment_path and os.path.exists(attachment_path):
        try:
            with open(attachment_path, "rb") as f:
                attachment_content = f.read()
            attachment_base64 = base64.b64encode(attachment_content).decode('utf-8')
            attachment_name = os.path.basename(attachment_path)
            
            # Guess content type, default to octet-stream
            content_type = "application/octet-stream"
            if attachment_name.endswith(".png"):
                content_type = "image/png"
            elif attachment_name.endswith(".jpg") or attachment_name.endswith(".jpeg"):
                content_type = "image/jpeg"
            elif attachment_name.endswith(".txt"):
                content_type = "text/plain"
            
            attachments_json.append({
                "@odata.type": "#microsoft.graph.fileAttachment",
                "name": attachment_name,
                "contentType": content_type,
                "contentBytes": attachment_base64
            })
            print(f"Successfully encoded attachment: {attachment_name}")
        except Exception as e:
            print(f"WARNING: Failed to read or encode attachment. Email will send without it. Error: {e}")
    elif attachment_path:
        print(f"WARNING: Attachment path specified but not found: {attachment_path}. Sending without it.")
    # --- END MODIFICATION ---

    email_payload = {
        "message": {
            "subject": subject,
            "body": {"contentType": "HTML", "content": html_body_content},
            "toRecipients": to_recipients_json,
            "attachments": attachments_json  # Add attachments list
        },
        "saveToSentItems": "true"
    }
    
    headers = {
        "Authorization": f"Bearer {access_token}",
        "Content-Type": "application/json"
    }
    
    try:
        response = requests.post(send_mail_url, headers=headers, data=json.dumps(email_payload))
        if response.status_code == 202:
            print(f"FAILURE REPORT SENT: From {SENDER_EMAIL} to {', '.join(to_email_list)}")
        else:
            print(f"CRITICAL: Graph API call failed with status code {response.status_code}")
            print(f"Response: {response.json()}")
    except Exception as e:
        print(f"CRITICAL: Failed to send the failure email itself. Error: {e}")

# --- 2. THE PUBLIC FUNCTION ---
def send_failure_alert(job_name: str, summary: str, error: Exception, recipients: List[str], attachment_path: Optional[str] = None):
    """
    This is the primary function to be called by other scripts.
    It orchestrates the entire alert process.
    
    Args:
        job_name (str): Name of the job that failed.
        summary (str): A brief, human-readable summary of what was happening.
        error (Exception): The exception object that was caught.
        recipients (List[str]): List of email addresses to notify.
        attachment_path (Optional[str]): Path to a single file to attach (e.g., a screenshot).
    """
    print(f"\n--- JOB FAILED: {job_name} ---")
    print(f"Error: {error}")
    
    # --- CHECK CONFIGURATION ---
    if not all([TENANT_ID, CLIENT_ID, CLIENT_SECRET]):
        print("FATAL: Alerter environment variables (TENANT_ID, CLIENT_ID, CLIENT_SECRET) are not set.")
        print("Ensure your .env file is correct.")
        return

    # --- GET TRACEBACK ---
    tb_str = traceback.format_exc()
    
    # --- GET TOKEN ---
    print("Acquiring token to send failure report...")
    token = _get_graph_token()
    if not token:
        print("Could not get token. Alert aborted.")
        return
        
    # --- GET LOGO ---
    logo_base64 = _encode_image_to_base64(LOGO_PATH)
    
    # --- BUILD & SEND ---
    attachment_name = os.path.basename(attachment_path) if attachment_path and os.path.exists(attachment_path) else None
    html_body = _build_html_body(job_name, summary, error, tb_str, logo_base64, attachment_name)
    subject = f"JOB FAILED: {job_name} ({error.__class__.__name__})"
    
    _send_graph_email(token, recipients, subject, html_body, attachment_path)


# --- 3. TEST BLOCK ---
if __name__ == "__main__":
    """
    This block only runs if you execute 'python job_alerter.py' directly.
    It is used to test the alerter module itself.
    """
    print("--- Running Alerter Self-Test ---")
    
    test_job = "Alerter Self-Test"
    test_summary = "This is a test of the job_alerter module. If you receive this, the module is configured correctly."
    test_recipients = ["bvanorden@aust-mfg.com"] # Send to yourself for testing
    
    try:
        # Simulate a real failure
        x = 10
        y = 0
        z = x / y
    except Exception as e:
        send_failure_alert(
            job_name=test_job,
            summary=test_summary,
            error=e,
            recipients=test_recipients,
            attachment_path=None # Test without attachment
        )
    print("--- Alerter Self-Test Complete ---")
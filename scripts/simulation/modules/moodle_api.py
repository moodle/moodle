"""
Moodle Web Services API Client.

Provides a Python interface for interacting with Moodle's REST API.
"""

import requests
import logging
from typing import Dict, List, Any, Optional
from urllib.parse import urljoin

logger = logging.getLogger(__name__)


class MoodleAPIClient:
    """Client for Moodle Web Services REST API."""

    def __init__(self, base_url: str, wstoken: str, service: str = 'moodle_mobile_app'):
        """
        Initialize Moodle API client.

        Args:
            base_url: Base URL of Moodle installation
            wstoken: Web service token
            service: Service name (default: moodle_mobile_app)
        """
        self.base_url = base_url.rstrip('/')
        self.wstoken = wstoken
        self.service = service
        self.endpoint = urljoin(self.base_url, 'webservice/rest/server.php')
        self.session = requests.Session()

        logger.info(f"Moodle API client initialized for {self.base_url}")

    def call(self, function: str, **params) -> Any:
        """
        Call a Moodle web service function.

        Args:
            function: Moodle web service function name
            **params: Function parameters

        Returns:
            Response data from Moodle

        Raises:
            requests.RequestException: If the request fails
            ValueError: If Moodle returns an error
        """
        data = {
            'wstoken': self.wstoken,
            'wsfunction': function,
            'moodlewsrestformat': 'json',
        }

        # Add function parameters.
        data.update(params)

        try:
            response = self.session.post(self.endpoint, data=data, timeout=30)
            response.raise_for_status()

            result = response.json()

            # Check for Moodle errors.
            if isinstance(result, dict) and 'exception' in result:
                error_msg = result.get('message', 'Unknown Moodle error')
                logger.error(f"Moodle API error in {function}: {error_msg}")
                raise ValueError(f"Moodle error: {error_msg}")

            logger.debug(f"Called {function} successfully")
            return result

        except requests.RequestException as e:
            logger.error(f"HTTP error calling {function}: {e}")
            raise

    def get_site_info(self) -> Dict:
        """Get Moodle site information."""
        return self.call('core_webservice_get_site_info')

    def create_users(self, users: List[Dict]) -> List[Dict]:
        """
        Create multiple users.

        Args:
            users: List of user dictionaries with keys: username, password, firstname, lastname, email

        Returns:
            List of created user objects
        """
        # Format users for Moodle API.
        formatted_users = []
        for i, user in enumerate(users):
            formatted_users.append({
                f'users[{i}][username]': user['username'],
                f'users[{i}][password]': user['password'],
                f'users[{i}][firstname]': user['firstname'],
                f'users[{i}][lastname]': user['lastname'],
                f'users[{i}][email]': user['email'],
            })

        # Flatten into single dict.
        params = {}
        for user_params in formatted_users:
            params.update(user_params)

        return self.call('core_user_create_users', **params)

    def get_users(self, criteria: Optional[List[Dict]] = None) -> List[Dict]:
        """Get users by criteria."""
        if criteria is None:
            criteria = []

        params = {}
        for i, crit in enumerate(criteria):
            params[f'criteria[{i}][key]'] = crit['key']
            params[f'criteria[{i}][value]'] = crit['value']

        result = self.call('core_user_get_users', **params)
        return result.get('users', [])

    def create_courses(self, courses: List[Dict]) -> List[Dict]:
        """
        Create multiple courses.

        Args:
            courses: List of course dictionaries

        Returns:
            List of created courses
        """
        params = {}
        for i, course in enumerate(courses):
            params[f'courses[{i}][fullname]'] = course['fullname']
            params[f'courses[{i}][shortname]'] = course['shortname']
            params[f'courses[{i}][categoryid]'] = course.get('categoryid', 1)
            if 'summary' in course:
                params[f'courses[{i}][summary]'] = course['summary']

        return self.call('core_course_create_courses', **params)

    def get_courses(self) -> List[Dict]:
        """Get all courses."""
        result = self.call('core_course_get_courses')
        return result if isinstance(result, list) else []

    def enrol_users(self, enrolments: List[Dict]) -> None:
        """
        Enrol users in courses.

        Args:
            enrolments: List of dicts with keys: roleid, userid, courseid
        """
        params = {}
        for i, enrol in enumerate(enrolments):
            params[f'enrolments[{i}][roleid]'] = enrol['roleid']
            params[f'enrolments[{i}][userid]'] = enrol['userid']
            params[f'enrolments[{i}][courseid]'] = enrol['courseid']

        self.call('enrol_manual_enrol_users', **params)

    def view_course(self, courseid: int, userid: Optional[int] = None) -> Dict:
        """Simulate viewing a course."""
        params = {'courseid': courseid}
        if userid:
            params['userid'] = userid
        return self.call('core_course_view_course', **params)

    def get_course_modules(self, courseid: int) -> List[Dict]:
        """Get course modules (activities)."""
        result = self.call('core_course_get_contents', courseid=courseid)
        return result if isinstance(result, list) else []

    def create_forum_discussion(self, forumid: int, subject: str, message: str) -> int:
        """Create a forum discussion."""
        result = self.call(
            'mod_forum_add_discussion',
            forumid=forumid,
            subject=subject,
            message=message
        )
        return result.get('discussionid', 0)

    def get_quizzes(self, courseids: Optional[List[int]] = None) -> List[Dict]:
        """Get quizzes by course."""
        if courseids is None:
            courseids = []

        params = {}
        for i, cid in enumerate(courseids):
            params[f'courseids[{i}]'] = cid

        result = self.call('mod_quiz_get_quizzes_by_courses', **params)
        return result.get('quizzes', [])

    def start_quiz_attempt(self, quizid: int) -> Dict:
        """Start a quiz attempt."""
        return self.call('mod_quiz_start_attempt', quizid=quizid)

    def get_assignments(self, courseids: Optional[List[int]] = None) -> List[Dict]:
        """Get assignments."""
        if courseids is None:
            courseids = []

        params = {}
        for i, cid in enumerate(courseids):
            params[f'courseids[{i}]'] = cid

        result = self.call('mod_assign_get_assignments', **params)
        return result.get('courses', [])

    def view_dashboard(self) -> Dict:
        """Simulate viewing the dashboard."""
        # Note: Not all Moodle versions have this function.
        # Fallback to site info if not available.
        try:
            return self.call('core_dashboard_view_dashboard')
        except ValueError:
            return self.get_site_info()

    def get_user_profile(self, userid: int) -> Dict:
        """Get user profile."""
        result = self.call('core_user_get_users_by_field', field='id', values=[userid])
        return result[0] if result else {}

    def close(self):
        """Close the session."""
        self.session.close()
        logger.info("Moodle API session closed")

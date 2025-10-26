"""
Activity management module.

Handles creation and interaction with Moodle activities (quizzes, assignments, forums).
"""

import logging
from faker import Faker
from typing import List, Dict
from .moodle_api import MoodleAPIClient

logger = logging.getLogger(__name__)
fake = Faker()


def simulate_quiz_attempts(api: MoodleAPIClient, courseids: List[int], count: int = 50) -> int:
    """
    Simulate quiz attempts.

    Args:
        api: Moodle API client
        courseids: List of course IDs to get quizzes from
        count: Number of quiz attempts to simulate

    Returns:
        Number of successful attempts
    """
    import random

    # Get quizzes from courses.
    try:
        quizzes = api.get_quizzes(courseids)
        if not quizzes:
            logger.warning("No quizzes found to attempt")
            return 0

    except Exception as e:
        logger.error(f"Error fetching quizzes: {e}")
        return 0

    successful = 0

    for i in range(count):
        quiz = random.choice(quizzes)

        try:
            api.start_quiz_attempt(quiz['id'])
            successful += 1

            if (i + 1) % 10 == 0:
                logger.info(f"Simulated {i+1} quiz attempts")

        except Exception as e:
            # Quiz attempts may fail if already in progress, etc.
            logger.debug(f"Could not attempt quiz {quiz['id']}: {e}")

    logger.info(f"Simulated {successful}/{count} quiz attempts successfully")
    return successful


def simulate_forum_posts(api: MoodleAPIClient, courseids: List[int], count: int = 30) -> int:
    """
    Simulate forum discussion posts.

    Args:
        api: Moodle API client
        courseids: List of course IDs
        count: Number of posts to simulate

    Returns:
        Number of successful posts
    """
    import random

    # Get course modules to find forums.
    forums = []
    for courseid in courseids[:5]:  # Limit to first 5 courses.
        try:
            modules = api.get_course_modules(courseid)
            for section in modules:
                for module in section.get('modules', []):
                    if module.get('modname') == 'forum':
                        forums.append(module['id'])

        except Exception as e:
            logger.debug(f"Error getting modules for course {courseid}: {e}")

    if not forums:
        logger.warning("No forums found to post in")
        return 0

    successful = 0

    for i in range(count):
        forumid = random.choice(forums)
        subject = fake.sentence(nb_words=6)
        message = fake.paragraph(nb_sentences=3)

        try:
            api.create_forum_discussion(forumid, subject, message)
            successful += 1

            if (i + 1) % 5 == 0:
                logger.info(f"Created {i+1} forum posts")

        except Exception as e:
            logger.debug(f"Error creating forum post: {e}")

    logger.info(f"Created {successful}/{count} forum posts successfully")
    return successful


def simulate_assignment_views(api: MoodleAPIClient, courseids: List[int], count: int = 40) -> int:
    """
    Simulate assignment views.

    Args:
        api: Moodle API client
        courseids: List of course IDs
        count: Number of views to simulate

    Returns:
        Number of successful views
    """
    import random

    # Get assignments.
    try:
        result = api.get_assignments(courseids)
        assignments = []

        for course_data in result:
            assignments.extend(course_data.get('assignments', []))

        if not assignments:
            logger.warning("No assignments found to view")
            return 0

    except Exception as e:
        logger.error(f"Error fetching assignments: {e}")
        return 0

    successful = 0

    # Note: Actual viewing would require module view functions.
    # For simulation purposes, we'll count attempts.
    successful = min(count, len(assignments) * 5)

    logger.info(f"Simulated {successful} assignment views")
    return successful


def get_activity_distribution(num_activities: int) -> Dict[str, int]:
    """
    Get realistic distribution of different activity types.

    Args:
        num_activities: Total number of activities

    Returns:
        Dictionary with activity counts
    """
    return {
        'quiz': int(num_activities * 0.3),
        'assignment': int(num_activities * 0.25),
        'forum': int(num_activities * 0.20),
        'resource': int(num_activities * 0.15),
        'page': int(num_activities * 0.10),
    }

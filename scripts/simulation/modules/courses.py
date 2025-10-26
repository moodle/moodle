"""
Course management module.

Creates courses and manages course-related operations.
"""

import logging
from faker import Faker
from typing import List, Dict
from .moodle_api import MoodleAPIClient

logger = logging.getLogger(__name__)
fake = Faker()


def generate_fake_courses(count: int) -> List[Dict]:
    """
    Generate fake course data.

    Args:
        count: Number of courses to generate

    Returns:
        List of course dictionaries
    """
    course_subjects = [
        'Introduction to Programming',
        'Data Structures and Algorithms',
        'Web Development',
        'Database Systems',
        'Machine Learning',
        'Computer Networks',
        'Operating Systems',
        'Software Engineering',
        'Cybersecurity Fundamentals',
        'Mobile App Development',
        'Cloud Computing',
        'Artificial Intelligence',
    ]

    courses = []
    for i in range(count):
        subject = course_subjects[i % len(course_subjects)]
        courses.append({
            'fullname': f"{subject} - {fake.year()}",
            'shortname': f"COURSE{i+1:03d}",
            'categoryid': 1,
            'summary': fake.paragraph(nb_sentences=3),
        })

    logger.info(f"Generated {count} fake courses")
    return courses


def create_courses_batch(api: MoodleAPIClient, courses: List[Dict]) -> List[Dict]:
    """
    Create courses in Moodle.

    Args:
        api: Moodle API client
        courses: List of course dictionaries

    Returns:
        List of created course objects
    """
    created_courses = []

    try:
        result = api.create_courses(courses)
        created_courses.extend(result)
        logger.info(f"Created {len(result)} courses")

    except Exception as e:
        logger.error(f"Error creating courses: {e}")

    return created_courses


def enrol_users_to_courses(
    api: MoodleAPIClient,
    userids: List[int],
    courseids: List[int],
    student_roleid: int = 5
) -> int:
    """
    Enrol users into courses.

    Args:
        api: Moodle API client
        userids: List of user IDs
        courseids: List of course IDs
        student_roleid: Role ID for student (default: 5)

    Returns:
        Number of successful enrolments
    """
    import random

    enrolments = []

    # Enrol each user in 2-5 random courses.
    for userid in userids:
        num_courses = random.randint(2, min(5, len(courseids)))
        user_courses = random.sample(courseids, num_courses)

        for courseid in user_courses:
            enrolments.append({
                'roleid': student_roleid,
                'userid': userid,
                'courseid': courseid,
            })

    # Batch enrol (Moodle handles multiple at once).
    try:
        api.enrol_users(enrolments)
        logger.info(f"Enrolled {len(userids)} users into courses ({len(enrolments)} total enrolments)")
        return len(enrolments)

    except Exception as e:
        logger.error(f"Error enrolling users: {e}")
        return 0


def simulate_course_views(api: MoodleAPIClient, courseids: List[int], count: int = 100) -> int:
    """
    Simulate course page views.

    Args:
        api: Moodle API client
        courseids: List of course IDs
        count: Number of views to simulate

    Returns:
        Number of successful views
    """
    import random

    successful = 0

    for i in range(count):
        courseid = random.choice(courseids)

        try:
            api.view_course(courseid)
            successful += 1

            if (i + 1) % 20 == 0:
                logger.info(f"Simulated {i+1} course views")

        except Exception as e:
            logger.error(f"Error viewing course {courseid}: {e}")

    logger.info(f"Simulated {successful}/{count} course views successfully")
    return successful

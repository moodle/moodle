"""
User management and simulation module.

Creates fake users and simulates user login/authentication patterns.
"""

import logging
from faker import Faker
from typing import List, Dict
from .moodle_api import MoodleAPIClient

logger = logging.getLogger(__name__)
fake = Faker()


def generate_fake_users(count: int) -> List[Dict]:
    """
    Generate fake user data.

    Args:
        count: Number of users to generate

    Returns:
        List of user dictionaries
    """
    users = []
    for i in range(count):
        username = f"student{i+1:04d}"
        users.append({
            'username': username,
            'password': 'Password123!',
            'firstname': fake.first_name(),
            'lastname': fake.last_name(),
            'email': f"{username}@example.com",
        })

    logger.info(f"Generated {count} fake users")
    return users


def create_users_batch(api: MoodleAPIClient, users: List[Dict], batch_size: int = 10) -> List[Dict]:
    """
    Create users in Moodle in batches.

    Args:
        api: Moodle API client
        users: List of user dictionaries
        batch_size: Number of users per batch

    Returns:
        List of created user objects
    """
    created_users = []

    for i in range(0, len(users), batch_size):
        batch = users[i:i+batch_size]

        try:
            result = api.create_users(batch)
            created_users.extend(result)
            logger.info(f"Created users batch {i//batch_size + 1} ({len(batch)} users)")

        except Exception as e:
            logger.error(f"Error creating users batch: {e}")

    logger.info(f"Total users created: {len(created_users)}")
    return created_users


def simulate_user_logins(api: MoodleAPIClient, userids: List[int], count: int = 50) -> int:
    """
    Simulate multiple user logins by viewing dashboard.

    Args:
        api: Moodle API client
        userids: List of user IDs to simulate
        count: Number of login simulations

    Returns:
        Number of successful logins
    """
    import random

    successful = 0

    for i in range(count):
        userid = random.choice(userids)

        try:
            api.view_dashboard()
            successful += 1

            if (i + 1) % 10 == 0:
                logger.info(f"Simulated {i+1} logins")

        except Exception as e:
            logger.error(f"Error simulating login for user {userid}: {e}")

    logger.info(f"Simulated {successful}/{count} logins successfully")
    return successful


def get_realistic_active_users(total_users: int, hour: int) -> int:
    """
    Calculate realistic number of active users based on time of day.

    Args:
        total_users: Total number of users in system
        hour: Hour of day (0-23)

    Returns:
        Number of active users
    """
    # Peak hours: 9-11am, 2-4pm, 7-9pm
    peak_hours = [9, 10, 11, 14, 15, 16, 19, 20]
    # Low hours: Late night/early morning
    low_hours = [0, 1, 2, 3, 4, 5, 6, 23]

    if hour in peak_hours:
        # 60-80% of users active during peak
        import random
        multiplier = random.uniform(0.6, 0.8)
    elif hour in low_hours:
        # 5-15% active during low hours
        import random
        multiplier = random.uniform(0.05, 0.15)
    else:
        # 30-50% during normal hours
        import random
        multiplier = random.uniform(0.3, 0.5)

    active_count = int(total_users * multiplier)
    return max(1, active_count)

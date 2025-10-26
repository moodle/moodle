"""
User interaction simulation module.

Simulates various user interactions and behaviors on the Moodle platform.
"""

import logging
import random
import time
from typing import List, Dict, Callable
from .moodle_api import MoodleAPIClient
from . import courses, activities

logger = logging.getLogger(__name__)


class InteractionSimulator:
    """Simulates realistic user interactions with Moodle."""

    def __init__(self, api: MoodleAPIClient, config: Dict):
        """
        Initialize interaction simulator.

        Args:
            api: Moodle API client
            config: Configuration dictionary
        """
        self.api = api
        self.config = config
        self.userids = []
        self.courseids = []

        # Activity weights for realistic distribution.
        self.activities = config.get('activities', {}).get('weights', {
            'course_view': 30,
            'quiz_attempt': 15,
            'assignment_submit': 10,
            'forum_post': 10,
            'resource_download': 20,
            'user_profile_view': 10,
            'dashboard_view': 5,
        })

    def set_context(self, userids: List[int], courseids: List[int]):
        """
        Set the context for simulations.

        Args:
            userids: List of user IDs
            courseids: List of course IDs
        """
        self.userids = userids
        self.courseids = courseids
        logger.info(f"Simulation context set: {len(userids)} users, {len(courseids)} courses")

    def weighted_random_activity(self) -> str:
        """
        Choose a random activity based on weights.

        Returns:
            Activity name
        """
        activities_list = []
        weights = []

        for activity, weight in self.activities.items():
            activities_list.append(activity)
            weights.append(weight)

        return random.choices(activities_list, weights=weights)[0]

    def simulate_single_interaction(self) -> bool:
        """
        Simulate a single random user interaction.

        Returns:
            True if successful, False otherwise
        """
        if not self.userids or not self.courseids:
            logger.error("Context not set for interaction simulation")
            return False

        activity = self.weighted_random_activity()

        try:
            if activity == 'course_view':
                courseid = random.choice(self.courseids)
                self.api.view_course(courseid)

            elif activity == 'quiz_attempt':
                activities.simulate_quiz_attempts(self.api, self.courseids, count=1)

            elif activity == 'forum_post':
                activities.simulate_forum_posts(self.api, self.courseids, count=1)

            elif activity == 'dashboard_view':
                self.api.view_dashboard()

            elif activity == 'user_profile_view':
                userid = random.choice(self.userids)
                self.api.get_user_profile(userid)

            else:
                # Default to course view for other activities.
                courseid = random.choice(self.courseids)
                self.api.view_course(courseid)

            return True

        except Exception as e:
            logger.debug(f"Error in {activity}: {e}")
            return False

    def simulate_burst(self, num_events: int, duration_seconds: int = 10) -> Dict:
        """
        Simulate a burst of activity.

        Args:
            num_events: Number of events in the burst
            duration_seconds: Duration of the burst

        Returns:
            Statistics dictionary
        """
        logger.info(f"Starting burst simulation: {num_events} events in {duration_seconds}s")

        successful = 0
        failed = 0
        start_time = time.time()

        delay = duration_seconds / num_events if num_events > 0 else 0

        for i in range(num_events):
            if self.simulate_single_interaction():
                successful += 1
            else:
                failed += 1

            # Add small delay between events.
            if delay > 0:
                time.sleep(delay)

            if (i + 1) % 20 == 0:
                logger.info(f"Burst progress: {i+1}/{num_events} events")

        elapsed = time.time() - start_time

        stats = {
            'total_events': num_events,
            'successful': successful,
            'failed': failed,
            'duration': elapsed,
            'events_per_second': num_events / elapsed if elapsed > 0 else 0,
        }

        logger.info(f"Burst completed: {successful}/{num_events} successful in {elapsed:.2f}s "
                   f"({stats['events_per_second']:.2f} events/sec)")

        return stats

    def simulate_steady(self, events_per_second: int, duration_seconds: int) -> Dict:
        """
        Simulate steady load.

        Args:
            events_per_second: Target events per second
            duration_seconds: Total duration

        Returns:
            Statistics dictionary
        """
        logger.info(f"Starting steady simulation: {events_per_second} events/sec for {duration_seconds}s")

        total_events = events_per_second * duration_seconds
        return self.simulate_burst(total_events, duration_seconds)

    def simulate_realistic(self, duration_seconds: int) -> Dict:
        """
        Simulate realistic user behavior with varying load.

        Args:
            duration_seconds: Total simulation duration

        Returns:
            Statistics dictionary
        """
        logger.info(f"Starting realistic simulation for {duration_seconds}s")

        import datetime

        successful = 0
        failed = 0
        start_time = time.time()
        events = []

        while time.time() - start_time < duration_seconds:
            # Get current hour.
            current_hour = datetime.datetime.now().hour

            # Determine activity level based on hour.
            scenarios = self.config.get('scenarios', {}).get('realistic', {})
            peak_hours = scenarios.get('peak_hours', [9, 10, 11, 14, 15, 16])
            low_hours = scenarios.get('low_hours', [0, 1, 2, 3, 4, 5])

            if current_hour in peak_hours:
                delay = 0.1  # 10 events/second during peak.
            elif current_hour in low_hours:
                delay = 1.0  # 1 event/second during low.
            else:
                delay = 0.3  # 3-4 events/second during normal.

            if self.simulate_single_interaction():
                successful += 1
            else:
                failed += 1

            events.append(time.time())

            time.sleep(delay)

            # Log progress every 50 events.
            if (successful + failed) % 50 == 0:
                logger.info(f"Realistic simulation progress: {successful + failed} events")

        elapsed = time.time() - start_time

        stats = {
            'total_events': successful + failed,
            'successful': successful,
            'failed': failed,
            'duration': elapsed,
            'events_per_second': (successful + failed) / elapsed if elapsed > 0 else 0,
        }

        logger.info(f"Realistic simulation completed: {successful}/{successful+failed} successful in {elapsed:.2f}s")

        return stats

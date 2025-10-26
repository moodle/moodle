#!/usr/bin/env python3
"""
Moodle Load Generation Script.

Orchestrates simulation of realistic user interactions with Moodle to generate
log data for testing the logstore_tsdb plugin.
"""

import sys
import json
import logging
import argparse
import time
from pathlib import Path

# Add modules to path.
sys.path.insert(0, str(Path(__file__).parent))

from modules import moodle_api, users, courses, activities, interactions


def setup_logging(config: dict) -> logging.Logger:
    """Setup logging configuration."""
    log_config = config.get('logging', {})

    log_level = getattr(logging, log_config.get('level', 'INFO'))
    log_file = log_config.get('file', 'simulation.log')
    console = log_config.get('console', True)

    handlers = []

    if log_file:
        handlers.append(logging.FileHandler(log_file))

    if console:
        handlers.append(logging.StreamHandler())

    logging.basicConfig(
        level=log_level,
        format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
        handlers=handlers
    )

    return logging.getLogger(__name__)


def load_config(config_path: str = 'config.json') -> dict:
    """Load configuration from JSON file."""
    with open(config_path, 'r') as f:
        return json.load(f)


def setup_environment(api: moodle_api.MoodleAPIClient, config: dict, logger: logging.Logger):
    """
    Setup Moodle environment with users and courses.

    Returns:
        Tuple of (userids, courseids)
    """
    logger.info("Setting up Moodle environment...")

    sim_config = config['simulation']

    # Generate and create users.
    logger.info(f"Creating {sim_config['num_users']} users...")
    fake_users = users.generate_fake_users(sim_config['num_users'])
    created_users = users.create_users_batch(api, fake_users, batch_size=10)
    userids = [user['id'] for user in created_users]

    if not userids:
        logger.error("Failed to create users. Exiting.")
        sys.exit(1)

    # Generate and create courses.
    logger.info(f"Creating {sim_config['num_courses']} courses...")
    fake_courses = courses.generate_fake_courses(sim_config['num_courses'])
    created_courses = courses.create_courses_batch(api, fake_courses)
    courseids = [course['id'] for course in created_courses]

    if not courseids:
        logger.error("Failed to create courses. Exiting.")
        sys.exit(1)

    # Enrol users in courses.
    logger.info("Enrolling users in courses...")
    courses.enrol_users_to_courses(api, userids, courseids)

    logger.info(f"Environment setup complete: {len(userids)} users, {len(courseids)} courses")

    return userids, courseids


def run_simulation(api: moodle_api.MoodleAPIClient, config: dict, userids: list, courseids: list, logger: logging.Logger):
    """Run the configured simulation mode."""
    sim_config = config['simulation']
    mode = sim_config.get('mode', 'realistic')

    # Create interaction simulator.
    simulator = interactions.InteractionSimulator(api, config)
    simulator.set_context(userids, courseids)

    logger.info(f"Starting simulation in '{mode}' mode...")

    if mode == 'burst':
        # Burst mode: High intensity for short duration.
        burst_size = sim_config.get('burst_size', 100)
        burst_interval = sim_config.get('burst_interval', 10)

        stats = simulator.simulate_burst(burst_size, burst_interval)

    elif mode == 'steady':
        # Steady mode: Constant rate.
        events_per_second = sim_config.get('events_per_second', 10)
        duration = sim_config.get('duration_seconds', 3600)

        stats = simulator.simulate_steady(events_per_second, duration)

    elif mode == 'realistic':
        # Realistic mode: Varies by time of day.
        duration = sim_config.get('duration_seconds', 3600)

        stats = simulator.simulate_realistic(duration)

    else:
        logger.error(f"Unknown simulation mode: {mode}")
        sys.exit(1)

    # Print final statistics.
    logger.info("=" * 60)
    logger.info("SIMULATION COMPLETE")
    logger.info("=" * 60)
    logger.info(f"Total Events: {stats['total_events']}")
    logger.info(f"Successful: {stats['successful']}")
    logger.info(f"Failed: {stats['failed']}")
    logger.info(f"Duration: {stats['duration']:.2f} seconds")
    logger.info(f"Average Rate: {stats['events_per_second']:.2f} events/second")
    logger.info("=" * 60)

    return stats


def main():
    """Main entry point."""
    parser = argparse.ArgumentParser(description='Moodle Load Generation Simulator')
    parser.add_argument('--config', default='config.json', help='Path to config file')
    parser.add_argument('--mode', choices=['burst', 'steady', 'realistic'], help='Override simulation mode')
    parser.add_argument('--duration', type=int, help='Override duration in seconds')
    parser.add_argument('--skip-setup', action='store_true', help='Skip environment setup')
    args = parser.parse_args()

    # Load configuration.
    try:
        config = load_config(args.config)
    except FileNotFoundError:
        print(f"ERROR: Config file not found: {args.config}")
        sys.exit(1)
    except json.JSONDecodeError as e:
        print(f"ERROR: Invalid JSON in config file: {e}")
        sys.exit(1)

    # Setup logging.
    logger = setup_logging(config)

    logger.info("Moodle Load Simulation Starting...")
    logger.info(f"Config file: {args.config}")

    # Override config with command line args.
    if args.mode:
        config['simulation']['mode'] = args.mode
    if args.duration:
        config['simulation']['duration_seconds'] = args.duration

    # Initialize Moodle API client.
    moodle_config = config['moodle']

    if not moodle_config.get('wstoken'):
        logger.error("ERROR: Web service token not configured in config.json")
        logger.error("Please configure wstoken in the 'moodle' section")
        sys.exit(1)

    try:
        api = moodle_api.MoodleAPIClient(
            base_url=moodle_config['base_url'],
            wstoken=moodle_config['wstoken'],
            service=moodle_config.get('service', 'moodle_mobile_app')
        )

        # Verify connection.
        site_info = api.get_site_info()
        logger.info(f"Connected to Moodle site: {site_info.get('sitename', 'Unknown')}")
        logger.info(f"Moodle version: {site_info.get('release', 'Unknown')}")

    except Exception as e:
        logger.error(f"Failed to connect to Moodle: {e}")
        sys.exit(1)

    # Setup environment or use existing.
    if not args.skip_setup:
        userids, courseids = setup_environment(api, config, logger)
    else:
        logger.info("Skipping environment setup (using existing users/courses)")

        # Get existing users and courses.
        try:
            all_courses = api.get_courses()
            courseids = [c['id'] for c in all_courses if c['id'] > 1]  # Skip site course.

            # Get some users (this is a simplified approach).
            all_users = api.get_users([{'key': 'email', 'value': '%'}])
            userids = [u['id'] for u in all_users[:100]]  # Limit to 100.

            logger.info(f"Found {len(userids)} users and {len(courseids)} courses")

        except Exception as e:
            logger.error(f"Error getting existing data: {e}")
            sys.exit(1)

    # Run simulation.
    try:
        stats = run_simulation(api, config, userids, courseids, logger)

    except KeyboardInterrupt:
        logger.info("\nSimulation interrupted by user")
        sys.exit(0)

    except Exception as e:
        logger.error(f"Simulation error: {e}", exc_info=True)
        sys.exit(1)

    finally:
        api.close()

    logger.info("Simulation completed successfully!")


if __name__ == '__main__':
    main()

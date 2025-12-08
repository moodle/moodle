# MasterBuilder Plugin (`local_masterbuilder`)

## Overview
The **MasterBuilder** plugin is a custom Moodle local plugin designed to facilitate automated course creation and management via an external API. It exposes web service functions that allow external scripts (like our Python `master_builder.py`) to:
- Create courses.
- Manage course content (modules, quizzes).
- Track build state versions to prevent redundant updates.

## Key Features
- **Build State Management**: Stores a version hash for each course to track changes.
- **Course Reset**: API to reset course progress for testing.
- **API Endpoints**: Exposes Moodle internals to external automation.

## API Functions

### `local_masterbuilder_get_build_state`
Retrieves the current build version for a specific course.
- **Parameters**: `courseid` (int)
- **Returns**: `version` (string), `last_updated` (timestamp)

### `local_masterbuilder_update_build_state`
Updates the build version for a course after a successful deployment.
- **Parameters**: `courseid` (int), `version` (string)
- **Returns**: `success` (bool)

### `local_masterbuilder_reset_build_state`
Resets the build state, forcing a rebuild on the next run.
- **Parameters**: `courseid` (int)
- **Returns**: `success` (bool)

### `local_masterbuilder_reset_course_progress`
Resets completion data for all users in a course. Useful for testing.
- **Parameters**: `courseid` (int)
- **Returns**: `success` (bool)

## Usage
This plugin is primarily used by the `master_builder.py` Python script. It requires the **Moodle Mobile Web Service** to be enabled and a valid token to be generated for an admin user.

## Installation
1. Place code in `local/masterbuilder`.
2. Run Moodle upgrade (admin page or CLI).
3. Enable Web Services in Site Administration.

## Developer Info
For technical details on the API structure and code, see the **[Developer Documentation](../dev/local_masterbuilder_dev.md)**.

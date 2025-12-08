# Course Matrix (`local_coursematrix`)

## Overview
The **Course Matrix** plugin provides a rule-based enrollment system. It allows administrators to define "rules" that automatically enroll users into specific courses based on their profile data or other criteria.

## Key Features
- **Rule-Based Enrollment**: Define criteria to match users.
- **Bulk Processing**: Automatically processes updates to ensure all matching users are enrolled.
- **Management UI**: Interface to create, update, and delete enrollment rules.

## Usage
1. Navigate to the Course Matrix settings in Site Administration.
2. Create a new Rule.
3. Define the criteria (e.g., Department = 'Engineering').
4. Select the courses to enroll matching users into.
5. Save. The plugin will process the rule and enroll users.

## API
- `local_coursematrix_save_rule($data)`: Creates or updates a rule.
- `local_coursematrix_process_rule_updates($ruleid)`: Triggers the enrollment process for a rule.

## Developer Info
For technical details on the internal logic and database structure, see the **[Developer Documentation](../dev/local_coursematrix_dev.md)**.

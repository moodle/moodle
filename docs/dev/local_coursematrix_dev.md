# Course Matrix Developer Documentation

## Overview
**Plugin Name**: `local_coursematrix`
**Location**: `public/local/coursematrix`
**Purpose**: Automates user enrollment based on profile fields (Department and Job Title).

## Code Structure
- **`lib.php`**: Core logic for rule processing and user enrollment.
- **`classes/observer.php`**: Event observer that triggers enrollment checks when a user profile is updated.
- **`classes/external.php`**: (Optional) External API if implemented.

## Core Logic (`lib.php`)

### `local_coursematrix_enrol_user($userid)`
This is the main function. It:
1.  Fetches the user record.
2.  Fetches all defined rules from `mdl_local_coursematrix`.
3.  Iterates through rules to find matches based on:
    -   `department` (Case-insensitive match)
    -   `jobtitle` (Mapped to `institution` field in DB, Case-insensitive match)
4.  Collects a list of `courseid`s from matching rules.
5.  Enrolls the user into these courses using the **Manual Enrollment** plugin instance.

**Note**: The course MUST have a "Manual" enrollment method enabled for this to work.

### `local_coursematrix_process_rule_updates($ruleid)`
Called when a rule is saved. It finds all existing users who match the new/updated rule and triggers `local_coursematrix_enrol_user` for them.

## Database Schema
**Table**: `mdl_local_coursematrix`
- `id`: Primary Key
- `department`: String (e.g., "Engineering")
- `jobtitle`: String (e.g., "Manager")
- `courses`: CSV string of Course IDs (e.g., "10,12,15")

## Event Observers
The plugin observes `\core\event\user_updated` and `\core\event\user_created` to automatically process enrollments whenever a user is added or modified.

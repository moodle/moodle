# MasterBuilder Developer Documentation

## Overview
**Plugin Name**: `local_masterbuilder`
**Location**: `public/local/masterbuilder`
**Purpose**: Exposes custom Web Service API functions to allow external scripts (specifically `master_builder.py`) to automate course creation, question generation, and build state management.

## Code Structure
- **`classes/external.php`**: Defines the external API functions.
- **`db/services.php`**: Registers the web services with Moodle.
- **`version.php`**: Plugin versioning.

## API Endpoints

### 1. `local_masterbuilder_create_question`
Creates a True/False question in a specific quiz.

**Parameters:**
- `quizid` (int): ID of the quiz module.
- `questionname` (string): Name of the question.
- `questiontext` (string): HTML text of the question.
- `correctanswer` (bool): `true` for True, `false` for False.

**Example Payload (Python):**
```python
{
    "quizid": 123,
    "questionname": "Safety Check 1",
    "questiontext": "Is it safe to run with scissors?",
    "correctanswer": 0
}
```

### 2. `local_masterbuilder_get_build_state`
Retrieves the stored version hash for a course to determine if it needs updating.

**Parameters:**
- `shortname` (string): Course shortname.

**Returns:**
```json
{
    "version": "a1b2c3d4...",
    "found": true
}
```

### 3. `local_masterbuilder_update_build_state`
Updates the version hash after a successful build.

**Parameters:**
- `shortname` (string): Course shortname.
- `version` (string): New hash.

### 4. `local_masterbuilder_reset_build_state`
**DANGER**: Truncates the build state table. Forces a rebuild of ALL courses on the next run.

### 5. `local_masterbuilder_reset_course_progress`
Resets user completion data, quiz attempts, and grades for a course. Used for testing.

**Parameters:**
- `courseid` (int): ID of the course.

## Database Tables
- `mdl_local_masterbuilder_state`: Stores `course_shortname`, `version`, and `timemodified`.

## Usage in `master_builder.py`
The Python script uses these endpoints to:
1. Check if a course needs building (`get_build_state`).
2. Create quizzes and questions (`create_question`).
3. Reset progress if needed (`reset_course_progress`).
4. Update the state upon completion (`update_build_state`).

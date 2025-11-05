# User Interface and Configuration

This document describes the user interface (UI) components of the Smart File Importer plugin, including the file upload process, the forms used, and the available settings.

## File Upload Process (`index.php`)

The main entry point for the user is `index.php`. This page handles the entire file import workflow:

1.  **Displays the Upload Form**: It presents the user with a form to upload their grade file.
2.  **File Type Discovery**: Once a file is submitted, it calls `smart_autodiscover_filetype()` to identify the file format.
3.  **Data Processing**: If the file type is recognized, it proceeds to:
    -   Validate each line of the file.
    -   Extract the student identifiers and grades.
    -   Convert the identifiers to Moodle user IDs.
    -   Insert the grades into the gradebook.
4.  **Feedback and Results**: After the import process, it displays a results page with:
    -   A success or failure notification.
    -   A list of any lines that could not be parsed (`bad_lines`).
    -   A list of any student identifiers that were not found in the course (`bad_ids`).

## Forms (`forms.php`)

The plugin uses two main forms, built with the Moodle forms library.

### `smart_file_form`

-   **Purpose**: This is the initial form presented to the user for uploading a grade file.
-   **Elements**:
    -   `userfile`: A file picker for selecting the grade file.
    -   `grade_item_id`: A dropdown menu to select the grade item that the imported grades will be associated with. This list is populated with the available manual grade items for the course.

### `smart_results_form`

-   **Purpose**: This form is displayed after the import attempt to show any notices or errors.
-   **Elements**:
    -   It dynamically adds static text elements to display messages, such as information about bad lines or unrecognized user IDs.

## Configuration (`settings.php`)

The Smart File Importer has one main configuration setting, which can be found in the site administration menu.

### Keypad Profile Field

-   **Setting**: `smart_import/keypadprofile`
-   **Purpose**: This setting allows the site administrator to specify which user profile field is used to store student keypad IDs.
-   **Details**:
    -   This is necessary for the `SmartFileKeypadidCSV` and `SmartFileKeypadidTabbed` importers to correctly map keypad IDs to Moodle users.
    -   The setting is a dropdown menu that lists all available user profile fields.

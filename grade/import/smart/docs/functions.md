# Functions in `lib.php`

This document provides a detailed explanation of the functions available in `lib.php`.

## `smart_autodiscover_filetype($file)`

This is the core function for identifying the format of an uploaded grade file. It reads the first few lines of the file and attempts to match it against the validation rules of the various `SmartFile*` classes.

- **Parameters:**
  - `$file`: A string containing the entire content of the uploaded file.

- **Returns:**
  - An object of the appropriate `SmartFile*` subclass if the file type is identified.
  - `false` if the file type cannot be determined.

- **Details:**
  - The function first removes any UTF-8 BOM from the beginning of the file.
  - It then calls the static `validate_line()` method on each of the `SmartFile*` classes in a specific order. The first class that successfully validates the file is instantiated and returned.

---

## `smart_split_file($file)`

This utility function splits a file's content into an array of lines and normalizes different newline characters.

- **Parameters:**
  - `$file`: A string containing the file's content.

- **Returns:**
  - An array of strings, where each element is a line from the file.

- **Details:**
  - It replaces `\r\n` and `\r` with `\n` to ensure consistent line breaks, then explodes the string into an array.
  - If the file ends with a blank line, it is removed from the array.

---

## Validation Functions

The following functions are used to validate specific data formats within the grade files. They are primarily used by the `validate_line()` methods of the `SmartFile*` classes.

### `smart_is_lsuid2($s)`

- **Purpose:** Checks if a string is a valid 8-digit LSU ID.
- **Returns:** `true` if the string is an 8-digit number, `false` otherwise.

### `smart_is_email($s)`

- **Purpose:** Checks if a string is a valid email address with a Moodle pawsid and a valid domain.
- **Returns:** `true` for a valid email format, `false` otherwise.

### `smart_is_mec_lsuid($s)`

- **Purpose:** Checks for a valid 11-digit MEC LSU ID, which starts with three digits followed by an 8-digit number.
- **Returns:** `true` if the format is valid, `false` otherwise.

### `smart_is_grade($s)`

- **Purpose:** Validates if a string represents a numerical grade, allowing for up to three digits and two decimal places.
- **Returns:** `true` for a valid grade format, `false` otherwise.

### `smart_is_anon_num($s)`

- **Purpose:** Checks for a 4-digit anonymous number, used for grading in some contexts.
- **Returns:** `true` for a valid 4-digit number, `false` otherwise.

### `smart_is_pawsid($s)`

- **Purpose:** Validates a Moodle pawsid, which can be 1-16 characters long and contain alphanumeric characters and hyphens.
- **Returns:** `true` if the pawsid is valid, `false` otherwise.

### `smart_is_keypadid($s)`

- **Purpose:** Checks for a 6-character alphanumeric keypad ID.
- **Returns:** `true` for a valid keypad ID, `false` otherwise.

### `smart_is_89_number($s)`

- **Purpose:** Validates a 9-digit number that must start with "89".
- **Returns:** `true` if the number is a valid 89 number, `false` otherwise.

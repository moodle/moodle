# Quiz Password Verify Developer Documentation

## Overview
**Plugin Name**: `local_quiz_password_verify`
**Location**: `public/local/quiz_password_verify`
**Purpose**: Injects a custom JavaScript-based password verification modal into Quiz pages.

## Mechanism
Unlike standard Moodle plugins that might use backend hooks, this plugin primarily relies on **Frontend Interception**.

### `lib.php`
- **Function**: `local_quiz_password_verify_extend_navigation`
- **Trigger**: Called when Moodle builds the navigation block (which happens on almost every page).
- **Logic**:
    1. Checks if the current page is a Quiz Attempt, Summary, View, or Course View page.
    2. If yes, it injects the AMD module: `local_quiz_password_verify/verify`.
    3. Passes the `attemptid` (if available) to the JS.

### AMD Module (`amd/src/verify.js`)
*(Inferred from `lib.php` call)*
The JavaScript module `init` function is responsible for:
1.  Detecting when a user tries to access a quiz or submit an attempt.
2.  Intercepting the action.
3.  Displaying a custom Modal (likely using Moodle's `core/modal`).
4.  Verifying the password (possibly via an AJAX call to `verify.php` or an external service).
5.  Allowing the action to proceed if verified.

## Backend Verification (`verify.php`)
The `verify.php` script (referenced in the file list) likely handles the actual password validation request sent by the JavaScript.

## Usage
No explicit API. The plugin works automatically by injecting its JS into the relevant pages.

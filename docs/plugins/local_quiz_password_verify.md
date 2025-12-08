# Quiz Password Verify (`local_quiz_password_verify`)

## Overview
The **Quiz Password Verify** plugin enhances Moodle's quiz security by implementing a custom password verification logic. It intercepts quiz access attempts and validates passwords against a custom set of rules or an external source (depending on specific implementation details).

## Key Features
- **Custom Password Logic**: Overrides default Moodle password checks.
- **Secure Access**: Ensures only authorized users can attempt quizzes.

## Usage
This plugin operates automatically when a user attempts to access a quiz that has password protection enabled. No manual configuration is required for the end-user.

## Configuration
Admin settings can be accessed via **Site Administration > Plugins > Local plugins > Quiz Password Verify**.

## Developer Info
For technical details on the JS injection and verification logic, see the **[Developer Documentation](../dev/local_quiz_password_verify_dev.md)**.

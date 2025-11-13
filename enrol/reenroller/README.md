# moodle-enrol_reenroller
Re-enrolls expired users from specified enrollment method into new enrollment method in specified role with configured expiration timeline.

## Features
- Searches in configured categories for expired and completed students in the configured source role.
- Enrolls above users via the 'reenroller' enrollment method into the course with your configured target role.

## Requirements
- Moodle 4.1 or higher

## Installation
1. Extract the contents of this plugin to the `enrol/reenroller` directory of your Moodle installation.
2. Log in as an administrator and visit the notifications page to complete the installation.
3. The enrollment method will be available to enable.
4. Please configure the target course categories, source role, target role, enrollment method, and number / units.
5. Configure the scheduled task.

## Usage
1. This runs as a scheduled task.

## Support
For support, please contact someone else. Please leave me alone.

## License
This plugin is licensed under the GNU GPL v3 or later.

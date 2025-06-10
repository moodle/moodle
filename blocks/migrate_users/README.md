# blocks_migrate_users
Migrate user data from one user to another in a single course context.

## Who can use this?
This is limited to a subset of named site administrators.

## Who should use this?
This system should not be used by anyone. It's seriously unsafe and only fits a VERY specific use-case.

## How do install this?
1. Copy the contents blocks_migrate_users folder to blocks/migrate_users.
1. Make sure the folder and its contents are owned by your web server.
1. Install the block as normal.
1. Make sure you add a comma separated list of the approved admins who are responsible enough to use this block.

## How do I actually migrate a user?
1. Add the block to a course in which you need to migrate a users' data to another (dummy) user.
1. Enter the original user's Moodle username (with real course data) as the "From" user.
1. Enter the new (dummy) user's Moodle username (without any course data) as the "To" user.
1. Click the "Migrate" button.
1. Read the dire warning regarding data being migrated and __make sure__ the users in question are correct.
1. Once confirming the process, click the "Continue" button.
1. The process should complete. If there is an error, please let me know ASAP.
1. Click the "Continue" button to return to the course page and check the user data.

## Does it migrate ALL Moodle data?
Not currently. We support the following:
1. Enrollment
1. Groups
1. Logs
1. Events
1. Forum posts
1. Course completion
1. Grades
1. Grade history
1. Assignment submissions
1. Assignment grades
1. Assignment user flags
1. Lesson data
1. Quiz attempts
1. Quiz grades
1. SCORM data
1. Choice data

### New modules will be added for LSUOnline as they require them.

## Why does this exist?
LSUOnline requested a way to reset the course for a single user while still maintaining that user's data.
This system allows you to enter the existing user's username along with the new (dummy) user's username to "migrate" the user info from the existing to new user, allowing the existing user to re-take the course as if it was their 1st time.

## Really?
Really.

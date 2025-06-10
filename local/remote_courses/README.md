Remote course web service
=========================

![Moodle Plugin CI](https://github.com/LafColITS/moodle-local_remote_courses/workflows/Moodle%20Plugin%20CI/badge.svg)

This local module provides a web service which returns a given user's courses based on username. It returns the courses sorted by access time, with the most recently-accessed course at the top. You may also configure it to return term information. The standard use case is to create deep links to courses in one Moodle installation in another Moodle installation.

Configuration
-------------
To use this service you will need to create the following:

1. A web service on a Moodle installation
2. A user with sufficient permissions to use the web service
3. A token for that user

See [Using web services](https://docs.moodle.org/29/en/Using_web_services) in the Moodle documentation for information about creating and enabling web services. The user will need the following capabilities in addition to whichever protocol you enable:

- `moodle/course:view`
- `moodle/course:viewhiddencourses`
- `moodle/course:viewparticipants`
- `moodle/user:viewdetails`

There is a setting for extracting a term code from the course `idnumber` using a regular expression. For example, the regular expression `/[0-9]+\.([0-9]+)/` would extract "201610" from "999999.201610".

Requirements
------------
- Moodle 3.9 (build 2020061500 or later)

Installation
------------
Copy the remote_courses folder into your /local directory and visit your Admin Notification page to complete the installation.

Author
------
Charles Fulton (fultonc@lafayette.edu)

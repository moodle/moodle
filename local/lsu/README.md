# UES LSU Enrollment Provider

This Moodle plugin enhances the UES enrollment with LSU enrollment
information.

For more information about UES and the enrollment process, please go to the
[UES wiki][ues].

[ues]: https://github.com/lsuits/ues/wiki

## Features

- Full SOAP web service integration
- In-memory credentials for safe requests
- `pre` and `post` process support for user meta information
- Custom error handlers for provider specific information
- Admin links for on-demand user data processing

## Installation

LSU enrollment information installs as a Moodle [local plugin][local] to be
used with UES. Once installed, UES must be configured to use it.

[local]: http://docs.moodle.org/dev/Local_plugins

## License

LSU enrollment adopts the same license that Moodle itself does.

##Known Issues
1. In the scenario where a non-primary, np1, of a course, c1 is promoted 
to primary instructor, p1, of c1, AND THEN the course is re-assigned 
to some other primary instructor p2, ALL enrollments are dropped from 
the course c1, including both roles for the instructor (np1, p1). __won't fix__

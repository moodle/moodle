# Add / Drop Tracking Plugin for UES

This is a block that tracks student adds and drops during the [UES] process.

Without actually modifiying the enrollment process, the block builds records
based on the enrollment diff during cron. The block provides a simple read only
access to the logs to all relevent users (Teachers, Admins, etc).

[UES]: https://github.com/lsuits/ues

## Features

- Enrollment log for each section
- Group awareness for FERPA
- Filter by status, section, or time

## Download

Visit [Add / Drop Tracking's Github Page][ues_logs] to either download a
package or clone the git repository.

[ues_logs]: https://github.com/lsuits/ues_logs

## Installation Instructions

1. Copy the `ues_logs` folder into your Moodle `/blocks/` folder.
2. Click on notifications in your Moodle site administration block and follow
   the on-screen installation instructions.
3. See [the Moodle Docs page on block installation][moodle_docs]

[moodle_docs]: http://docs.moodle.org/20/en/Installing_contributed_modules_or_plugins#Block_installation

## Contributions

Contributions of any form are welcome. Github pull requests are preferred. File
any bugs, improvements, or feature requests in our [issue tracker][issues].

[issues]: https://github.com/lsuits/ues_logs/issues

## License

Add / Drop Tracking adopts the same license that Moodle does.

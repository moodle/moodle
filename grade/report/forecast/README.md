# Grade Forecast Report

This report allows students to fill in unset gradebook values to obtain
a projected result of their final grade within the course.

This report was originally developed by LSU and titled "Projected Final Grade".
To address some breaking gradebook changes in upgrading from Moodle 2.7 to 3.1,
the report was re-created by cloning Moodle's core "User Report" as a base.

## Features

- Dynamic calls to calculate gradebook values
- Teachers can optionally disable feature
- _Must make_ feature gives students the ability to find out exactly what's
  required to achieve a certain letter grade in the course.

## Download

Visit [Grade Forecast Report's Github page][forecast] to either download
a package or clone the git repository.

[forecast]: https://github.com/lsuits/gradereport_forecast

## Installation

Grade Forecast Report should be installed like any other Moodle grade report. Simply
copy the codebase to `{MOODLE_ROOT}/grade/report/forecast`.

## Contributions

Contributions of any form are welcome. Github pull requests are preferred.

File any bugs, improvements, or feature requests in our [issue
tracker][issues].

[issues]: https://github.com/lsuits/gradereport_forecast/issues

## License

Grade Forecast Report adopts the same license that Moodle does.
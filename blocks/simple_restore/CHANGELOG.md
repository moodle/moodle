## v1.2.1

- Adds user private backup files [#22][22]
- Pass in entire previous course to event [6ffb23][6ffb23]
- Add icons [8568c0][8568c0]

[22]: https://github.com/lsuits/simple_restore/issues/22
[6ffb23]: https://github.com/lsuits/simple_restore/commit/6ffb233a29eec9d7399a1ac5d1c7a4fea8a062a5
[8568c0]: https://github.com/lsuits/simple_restore/commit/8568c0e31ea296ecc488e08b1b6d081d00230583

## v1.2.0

- Restore default blocks on overwrite [#20][20]
- Correctly handles 1.9 backups [#20][20]
- Gracefully skips non-existent filters and blocks [#20][20]

[20]: https://github.com/lsuits/simple_restore/issues/20

## v1.1.1

- Minor bug in course variable name for course summary
- Use correct temp directory $CFG setting
- Bug in backadel integration on older systems

## v1.1.0

- Fixes minor bug in table building [8bf4eb6](https://github.com/lsuits/simple_restore/commit/8bf4eb6bcf7234c02d43074156ec2e399d2224ca)
- Added admin options: Overwrite course conifg, keep enrollments, keep groups [#17](https://github.com/lsuits/simple_restore/issues/17)
- Added events on selection and restore [#7](https://github.com/lsuits/simple_restore/issues/7)

## v1.0.0

- Initial Public Release
- Features:
  - Overwrite the current course with materials from previously taught courses
  - Import old matierals into the current course from previously taught courses
  - Integrates with LSU's Baclup and Delete (backadel) for seamless end of semester backups

## v1.1
- Removed events
- Added 3.6 compatibility

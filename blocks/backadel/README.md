# Backup and Delete

This is the Backup and Delete block for Moodle 2.x.

##Block Features
* Runs backups via Moodle CRON.
* Back up to slow storage or external drive mounts.
* Search for courses based on fullname, shortname, ID Number, or category.
* Non-technical query builder for backing up complex groups of courses.
* Successfully backed up courses are transferred to a deletion cue for deletion at your leisure.
* Failure cue can be automatically restarted.
* Emails instructors when the backup job is comlpete.
* Integrates with LSU's [Simplified Restore][simple_restore_github] plugin for seamless end-of semester backups and maximum course re-use.
* Easily re-use course backups across mutliple systems with [Backup and Delete][backadel_github] and [Simplified Restore][simple_restore_github] for a perfect end-to-end course archiving and re-use system.

##Download
Visit [Backup and Delete's Github page][backadel_github] to either download a package or clone the git repository.

##Installation instructions
1. Copy the backadel block folder into your Moodle /blocks/ folder.
1. Click on notifications in your Moodle site administration block and follow the on-screen installation instructions.
1. See [the Moodle Docs page on block installation][block_doc].

## Contributions
Contributions of any form are welcome. Github pull requests are preferred.
File any bugs, improvements, or feature requiests in our [issue tracker][issues].

## License
Backup and Delete adopts the same license that Moodle does.

[simple_restore_github]: https://github.com/lsuits/simple_restore
[backadel_github]: https://github.com/lsuits/backadel
[block_doc]: http://docs.moodle.org/20/en/Installing_contributed_modules_or_plugins#Block_installation
[issues]: https://github.com/lsuits/backadel/issues

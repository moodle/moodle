# mod_subsection Upgrade notes

## 5.2dev

### Added

- A new ad-hoc task, `migrate_subsection_descriptions_task`, has been added. This task will migrate all existing subsection descriptions into Text and media. To ensure system stability, the task processes records in batches of 100 and clears the original description upon successful migration.

  For more information see [MDL-87281](https://tracker.moodle.org/browse/MDL-87281)
- The subsection generator now includes support for the `summary` field. This has been added specifically to test migration tool compatibility and will be removed in Moodle 7.0. Developers should use this field only for testing migration workflows.

  For more information see [MDL-87621](https://tracker.moodle.org/browse/MDL-87621)
- A new ad-hoc task, `remove_existing_descriptions`, has been added. This task will remove the descriptions for all existing subsection instances. To ensure system stability, the task processes records in batches of 100 and clears the original description upon completion.

  For more information see [MDL-87621](https://tracker.moodle.org/browse/MDL-87621)
- The `manager::clear_description()` method has been added to remove legacy data. When called, it deletes the description text associated with a subsection and any files linked to that description.

  For more information see [MDL-87621](https://tracker.moodle.org/browse/MDL-87621)

### Changed

- When restoring backups, subsection descriptions are now ignored. This change ensures that subsection do not incorrectly restore legacy summary.

  For more information see [MDL-87621](https://tracker.moodle.org/browse/MDL-87621)

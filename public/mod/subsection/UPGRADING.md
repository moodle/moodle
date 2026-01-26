# mod_subsection Upgrade notes

## 5.2dev

### Added

- A new ad-hoc task, `migrate_subsection_descriptions_task`, has been added. This task will migrate all existing subsection descriptions into Text and media. To ensure system stability, the task processes records in batches of 100 and clears the original description upon successful migration.

  For more information see [MDL-87281](https://tracker.moodle.org/browse/MDL-87281)

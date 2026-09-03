# tool_task Upgrade notes

## 5.3dev

### Deprecated

- The `\core\task\manager::task_is_scheduled()` method has been deprecated. Use `\core\task\manager::get_queued_adhoc_task_record()` directly instead.

  For more information see [MDL-86422](https://tracker.moodle.org/browse/MDL-86422)

### Fixed

- Change semantic of queue_adhoc_task so now it always returns the task id of newly inserted task or existing task (depending on the $checkforexisting) or false if the task component is deprecated or the task could not be queued due to DML error.

  For more information see [MDL-86422](https://tracker.moodle.org/browse/MDL-86422)


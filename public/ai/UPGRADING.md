# core_ai (subsystem) Upgrade notes

## 5.3dev

### Added

- The ai_action_register table now includes a courseid column, populated at log time (and backfilled in the background for existing rows) by resolving the action's context to a course. A new report_aiusage plugin adds a course-level AI usage report, with new report/aiusage:view and report/aiusage:viewown capabilities.

  For more information see [MDL-80893](https://tracker.moodle.org/browse/MDL-80893)

### Changed

- The `prompttokens` and `completiontokens` fields have been moved from the AI action child tables (`ai_action_generate_text`, `ai_action_summarise_text`, `ai_action_explain_text`) up to the parent `ai_action_register` table, since they describe the AI provider call rather than the action-specific content. The token counts are now written to the register record by `core_ai\manager::store_action_result()`. Any code querying these columns directly on the child tables will need to be updated.

  For more information see [MDL-89123](https://tracker.moodle.org/browse/MDL-89123)

## 5.1

### Added

- Error message handler for AI subsystem.
  - Object creation
    Use `core_ai\error\factory::create($errorcode, $reason, $errorsource)` to generate the appropriate error object.

  - Extensibility
    Add new error types by extending `core_ai\error\base` and registering them in the factory.
    Please see `core_ai\error\ratelimit` as an example.

  For more information see [MDL-83147](https://tracker.moodle.org/browse/MDL-83147)
- - Added `get_enabled_actions_in_course_module` method in public/ai/classes/manager.php to get enabled AI actions in course module. - Added `is_ai_tools_enabled_in_course` method in public/ai/classes/manager.php to check if AI tools is enabled in course. - Added `is_action_enabled_in_context` method in public/ai/classes/manager.php to check if an action is enabled in a particular context. - Added `get_ai_fields_from_course_module` method in public/ai/classes/manager.php to get the AI related fields from the course module. - Added `is_html_editor_placement_available` method in public/ai/placement/editor/classes/utils.php to check if editor placement is enabled. - Added `get_actions_available` method in public/ai/placement/editor/classes/utils.php to get available actions for editor placement.

  For more information see [MDL-85738](https://tracker.moodle.org/browse/MDL-85738)

### Changed

- The method `has_model_settings` inside `core_ai\aimodel\base` is now determined by values returned from a new method called `get_model_settings`.

  For more information see [MDL-84779](https://tracker.moodle.org/browse/MDL-84779)

## 5.0

### Added

- - A new hook, `\core_ai\hook\after_ai_action_settings_form_hook`, has been introduced. It will allows AI provider plugins to add additional form elements for action settings configuration.

  For more information see [MDL-82980](https://tracker.moodle.org/browse/MDL-82980)
- - AI provider plugins that want to implement `pre-defined models` and display additional settings for models must now extend the `\core_ai\aimodel\base` class.

  For more information see [MDL-82980](https://tracker.moodle.org/browse/MDL-82980)

### Changed

- - The `\core_ai\form\action_settings_form` class has been updated to automatically include action buttons such as Save and Cancel.
  - AI provider plugins should update their form classes by removing the `$this->add_action_buttons();` call, as it is no longer required.

  For more information see [MDL-82980](https://tracker.moodle.org/browse/MDL-82980)

### Deprecated

- The ai_provider_management_table has been refactored to inherit from flexible_table instead of plugin_management_table. As a result the methods get_plugintype and get_action_url are now unused and have been deprecated in the class.

  For more information see [MDL-82922](https://tracker.moodle.org/browse/MDL-82922)

# core_question (subsystem) Upgrade notes

## 4.5.3

### Added

- Question bank Condition classes can now implement a function called "filter_invalid_values($filterconditions)" to remove anything from the filterconditions array which is invalid or should not be there.

  For more information see [MDL-83784](https://tracker.moodle.org/browse/MDL-83784)

### Fixed

- Duplication or multiple restores of questions has been modified to avoid errors where a question with the same stamp already exists in the target category.
  To achieve this all data for the question is hashed, excluding any ID fields.

  The question data from the backup is first reformatted to match the questiondata structure returned by calling `get_question_options()` (see  https://docs.moodle.org/dev/Question_data_structures#Representation_1:_%24questiondata). Common question elements will be handled automatically, but any elements that the qtype adds to the backup will need to be handled by overriding `restore_qtype_plugin::convert_backup_to_questiondata`. See `restore_qtype_match_plugin` as an example.
  If a qtype plugin calls any `$this->add_question_*()` methods in its `restore_qtype_*_plugin::define_question_plugin_structure()` method, the ID fields used in these records will be excluded automatically.
  If a qtype plugin defines its own tables with ID fields, it must define `restore_qtype_*_plugin::define_excluded_identity_hash_fields()` to return  an array of paths to these fields within the question data. This should be all that is required for the majority of plugins. See the PHPDoc of `restore_qtype_plugin::define_excluded_identity_hash_fields()` for a full explanation of how these paths should be defined, and  `restore_qtype_truefalse_plugin` for an example.
  If the data structure for a qtype returned by calling `get_question_options()` contains data other than ID fields that are not contained in the backup structure or vice-versa, it will need to override `restore_qtype_*_plugin::remove_excluded_question_data()`  to remove the inconsistent data. See `restore_qtype_multianswer_plugin` as  an example.

  For more information see [MDL-83541](https://tracker.moodle.org/browse/MDL-83541)

## 4.5.2

### Added

- The `get_bulk_actions()` method on the base `plugin_features_base` class has been changed to allow a qbank view object to be passed through. This is nullable and therefore optional for qbank plugins which don't need to do so.

  For more information see [MDL-79281](https://tracker.moodle.org/browse/MDL-79281)

## 4.5

### Added

- A new utility function `\question_utils::format_question_fragment()` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

### Changed

- `\core_question\local\bank\column_base::from_column_name()` method now accepts a `bool $ingoremissing` parameter, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81407](https://tracker.moodle.org/browse/MDL-81407)

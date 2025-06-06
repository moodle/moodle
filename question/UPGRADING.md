# core_question (subsystem) Upgrade notes

## 5.0.1

### Fixed

- The unit test repeated\_restore\_test::test\_restore\_course\_with\_same\_stamp\_questions was passing incorrectly on 5.x for question types that use answers.
  Maintainers of third-party question types may want to re-run the test with the fix in place, or if they have copied parts of this test as the basis of a test in their own plugin, review the changes and see if they should be reflected in their own test.

  For more information see [MDL-85556](https://tracker.moodle.org/browse/MDL-85556)

## 5.0

### Added

- The `get_bulk_actions()` method on the base `plugin_features_base` class has been changed to allow a qbank view object to be passed through. This is nullable and therefore optional for qbank plugins which don't need to do so.

  For more information see [MDL-79281](https://tracker.moodle.org/browse/MDL-79281)
- Question bank Condition classes can now implement a function called "filter_invalid_values($filterconditions)" to remove anything from the filterconditions array which is invalid or should not be there.

  For more information see [MDL-83784](https://tracker.moodle.org/browse/MDL-83784)

### Changed

- question_attempt_step's constructor now accepts the class constant TIMECREATED_ON_FIRST_RENDER as a value for the
  $timecreated parameter. Calling question_attempt::render for the first time will now set the first step's timecreated
  to the current time if it is set to this value. Note, null could not be used here as it is already used to indicate
  timecreated should be set to the current time.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- The definition of the abstract `core_question\local\bank\condition` class has changed to make it clearer which methods are required  in child classes.
  The `get_filter_class` method is no longer declared as abstract, and will return `null` by default to use the base  `core/datafilter/filtertype` class. If you have defined this method to return `null` in your own class, it will continue to work, but it is no longer necessary.
  `build_query_from_filter` and `get_condition_key` are now declared as abstract, since all filter condition classes must define these  (as well as existing abstract methods) to function. Again, exsiting child classes will continue to work if they did before, as they  already needed these methods.

  For more information see [MDL-83859](https://tracker.moodle.org/browse/MDL-83859)

### Deprecated

- question_type::generate_test

  No replacement, not used anywhere in core.

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- Deprecated method `mod_quiz\question\bank\qbank_helper::get_version_options` in favour of `core_question\local\bank\version_options::get_version_options` so that the method is in core rather than a module, and can safely be used from anywhere as required.

  For more information see [MDL-77713](https://tracker.moodle.org/browse/MDL-77713)
- Behat steps `behat_qbank_comment::i_should_see_on_the_column` and `behat_qbank_comment::i_click_on_the_row_containing` have been deprecated in favour of the new component named selectors, `qbank_comment > Comment count link` and `qbank_comment > Comment count text` which can be used with the standard `should exist` and `I click on` steps to replace the custom steps.

  For more information see [MDL-79122](https://tracker.moodle.org/browse/MDL-79122)

### Removed

- Final deprecation of:
    - core_question\local\bank\action_column_base::class
    - core_question\local\bank\menu_action_column_base::class
    - core_question\local\bank\menuable_action::interface
    - core_question\local\bank\view::print_choose_category_message()
    - core_question\local\bank\view::display_advanced_search_form()
    - core_question\local\bank\view::display_showtext_checkbox()
    - core_question\local\bank\view::init_search_conditions()
    - core_question\local\bank\view::get_current_category()
    - core_question\local\bank\view::display_options_form()
    - core_question\local\bank\view::start_table()
    - core_question\local\bank\view::end_table()
    - core_question\statistics\questions\all_calculated_for_qubaid_condition::TIME_TO_CACHE
    - core_question\statistics\responses\analyser::TIME_TO_CACHE
    - core_question_bank_renderer::render_category_condition_advanced()
    - core_question_bank_renderer::render_hidden_condition_advanced()
    - core_question_bank_renderer::render_category_condition()

  For more information see [MDL-78090](https://tracker.moodle.org/browse/MDL-78090)

### Fixed

- Duplication or multiple restores of questions has been modified to avoid errors where a question with the same stamp already exists in the target category.
  To achieve this all data for the question is hashed, excluding any ID fields.

  The question data from the backup is first reformatted to match the questiondata structure returned by calling `get_question_options()` (see  https://docs.moodle.org/dev/Question_data_structures#Representation_1:_%24questiondata). Common question elements will be handled automatically, but any elements that the qtype adds to the backup will need to be handled by overriding `restore_qtype_plugin::convert_backup_to_questiondata`. See `restore_qtype_match_plugin` as an example.
  If a qtype plugin calls any `$this->add_question_*()` methods in its `restore_qtype_*_plugin::define_question_plugin_structure()` method, the ID fields used in these records will be excluded automatically.
  If a qtype plugin defines its own tables with ID fields, it must define `restore_qtype_*_plugin::define_excluded_identity_hash_fields()` to return  an array of paths to these fields within the question data. This should be all that is required for the majority of plugins. See the PHPDoc of `restore_qtype_plugin::define_excluded_identity_hash_fields()` for a full explanation of how these paths should be defined, and  `restore_qtype_truefalse_plugin` for an example.
  If the data structure for a qtype returned by calling `get_question_options()` contains data other than ID fields that are not contained in the backup structure or vice-versa, it will need to override `restore_qtype_*_plugin::remove_excluded_question_data()`  to remove the inconsistent data. See `restore_qtype_multianswer_plugin` as  an example.

  For more information see [MDL-83541](https://tracker.moodle.org/browse/MDL-83541)

## 4.5

### Added

- A new utility function `\question_utils::format_question_fragment()` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

### Changed

- `\core_question\local\bank\column_base::from_column_name()` method now accepts a `bool $ingoremissing` parameter, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81407](https://tracker.moodle.org/browse/MDL-81407)

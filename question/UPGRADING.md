# core_question (subsystem) Upgrade notes

## 5.0dev

### Changed

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

## 4.5

### Added

- A new utility function `\question_utils::format_question_fragment()` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

### Changed

- `\core_question\local\bank\column_base::from_column_name()` method now accepts a `bool $ingoremissing` parameter, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81407](https://tracker.moodle.org/browse/MDL-81407)

# core_reportbuilder (subsystem) Upgrade notes

## 5.2dev

### Added

- The text filter "Contains" and "Not contains" operators now support `*` and `?` wildcard characters for better text content filtering

  For more information see [MDL-84082](https://tracker.moodle.org/browse/MDL-84082)
- The base entity class now implements a default `initialise` method, that will automatically call each of the following methods to load entity report data:

  * `get_available_columns()`
  * `get_available_filters()`
  * `get_available_conditions()`

  This change allows for a lot of boilerplate to be removed from report entity classes

  For more information see [MDL-86678](https://tracker.moodle.org/browse/MDL-86678)
- There are two new entities intended for reports specific to course module data, in order to provide a baseline in terms of module reporting and API usage:

  * `core_course\reportbuilder\local\entities\{course_module,course_module_base}`

  For more information see [MDL-86699](https://tracker.moodle.org/browse/MDL-86699)

### Deprecated

- The following `user_filter_manager` methods have been deprecated:

  * `reset_all()` - to be replaced by new `reset()` method
  * `reset_single()`
  * `merge()`

  For more information see [MDL-86997](https://tracker.moodle.org/browse/MDL-86997)
- The following enrolment entity formatter methods have been deprecated:

  * `enrolment_status()`
  * `enrolment_values()`

  For more information see [MDL-87000](https://tracker.moodle.org/browse/MDL-87000)

## 5.1

### Added

- The `count[distinct]` aggregation types support optional `'callback'` value to customise the formatted output when applied to columns

  For more information see [MDL-82464](https://tracker.moodle.org/browse/MDL-82464)
- The `report_action` class now accepts a `pix_icon` to include inside the rendered action element

  For more information see [MDL-85216](https://tracker.moodle.org/browse/MDL-85216)
- Report schedule types are now extendable by third-party plugins by extending the `core_reportbuilder\local\schedules\base` class in your component namespace: `<component>\reportbuilder\schedule\<type>`

  For more information see [MDL-86066](https://tracker.moodle.org/browse/MDL-86066)
- The report column class has a new `get_effective_type()` method to determine the returned column type, taking into account applied aggregation method

  For more information see [MDL-86151](https://tracker.moodle.org/browse/MDL-86151)

### Deprecated

- The following methods from the `schedule` helper class have been deprecated, in favour of usage of the new schedule type system:

  * `create_schedule`
  * `get_report_empty_options`
  * `send_schedule_message`

  For more information see [MDL-86066](https://tracker.moodle.org/browse/MDL-86066)

## 5.0

### Added

- New `report` helper class `get_report_row_count` method for retrieving row count of custom or system report, without having to retrieve the report content

  For more information see [MDL-74488](https://tracker.moodle.org/browse/MDL-74488)
- New `get_deprecated_tables` method in base entity, to be overridden when an entity no longer uses a table (due to column/filter re-factoring, etc) in order to avoid breaking third-party reports

  For more information see [MDL-78118](https://tracker.moodle.org/browse/MDL-78118)
- The base report class, used by both `\core_reportbuilder\system_report` and `\core_reportbuilder\datasource`, contains new methods for enhancing report rendering

  * `set_report_action` allows for an action button to belong to your report, and be rendered alongside the filters button;
  * `set_report_info_container` allows for content to be rendered by your report, between the action buttons and the table content

  For more information see [MDL-82936](https://tracker.moodle.org/browse/MDL-82936)
- The base aggregation class has a new `column_groupby` method, to be implemented in aggregation types to determime whether report tables should group by the fields of the aggregated column

  For more information see [MDL-83361](https://tracker.moodle.org/browse/MDL-83361)
- There is a new `date` aggregation type, that can be applied in custom and system reports

  For more information see [MDL-83361](https://tracker.moodle.org/browse/MDL-83361)
- The `core_reportbuilder_testcase` class has been moved to new autoloaded `core_reportbuilder\tests\core_reportbuilder_testcase` location, affected tests no longer have to manually require `/reportbuilder/tests/helpers.php`

  For more information see [MDL-84000](https://tracker.moodle.org/browse/MDL-84000)
- Columns added to system reports can render help icons in table headers via `[set|get]_help_icon` column instance methods

  For more information see [MDL-84016](https://tracker.moodle.org/browse/MDL-84016)
- The `groupconcat[distinct]` aggregation types support optional `'separator'` value to specify the text to display between aggregated items

  For more information see [MDL-84537](https://tracker.moodle.org/browse/MDL-84537)

### Changed

- The `get_active_conditions` method of the base report class has a new `$checkavailable` parameter to determine whether to check the returned conditions availability

  For more information see [MDL-82809](https://tracker.moodle.org/browse/MDL-82809)
- When the `select` filter contains upto two options only then the operator field is removed, switching to a simpler value selection field only (this may affect your Behat scenarios)

  For more information see [MDL-82913](https://tracker.moodle.org/browse/MDL-82913)
- Report table instances no longer populate the `countsql` and `countparams` class properties. Instead calling code can access `totalrows` to obtain the same value, or by calling the helper method `report::get_report_row_count`

  For more information see [MDL-83718](https://tracker.moodle.org/browse/MDL-83718)
- For columns implementing custom sorting via their `set_is_sortable` method, the specified sort fields must also be part of the columns initially selected fields

  For more information see [MDL-83718](https://tracker.moodle.org/browse/MDL-83718)
- The `select` filter type is now stricter in it's filtering, in that it will now discard values that aren't present in available filter options

  For more information see [MDL-84213](https://tracker.moodle.org/browse/MDL-84213)
- Aggregation types can access passed options set via the base class constructor in the `$this->options[]` class property. As such, their `format_value` method is no longer static and is always called from an instantiated class instance

  For more information see [MDL-84537](https://tracker.moodle.org/browse/MDL-84537)
- New `$options` argument added to the `column::set_aggregation` method for system reports, to set aggregation type-specific options

  Report entities can call new `column::set_aggregation_options` to achieve the same

  For more information see [MDL-84537](https://tracker.moodle.org/browse/MDL-84537)

### Deprecated

- The `schedule` helper class `get_schedule_report_count` method is now deprecated, existing code should instead use `report::get_report_row_count`

  For more information see [MDL-74488](https://tracker.moodle.org/browse/MDL-74488)
- The `render_new_report_button` method of the `core_reportbuilder` renderer has been deprecated. Instead, refer to the report instance `set_report_action` method

  For more information see [MDL-82936](https://tracker.moodle.org/browse/MDL-82936)
- Use of the `course_completion` table is deprecated in the `completion` entity, please use `course_completions` instead

  For more information see [MDL-84135](https://tracker.moodle.org/browse/MDL-84135)

### Removed

- The following deprecated report entity elements have been removed:

  - `comment:context`
  - `comment:contexturl`
  - `enrolment:method` (plus enrolment formatter `enrolment_name` method)
  - `enrolment:role`
  - `file:context`
  - `file:contexturl`
  - `instance:context` (tag)
  - `instance:contexturl` (tag)

  Use of the `context` table is also deprecated in the `file` and `instance` (tag) entities

  For more information see [MDL-78118](https://tracker.moodle.org/browse/MDL-78118)
- Various Oracle-specific support/workarounds in APIs and component report entities have been removed

  For more information see [MDL-80173](https://tracker.moodle.org/browse/MDL-80173)
- Final removal of support for `get_default_table_aliases` method. Entities must now implement `get_default_tables`, which is now abstract, to define the tables they use

  For more information see [MDL-80430](https://tracker.moodle.org/browse/MDL-80430)

## 4.5

### Added

- The return type of the `set_checkbox_toggleall` callback, defined by system reports, can now be null. Use if the checkbox should not be shown for the row.

  For more information see [MDL-52046](https://tracker.moodle.org/browse/MDL-52046)
- System reports now support native entity column aggregation via each columns `set_aggregation()` method

  For more information see [MDL-76392](https://tracker.moodle.org/browse/MDL-76392)
- The following external methods now return tags data relevant to each custom report:
    - `core_reportbuilder_list_reports`
    - `core_reportbuilder_retrieve_report`

  For more information see [MDL-81433](https://tracker.moodle.org/browse/MDL-81433)
- Added a new database helper method `sql_replace_parameters` to help ensure uniqueness of parameters within a SQL expression.

  For more information see [MDL-81434](https://tracker.moodle.org/browse/MDL-81434)
- A new static method, `\core_reportbuilder\local\helpers\format::format_time()`, has been added for use in column callbacks that represent a duration of time (for example "3 days 4 hours").

  For more information see [MDL-82466](https://tracker.moodle.org/browse/MDL-82466)
- The following methods have been moved from `\core_reportbuilder\datasource` class to its parent class `\core_reportbuilder\base` to make them available for use in system reports:

    - `add_columns_from_entity()`
    - `add_filters_from_entity()`
    - `report_element_search()`

  For more information see [MDL-82529](https://tracker.moodle.org/browse/MDL-82529)

### Changed

- In order to better support float values in filter forms, the following filter types now cast given SQL prior to comparison:

    - `duration`
    - `filesize`
    - `number`

  For more information see [MDL-81168](https://tracker.moodle.org/browse/MDL-81168)
- The base datasource `\core_reportbuilder\datasource::add_all_from_entities()` method accepts a new optional `array $entitynames` parameter to specify which entities to add elements from.

  For more information see [MDL-81330](https://tracker.moodle.org/browse/MDL-81330)
- All time-related code has been updated to the PSR-20 Clock interface, as such the following methods no longer accept a `$timenow` parameter (instead please use `\core\clock` dependency injection):
  - `core_reportbuilder_generator::create_schedule`
  - `core_reportbuilder\local\helpers\schedule::create_schedule()`
  - `core_reportbuilder\local\helpers\schedule::calculate_next_send_time()`

  For more information see [MDL-82041](https://tracker.moodle.org/browse/MDL-82041)
- The following classes have been moved to use the new exception API as a L2 namespace:

  | Old class                                           | New class                                                     |
  | -----------                                         | -----------                                                   |
  | `\core_reportbuilder\report_access_exception`       | `\core_reportbuilder\exception\report_access_exception`       |
  | `\core_reportbuilder\source_invalid_exception`      | `\core_reportbuilder\exception\source_invalid_exception`      |
  | `\core_reportbuilder\source_unavailable_exception`  | `\core_reportbuilder\exception\source_unavailable_exception`  |

  For more information see [MDL-82133](https://tracker.moodle.org/browse/MDL-82133)

### Removed

- Support for the following entity classes, renamed since 4.1, have now been removed completely:

  - `\core_admin\local\entities\task_log`
  - `\core_cohort\local\entities\cohort`
  - `\core_cohort\local\entities\cohort_member`
  - `\core_course\local\entities\course_category`
  - `\report_configlog\local\entities\config_change`

  For more information see [MDL-74583](https://tracker.moodle.org/browse/MDL-74583)
- The following previously deprecated local helper methods have been removed and can no longer be used:
    - `\core_reportbuilder\local\helpers\audience::get_all_audiences_menu_types()`
    - `\core_reportbuilder\local\helpers\report::get_available_columns()`

  For more information see [MDL-76690](https://tracker.moodle.org/browse/MDL-76690)

# core_courseformat (subsystem / plugintype) Upgrade notes

## 4.5dev

### Added

- The constructor of `core_courseformat\output\local\state\cm` has been updated to accept a new optional parameter, `$istrackeduser`.
  If `istrackeduser` is pre-computed for the course module's course, it can be provided here to avoid an additional function call.

  For more information see [MDL-81610](https://tracker.moodle.org/browse/MDL-81610)
- New $disabled parameter has been added to select, select_optgroup and select_option html_writers to create disabled option elements.

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)
- New \core_courseformat\output\local\content\basecontrolmenu class has been created. Existing \core_courseformat\output\local\content\cm\controlmenu and \core_courseformat\output\local\content\section\controlmenu classes extend the new \core_courseformat\output\local\content\basecontrolmenu class.

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)
- New \core_courseformat\output\local\content\cm\delegatedcontrolmenu class has been created extending \core_courseformat\output\local\content\basecontrolmenu class to render delegated section action menu combining section and module action menu.

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)

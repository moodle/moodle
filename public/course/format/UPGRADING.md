# core_courseformat (subsystem / plugintype) Upgrade notes

## 5.2dev

### Added

- Add delete method to the core_courseformat\cmactions

  For more information see [MDL-86856](https://tracker.moodle.org/browse/MDL-86856)
- Add set_groupmode method to the core_courseformat\cmactions (course format actions)

  For more information see [MDL-86857](https://tracker.moodle.org/browse/MDL-86857)
- Added `set_marker` and `remove_all_markers` methods to the `core_courseformat\sectionactions` class.

  For more information see [MDL-86860](https://tracker.moodle.org/browse/MDL-86860)
- Added the `set_visibility` method to the `core_courseformat\sectionactions` class. To optimize performance, this method does not return the list of affected resources, avoiding unnecessary database queries since the return value is unused.

  For more information see [MDL-86861](https://tracker.moodle.org/browse/MDL-86861)

### Changed

- The `$cm` attribute in `activityoverviewbase` has been updated to public visibility, allowing direct access to the course module instance

  For more information see [MDL-86660](https://tracker.moodle.org/browse/MDL-86660)
- A new `available` attribute has been added to `activityname_exporter` class. It allows the external API to return the activity's availability status relative to the current user.

  For more information see [MDL-86660](https://tracker.moodle.org/browse/MDL-86660)
- Two new public static methods have been added to the `overviewtable` class: - `is_cm_displayable`: Determines if a course module should be listed in the overview table. - `is_cm_available`: Checks if a course module is accessible to the user (and should therefore be rendered as a link).

  For more information see [MDL-86660](https://tracker.moodle.org/browse/MDL-86660)
- Subsections are now always displayed inline within their respective sections (the separate subsection page is no longer used). Descriptions are no longer shown for delegated sections.

  For more information see [MDL-87276](https://tracker.moodle.org/browse/MDL-87276)

### Deprecated

- The `set_section_visible` function has been deprecated and should no longer be used. Please consider using the equivalent method, `set_visibility`, in `core_courseformat\local\sectionactions` instead.

  For more information see [MDL-86861](https://tracker.moodle.org/browse/MDL-86861)

## 5.1

### Added

- From now on, the activity chooser will use core_courseformat_get_section_content_items to get the available modules for a specific section

  For more information see [MDL-80295](https://tracker.moodle.org/browse/MDL-80295)
- Added new core_courseformat\output\local\overview\overviewdialog output class to create dialog elements in the course overview page. Overview dialog will display a combination of title, description and a list of items (label: value).

  For more information see [MDL-83896](https://tracker.moodle.org/browse/MDL-83896)
- A new interface, `main_activity_interface`, is now available. Course format plugins should implement it if they intend to display only a single activity in the course page.

  For more information see [MDL-85433](https://tracker.moodle.org/browse/MDL-85433)
- The core_courseformat\local\overview\overviewfactory now includes a new method, activity_has_overview_integration, which determines if a module supports overview integration.

  For more information see [MDL-85509](https://tracker.moodle.org/browse/MDL-85509)
- New needs_filtering_by_groups() and get_groups_for_filtering() had been created in activityoverviewbase class for a better management of groups filtering in Activities overview page by activities.   needs_filtering_by_groups() returns whether the user needs to filter by groups in the current module, and get_groups_for_filtering() returns which is the filter the user should use with groups API.

  For more information see [MDL-85852](https://tracker.moodle.org/browse/MDL-85852)
- A new has_error() function has been created in activityoverviewbase class to raise when a user is trying to check information about a module set as SEPARATE_GROUPS but the user is not in any group.

  For more information see [MDL-85852](https://tracker.moodle.org/browse/MDL-85852)
- New optional $nogroupserror parameter has been added to activityname class constructor. A set_nogroupserror() setter to change the value after the constructor has been also added.

  For more information see [MDL-85852](https://tracker.moodle.org/browse/MDL-85852)
- Added new `core_courseformat\output\local\overview\overviewaction` output class to create action buttons that now include a badge right next to the button text. It essentially extends the existing action_link class to add a badge, making important actions stand out more on the course overview. Plus, this new structure also makes these badged action links easier to export this information for web services.

  For more information see [MDL-85981](https://tracker.moodle.org/browse/MDL-85981)
- Add a new modinfo::get_instance_of() to retrieve an instance of a cm via its name and instance id. Add a new modinfo::sort_cm_array() to sort an array of cms in their order of appearance in the course page. Replaces calls to get_course_and_cm_from_instance() and get_instances_of() whenever it was just used to retrieve a single instance of a cm.

  For more information see [MDL-86021](https://tracker.moodle.org/browse/MDL-86021)
- The `core_course\output\activitychooserbutton` has been moved to `core_courseformat\output\local\activitychooserbutton` . From now on, format plugins can provide alternative outputs for this element. Also, all the javascript and templates related to the activity chooser are now located inside the core_courseformat subsystem.

  For more information see [MDL-86337](https://tracker.moodle.org/browse/MDL-86337)
- All activity chooser related code has been moved to the `core_courseformat` subsystem. This includes all templates, javascript, and the main output class. If your theme overrides any of these, you will need to update your code accordingly.

  For more information see [MDL-86337](https://tracker.moodle.org/browse/MDL-86337)

### Changed

- The param $maxsections of get_num_sections_data in addsection output is not used anymore. If your format overrides this method, you should add a default value 0 to be consistent with the new implementation.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)

### Deprecated

- The maxsections setting is now considered deprecated and will be removed in Moodle 6.0. Consider implementing your own setting in your format plugin if needed.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)
- The format base method get_max_sections has been deprecated, as the maxsections setting is also deprecated and no longer in use.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)
- The course format "numsections" option to increment and decrement the number of sections of the course one by one is now deprecated and will be removed in Moodle 6.0.

  For more information see [MDL-85284](https://tracker.moodle.org/browse/MDL-85284)

## 5.0

### Added

- A new core_courseformat\base::get_generic_section_name method is created to know how a specific format name the sections. This method is also used by plugins to know how to name the sections instead of using using a direct get_string on "sectionnamer" that may not exists.

  For more information see [MDL-82349](https://tracker.moodle.org/browse/MDL-82349)
- A new course/format/update.php url is added as a non-ajax alternative to the core_courseformat_course_update webservice

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- Add core_courseformat\base::invalidate_all_session_caches to reset course editor cache for all users when course is changed. This method can be used as an alternative to core_courseformat\base::session_cache_reset for resetting the cache for the current user  in case the change in the course should be reflected for all users.

  For more information see [MDL-83185](https://tracker.moodle.org/browse/MDL-83185)
- Add after_course_content_updated hook triggered when a course content is updated (module modified, ...) through edition.

  For more information see [MDL-83542](https://tracker.moodle.org/browse/MDL-83542)

### Changed

- From now on, deleting an activity without Ajax will be consistent with deleting an activity using Ajax. This ensures that all activity deletions will use the recycle bin and avoid code duplication. If your format uses the old non-Ajax method to bypass the recycle bin it won't work anymore as the non-Ajax deletions are now handled in course/format/update.php.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)

### Deprecated

- The state actions section_move and all related functions are final deprecated and cannot be used anymore. Use the newer section_move_after from now on.

  For more information see [MDL-80116](https://tracker.moodle.org/browse/MDL-80116)
- The core_courseformat::base get_section_number and set_section_number are now final deprecated. Use get_sectionum and set_sectionnum instead.

  For more information see [MDL-80116](https://tracker.moodle.org/browse/MDL-80116)
- All course editing YUI modules are now deprecated. All course formats not using components must migrate before 6.0. Follow the devdocs guide https://moodledev.io/docs/5.0/apis/plugintypes/format/migration to know how to proceed.

  For more information see [MDL-82341](https://tracker.moodle.org/browse/MDL-82341)
- The core_courseformat\base::get_non_ajax_cm_action_url is now deprecated. Use get_update_url instead.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- Many get actions from course/view.php and course/mod.php are now deprecated. Use the new course/format/update.php instead to replace all direct edit urls  in your code. The affected actions are: indent, duplicate, hide, show, stealth, delete, groupmode and marker (highlight). The course/format/updates.php uses the same parameters as the core_courseformat_course_update webservice

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- Deprecate the use of element ID selectors in favor of querySelector for Reactive component initialisation. We will use '#id' instead of 'id' for example.

  For more information see [MDL-83339](https://tracker.moodle.org/browse/MDL-83339)
- The core_courseformat_create_module web service has been deprecated. Please use core_courseformat_new_module as its replacement.

  For more information see [MDL-83469](https://tracker.moodle.org/browse/MDL-83469)
- The state mutation addModule, primarily used for creating mod_subsection instances, has been deprecated. Replace it with newModule. Additionally, all course formats using links with data-action="addModule" must be updated to use data-action="newModule" and include a data-sectionid attribute specifying the target section ID.

  For more information see [MDL-83469](https://tracker.moodle.org/browse/MDL-83469)
- Using arrays to define course menu items is deprecated. All course formats that extend the section or activity control menus (format_NAME\output\courseformat\content\section\controlmenu or format_NAME\output\courseformat\cm\section\controlmenu) should return standard action_menu_link objects instead.

  For more information see [MDL-83527](https://tracker.moodle.org/browse/MDL-83527)
- The externservercourse.php feature used to override the course view page has been deprecated in favor of using hooks. The following hooks are available to do  something similar: \core_course\hook\before_course_viewed.

  For more information see [MDL-83764](https://tracker.moodle.org/browse/MDL-83764)

### Removed

- Protected function `core_courseformat\output\local\content\section\availability::availability_info()` has been fully removed. Use `core_courseformat\output\local\content\section\availability::get_availability_data()` instead.

  For more information see [MDL-78489](https://tracker.moodle.org/browse/MDL-78489)
- The old UI for moving activities and sections without javascript is not avaiable anymore from the actions dropdown. From now, on the only UI to move activities and sections is using the move action in the course editor. Format plugins can still use the old links to make the "move here" elements appear, but they will show deprecated messages. All the non-ajax moving will be removed in Moodle 6.0.

  For more information see [MDL-83562](https://tracker.moodle.org/browse/MDL-83562)

### Fixed

- HTML IDs relating to section collapse/expand have been changed in the course format templates.
  - core_courseformat/local/content/section/header #collapssesection{{num}} has been changed to #collapsesectionid{{id}}
  - core_courseformat/local/content/section/content #coursecontentcollapse{{num}} had been changed to #coursecontentcollapseid{{id}}

  For more information see [MDL-82679](https://tracker.moodle.org/browse/MDL-82679)

## 4.5

### Added

- The constructor of `\core_courseformat\output\local\state\cm` has been updated to accept a new optional parameter, `$istrackeduser`.
  If `istrackeduser` is pre-computed for the course module's course, it can be provided here to avoid an additional function call.

  For more information see [MDL-81610](https://tracker.moodle.org/browse/MDL-81610)
- Added new `core_courseformat_create_module` webservice to create new module (with quickcreate feature) instances in the course.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- A new `$disabled` parameter has been added to the following `html_writer` methods:

  - `\core\output\html_writer::select()`
  - `\core\output\html_writer::select_optgroup()`
  - `\core\output\html_writer::select_option()`

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)
- A new class, `\core_courseformat\output\local\content\basecontrolmenu`, has been created.
  The following existing classes extend the new class:

   - `\core_courseformat\output\local\content\cm\controlmenu`
   - `\core_courseformat\output\local\content\section\controlmenu`

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)
- Course sections now use an action menu to display possible actions that a user may take in each section. This action menu is rendered using the `\core_courseformat\output\local\content\cm\delegatedcontrolmenu` renderable class.

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)

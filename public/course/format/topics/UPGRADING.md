# format_topics Upgrade notes

## 5.1

### Added

- Now the custom sections format won't ask for initial sections on the creation form. Instead it will use the system number of sections settings directly.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)

## 5.0

### Deprecated

- In format topics, the section controlmenu class deprecates the get_course_url method. This may affect formats extending the topics format and adding extra items to the section menu. Use $this->format->get_update_url instead.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- The get_highlight_control in the section controlmenu class is now deprecated. Use get_section_highlight_item instead

  For more information see [MDL-83527](https://tracker.moodle.org/browse/MDL-83527)

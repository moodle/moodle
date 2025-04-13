# format_topics Upgrade notes

## 5.0

### Deprecated

- In format topics, the section controlmenu class deprecates the get_course_url method. This may affect formats extending the topics format and adding extra items to the section menu. Use $this->format->get_update_url instead.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- The get_highlight_control in the section controlmenu class is now deprecated. Use get_section_highlight_item instead

  For more information see [MDL-83527](https://tracker.moodle.org/browse/MDL-83527)

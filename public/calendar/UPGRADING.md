# core_calendar (subsystem) Upgrade notes

## 5.0

### Deprecated

- Initial deprecation of calendar_sub_month. Use \core_calendar\type_factory::get_calendar_instance()->get_prev_month() instead.

  For more information see [MDL-79434](https://tracker.moodle.org/browse/MDL-79434)
- calendar_day_representation(), calendar_time_representation() and calendar_format_event_time() functions have been deprecated and can't be used anymore. Use humandate and humantimeperiod classes instead.

  For more information see [MDL-83873](https://tracker.moodle.org/browse/MDL-83873)
- calendar_get_courselink(), calendar_events_by_day() functions have been deprecated.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)
- Initial deprecation of calendar_add_month(). Use \core_calendar\type_factory::get_calendar_instance()->get_next_month() instead.

  For more information see [MDL-84657](https://tracker.moodle.org/browse/MDL-84657)

### Removed

- Final removal of calendar functions:
    - calendar_top_controls()
    - calendar_get_link_previous()
    - calendar_get_link_next()

  For more information see [MDL-79434](https://tracker.moodle.org/browse/MDL-79434)
- prepare_for_view(), calendar_add_event_metadata() functions have been removed.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)
- core_calendar_renderer::event() method has been removed.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)

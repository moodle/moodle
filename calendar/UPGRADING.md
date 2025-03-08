# core_calendar (subsystem) Upgrade notes

## 5.0dev+

### Deprecated

- calendar_day_representation(), calendar_time_representation() and calendar_format_event_time() functions have been deprecated and can't be used anymore. Use humandate and humantimeperiod classes instead.

  For more information see [MDL-83873](https://tracker.moodle.org/browse/MDL-83873)
- calendar_get_courselink(), calendar_events_by_day() functions have been deprecated.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)

### Removed

- prepare_for_view(), calendar_add_event_metadata() functions have been removed.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)
- core_calendar_renderer::event() method has been removed.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)

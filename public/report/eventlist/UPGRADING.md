# report_eventlist Upgrade notes

## 5.2dev

### Removed

- - The following methods have been removed from `public/report/eventlist/classes/list_generator.php`:
    - `\report_eventlist_list_generator::get_core_events_list()`
    - `\report_eventlist_list_generator::get_non_core_event_list()`

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)

## 4.5

### Deprecated

- The following deprecated methods in `report_eventlist_list_generator` have been removed:
  - `\report_eventlist_list_generator::get_core_events_list()`
  - `\report_eventlist_list_generator::get_non_core_event_list()`

  For more information see [MDL-72786](https://tracker.moodle.org/browse/MDL-72786)

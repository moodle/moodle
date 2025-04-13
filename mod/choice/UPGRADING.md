# mod_choice Upgrade notes

## 5.0

### Changed

- The WebService `mod_choice_get_choice_results` has a new parameter `groupid` that allows specifying the group to get the results for. The default behaviour hasn't changed: if a choice has groups and the parameter isn't specified the WebService will return the results for the active group.

  For more information see [MDL-78449](https://tracker.moodle.org/browse/MDL-78449)
- The function `choice_get_response_data` has a new parameter that allows specifying the group to get the results for. The default behaviour hasn't changed: if a choice has groups and the parameter isn't used, the function will return the results for the active group.

  For more information see [MDL-78449](https://tracker.moodle.org/browse/MDL-78449)

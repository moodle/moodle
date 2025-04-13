# tool_behat Upgrade notes

## 5.0

### Added

- New Behat step `\behat_general::the_url_should_match()` has been added to allow checking the current URL. You can use it to check whether a user has been redirected to the expected location.
  e.g. `And the url should match "/mod/forum/view\.php\?id=[0-9]+"`

  For more information see [MDL-83617](https://tracker.moodle.org/browse/MDL-83617)

## 4.5

### Added

- Behat tests are now checking for deprecated icons. This check can be disabled by using the `--no-icon-deprecations` option in the behat CLI.

  For more information see [MDL-82212](https://tracker.moodle.org/browse/MDL-82212)

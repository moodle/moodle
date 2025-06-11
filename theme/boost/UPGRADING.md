# theme_boost Upgrade notes

## 4.5

### Added

- Bridged `theme-color-level` using a new `shift-color` function to prepare for its deprecation in Boostrap 5.

  For more information see [MDL-81816](https://tracker.moodle.org/browse/MDL-81816)
- Upon upgrading Font Awesome from version 4 to 6, the solid family was selected by default.

  Support for the `regular`, and `brands` families of icons has now been added, allowing icons defined with `\core\outut\icon_system::FONTAWESOME` to use them.

  Icons can select the FontAwesome family (`fa-regular`, `fa-brands`, `fa-solid`) by using the relevant class name when display the icon.

  For more information see [MDL-82210](https://tracker.moodle.org/browse/MDL-82210)

### Changed

- The Bootstrap `.no-gutters` class is no longer used, use `.g-0`  instead.

  For more information see [MDL-81818](https://tracker.moodle.org/browse/MDL-81818)
- The `.page-header-headings` CSS class now has a background colour applied to the maintenance and secure layouts.
  You may need to override this class in your maintenance and secure layouts if both of the following are true:
  - Your theme plugin inherits from `theme_boost` and uses this CSS class
  - Your theme plugin applies a different styling for the page header for the maintenance and secure layouts.

  For more information see [MDL-83047](https://tracker.moodle.org/browse/MDL-83047)

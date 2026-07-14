# theme_boost Upgrade notes

## 5.3dev

### Changed

- The default UI typeface for Boost has changed from the system-ui font stack to Noto Sans. Noto Sans is now self-hosted under `theme/boost/fonts/` and declared via `@font-face` in `theme/boost/scss/moodle/fonts.scss`. The latin and latin-ext subsets are included (normal and italic, weight 100-900). The `$font-family-sans-serif` Bootstrap variable is now set from the `$mds-font-family-base` MDS token. Child themes that override `$font-family-sans-serif` are unaffected. Child themes that rely on the system-ui fallback behaviour will now render Noto Sans instead.

  For more information see [MDL-88412](https://tracker.moodle.org/browse/MDL-88412)

### Deprecated

- AMD modules **must not** depend upon core Bootstrap modules from
  `theme_boost/bootstrap/*`. Direct loading of Bootstrap submodules
  is not supported by the Bootstrap project.

  Instead of:
  ```js
  import Tooltip from 'theme_boost/bootstrap/tooltip';
  ```

  You can use either of the following approaches:

  ### For Moodle 5.2 and earlier

  ```js
  // For Moodle 5.2 and earlier:
  // This option will be supported until Moodle 7.0 when it will be removed.
  // You are encouraged to switch to the new approach as soon as possible to
  // avoid last-minute issues when upgrading to Moodle 7.0.
  import {Tooltip} from 'theme_boost/index';

  // For Moodle 5.3 and later
  import {Tooltip} from 'bootstrap';
  ```

  ### Important note

  The `util` and `dom` helper directories **must** still directly load modules.
  These modules are _not_ a part of the public Bootstrap API.
  Use of these modules is at your own risk.

  To use these modules you can use:

  ```js
  // Moodle 5.2 and earlier:
  import EventHandler from 'theme_boost/bootstrap/dom/event-handler';

  // Moodle 5.3 and later:
  import EventHandler from 'bootstrap/dom/event-handler';
  ```

  For more information see [MDL-88766](https://tracker.moodle.org/browse/MDL-88766)

## 5.2

### Removed

- - The `public/theme/boost/templates/flat_navigation.mustache` file has been removed.
  - The `public/theme/boost/templates/nav-drawer.mustache` file has been removed.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)

## 5.1

### Added

- Theme can now inherit from their grand-parent and parents.  So if a child theme inherit from a parent theme that declares a new layout, the child theme can use it without redeclaring it. Also inheritance for layout uses the expected grandparent > parent > child with precedence to the child theme.

  For more information see [MDL-79319](https://tracker.moodle.org/browse/MDL-79319)
- Tables affected by unwanted styling (e.g., borders) from the reset of Bootstrap _reboot.scss styles can now opt out and preserve the original behavior by adding the styleless .table-reboot class.

  For more information see [MDL-86548](https://tracker.moodle.org/browse/MDL-86548)

### Deprecated

- The `core:e/text_highlight` and `core:e/text_highlight_picker` icons are deprecated and will be removed in Moodle 6.0. The UX team recommended this change to reduce visual clutter and improve readability. The icons were removed because they didn't indicate status changes, were repetitive across all notifications, and took up space that could be used for more content.

  For more information see [MDL-85146](https://tracker.moodle.org/browse/MDL-85146)

## 5.0

### Changed

- From now on, themes can customise the activity icon colours using simple CSS variables. The new variables are $activity-icon-administration-bg, $activity-icon-assessment-bg, $activity-icon-collaboration-bg, $activity-icon-communication-bg, $activity-icon-content-bg, $activity-icon-interactivecontent-bg. All previous `$activity-icon-*-filter` elements can be removed, as they are no longer in use.

  For more information see [MDL-83725](https://tracker.moodle.org/browse/MDL-83725)

### Deprecated

- Added new bs4-compat SCSS file (initially deprecated) to help third-party plugins the migration process from BS4 to BS5

  For more information see [MDL-80519](https://tracker.moodle.org/browse/MDL-80519)
- New `theme_boost/bs4-compat` JS module added (directly deprecated) to allow third-party-plugins to directly convert old Bootstrap 4 data attribute syntax to the new Bootstrap 5

  For more information see [MDL-84450](https://tracker.moodle.org/browse/MDL-84450)

### Removed

- Remove SCSS deprecated in 4.4

  For more information see [MDL-80156](https://tracker.moodle.org/browse/MDL-80156)
- Remove chat and survey styles. Important note: the styles have been moved to the plugins as CSS files (and not SCSS) so themes might now need to override the mod_chat and mod_survey styles specifically as css does not have any definition for primary, gray and other colors accessible in the original scss version.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

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

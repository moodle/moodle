# theme_boost Upgrade notes

## 4.5dev

### Changed

- Bootstrap .no-gutters class is no longer used, use .g-0  instead.

  For more information see [MDL-81818](https://tracker.moodle.org/browse/MDL-81818)

### Added

- Upon upgrading Font Awesome from version 4 to 6, the solid family was selected by default. However, FA6 includes additional families such as regular and brands. Support for these families has now been integrated, allowing icons defined with icon_system::FONTAWESOME to use them. Icons can add the FontAwesome family (fa-regular, fa-brands, fa-solid) near the icon name to display it using this styling.

  For more information see [MDL-82210](https://tracker.moodle.org/browse/MDL-82210)

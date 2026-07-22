# block_myoverview Upgrade notes

## 5.2.1+

### Changed

- For the correct display of title and context menus, fields like fullname are returned with numeric HTML entities (&#60;) instead of named entities (&lt;) and unencoded quotes.

  For more information see [MDL-79755](https://tracker.moodle.org/browse/MDL-79755)

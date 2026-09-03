# block_timeline Upgrade notes

## 5.3dev

### Added

- The legacy AMD/Mustache frontend for the Timeline block has been replaced with an ESM + React implementation.

  For more information see [MDL-88287](https://tracker.moodle.org/browse/MDL-88287)

### Removed

- block_timeline\output\main, block_timeline\output\renderer, and the block's external service classes (see db/services.php) have been removed entirely, with no deprecation stub — themes overriding the renderer, or code calling the removed external functions, must be updated.

  For more information see [MDL-88287](https://tracker.moodle.org/browse/MDL-88287)


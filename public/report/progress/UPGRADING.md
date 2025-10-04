# report_progress Upgrade notes

## 5.1

### Added

- Add download widget for report to download multiple formats.

  For more information see [MDL-83838](https://tracker.moodle.org/browse/MDL-83838)

### Changed

- Added a new optional parameter $activegroup to render_groups_select()

  For more information see [MDL-82381](https://tracker.moodle.org/browse/MDL-82381)

### Deprecated

- `report_progress\output\renderer::render_download_buttons` No replacement. We no longer need to render the download custom button links.

  For more information see [MDL-83838](https://tracker.moodle.org/browse/MDL-83838)

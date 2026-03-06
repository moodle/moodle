# block_html Upgrade notes

## 5.1.3+

### Changed

- Treat Dashboard (pagetype 'my-index') as trusted in web services so get_content_for_external preserves embedded HTML (e.g. iframes) on user Dashboard.

  For more information see [MDL-85322](https://tracker.moodle.org/browse/MDL-85322)


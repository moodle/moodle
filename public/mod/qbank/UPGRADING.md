# mod_qbank Upgrade notes

## 5.1

### Changed

- The bulk_action_base class has gotten a get_bulk_action_classes function to let bulk actions specify additional classes to add to the bulk action menu entry. If none is defined in the child, '' is returned.

  For more information see [MDL-84548](https://tracker.moodle.org/browse/MDL-84548)

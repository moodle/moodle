# qbank_columnsortorder Upgrade notes

## 5.2dev

### Removed

- The Behat selector `column move handle` for the `qbank_columnsortorder` plugin has been removed.
  When interacting with the column's move handle, please use the move handle's accessible name and type.
  For example: - `And I drag "Move Created by" "button" and I drop it in "Move T" "button"`

  For more information see [MDL-86855](https://tracker.moodle.org/browse/MDL-86855)

# gradereport_singleview Upgrade notes

## 5.1

### Added

- The `grade/report/singleview/js/singleview.js` file has been removed. And the `grade/report/singleview/amd/src/singleview.js` file has been added. The new file is converted from YUI to native JS.

  For more information see [MDL-84071](https://tracker.moodle.org/browse/MDL-84071)

## 4.5

### Deprecated

- The `gradereport_singleview/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

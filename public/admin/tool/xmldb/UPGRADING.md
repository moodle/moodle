# tool_xmldb Upgrade notes

## 5.2dev

### Changed

- Generated `rename_field(...)` upgrade step code now checks for field existence, to ensure it can be executed multiple times

  For more information see [MDL-87158](https://tracker.moodle.org/browse/MDL-87158)


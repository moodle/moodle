# tool_brickfield Upgrade notes

## 5.0

### Deprecated

- tool_brickfield\local\areas\core_question\answerbase::find_system_areas

  No replacement. System context no longer a valid context to assign a question category

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- tool_brickfield\local\areas\core_question\base::find_system_areas

  No replacement. System context no longer a valid context to assign a question category

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)

### Removed

- Remove chat and survey support from tool_brickfield.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

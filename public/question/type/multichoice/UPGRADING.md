# qtype_multichoice Upgrade notes

## 5.1

### Changed

- Restrict override of margin-bottom for fitem_id_answer_* and fitem_id_fraction_* divs to own edit form. Question type plugins currently benefitting from the unlimited style override will need to change their styles.css accordingly. An example can be found in calculatedmulti's style sheet.

  For more information see [MDL-85240](https://tracker.moodle.org/browse/MDL-85240)

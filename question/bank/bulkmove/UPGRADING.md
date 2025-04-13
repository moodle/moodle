# qbank_bulkmove Upgrade notes

## 5.0

### Deprecated

- qbank_bulkmove/helper::get_displaydata

  Superceded by a modal and webservice, see qbank_bulkmove/modal_question_bank_bulkmove and core_question_external\move_questions

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- qbank_bulkmove\output\renderer::render_bulk_move_form

  Superceded by qbank_bulkmove\output\bulk_move

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)

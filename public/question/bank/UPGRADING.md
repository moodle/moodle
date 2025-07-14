# qbank (plugin type) Upgrade notes

## 5.0

### Removed

- Final deprecation of:
    - qbank_managecategories\output\renderer::class
    - qbank_statistics\helper::calculate_average_question_discriminative_efficiency()
    - qbank_statistics\helper::calculate_average_question_discrimination_index()
    - qbank_statistics\helper::get_all_places_where_questions_were_attempted()
    - qbank_statistics\helper::calculate_average_question_stats_item()
    - qbank_statistics\helper::calculate_average_question_facility()
    - qbank_statistics\helper::load_statistics_for_place()
    - qbank_statistics\helper::extract_item_value()
    - template qbank_managecategories/category_condition_advanced
    - template qbank_managecategories/category_condition
    - template qbank_managecategories/listitem

  For more information see [MDL-78090](https://tracker.moodle.org/browse/MDL-78090)

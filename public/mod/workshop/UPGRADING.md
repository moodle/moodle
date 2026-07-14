# mod_workshop Upgrade notes

## 5.3dev

### Deprecated

- Deprecated the Behat step `behat_mod_workshop::i_set_portfolio_instance_to`, please use `behat_portfolio::i_set_the_portfolio_instance_to` instead.

  For more information see [MDL-89069](https://tracker.moodle.org/browse/MDL-89069)

## 5.1

### Deprecated

- The function `workshop::count_submissions` has been deprecated and should no longer be used, use `workshop::count_all_submissions` instead.

  For more information see [MDL-84809](https://tracker.moodle.org/browse/MDL-84809)
- The function `workshop::count_assessments` has been deprecated and should no longer be used, use `workshop::count_all_assessments` instead.

  For more information see [MDL-84809](https://tracker.moodle.org/browse/MDL-84809)

@core_form
Feature: Repeated elements in moodleforms

  Scenario: Clicking button to add repeat elements creates repeat elements with the correct default values
    Given I log in as "admin"
    And I am on fixture page "/lib/form/tests/behat/fixtures/repeat_defaults_form.php"
    When I press "Add repeats"
    Then the following fields match these values:
      | testcheckbox[1]           | 1           |
      | testadvcheckbox[1]        | 1           |
      | testdate[1][day]          | 8           |
      | testdate[1][month]        | September   |
      | testdate[1][year]         | 2013        |
      | testdatetime[1][day]      | 8           |
      | testdatetime[1][month]    | September   |
      | testdatetime[1][year]     | 2013        |
      | testdatetime[1][hour]     | 10          |
      | testdatetime[1][minute]   | 30          |
      | testduration[1][number]   | 3           |
      | testduration[1][timeunit] | hours       |
      | testselect[1]             | B           |
      | testselectyes[1]          | Yes         |
      | testselectno[1]           | No          |
      | testtext[1]               | Testing 123 |

  Scenario: Functionality to delete an option in the repeated elements
    Given I log in as "admin"
    And I am on fixture page "/lib/form/tests/behat/fixtures/repeat_with_delete_form.php"
    And I set the field "Test text 1" to "value 1"
    When I press "Add repeats"
    Then the following fields match these values:
      | Test text 1 | value 1 |
      | Test text 2 | Testing |
    And I set the field "Test text 2" to "value 2"
    And I press "Add repeats"
    And the following fields match these values:
      | Test text 1 | value 1 |
      | Test text 2 | value 2 |
      | Test text 3 | Testing |
    And I set the field "Test text 3" to "value 3"
    And I press "Delete option 2"
    And the following fields match these values:
      | Test text 1 | value 1 |
      | Test text 3 | value 3 |
    And I should not see "Test text 2"
    And I should not see "Delete option 2"
    And I press "Save changes"
    And I should see "{\"0\":\"value 1\",\"2\":\"value 3\"}"

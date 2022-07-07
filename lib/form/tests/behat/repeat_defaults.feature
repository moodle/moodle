@core_form
Feature: Newly created repeat elements have the correct default values

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

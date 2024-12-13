@core @javascript
Feature: hide_if functionality in admin settings
  For admin settings using hide_if functionality
  As a user
  If I trigger the hide_if condition then the admin setting will be hidden

  Background:
    Given I log in as "admin"

  Scenario Outline: Admin settings are hidden when hide_if conditions dependent on a configmultiselect setting are met
    Given I am on fixture page "/lib/tests/behat/fixtures/multiselect_hide_if_admin_settingspage.php"
    When I set the field "s__multiselect1[]" to "<selection>"
    Then I <shouldSeeEq_> see "Hide if selection 'eq' []"
    And I <shouldSeeIn_> see "Hide if selection 'in' []"
    And I <shouldSeeNeq_> see "Hide if selection 'neq' []"
    And I <shouldSeeEq1> see "Hide if selection 'eq' ['1']"
    And I <shouldSeeIn1> see "Hide if selection 'in' ['1']"
    And I <shouldSeeNeq1> see "Hide if selection 'neq' ['1']"
    And I <shouldSeeEq12> see "Hide if selection 'eq' ['1', '2']"
    And I <shouldSeeIn12> see "Hide if selection 'in' ['1', '2']"
    And I <shouldSeeNeq12> see "Hide if selection 'neq' ['1', '2']"

    Examples:
      | selection          | shouldSeeEq_ | shouldSeeIn_ | shouldSeeNeq_ | shouldSeeEq1 | shouldSeeIn1 | shouldSeeNeq1 | shouldSeeEq12 | shouldSeeIn12 | shouldSeeNeq12 |
      |                    | should not   | should not   | should        | should       | should       | should not    | should        | should        | should not     |
      | Option 1           | should       | should       | should not    | should not   | should not   | should        | should        | should        | should not     |
      | Option 2           | should       | should       | should not    | should       | should       | should not    | should        | should        | should not     |
      | Option 1, Option 2 | should       | should       | should not    | should       | should       | should not    | should not    | should not    | should         |

@customfield @customfield_textarea
Feature: Managers can manage course custom fields textarea
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and sort custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration

  Scenario: Create a custom course textarea field
    When I click on "Add a new custom field" "link"
    And I click on "Text area" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    Then I should see "Test field"
    And I log out

  Scenario: Edit a custom course textarea field
    When I click on "Add a new custom field" "link"
    And I click on "Text area" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    And I click on "Edit" "link" in the "Test field" "table_row"
    And I set the following fields to these values:
      | Name | Edited field |
    And I press "Save changes"
    Then I should see "Edited field"
    And I should not see "Test field"
    And I log out

  @javascript
  Scenario: Delete a custom course textarea field
    When I click on "Add a new custom field" "link"
    And I click on "Text area" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    And I click on "Delete" "link" in the "Test field" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then I should not see "Test field"
    And I log out

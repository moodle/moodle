@customfield @customfield_select
Feature: Managers can manage course custom fields select
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and sort custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration

  Scenario: Create a custom course select field
    When I click on "Add a new custom field" "link"
    And I click on "Dropdown menu" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I set the field "Menu options (one per line)" to multiline:
    """
    a
    b
    """
    And I press "Save changes"
    Then I should see "Test field"
    And I log out

  Scenario: Edit a custom course select field
    When I click on "Add a new custom field" "link"
    And I click on "Dropdown menu" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I set the field "Menu options (one per line)" to multiline:
    """
    a
    b
    """
    And I press "Save changes"
    And I click on "Edit" "link" in the "Test field" "table_row"
    And I set the following fields to these values:
      | Name | Edited field |
    And I press "Save changes"
    Then I should see "Edited field"
    And I should not see "Test field"
    And I log out

  @javascript
  Scenario: Delete a custom course select field
    When I click on "Add a new custom field" "link"
    And I click on "Dropdown menu" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I set the field "Menu options (one per line)" to multiline:
    """
    a
    b
    """
    And I press "Save changes"
    And I click on "Delete" "link" in the "Test field" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then I should not see "Test field"
    And I log out

  Scenario: Validation of custom course select field configuration
    When I click on "Add a new custom field" "link"
    And I click on "Dropdown menu" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    And I should see "Please provide at least two options, with each on a new line." in the "Menu options (one per line)" "form_row"
    And I set the field "Menu options (one per line)" to multiline:
    """
    a
    b
    """
    And I set the field "Default value" to "c"
    And I press "Save changes"
    And I should see "The default value must be one of the options from the list above" in the "Default value" "form_row"
    And I set the field "Default value" to "b"
    And I press "Save changes"
    And "testfield" "text" should exist in the "Test field" "table_row"
    And I log out

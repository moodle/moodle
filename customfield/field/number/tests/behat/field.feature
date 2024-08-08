@customfield @customfield_number @javascript
Feature: Managers can manage course custom fields number
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and display number custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration

  Scenario: Create a custom course number field
    When I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    When I set the following fields to these values:
      | Name               | Number field |
      | Short name         | numberfield  |
      | Display template   | test         |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    Then I should see "The placeholder is not invalid"
    And I set the following fields to these values:
      | Name               | Number field |
      | Short name         | numberfield  |
      | Display template   | {value}      |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I should see "Number field"
    And I log out

  Scenario: Edit a custom course number field
    When I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    And I set the following fields to these values:
      | Name       | Number field |
      | Short name | numberfield  |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    Then I should see "Number field"
    And I click on "Edit" "link" in the "Number field" "table_row"
    And I set the following fields to these values:
      | Name | Edited number field |
    And I click on "Save changes" "button" in the "Updating Number field" "dialogue"
    Then I should see "Edited number field"
    And I log out

  Scenario: Delete a custom course number field
    When I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    And I set the following fields to these values:
      | Name       | Number field |
      | Short name | numberfield  |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I click on "Delete" "link" in the "Number field" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I wait until "Number field" "text" does not exist
    Then I should not see "Number field"
    And I log out

  Scenario Outline: A number field must shown correctly on course listing
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Number" "link"
    When I set the following fields to these values:
      | Name               | Test number |
      | Short name         | testnumber  |
      | Decimal places     | 2           |
      | Display template   | <template>  |
      | Display when zero  | <whenzero>  |
    And I click on "Save changes" "button" in the "Adding a new Number" "dialogue"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Test number | <fieldvalue> |
    And I press "Save and display"
    And I am on site homepage
    And I should see "Test number" in the ".customfields-container .customfieldname" "css_element"
    And I should see "<expectedvalue>" in the ".customfields-container .customfieldvalue" "css_element"
    Examples:
      | template  | whenzero    | fieldvalue | expectedvalue |
      | $ {value} | 0           | 150        | $ 150.00      |
      | {value}   | Free        | 0          | Free          |

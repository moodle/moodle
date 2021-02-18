@customfield @customfield_text @javascript
Feature: Managers can manage course custom fields text
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and sort custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration

  Scenario: Create a custom course text field
    When I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then I should see "Test field"
    And I log out

  Scenario: Edit a custom course text field
    When I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I click on "Edit" "link" in the "Test field" "table_row"
    And I set the following fields to these values:
      | Name | Edited field |
    And I click on "Save changes" "button" in the "Updating Test field" "dialogue"
    Then I should see "Edited field"
    And I navigate to "Reports > Logs" in site administration
    And I press "Get these logs"
    And I log out

  Scenario: Delete a custom course text field
    When I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I click on "Delete" "link" in the "Test field" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I wait until "Test field" "text" does not exist
    Then I should not see "Test field"
    And I log out

  Scenario: A text field with a link setting must show link on course listing
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | See more on website       |
      | Short name | testfield                 |
      | Visible to | Everyone                  |
      | Link       | https://www.moodle.org/$$ |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | See more on website | course/view.php?id=35 |
    And I press "Save and display"
    And I am on site homepage
    Then I should see "course/view.php?id=35" in the ".customfields-container .customfieldvalue a" "css_element"
    Then I should see "See more on website" in the ".customfields-container .customfieldname" "css_element"

  Scenario: A text field with a max length must validate it on course edit form
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
      | Maximum number of characters | 3          |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Test field | 1234 |
    And I press "Save and display"
    Then I should see "The maximum number of characters allowed in this field is 3."

  Scenario: A text field with a default value must be shown on listing but allow empty values that will not be shown
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name          | Test field  |
      | Short name    | testfield   |
      | Default value | testdefault |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I log out
    Then I log in as "teacher1"
    When I am on site homepage
    Then I should see "Test field: testdefault"
    When I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    Then the "value" attribute of "#id_customfield_testfield" "css_element" should contain "testdefault"
    When I set the following fields to these values:
      | Test field |  |
    And I press "Save and display"
    And I am on site homepage
    And I should not see "Test field"

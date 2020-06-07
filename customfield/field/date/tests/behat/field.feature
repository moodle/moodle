@customfield @customfield_date
Feature: Managers can manage course custom fields date
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and sort custom fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration

  Scenario: Create a custom course date field
    When I click on "Add a new custom field" "link"
    And I click on "Date and time" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    Then I should see "Test field"
    And I log out

  Scenario: Edit a custom course date field
    When I click on "Add a new custom field" "link"
    And I click on "Date and time" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    And I click on "[data-role='editfield']" "css_element"
    And I set the following fields to these values:
      | Name | Edited field |
    And I press "Save changes"
    Then I should see "Edited field"
    And I log out

  @javascript
  Scenario: Delete a custom course date field
    When I click on "Add a new custom field" "link"
    And I click on "Date and time" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I press "Save changes"
    And I click on "[data-role='deletefield']" "css_element"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then I should not see "Test field"
    And I log out

  @javascript
  Scenario: A date field makerd to include time must show those fields on course form
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    When I click on "Add a new custom field" "link"
    And I click on "Date and time" "link"
    And I set the following fields to these values:
      | Name         | Test field |
      | Short name   | testfield  |
      | Include time | 1          |
    And I press "Save changes"
    And I log out
    Then I log in as "teacher1"
    When I am on site homepage
    When I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then "#id_customfield_testfield_hour" "css_element" should be visible
    Then "#id_customfield_testfield_minute" "css_element" should be visible
    And I log out

  @javascript
  Scenario: A date field makerd to not include time must not show those fields on course form
    Given the following "users" exist:
      | username | firstname | lastname  | email                |
      | teacher1 | Teacher   | Example 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    When I click on "Add a new custom field" "link"
    And I click on "Date and time" "link"
    And I set the following fields to these values:
      | Name         | Test field |
      | Short name   | testfield  |
      | Include time |            |
    And I press "Save changes"
    And I log out
    Then I log in as "teacher1"
    When I am on site homepage
    When I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then "#id_customfield_testfield_hour" "css_element" should not be visible
    Then "#id_customfield_testfield_minute" "css_element" should not be visible
    And I log out

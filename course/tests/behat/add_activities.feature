@core @core_course
Feature: Add activities to courses
  In order to provide tools for students learning
  As a teacher
  I need to add activites to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | Course 1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | Course 1 | student |
      | student2 | Course 1 | student |

  @javascript
  Scenario: Add an activity to a course
    Given I am on the "Course 1" Course page logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
      | Description | Test database description |
      | ID number | TESTNAME |
      | Allow comments on entries | Yes |
    And I turn editing mode off
    Then I should not see "Adding a new"
    And I turn editing mode on
    And I open "Test name" actions menu
    And I click on "Edit settings" "link" in the "Test name" activity
    And I expand all fieldsets
    And the field "Name" matches value "Test name"
    And the field "ID number" matches value "TESTNAME"
    And the field "Allow comments on entries" matches value "Yes"

  @javascript
  Scenario: Add an activity supplying only the name
    Given I am on the "Course 1" Course page logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Test name"

  @javascript
  Scenario: Set activity description to required then add an activity supplying only the name
    Given the following config values are set as admin:
      | requiremodintro | 1 |
    And I am on the "Course 1" Course page logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Required"

  @javascript
  Scenario: The activity description should use the user's preferred editor on creation
    Given the following "user preferences" exist:
      | user   | preference   | value    |
      | admin  | htmleditor   | textarea |
    And I am logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Database" to section "3"
    Then the field "Description format" matches value "0"

  @javascript
  Scenario: The activity description should preserve the format used once edited (markdown version)
    Given the following "activities" exist:
      | activity   | name         | intro  | introformat | course   |
      | assign     | A4           | Desc 4 | 4           | Course 1 |
    And the following "user preferences" exist:
      | user   | preference   | value    |
      | admin  | htmleditor   | textarea |
    And I am logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    And I open "A4" actions menu
    When I click on "Edit settings" "link" in the "A4" activity
    Then the field "Description format" matches value "4"

  @javascript
  Scenario: The activity description should preserve the format used once edited (plain text version)
    Given the following "activities" exist:
      | activity   | name         | intro  | introformat | course   |
      | assign     | A2           | Desc 2 | 2           | Course 1 |
    And the following "user preferences" exist:
      | user   | preference   | value    |
      | admin  | htmleditor   | textarea |
    And I am logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    And I open "A2" actions menu
    When I click on "Edit settings" "link" in the "A2" activity
    Then the field "Description format" matches value "2"

@core @core_course
Feature: Add activities to courses
  In order to provide tools for students learning
  As a teacher
  I need to add activites to a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | Course 1  | topics |

  @javascript
  Scenario: Add an activity to a course
    Given I log in as "admin"
    When I add a data activity to course "Course 1" section "3" and I fill the form with:
      | Name                      | Test name                 |
      | Description               | Test database description |
      | ID number                 | TESTNAME                  |
      | Allow comments on entries | Yes                       |
      | Force language            | English                   |
    Then I should not see "Adding a new"
    And I turn editing mode on
    And I open "Test name" actions menu
    And I click on "Edit settings" "link" in the "Test name" activity
    And the following fields match these values:
      | Name                      | Test name    |
      | ID number                 | TESTNAME     |
      | Allow comments on entries | Yes          |
      | Force language            | English ‎(en)‎ |

  Scenario: Add an activity supplying only the name
    Given I log in as "admin"
    When I add a data activity to course "Course 1" section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Test name"

  Scenario: Set activity description to required then add an activity supplying only the name
    Given the following config values are set as admin:
      | requiremodintro | 1 |
    And I log in as "admin"
    And I add a data activity to course "Course 1" section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Required"

  Scenario: The activity description should use the user's preferred editor on creation
    Given the following "user preferences" exist:
      | user   | preference   | value    |
      | admin  | htmleditor   | textarea |
    And I am logged in as admin
    When I add a data activity to course "Course 1" section "3"
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

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
    Given I am on the "Course 1" Course page logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Database" to section "3" and I fill the form with:
      | Name                      | Test name                 |
      | Description               | Test database description |
      | ID number                 | TESTNAME                  |
      | Allow comments on entries | Yes                       |
      | Force language            | English                   |
    And I turn editing mode off
    Then I should not see "Adding a new"
    And I turn editing mode on
    And I open "Test name" actions menu
    And I click on "Edit settings" "link" in the "Test name" activity
    And the following fields match these values:
      | Name                      | Test name    |
      | ID number                 | TESTNAME     |
      | Allow comments on entries | Yes          |
      | Force language            | English ‎(en)‎ |

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

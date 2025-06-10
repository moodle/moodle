@eWallah @availability @availability_relativedate
Feature: availability_relativedate delete relative activities
  In order to use conditions that are based on other activities
  As a teacher
  I need to be able to delete activities that are part of the condition without errors

  Background:
    Given the following "users" exist:
      | username |
      | student1 |
      | teacher1 |
    And the following config values are set as admin:
      | enableavailability  | 1 |        |
    And the following "course" exists:
      | fullname          | Course 1             |
      | shortname         | C1                   |
      | category          | 0                    |
      | enablecompletion  | 1                    |
      | startdate         | ## -10 days 17:00 ## |
      | enddate           | ## +2 weeks 17:00 ## |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario Outline: Delete a module that is part of a Relative condition in a section
    Given the following "activities" exist:
      | activity   | name | course | idnumber | section | completion |
      | <activity> | Act1 | C1     | id1      | 1       | 1          |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "1"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "1"
    And I set the field "relativednw" to "1"
    And I set the field "relativestart" to "7"
    And I set the field "relativecoursemodule" to "Act1"
    When I press "Save changes"
    And I should see "1 hour after completion of activity Act1"
    And I delete "Act1" activity
    And I should see "1 hour after completion of activity Act1"
    And I reload the page
    And I run all adhoc tasks
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    Then I should not see "Act1" in the "region-main" "region"
    And I should see "1 hour after completion of activity (missing)"

    Examples:
      | activity |
      | lesson   |
      | page     |

  @javascript
  Scenario Outline: Relative condition for each module
    Given the following "activities" exist:
      | activity   | name | course | idnumber | section | completion |
      | <activity> | Act1 | C1     | id1      | 1       | 1          |
      | <activity> | Act2 | C1     | id2      | 1       | 1          |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I open "Act2" actions menu
    And I click on "Edit settings" "link" in the "Act2" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    When I click on "Relative date" "button" in the "Add restriction..." "dialogue"
    And I set the field "relativenumber" to "1"
    And I set the field "relativednw" to "1"
    And I set the field "relativestart" to "7"
    And I set the field "relativecoursemodule" to "Act1"
    And I press "Save and return to course"
    And I should see "1 hour after completion of activity Act1"
    And I delete "Act1" activity
    And I should see "1 hour after completion of activity Act1"
    And I reload the page
    And I run all adhoc tasks
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    Then I should see "Act2" in the "region-main" "region"
    And I should see "1 hour after completion of activity (missing)"

    Examples:
      | activity |
      | book     |
      | url      |

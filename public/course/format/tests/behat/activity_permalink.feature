@core @core_courseformat
Feature: Activity permalink action menu item.
  In order to share a course activity
  As a teacher
  I need to be able to access the activity permalink from the course page

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | numsections      | 3        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Teacher can use the permalink action to copy an activity link.
    Given the following "activities" exist:
      | activity | name              | intro                  | course | idnumber | section |
      | forum    | Activity sample 1 | Test forum description | C1     | sample1  | 1       |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    When I open "Activity sample 1" actions menu
    And I choose "Permalink" in the open action menu
    And I click on "Copy to clipboard" "link" in the "Permalink" "dialogue"
    Then I should see "Text copied to clipboard"

  Scenario: Only activities with url have permalink action.
    Given the following "activities" exist:
      | activity | name              | intro                  | course | idnumber | section |
      | forum    | Activity sample 1 | Test forum description | C1     | sample1  | 1       |
      | label    | Activity sample 2 | Test label description | C1     | sample2  | 1       |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    Then "Permalink" "link" should exist in the "Activity sample 1" "activity"
    But "Permalink" "link" should not exist in the "Activity sample 2" "activity"

@block @block_myoverview @javascript
Feature: Course overview block show users their progress on courses
  In order to enable the my overview block in a course
  As a student
  I can see the progress percentage of the courses I am enrolled in

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | startdate     | enddate       |
      | Course 1 | C1        | 0        | 1                | ##yesterday## | ##tomorrow##  |
    And the following "activities" exist:
      | activity | course | idnumber | name          | intro                   | timeopen      | timeclose     |
      | choice   | C1     | choice1  | Test choice 1 | Test choice description | ##yesterday## | ##tomorrow##  |
    And the following "course enrolments" exist:
      | user | course | role            |
      | teacher1 | C1 | editingteacher  |
      | student1 | C1 | student         |

  Scenario: Course progress percentage should not be displayed if completion is not enabled
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "All" "link" in the "Course overview" "block"
    Then I should not see "0%" in the "Course overview" "block"

  Scenario: User complete activity and verify his progress
    Given I am on the "Test choice 1" "choice activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Add requirements         | 1                  |
      | id_completionview   | 1                                                 |
    And I press "Save and return to course"
    And I log out
    When I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should see "0%" in the "Course overview" "block"
    And I am on the "Test choice 1" "choice activity" page
    And I am on the "My courses" page
    And I click on "All" "button" in the "Course overview" "block"
    And I should see "100%" in the "Course overview" "block"

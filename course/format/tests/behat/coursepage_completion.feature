@core @core_course @core_courseformat @core_completion @javascript
Feature: Course page activities completion
  In order to check activities completions
  As a student
  I need to see the activity completion criterias dropdown.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | shortname | fullname | enablecompletion |
      | C1        | Course 1 | 1                |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section | completion | completionview |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       | 1          | 0              |
      | assign   | Activity sample 2 | Test assignment description | C1     | sample2  | 1       | 2          | 1              |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Teacher does not see manual completion button
    When I am on the "C1" "Course" page logged in as "teacher1"
    Then "Mark as done" "button" should not exist in the "Activity sample 1" "activity"
    And I click on "Completion" "button" in the "Activity sample 1" "activity"
    And I should see "Mark as done" in the "Activity sample 1" "core_courseformat > Activity completion"

  Scenario: Student should see the manual completion button
    When I am on the "C1" "Course" page logged in as "student1"
    Then the manual completion button for "Activity sample 1" should exist
    And the manual completion button of "Activity sample 1" is displayed as "Mark as done"
    And I toggle the manual completion state of "Activity sample 1"
    And the manual completion button of "Activity sample 1" is displayed as "Done"

  Scenario: Teacher should see the automatic completion criterias of activities
    When I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Completion" "button" in the "Activity sample 2" "activity"
    Then I should see "View" in the "Activity sample 2" "core_courseformat > Activity completion"
    # After viewing the activity, the completion criteria dropdown should still display "Completion".
    And I am on the "sample2" Activity page
    And I am on the "Course 1" course page
    And "Completion" "button" should exist in the "Activity sample 2" "activity"

  Scenario: Student should see the automatic completion criterias statuses of activities
    When I am on the "C1" "Course" page logged in as "student1"
    And I click on "To do" "button" in the "Activity sample 2" "activity"
    Then I should see "View" in the "Activity sample 2" "core_courseformat > Activity completion"
    # After viewing the activity, the completion criteria dropdown should display "Done" instead of "To do".
    And I am on the "sample2" Activity page
    And I am on the "Course 1" course page
    And "To do" "button" should not exist in the "Activity sample 2" "activity"
    And I click on "Done" "button" in the "Activity sample 2" "activity"
    And I should see "View" in the "Activity sample 2" "core_courseformat > Activity completion"

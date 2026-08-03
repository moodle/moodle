@grade @gradingform @gradingform_guide
Feature: Verify incomplete marking guides
  In order to ensure teachers are not blocked from grading
  As a teacher
  I need to be presented with the simple grading interface if a marking guide is incomplete

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name         | assignfeedback_comments_enabled | advancedgradingmethod_submissions |
      | assign   | C1     | assign1  | Assignment 1 | 1                               | guide                             |
    And the following "mod_assign > submissions" exist:
      | assign  | user     | onlinetext                   |
      | assign1 | student1 | Student submission text here |

  @javascript
  Scenario: Grading an assignment with an incomplete marking guide
    Given I am on the "Assignment 1" "assign activity" page logged in as "teacher1"
    And I go to "Student 1" "Assignment 1" activity advanced grading page
    And I should see "Grade out of 100"
    And I should not see "Marking guide"
    When I am on the "Course 1" course page
    And I go to "Assignment 1" advanced grading definition page
    And I set the following fields to these values:
      | Name        | My Marking Guide  |
      | Description | Guide description |
    And I define the following marking guide:
      | Criterion name | Description for students | Description for markers | Maximum score |
      | Criterion 1    | Student desc             | Marker desc             | 50            |
    And I press "Save as draft"
    And I am on the "Assignment 1" "assign activity" page
    And I go to "Student 1" "Assignment 1" activity advanced grading page
    And I should see "Grade out of 100"
    And I set the following fields to these values:
      | Grade out of 100  | 25                         |
      | Feedback comments | Good start, but incomplete |
    And I press "Save changes"
    And I am on the "Course 1" course page
    And I go to "Assignment 1" advanced grading definition page
    And I press "Save marking guide and make it ready"
    And I am on the "Assignment 1" "assign activity" page
    And I go to "Student 1" "Assignment 1" activity advanced grading page
    Then I should see "Criterion 1"
    And I should not see "Grade out of 100"
    And the following fields match these values:
      | Feedback comments | Good start, but incomplete |
      | Criterion 1       |                            |

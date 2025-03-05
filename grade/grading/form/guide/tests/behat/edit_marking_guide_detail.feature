@gradingform @gradingform_guide
Feature: Marking guide details can be updated for activity with graded submissions
  In order to update marking guide details
  As a teacher
  I need to be able to grade a submission

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignfeedback_comments_commentinline | assignfeedback_comments_enabled |
      | assign   | C1     | Assign 1 | guide                             | 1                                   | 0                             | 1                                     | 1                               |
    And the following "mod_assign > submissions" exist:
      | assign   | user     | onlinetext          |
      | Assign 1 | student1 | Assign 1 submission |
    And I am on the "Course 1" course page logged in as teacher1
    And I change window size to "large"
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name                                 | Assign 1 marking guide    |
      | Description                          | Marking guide description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Grade criterion A | Grade 1 description for students | Grade 1 description for markers | 70            |
      | Grade criterion B | Grade 2 description for students | Grade 2 description for markers | 30            |
    And I define the following frequently used comments:
      | Comment 1 |
      | Comment 2 |
      | Comment 3 |
    And I press "Save marking guide and make it ready"

  @javascript
  Scenario: Teacher can update marking guide details for activity with graded submissions
    Given I navigate to "Assignment" in current page administration
    And I go to "Student One" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade criterion A | 25 | Needs improvement |
      | Grade criterion B | 20 | Excellent!        |
    # Inserting frequently used comment.
    And I click on "Insert frequently used comment" "button" in the "Grade criterion A" "table_row"
    And I wait "1" seconds
    And I press "Comment 1"
    And I wait "1" seconds
    And I click on "Insert frequently used comment" "button" in the "Grade criterion B" "table_row"
    And I wait "1" seconds
    And I press "Comment 2"
    And I wait "1" seconds
    And I press "Save changes"
    When I am on "Course 1" course homepage
    And I go to "Assign 1" advanced grading definition page
#    And I set the field "Grade criterion A" to "Criteria 1"
#    And I set the field "guide[criteria][477000][shortname]" to "Criteria 1"
#    And I set the following fields to these values:
#      | Criterion name | Description for students | Description for markers | Maximum score |
#      | Criteria 1     | Criteria 1 student       | Criteria 1 marker       | 40            |
#      | Criteria 2     | Criteria 2 student       | Criteria 2 marker       | 70            |
    And I click on "Move down" "button" in the "Comment 1" "table_row"
    And I click on "Move up" "button" in the "Comment 3" "table_row"
    And I press "Save"
    # Remove after testing
    Then the following should exist in the "guide-comments" table:
      | Comment 2 |
      | Comment 3 |
      | Comment 1 |

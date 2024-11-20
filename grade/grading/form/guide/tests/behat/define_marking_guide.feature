@gradingform @gradingform_guide
Feature: Teacher can define a marking guide
  As a teacher,
  I should be able to define a marking guide

  Background:
    Given the following "users" exist:
      | username | firtname | lastname | email                |
      | teacher1 | Teacher  | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions |
      | assign   | C1     | Assign 1 | guide                             |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name | Marking guide 1 |

  Scenario: No criterion added to marking guide
    When I press "Save as draft"
    # Confirm that criterion parameters are required
    Then I should see "Criterion name can not be empty"
    And I should see "Criterion max score can not be empty"
    # Confirm that marking guide is not saved due to the missing criterion
    And I should not see "Marking guide 1 Draft"
    And I should not see "Please note: the advanced grading form is not ready at the moment. Simple grading method will be used until the form has a valid status."

  @javascript
  Scenario: Marking guide criterion is added to marking guide
    Given I define the following marking guide:
      | Criterion name | Description for students           | Description for markers           | Maximum score |
      | Criteria 1     | Criteria 1 description for student | Criteria 1 description for marker | 70            |
      | Criteria 2     | Criteria 2 description for student | Criteria 2 description for marker | 30            |
    # Move Criteria 1 below Criteria 2
    And I click on "Move down" "button" in the "Criteria 1" "table_row"
    When I press "Save as draft"
    And I go to "Assign 1" advanced grading definition page
    # Confirm that the order of criterion shown matches input -- Criteria 2 is listed before Criteria 1
    Then "Move down" "button" in the "Criteria 2" "table_row" should be visible
    And "Move up" "button" in the "Criteria 2" "table_row" should not be visible
    And "Move up" "button" in the "Criteria 1" "table_row" should be visible
    And "Move down" "button" in the "Criteria 1" "table_row" should not be visible
    # Confirm the other information entered were saved
    And I should see "Criteria 2 description for student" in the "Criteria 2" "table_row"
    And I should see "Criteria 2 description for marker" in the "Criteria 2" "table_row"
    And I should see "30" in the "Criteria 2" "table_row"
    And I should see "Criteria 1 description for student" in the "Criteria 1" "table_row"
    And I should see "Criteria 1 description for marker" in the "Criteria 1" "table_row"
    And I should see "70" in the "Criteria 1" "table_row"

  Scenario: Marking guide options and frequently used comment are added to marking guide
    Given I define the following marking guide:
      | Criterion name | Description for students           | Description for markers           | Maximum score |
      | Criteria 1     | Criteria 1 description for student | Criteria 1 description for marker | 50            |
      | Criteria 2     | Criteria 2 description for student | Criteria 2 description for marker | 50            |
    # Add frequently used comments and other marking guide options
    And I define the following frequently used comments:
      | Comment 1 |
      | Comment 2 |
    And I set the following fields to these values:
      | Show guide definition to students    | 1 |
      | Show marks per criterion to students | 0 |
    When I press "Save as draft"
    And I go to "Assign 1" advanced grading definition page
    #  Confirm that frequently used comments and marking guide options specified during registration are retained
    Then I should see "Comment 1"
    And I should see "Comment 2"
    And the field "Show guide definition to students" matches value "1"
    And the field "Show marks per criterion to students" matches value "0"

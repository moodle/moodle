@gradingform @gradingform_guide
Feature: Editing a marking guide already used for grading updates regrade state and student visible grades
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
      | activity | course | name     | advancedgradingmethod_submissions | assignsubmission_onlinetext_enabled |
      | assign   | C1     | Assign 1 | guide                             | 1                                   |
    And the following "mod_assign > submissions" exist:
      | assign   | user     | onlinetext          |
      | Assign 1 | student1 | Assign 1 submission |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name                                 | Assign 1 marking guide    |
      | Description                          | Marking guide description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Grade criterion A | Grade 1 description for students | Grade 1 description for markers | 70            |
      | Grade criterion B | Grade 2 description for students | Grade 2 description for markers | 30            |
    And I press "Save marking guide and make it ready"
    And I navigate to "Assignment" in current page administration
    And I go to "Student One" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade criterion A | 25 | Needs improvement |
      | Grade criterion B | 20 | Excellent!        |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I go to "Assign 1" advanced grading definition page
    And I edit the marking guide criterion "Grade criterion A" with the following values:
      | Field name          | New value                        |
      | shortname           | Updated Grade criterion A        |
      | description         | Updated description for students |
      | descriptionmarkers  | Updated description for markers  |
      | maxscore            | 60                               |
    And I press "Save"

  @javascript
  Scenario: Teacher edits a used marking guide and mark it for regrade and student sees breakdown only after teacher regrades
    # Set the marking guide to be "Mark for regrade".
    Given I set the field "menuguideregrade" to "Mark for regrade"
    And I should see "You are about to save changes to a marking guide that has already been used for grading. Please indicate if existing grades need to be reviewed. If you set this then the marking guide will be hidden from students until their item is regraded."
    And I click on "Continue" "button"
    And I am on the "Assign 1" "assign activity" page logged in as student1
    # Student should not see the grade breakdown as the activity is marked for regrade.
    And I should not see "Grade breakdown"
    When I am on the "Assign 1" "assign activity" page logged in as teacher1
    And I go to "Student One" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Updated Grade criterion A | 50 | I changed my mind |
      | Grade criterion B         | 30 | It's all good now |
    And I press "Save changes"
    And I am on the "Assign 1" "assign activity" page logged in as student1
    # Now the student should see the updated grade breakdown.
    Then I should see "Grade breakdown"
    And I should see the marking guide information displayed as:
      | criteria                  | description                      | remark            | maxscore | criteriascore |
      | Updated Grade criterion A | Updated description for students | I changed my mind | 60       | 50 / 60       |
      | Grade criterion B         | Grade 2 description for students | It's all good now | 30       | 30 / 30       |

  @javascript
  Scenario: Teacher edits a used marking guide does not mark for regrade existing student grades remain visible and unchanged
    # Set the marking guide to be "Do not mark for regrade".
    Given I set the field "menuguideregrade" to "Do not mark for regrade"
    And I should see "You are about to save changes to a marking guide that has already been used for grading. Please indicate if existing grades need to be reviewed. If you set this then the marking guide will be hidden from students until their item is regraded."
    And I click on "Continue" "button"
    When I am on the "Assign 1" "assign activity" page logged in as student1
    # Student should see the updated grade breakdown as the activity was not marked for regrading.
    Then I should see "Grade breakdown"
    And I should see the marking guide information displayed as:
      | criteria                  | description                      | remark            | maxscore | criteriascore |
      | Updated Grade criterion A | Updated description for students | Needs improvement | 60       | 25 / 60       |
      | Grade criterion B         | Grade 2 description for students | Excellent!        | 30       | 20 / 30       |

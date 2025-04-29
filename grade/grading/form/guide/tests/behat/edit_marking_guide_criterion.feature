@gradingform @gradingform_guide
Feature: Marking guide criterion can be edited and replaced
  In order to edit and replace a marking guide criterion
  As a teacher
  I need to have an existing marking guide criterion

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
      | activity | course | name     | advancedgradingmethod_submissions | assignsubmission_onlinetext_enabled |
      | assign   | C1     | Assign 1 | guide                             | 1                                   |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name        | Assign 1 marking guide    |
      | Description | Marking guide description |
    And I define the following marking guide:
      | Criterion name   | Description for students         | Description for markers         | Maximum score |
      | Grade Criteria 1 | Grade 1 description for students | Grade 1 description for markers | 70            |
      | Grade Criteria 2 | Grade 2 description for students | Grade 2 description for markers | 30            |
    And I define the following frequently used comments:
      | Comment 1 |
      | Comment 2 |
      | Comment 3 |
    And I press "Save marking guide and make it ready"
    And the following "mod_assign > submissions" exist:
      | assign   | user      | onlinetext                       |
      | Assign 1 | student1  | I'm the student first submission |
    And I am on the "Assign 1" "assign activity" page
    And I go to "Student 1" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade Criteria 1 | 50 | Comment 1 |
      | Grade Criteria 2 | 20 | Comment 2 |
    And I press "Save changes"

  @javascript
  Scenario: Marking guide frequently used comments can be updated and reordered
    Given I go to "Assign 1" advanced grading definition page
    And I click on "Move up" "button" in the "Comment 3" "table_row"
    And I click on "Move down" "button" in the "Comment 1" "table_row"
    When I press "Save"
    # Confirm that the order of the frequently used comments are updated.
    Then "Comment 3" "text" should appear before "Comment 1" "text"
    And "Comment 2" "text" should appear after "Comment 1" "text"
    And I am on the "Assign 1" "assign activity" page
    And I go to "Student 1" "Assign 1" activity advanced grading page
    # Confirm that there are no changes to the existing marking records.
    And I should see "Comment 1" in the "Grade Criteria 1" "table_row"
    And I should see "Comment 2" in the "Grade Criteria 2" "table_row"

  @javascript
  Scenario: Marking guide criterion can be modified
    Given I go to "Assign 1" advanced grading definition page
    And I click on "Delete criterion" "button" in the "Grade Criteria 1" "table_row"
    And I press "Yes"
    And I wait "1" seconds
    And I should not see "Grade Criteria 1"
    And I press "Add criterion"
    # Used xpath_element selectors in order to differentiate multiple criterion fields.
    And I click on "//table[@id='guide-criteria']//tr[@class='criterion odd last']//div[@class='criterionname']" "xpath_element"
    # Set the new criterion name.
    And I set the field with xpath "//input[@id='guide-criteria-NEWID1-shortname']" to "Grade Criteria 3"
    And I click on "//table[@id='guide-criteria']//tr[@class='criterion odd last']//div[@class='criteriondesc']//span[@class='textvalue']" "xpath_element"
    # Set the new criterion description for students.
    And I set the field with xpath "//textarea[@id='guide[criteria][NEWID1][description]']" to "Grade 3 description for students"
    And I click on "//table[@id='guide-criteria']//tr[@class='criterion odd last']//div[@class='criteriondescmarkers']//span[@class='textvalue']" "xpath_element"
    # Set the new criterion description for markers.
    And I set the field with xpath "//textarea[@id='guide[criteria][NEWID1][descriptionmarkers]']" to "Grade 3 description for markers"
    And I click on "//table[@id='guide-criteria']//tr[@class='criterion odd last']//div[@class='criterionmaxscore']//span[@class='textvalue']" "xpath_element"
    # Set the new criterion max score.
    And I set the field with xpath "//input[@id='guide[criteria][NEWID1][maxscore]']" to "70"
    When I press "Save"
    Then "You are about to save significant changes to a marking guide that has already been used for grading. The gradebook value will be unchanged, but the marking guide will be hidden from students until their item is regraded." "text" should exist
    And I press "Continue"
    And I am on the "Assign 1" "assign activity" page logged in as student1
    # Confirm that the remarks for each grading criterion are not displayed.
    And "Comment 1" "text" should not exist
    And "Comment 2" "text" should not exist
    # Confirm that the submission is not regraded.
    And I should see "70.00 / 100.00"
    # Regrade the submission.
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Student 1" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade Criteria 2 | 15 | Comment 3 |
      | Grade Criteria 3 | 50 | Comment 2 |
    And I press "Save changes"
    And I am on the "Assign 1" "assign activity" page logged in as student1
    # Confirm that submission is re-marked and regraded.
    And I should see "65.00 / 100.00"
    And I should see the marking guide information displayed as:
      | criteria         | description                      | remark    | maxscore | criteriascore |
      | Grade Criteria 2 | Grade 2 description for students | Comment 3 | 30       | 15 / 30       |
      | Grade Criteria 3 | Grade 3 description for students | Comment 2 | 70       | 50 / 70       |

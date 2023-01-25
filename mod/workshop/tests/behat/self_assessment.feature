@mod @mod_workshop
Feature: Workshop self-assessment
  In order to use workshop activity
  As a student
  I need to be able to add and assess my own submission

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
      | student3 | Student   | Three    | student3@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c1     | student        |
      | student2 | c1     | student        |
      | student3 | c1     | student        |
      | teacher1 | c1     | editingteacher |
    And the following "activities" exist:
      | activity | name         | course | idnumber  | useselfassessment |
      | workshop | TestWorkshop | c1     | workshop1 | 1                 |
    And I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I edit assessment form in workshop "TestWorkshop" as:
      | id_description__idx_0_editor | Aspect1 |
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission1  |
      | Submission content | Some content |
    And I am on the "TestWorkshop" "workshop activity" page logged in as student2
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission2  |
      | Submission content | Some content |
    And I am on the "TestWorkshop" "workshop activity" page logged in as student3
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission3  |
      | Submission content | Some content |
    And I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I click on "Submissions allocation" "link"
    And I select "Random allocation" from the "jump" singleselect
    And I set the following fields to these values:
      | addselfassessment | 1 |
    And I press "Save changes"

  Scenario: Student can assess their own submission
    When I select "Manual allocation" from the "jump" singleselect
    # Verify that each student has themself listed as a reviewer.
    And the following should exist in the "allocations" table:
      | -1-           | -2-           | -3-           |
      | Student One   | Student One   | Student One   |
      | Student Three | Student Three | Student Three |
      | Student Two   | Student Two   | Student Two   |
    And I change phase in workshop "TestWorkshop" to "Assessment phase"
    # Confirm that the student can assess their own submission.
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    Then I should see "Assess yourself"
    And the "Assess" "button" should be enabled
    And I assess submission "Student One" in workshop "TestWorkshop" as:
      | grade__idx_0            | 10 / 10                             |
      | peercomment__idx_0      | My work is amazing hence the grade. |
      | Feedback for the author | Good work as always                 |
    And the "Re-assess" "button" should be enabled
    And I should see "Already graded"
    # As teacher, confirm that Student One assessed his own work and received a grade.
    And I am on the TestWorkshop "workshop activity" page logged in as teacher1
    And I should see grade "80" for workshop participant "Student One" set by peer "Student One"

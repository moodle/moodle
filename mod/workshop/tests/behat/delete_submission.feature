@mod @mod_workshop
Feature: Workshop submission removal
  In order to get rid of accidentally submitted or otherwise inappropriate contents
  As a student and as a teacher
  I need to be able to delete my submission, or any submission respectively

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | student1 | Sam1      | Student1 | student1@example.com  |
      | student2 | Sam2      | Student2 | student2@example.com  |
      | student3 | Sam3      | Student3 | student3@example.com  |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com  |
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
      | activity | name         | course | idnumber  | submissiontypefile |
      | workshop | TestWorkshop | c1     | workshop1 | 1                  |
    # Teacher sets up assessment form and changes the phase to submission.
    And I am on the "Course1" course page logged in as teacher1
    And I edit assessment form in workshop "TestWorkshop" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
      | id_description__idx_2_editor |         |
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    # Student1 submits.
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission1  |
      | Submission content | Some content |
    # Student2 submits.
    And I am on the "Course1" course page logged in as student2
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission2  |
      | Submission content | Some content |
    # Teacher allocates student3 to be reviewer of student2's submission.
    And I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I allocate submissions in workshop "TestWorkshop" as:
      | Participant   | Reviewer      |
      | Sam2 Student2 | Sam3 Student3 |

  Scenario: Students can delete their submissions as long as the submissions are editable and not allocated for assessments
    Given I am on the "TestWorkshop" "workshop activity" page logged in as student1
    When I follow "Submission1"
    Then I should see "Submission1"
    And "Delete submission" "button" should exist
    And I click on "Delete submission" "button"
    And I should see "Are you sure you want to delete the following submission?"
    And I should see "Submission1"
    And I click on "Continue" "button"
    And I should see "You have not submitted your work yet"

  Scenario: Students cannot delete their submissions if the submissions are not editable
    Given I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I change phase in workshop "TestWorkshop" to "Closed"
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    When I follow "Submission1"
    Then I should see "Submission1"
    And "Delete submission" "button" should not exist

  Scenario: Students cannot delete their submissions if the submissions are allocated for assessments
    Given I am on the "TestWorkshop" "workshop activity" page logged in as student2
    When I follow "Submission2"
    Then I should see "Submission2"
    And "Delete submission" "button" should not exist

  Scenario: Teachers can delete submissions even if the submissions are allocated for assessments.
    Given I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And "Submission1" "link" should exist
    And "Submission2" "link" should exist
    When I follow "Submission2"
    Then "Delete submission" "button" should exist
    And I click on "Delete submission" "button"
    And I should see "Are you sure you want to delete the following submission?"
    And I should see "Note this will also delete 1 assessments associated with this submission, which may affect the reviewers' grades."
    And I click on "Continue" "button"
    And "Submission1" "link" should exist
    And "Submission2" "link" should not exist

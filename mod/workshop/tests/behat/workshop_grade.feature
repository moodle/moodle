@mod @mod_workshop
Feature: Workshop grade submission and assessment
  In order to use workshop activity
  As a teacher
  I need to be able to grade student's submissions and feedbacks

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Sam1      | Student1 | student1@example.com |
      | student2 | Sam2      | Student2 | student2@example.com |
      | student3 | Sam3      | Student3 | student3@example.com |
      | student4 | Sam4      | Student4 | student3@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course1  | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c1     | student        |
      | student2 | c1     | student        |
      | student3 | c1     | student        |
      | student4 | c1     | student        |
      | teacher1 | c1     | editingteacher |
    And the following "activities" exist:
      | activity | name         | intro                     | course | idnumber  | submissiontypetext | submissiontypefile | grade | gradinggrade | gradedecimals | overallfeedbackmethod |
      | workshop | TestWorkshop | Test workshop description | c1     | workshop1 | 2                  | 1                  | 10    | 5            | 1             | 2                     |

  Scenario: Assess submissions and gradings in workshop with javascript enabled
    # teacher1 sets up assessment form and changes the phase to submission
    Given I log in as "teacher1"
    And I am on the "Course1" course page logged in as teacher1
    And I edit assessment form in workshop "TestWorkshop" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor |         |
      | id_description__idx_2_editor |         |
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    # student1 submits
    And I am on the TestWorkshop "workshop activity" page logged in as student1
    And I should see "Submit your work"
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission1  |
      | Submission content | Some content |
    And I should see "Submission1"
    # teacher1 allocates reviewers and changes the phase to assessment
    When I am on the TestWorkshop "workshop activity" page logged in as teacher1
    Then the following should exist in the "grading-report" table:
      | First name / Last name | Submission / Last modified        |
      | Sam1 Student1          | Submission1                       |
      | Sam2 Student2          | No submission found for this user |
      | Sam3 Student3          | No submission found for this user |
      | Sam4 Student4          | No submission found for this user |
    And I allocate submissions in workshop "TestWorkshop" as:
      | Participant   | Reviewer      |
      | Sam1 Student1 | Sam2 Student2 |
      | Sam1 Student1 | Sam3 Student3 |
      | Sam1 Student1 | Sam4 Student4 |
    And I am on the TestWorkshop "workshop activity" page
    And I should see "to allocate: 0"
    And I change phase in workshop "TestWorkshop" to "Assessment phase"
    # student2 assesses work of student1
    And I am on the TestWorkshop "workshop activity" page logged in as student2
    And I should see "Submission1"
    And I should see "Sam1 Student1"
    And I assess submission "Sam1" in workshop "TestWorkshop" as:
      | grade__idx_0            | 10 / 10   |
      | peercomment__idx_0      | Amazing   |
      | Feedback for the author | Good work |
    And I should see "Already graded"
    # student3 assesses work of student1
    And I am on the TestWorkshop "workshop activity" page logged in as student3
    And I should see "Submission1"
    And I should see "Sam1 Student1"
    And I assess submission "Sam1" in workshop "TestWorkshop" as:
      | grade__idx_0            | 10 / 10   |
      | peercomment__idx_0      | Amazing   |
      | Feedback for the author | Good work |
    And I should see "Already graded"
    # student4 assesses work of student1
    And I am on the TestWorkshop "workshop activity" page logged in as student4
    And I should see "Submission1"
    And I should see "Sam1 Student1"
    And I assess submission "Sam1" in workshop "TestWorkshop" as:
      | grade__idx_0            | 6 / 10            |
      | peercomment__idx_0      | You can do better |
      | Feedback for the author | Good work         |
    And I should see "Already graded"
    # teacher1 makes sure he can see all peer grades and changes to grading evaluation phase
    And I am on the TestWorkshop "workshop activity" page logged in as teacher1
    And I should see grade "10.0" for workshop participant "Sam1" set by peer "Sam2"
    And I should see grade "10.0" for workshop participant "Sam1" set by peer "Sam3"
    And I should see grade "6.0" for workshop participant "Sam1" set by peer "Sam4"
    And the following should exist in the "grading-report" table:
      | First name / Last name | Submission / Last modified        |
      | Sam2 Student2          | No submission found for this user |
      | Sam3 Student3          | No submission found for this user |
      | Sam4 Student4          | No submission found for this user |
    And I change phase in workshop "TestWorkshop" to "Grading evaluation phase"
    And I press "Re-calculate grades"
    And the following should exist in the "grading-report" table:
      | First name / Last name | Submission / Last modified        | Grade for submission (of 10.0) | Grades given | Grade for assessment (of 5.0) |
      | Sam1 Student1          | Submission1                       | 8.7                            | -            | -                             |
      | Sam2 Student2          | No submission found for this user | -                              | 10.0 (5.0)   | 5.0                           |
      | Sam3 Student3          | No submission found for this user | -                              | 10.0 (5.0)   | 5.0                           |
      | Sam4 Student4          | No submission found for this user | -                              | 6.0 (3.2)    | 3.2                           |
    And I click on "6.0 (3.2)" "link" in the "Sam4 Student4" "table_row"
    And I set the following fields to these values:
      | Override grade for assessment | 4 |
    And I press "Save and close"
    And the following should exist in the "grading-report" table:
      | First name / Last name | Grades given    | Grade for assessment (of 5.0) |
      | Sam1 Student1          | -               | -                             |
      | Sam4 Student4          | 6.0 (3.2 / 4.0) | 4.0                           |
    # Undo teacher1 overrides the grade on assessment by student2
    And I click on "6.0 (3.2 / 4.0)" "link" in the "Sam4 Student4" "table_row"
    And I set the following fields to these values:
      | Override grade for assessment | Not overridden |
    And I press "Save and close"
    And the following should exist in the "grading-report" table:
      | First name / Last name | Grades given | Grade for assessment (of 5.0) |
      | Sam1 Student1          | -            | -                             |
      | Sam4 Student4          | 6.0 (3.2)    | 3.2                           |

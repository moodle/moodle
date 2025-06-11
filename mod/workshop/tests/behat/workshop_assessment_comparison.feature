@mod @mod_workshop
Feature: Workshop assessment comparison
  In order to compare workshop assessments
  As a teacher
  I need to be able to set how strict comparison is

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student1  | 1        | student1@example.com |
      | student2 | Student2  | 2        | student2@example.com |
      | student3 | Student3  | 3        | student3@example.com |
      | student4 | Student4  | 4        | student4@example.com |
      | student5 | Student5  | 5        | student5@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name       | course | idnumber | submissiontypetext | grade | gradinggrade | gradedecimals | overallfeedbackmethod |
      | workshop | Workshop 1 | C1     | W1       | 2                  | 10    | 5            | 1             | 2                     |
    And I am on the "Course 1" course page logged in as teacher1
    And I edit assessment form in workshop "Workshop 1" as:
      | id_description__idx_0_editor | Aspect 1 |
      | id_description__idx_1_editor |          |
      | id_description__idx_2_editor |          |
    And I change phase in workshop "Workshop 1" to "Submission phase"
    And I am on the "Workshop 1" "workshop activity" page logged in as student1
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Submitted by s1    |
      | Submission content | Some content by s1 |

  Scenario Outline: Teacher can set how strict the comparison of assessment is
    Given I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I allocate submissions in workshop "Workshop 1" as:
      | Participant | Reviewer   |
      | Student1 1  | Student2 2 |
      | Student1 1  | Student3 3 |
      | Student1 1  | Student4 4 |
      | Student1 1  | Student5 5 |
    And I change phase in workshop "Workshop 1" to "Assessment phase"
    And I am on the "Workshop 1" "workshop activity" page logged in as student2
    And I assess submission "Student1" in workshop "Workshop 1" as:
      | grade__idx_0            | 10 / 10   |
    And I am on the "Workshop 1" "workshop activity" page logged in as student3
    And I assess submission "Student1" in workshop "Workshop 1" as:
      | grade__idx_0            | 8 / 10    |
    And I am on the "Workshop 1" "workshop activity" page logged in as student4
    And I assess submission "Student1" in workshop "Workshop 1" as:
      | grade__idx_0            | 6 / 10    |
    And I am on the "Workshop 1" "workshop activity" page logged in as student5
    And I assess submission "Student1" in workshop "Workshop 1" as:
      | grade__idx_0            | 0 / 10    |
    When I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Workshop 1" to "Grading evaluation phase"
    And I set the field "Comparison of assessments" to "<strictness>"
    And I press "Re-calculate grades"
    Then the following should exist in the "grading-report" table:
      | First name / Last name | Submission / Last modified        | Grade for submission (of 10.0) | Grades given                    | Grade for assessment (of 5.0) |
      | Student1 1             | Submitted by s1                   | <Grade for submission>         | -                               | -                             |
      | Student2 2             | No submission found for this user | -                              | 10.0 (<Grade for assessment 2>) | <Grade for assessment 2>      |
      | Student3 3             | No submission found for this user | -                              | 8.0 (<Grade for assessment 3>)  | <Grade for assessment 3>      |
      | Student4 4             | No submission found for this user | -                              | 6.0 (<Grade for assessment 4>)  | <Grade for assessment 4>      |
      | Student5 5             | No submission found for this user | -                              | 0.0 (<Grade for assessment 5>)  | <Grade for assessment 5>      |

    Examples:
      | strictness   | Grade for submission | Grade for assessment 2 | Grade for assessment 3 | Grade for assessment 4 | Grade for assessment 5 |
      | very lax     | 6.0                  | 4.7                    | 5.0                    | 5.0                    | 4.1                    |
      | lax          | 6.0                  | 4.7                    | 5.0                    | 5.0                    | 3.9                    |
      | fair         | 6.0                  | 4.5                    | 4.9                    | 5.0                    | 3.5                    |
      | strict       | 6.0                  | 4.2                    | 4.9                    | 5.0                    | 2.4                    |
      | very strict  | 6.0                  | 2.7                    | 4.7                    | 5.0                    | 0.0                    |

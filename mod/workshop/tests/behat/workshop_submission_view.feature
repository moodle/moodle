@mod @mod_workshop
Feature: Student can view their submission assessments
  In order to view submission assessments when workshop is closed
  As a teacher
  I should be able to set the workshop to closed phase

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | One       | Student  | student1@example.com |
      | student2 | Two       | Student  | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | name       | course | submissiontypetext |
      | workshop | Workshop 1 | C1     | 2                  |
    And I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I edit assessment form in workshop "Workshop 1" as:
      | id_description__idx_0_editor | Aspect1 |
    And I change phase in workshop "Workshop 1" to "Submission phase"
    # Create workshop submissions.
    And I am on the "Workshop 1" "workshop activity" page logged in as student1
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Submission 1         |
      | Submission content | Submission 1 content |
    And I am on the "Workshop 1" "workshop activity" page logged in as student2
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Submission 2         |
      | Submission content | Submission 2 content |
    And I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Workshop 1" to "Assessment phase"
    # Allocate and assess submissions.
    And I allocate submissions in workshop "Workshop 1" as:
      | Participant | Reviewer    |
      | One Student | Two Student |
      | Two Student | One Student |
    And I am on the "Workshop 1" "workshop activity" page logged in as student1
    And I assess submission "Two" in workshop "Workshop 1" as:
      | grade__idx_0            | 5 / 10            |
      | peercomment__idx_0      | You can do better |
    And I am on the "Workshop 1" "workshop activity" page logged in as student2
    And I assess submission "One" in workshop "Workshop 1" as:
      | grade__idx_0            | 8 / 10            |
      | peercomment__idx_0      | Great job!        |

  Scenario: Student can view their submission assessment after workshop is closed
    # Re-calculate grades to generate workshop grades from assessment.
    Given I am on the "Course 1" course page logged in as teacher1
    And I change phase in workshop "Workshop 1" to "Grading evaluation phase"
    And I am on the "Workshop 1" "workshop activity" page
    And I click on "Re-calculate grades" "button"
    # Close workshop activity.
    And I change phase in workshop "Workshop 1" to "Closed"
    When I am on the "Course 1" "grades > Grader report > View" page
    # Confirm that grades are reflected on the gradebook.
    Then the following should exist in the "user-grades" table:
      | -1-         | -2-                  | -3-   |
      | One Student | student1@example.com | 64.00 |
      | Two Student | student2@example.com | 40.00 |
    # Confirm that student can view submission assessment grades and comments after workshop is closed.
    And I am on the "Workshop 1" "workshop activity" page logged in as student1
    And I click on "Submission 1" "link"
    And I should see "8 / 10"
    And I should see "Great job!"
    And I am on the "Workshop 1" "workshop activity" page logged in as student2
    And I click on "Submission 2" "link"
    And I should see "5 / 10"
    And I should see "You can do better"

@mod @mod_workshop
Feature: Workshop assessment navigation display for reviewers
  As a reviewer
  I need to be able to see the "Save and show next" button during assessment

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activities" exist:
      | activity | name       | course | submissiontypetext |
      | workshop | Workshop 1 | C1     | 2                  |

  Scenario: Reviewers can navigate between submissions using save and show next button
    Given I am on the "Course 1" course page logged in as teacher1
    And I edit assessment form in workshop "Workshop 1" as:
      | id_description__idx_0_editor | Aspect 1 |
      | id_description__idx_1_editor | Aspect 2 |
    And I change phase in workshop "Workshop 1" to "Submission phase"
    # Add a submission for students to be assessed.
    And I am on the "Workshop 1" "workshop activity" page logged in as student2
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Student 2 submission |
      | Submission content | Submission content   |
    And I am on the "Workshop 1" "workshop activity" page logged in as student3
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Student 3 submission |
      | Submission content | Submission content   |
    # Allocate student1 as reviewer for other student submissions.
    And I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I allocate submissions in workshop "Workshop 1" as:
      | Participant | Reviewer  |
      | Student 2   | Student 1 |
      | Student 3   | Student 1 |
    And I change phase in workshop "Workshop 1" to "Assessment phase"
    When I am on the "Workshop 1" "workshop activity" page logged in as student1
    And I press "Assess"
    # Confirm student1 can see "Save and show next" button while assessing the first submission.
    Then "Save and show next" "button" should exist
    And I set the following fields to these values:
      | Grade for Aspect 1      | 6          |
      | Grade for Aspect 2      | 7          |
      | Feedback for the author | Keep it up |
    And I press "Save and show next"
    # Confirm student1 can't see "Save and show next" button while assessing the last submission.
    And "Save and show next" "button" should not exist
    And "Save and close" "button" should exist
    And I set the following fields to these values:
      | Grade for Aspect 1      | 7          |
      | Grade for Aspect 2      | 6          |
      | Feedback for the author | Keep it up |
    And I press "Save and close"
    # Confirm that the corresponding buttons are not displayed after pressing "Save and close".
    And "Save and show next" "button" should not exist
    And "Save and close" "button" should not exist

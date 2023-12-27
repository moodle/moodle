@mod @mod_workshop @core_completion
Feature: View activity completion information in the Workshop activity
  In order to have visibility of lesson completion requirements
  As a student
  I need to be able to view my lesson completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Rex       | Student2 | student2@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | student2 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
        | activity | name          | course | submissiontypefile | completion | completionview |
        | workshop | Music history | C1     | 1                  | 2          | 1              |
    And I am on the "Music history" "workshop activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Receive a grade       | Submission                                        |
      | Any grade             | 1                                                 |
    And I press "Save and return to course"
    And I edit assessment form in workshop "Music history" as:
      | id_description__idx_0_editor | Aspect1 |
    And I change phase in workshop "Music history" to "Submission phase"

  Scenario: View automatic completion items as a teacher
    Given I am on the "Music history" "workshop activity" page
    Then "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "View" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "workshop activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    # Add a submission.
    And I press "Add submission"
    And I set the field "Title" to "Pinch harmonics"
    And I set the field "Submission content" to "Satisfying to play"
    And I press "Save changes"
    And I am on the "Course 1" course page logged in as teacher1
    And I change phase in workshop "Music history" to "Assessment phase"
    And I allocate submissions in workshop "Music history" as:
      | Participant     | Reviewer      |
      | Vinnie Student1 | Rex Student2  |
    # Assess the submission.
    And I am on the "Music history" "workshop activity" page logged in as student2
    And I assess submission "Pinch harmonics" in workshop "Music history" as:
      | grade__idx_0            | 9 / 10      |
      | peercomment__idx_0      | Well done   |
    # Evaluate and close the workshop so a grade is recorded for the student.
    And I am on the "Course 1" course page logged in as teacher1
    And I change phase in workshop "Music history" to "Grading evaluation phase"
    And I am on the "Music history" "workshop activity" page
    And I click on "Re-calculate grades" "button"
    And I change phase in workshop "Music history" to "Closed"
    # Confirm completion condition is updated.
    When I am on the "Music history" "workshop activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I am on the "Music history" "workshop activity" page
    And I am on the "Music history" "workshop activity editing" page
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and display"
    # Teacher view
    And the manual completion button for "Music history" should be disabled
    # Student view.
    When I am on the "Music history" "workshop activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

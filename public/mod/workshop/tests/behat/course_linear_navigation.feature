@mod @mod_workshop
Feature: Display the course linear navigation in the workshop pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in workshop pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | course | idnumber   | useexamples | useselfassessment |
      | workshop | Workshop1 | C1     | workshop1  | 1           | 1                 |

  @_file_upload @javascript
  Scenario: As a student I should see the course linear navigation in workshop pages that allow it
    Given I am on the "Workshop1" "workshop activity" page logged in as teacher
    And I edit assessment form in workshop "Workshop1" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
      | id_description__idx_2_editor |         |
    And I press "Add example submission"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Title              | First example submission           |
      | Submission content | Just an example but hey, it works! |
      | Attachment         | lib/tests/fixtures/empty.txt       |
    And I press "Save changes"
    # Setup phase.
    When I am on the "Workshop1" "workshop activity" page logged in as student
    Then the course linear navigation should be visible
    # Switch to submission phase.
    And I am on the "Workshop1" "workshop activity" page logged in as teacher
    And I change phase in workshop "Workshop1" to "Submission phase"
    # Submission phase.
    And I am on the "Workshop1" "workshop activity" page logged in as student
    And the course linear navigation should be visible
    # Assessment of an example submission.
    And I press "Assess"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | grade__idx_0            | 9 / 10         |
      | peercomment__idx_0      | Amazing        |
      | grade__idx_1            | 2 / 10         |
      | peercomment__idx_1      | Not so amazing |
      | Feedback for the author | Keep going!    |
    And I press "Save and close"
    And the course linear navigation should not be visible
    And I press "Back"
    # Submission.
    And I press "Add submission"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Title              | My submission             |
      | Submission content | Please don't be too harsh |
    And I press "Save changes"
    # The page right after submitting should display the course linear navigation.
    And the course linear navigation should be visible
    # The delete and edit submission pages should not.
    And I press "Delete submission"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I follow "Submit your work"
    And I press "Edit submission"
    And the course linear navigation should not be visible
    # Set self assessment.
    And I am on the "Workshop1" "workshop activity" page logged in as teacher
    And I follow "Allocate submissions"
    And I select "Random allocation" from the "jump" singleselect
    And I set the following fields to these values:
      | addselfassessment | 1 |
    And I press "Save changes"
    And I change phase in workshop "Workshop1" to "Assessment phase"
    # Assess a submission.
    And I am on the "Workshop1" "workshop activity" page logged in as student
    And the course linear navigation should be visible
    And I press "Assess"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | grade__idx_0            | 10 / 10                                   |
      | peercomment__idx_0      | WOW                                       |
      | grade__idx_1            | 10 / 10                                   |
      | peercomment__idx_1      | Astounding work                           |
      | Feedback for the author | This is the best thing I've ever reviewed |
    And I press "Save and close"
    And the course linear navigation should be visible
    # Switch phase to grading evaluation.
    And I am on the "Workshop1" "workshop activity" page logged in as teacher
    And I change phase in workshop "Workshop1" to "Grading evaluation phase"
    And I am on the "Workshop1" "workshop activity" page logged in as student
    And the course linear navigation should be visible
    # Switch phase to closed.
    And I am on the "Workshop1" "workshop activity" page logged in as teacher
    And I change phase in workshop "Workshop1" to "Closed"
    And I am on the "Workshop1" "workshop activity" page logged in as student
    And the course linear navigation should be visible

  @_file_upload @javascript
  Scenario: As a teacher I should see the course linear navigation in workshop pages that allow it
    # Main view.
    When I am on the "Workshop1" "workshop activity" page logged in as teacher
    Then the course linear navigation should be visible
    # Assessment form.
    And I follow "Assessment form"
    And the course linear navigation should not be visible
    # Assessment form preview.
    And I press "Save and preview"
    And the course linear navigation should not be visible
    # Example submission form.
    And I edit assessment form in workshop "Workshop1" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
      | id_description__idx_2_editor |         |
    And I press "Add example submission"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Title              | First example submission           |
      | Submission content | Just an example but hey, it works! |
      | Attachment         | lib/tests/fixtures/empty.txt       |
    And I press "Save changes"
    # Assess example submission.
    And I follow "Assess"
    And the course linear navigation should not be visible
    And I press "Save and close"
    # Switch phase.
    And I follow "Switch to the next phase"
    And the course linear navigation should not be visible
    And I change phase in workshop "Workshop1" to "Submission phase"
    # Submissions allocation.
    And I follow "Allocate submissions"
    And the course linear navigation should not be visible
    # Assessment phase.
    And I change phase in workshop "Workshop1" to "Assessment phase"
    And the course linear navigation should be visible
    # Grading evaluation phase.
    And I change phase in workshop "Workshop1" to "Grading evaluation phase"
    And the course linear navigation should be visible
    # Close workshop.
    And I change phase in workshop "Workshop1" to "Closed"
    And the course linear navigation should be visible

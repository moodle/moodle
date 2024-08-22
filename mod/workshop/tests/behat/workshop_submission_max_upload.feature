@mod @mod_workshop
Feature: Student can upload a set number of attachments in submission
  In order to create a submission with a set number of attachments
  As a teacher
  I should be able to set the number of attachments

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name       | course | nattachments |
      | workshop | Workshop 1 | C1     | 2            |
    And I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I edit assessment form in workshop "Workshop 1" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
      | id_description__idx_2_editor | Aspect3 |
    And I change phase in workshop "Workshop 1" to "Submission phase"

  @javascript @_file_upload
  Scenario: Student can create submission with set number of attachments
    Given I am on the "Workshop 1" "workshop activity" page logged in as student1
    When I press "Add submission"
    # Add... button is visible initially because there are no attachments yet.
    Then "Add..." "button" should be visible
    # Add 2 attachments - the set number of attachments.
    And I upload "mod/workshop/tests/fixtures/moodlelogo.png" file to "Attachment" filemanager
    And I upload "lib/tests/fixtures/gd-logo.png" file to "Attachment" filemanager
    # Confirm that Add... button is not visible after set number of attachments is met.
    And "Add..." "button" should not be visible
    # Delete one attachment.
    And I click on "gd-logo.png" "link"
    And I click on "Delete" "button" in the "Edit gd-logo.png" "dialogue"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    # Confirm that Add... button is visible again after 1 attachment is deleted.
    And "Add..." "button" should be visible

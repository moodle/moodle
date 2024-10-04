@mod @mod_workshop
Feature: Provide example submission
  In order to let students practise the assessment process in the workshop
  As a teacher
  I need to be able to define example submission and its referential assessment

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
      | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
      | student1 | c1     | student        |
    And the following "activities" exist:
      | activity | name         | course | idnumber  | useexamples |
      | workshop | TestWorkshop | c1     | workshop1 | 1           |
    # As a teacher, define the assessment form to be used in the workshop.
    And I am on the "Course1" course page logged in as teacher1
    And I edit assessment form in workshop "TestWorkshop" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
      | id_description__idx_2_editor |         |

  @javascript @_file_upload
  Scenario: Add example submission with attachments to a workshop
    # Add an example submission with an attachment.
    Given I press "Add example submission"
    And I set the following fields to these values:
      | Title              | First example submission           |
      | Submission content | Just an example but hey, it works! |
      | Attachment         | lib/tests/fixtures/empty.txt       |
    And I press "Save changes"
    And I should see "First example submission"
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I should see "Example submissions to assess"
    When I click on "Assess" "button"
    Then I should see "Assessed example submission"
    And I should see "First example submission"
    And I should see "Just an example but hey, it works!"
    And "empty.txt" "link" should exist

@core @core_completion @javascript
Feature: Check activity grading settings when requiring grading completion
  In order to confirm the activity completion values are fine
  As a teacher
  I need to be able to be informed about the wrong settings.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion | numsections |
      | Course 1 | C1        | 0        | 1                | 1           |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | First | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity   | forum      |
      | course     | C1         |
      | name       | Test forum |
      | completion | 0          |

  Scenario: Require any grade for multiple grading items activities
    Given I am on the "Test forum" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Add requirements" to "1"
    And I set the field "Receive a grade" to "1"
    When I click on "Save and display" "button"
    Then I should see "Require grade can't be enabled for Rating because grading by Rating is not enabled."
    And I set the following fields to these values:
      | Ratings > Aggregate type        | Average of ratings |
      | id_scale_modgrade_type          | Point              |
      | Ratings > scale[modgrade_point] | 60                 |
    And I click on "Save and display" "button"
    And I should not see "Require grade can't be enabled for Rating because grading by Rating is not enabled."
    And I should see "Receive a grade"
    And I am on the "Test forum" "forum activity editing" page
    And I set the following fields to these values:
      | completiongradeitemnumber             | Whole forum |
    And I click on "Save and display" "button"
    And I should see "Require grade can't be enabled for Whole forum because grading by Whole forum is not enabled."
    And I set the following fields to these values:
      | Whole forum grading > Type            | Point       |
    And I click on "Save and display" "button"
    And I should not see "Require grade can't be enabled for Whole forum because grading by Whole forum is not enabled."
    And I should see "Receive a grade"

  Scenario: Require passing grade for multiple grading items activities
    Given I am on the "Test forum" "forum activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Ratings > Aggregate type        | Average of ratings |
      | id_scale_modgrade_type          | Point              |
      | Ratings > scale[modgrade_point] | 60                 |
      | Whole forum grading > Type      | Point              |
    And I expand all fieldsets
    And I set the field "Add requirements" to "1"
    And I set the field "Receive a grade" to "1"
    And I set the field "Passing grade" to "1"
    When I click on "Save and display" "button"
    Then I should see "This activity does not have a valid grade to pass set"
    And I set the following fields to these values:
      | gradepass | 50 |
    And I click on "Save and display" "button"
    And I should see "Receive a passing grade"
    And I am on the "Test forum" "forum activity editing" page
    And I set the following fields to these values:
      | completiongradeitemnumber             | Whole forum |
    And I click on "Save and display" "button"
    And I should see "This activity does not have a valid grade to pass set"
    And I expand all fieldsets
    And I set the field "Grade to pass" to "50"
    And I click on "Save and display" "button"
    And I should not see "This activity does not have a valid grade to pass set"
    And I should see "Receive a passing grade"

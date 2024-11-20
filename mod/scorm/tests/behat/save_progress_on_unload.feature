@mod @mod_scorm @_file_upload @_switch_iframe @_alert
Feature: Confirm progress gets saved on unload events
  In order to let students access a scorm package
  As a teacher
  I need to add scorm activity to a course
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I change window size to "large"

  @javascript
  Scenario: Test progress gets saved correctly when the user navigates away from the scorm activity
    Given the following "activity" exists:
      | activity        | scorm                                                              |
      | course          | C1                                                                 |
      | name            | Runtime Basic Calls SCORM 2004 3rd Edition package                 |
      | packagefilepath | mod/scorm/tests/packages/RuntimeBasicCalls_SCORM20043rdEdition.zip |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on the "Runtime Basic Calls SCORM 2004 3rd Edition package" "scorm activity" page
    Then I should see "Enter"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I press "Next"
    And I press "Next"
    And I switch to "contentFrame" iframe
    And I should see "Scoring"
    And I switch to the main frame
    And I am on the "Runtime Basic Calls SCORM 2004 3rd Edition package" "scorm activity" page
    And I should see "Enter"
    And I click on "Enter" "button" confirming the dialogue
    And I switch to "scorm_object" iframe
    And I switch to "contentFrame" iframe
    And I should see "Scoring"
    And I switch to the main frame
    # Go away from the scorm to stop background requests
    And I am on homepage

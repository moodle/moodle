@mod @mod_scorm @_file_upload @_switch_iframe
Feature: Scorm multi-sco review mode.
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

  @javascript
  Scenario: Test re-entry and make sure next uncompleted SCO is shown on second entry.
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Multi-sco SCORM package |
      | Description | Description |
      | Completion tracking | Show activity as complete when conditions are met |
    And I set the field "Completed" to "1"
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I click on "Par?" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Par"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    And I am on homepage
    And I follow "Course 1"
    And I follow "Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    Then I should see "Scoring"
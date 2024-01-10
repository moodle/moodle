@mod @mod_scorm @core_completion
Feature: Scorm activity default completion
  In order to make easier for teachers to set completion conditions
  As a teacher or admin
  I need to be able to set default condition completion
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | enablecompletion | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Completion conditions when there is no default set at course level.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "Completion tracking" matches value "Students can manually mark the activity as completed"
    And I set the following fields to these values:
      | completion | Show activity as complete when conditions are met|
    And the field "completionstatusrequired[4]" matches value "1"
    And the field "completionstatusrequired[2]" matches value "0"

  @javascript
  Scenario: Completion conditions when default completion is set at course level.
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "SCORM" "checkbox"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | completion | Show activity as complete when conditions are met|
      | completionview | 1 |
      | completionstatusrequired[4] | 0 |
      | completionstatusrequired[2]   | 0 |
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "Completion tracking" matches value "Show activity as complete when conditions are met"
    And the field "completionview" matches value "1"
    And the field "completionstatusrequired[4]" matches value "0"
    And the field "completionstatusrequired[2]" matches value "0"

  @javascript
  Scenario: Completion conditions when 'Passed' is marked as default but 'Completed' is unmarked.
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "SCORM" "checkbox"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | completion | Show activity as complete when conditions are met|
      | completionview | 1 |
      | completionstatusrequired[4] | 0 |
      | completionstatusrequired[2]   | 1 |
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "Completion tracking" matches value "Show activity as complete when conditions are met"
    And the field "completionview" matches value "1"
    And the field "completionstatusrequired[4]" matches value "0"
    And the field "completionstatusrequired[2]" matches value "1"

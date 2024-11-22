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
  Scenario: Completion conditions when there is no default set at site or course level.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "None" matches value "1"
    And the field "Add requirements" matches value "0"
    And I set the field "Add requirements" to "1"
    And the field "completionstatusrequired[4]" matches value "1"
    And the field "completionstatusrequired[2]" matches value "0"

  @javascript
  Scenario: Completion conditions when default completion is set at site level but not at course level.
    Given I log in as "admin"
    And I navigate to "Courses > Default settings > Default activity completion" in site administration
    And I click on "Expand SCORM" "button"
    And I set the following fields to these values:
      | id_completion_scorm_2    | 1 |
      | completionview_scorm     | 1 |
      | completionstatusrequired_scorm[4] | 0 |
      | completionstatusrequired_scorm[2]   | 0 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-scorm']" "css_element"
    And I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "None" matches value "0"
    And the field "Add requirements" matches value "1"
    And the field "completionview" matches value "1"
    And the field "completionstatusrequired[4]" matches value "0"
    And the field "completionstatusrequired[2]" matches value "0"

  @javascript
  Scenario: Completion conditions when default completion is set at course level but not at site level.
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Expand SCORM" "button"
    And I set the following fields to these values:
      | id_completion_scorm_2    | 1 |
      | completionview_scorm     | 1 |
      | completionstatusrequired_scorm[4] | 0 |
      | completionstatusrequired_scorm[2]   | 0 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-scorm']" "css_element"
    And I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "None" matches value "0"
    And the field "Add requirements" matches value "1"
    And the field "completionview" matches value "1"
    And the field "completionstatusrequired[4]" matches value "0"
    And the field "completionstatusrequired[2]" matches value "0"

  @javascript
  Scenario: Completion conditions when default completion is set at site and course level.
    Given I log in as "admin"
    And I navigate to "Courses > Default settings > Default activity completion" in site administration
    And I click on "Expand SCORM" "button"
    And I set the following fields to these values:
      | id_completion_scorm_2    | 1 |
      | completionview_scorm     | 1 |
      | completionstatusrequired_scorm[4] | 0 |
      | completionstatusrequired_scorm[2]   | 0 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-scorm']" "css_element"
    And the following "core_completion > Course defaults" exist:
      | course | module | completion |
      | C1    | scorm  | 1          |
    And I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "None" matches value "0"
    And the field "Students must manually mark the activity as done" matches value "1"
    And the field "Add requirements" matches value "0"

  @javascript
  Scenario: Completion conditions when 'Passed' is marked as default but 'Completed' is unmarked.
    Given I log in as "admin"
    And I navigate to "Courses > Default settings > Default activity completion" in site administration
    And I click on "Expand SCORM" "button"
    And I set the following fields to these values:
      | id_completion_scorm_2    | 1 |
      | completionview_scorm     | 1 |
      | completionstatusrequired_scorm[4] | 0 |
      | completionstatusrequired_scorm[2]   | 1 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-scorm']" "css_element"
    And I am on "Course 1" course homepage with editing mode on
#    When I add a "Teaching Tool 1" to section "1" using the activity chooser
    And I open the activity chooser
    When I click on "Add a new SCORM" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    Then the field "None" matches value "0"
    And the field "Add requirements" matches value "1"
    And the field "completionview" matches value "1"
    And the field "completionstatusrequired[4]" matches value "0"
    And the field "completionstatusrequired[2]" matches value "1"

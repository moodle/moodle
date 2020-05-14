@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript
Feature: Set up attempt grading options into H5P activity
  In order to use automatic grading in H5P activity
  As a teacher
  I need to be able to configure the attempt settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"

  Scenario: Default values should have tracking and grading
    When the field "Type" matches value "Point"
    Then the "Grading method" "select" should be enabled

  Scenario: Scale grading should not have a grading method.
    When I set the following fields to these values:
          | Name        | Awesome H5P package |
          | Type        | Scale               |
    Then the "Grading method" "select" should be disabled

  Scenario: None grading should not have a grading method.
    When I set the following fields to these values:
          | Name        | Awesome H5P package |
          | Type        | None                |
    Then the "Grading method" "select" should be disabled

  Scenario: Point grading should have a grading method.
    When I set the following fields to these values:
          | Name        | Awesome H5P package |
          | Type        | Point               |
    Then the "Grading method" "select" should be enabled

  Scenario: Disable tracking should make grading method disappear.
    When I set the following fields to these values:
          | Name                    | Awesome H5P package |
          | Enable attempt tracking | No                   |
    Then "Grading method" "field" should not be visible

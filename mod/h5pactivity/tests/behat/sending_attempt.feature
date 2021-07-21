@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Do a H5P attempt
  In order to let students do a H5P attempt
  As a teacher
  I need to list students attempts on the log report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name        | Awesome H5P package |
      | Description | Description         |
    And I upload "h5p/tests/fixtures/multiple-choice-2-6.h5p" file to "Package file" filemanager

  @javascript
  Scenario: View an H5P as a teacher
    When I click on "Save and display" "button"
    And I wait until the page is ready
    Then I should see "This content is displayed in preview mode"

  @javascript
  Scenario: To an attempts and check on course log report
    When I click on "Save and return to course" "button"
    And I log out
    Given I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I wait until the page is ready
    And I should not see "This content is displayed in preview mode"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Correct one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    And I follow "Student 1"
    Then I follow "Today's logs"
    And I should see "xAPI statement received"

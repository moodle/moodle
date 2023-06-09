@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript
Feature: Users can see the H5P recent activity from the recent activity block
  In order to quickly see the updates from H5P activity in my course
  As a user
  I need to be able to see the recent activity in the recent activity block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And the following "activity" exists:
      | activity        | h5pactivity                                |
      | course          | C1                                         |
      | name            | Awesome H5P package                        |
      | packagefilepath | h5p/tests/fixtures/multiple-choice-2-6.h5p |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | recent_activity | Course       | C1        | course-view-*   | side-pre      |
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    # The H5P content needs some time to be displayed (so better to wait for 1 second to avoid random errors).
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Wrong one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student2
    # The H5P content needs some time to be displayed (so better to wait for 1 second to avoid random errors).
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Correct one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame

  Scenario: Student see only his own activity
    Given I am on the "Course 1" course page logged in as student1
    And I should see "H5P submitted:" in the "Recent activity" "block"
    And I should see "Student 1" in the "Recent activity" "block"
    And I should not see "Grade:" in the "Recent activity" "block"
    And I should not see "Student 2" in the "Recent activity" "block"
    When I click on "Full report of recent activity" "link"
    Then I should see "H5P Awesome H5P package"
    And I should see "Student 1 - "
    And I should not see "Grade:"
    And I should not see "Student 2 - "

  Scenario: Teacher see each student activity
    Given I am on the "Course 1" course page logged in as teacher1
    And I should see "H5P submitted:" in the "Recent activity" "block"
    And I should see "Student 1" in the "Recent activity" "block"
    And I should not see "Grade:" in the "Recent activity" "block"
    And I should see "Student 2" in the "Recent activity" "block"
    When I click on "Full report of recent activity" "link"
    Then I should see "H5P Awesome H5P package"
    And I should see "Student 1 - "
    And I should see "Grade:"
    And I should see "Student 2 - "

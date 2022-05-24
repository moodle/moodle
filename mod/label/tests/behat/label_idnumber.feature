@mod @mod_label

Feature: set label idnumber
  In order to set label idnumber
  As a teacher
  I should create label activity and set an ID number

  Scenario: label ID number input box should be shown.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | Frist | teacher1@example.com |
      | student | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C1 | editingteacher |
      | student | C1 | student |
    And the following "activities" exist:
      | activity | course | section | intro                    | idnumber |
      | label    | C1     | 1       | Label with ID number set | C1LABEL1 |
    When I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    Then "Label with ID number set" activity should be visible
    And I turn editing mode off
    And "Label with ID number set" activity should be visible
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should see "Label with ID number set"
    And I log out
    And I am on the "Label with ID number set" "label activity editing" page logged in as teacher
    And I expand all fieldsets
    And I should see "ID number" in the "Common module settings" "fieldset"
    And the field "ID number" matches value "C1LABEL1"

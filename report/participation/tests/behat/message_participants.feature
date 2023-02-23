@report @report_participation @javascript
Feature: Use the particiaption report to message groups of students
  In order to engage with students based on participation
  As a teacher
  I need to be able to message students who have not participated in an activity

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activity" exists:
      | course      | C1             |
      | activity    | book           |
      | name        | Test book name |
      | idnumber    | Test book name |
      | idnumber    | book1          |
    And I am on the "Test book name" "book activity" page logged in as student1

  Scenario: Message all students from the participation report
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the field "instanceid" to "Test book name"
    And I set the field "roleid" to "Student"
    And I press "Go"
    When I click on "select-all-participants" "checkbox"
    And I choose "Send a message" from the participants page bulk action menu
    Then "Send message to 3 people" "dialogue" should exist
    And I set the field "Message" to "Hi there"
    And I press "Send message to 3 people"
    And I should see "Message sent to 3 people"

  Scenario: Message students who have not participated in book
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the field "instanceid" to "Test book name"
    And I set the field "roleid" to "Student"
    And I press "Go"
    And I should see "Yes (1)" in the "Student 1" "table_row"
    And I should see "No" in the "Student 2" "table_row"
    And I should see "No" in the "Student 3" "table_row"
    When I press "Select all 'No'"
    And I choose "Send a message" from the participants page bulk action menu
    Then "Send message to 2 people" "dialogue" should exist
    And I set the field "Message" to "Hi there"
    And I press "Send message to 2 people"
    And I should see "Message sent to 2 people"

  Scenario: Ensure no message options when messaging is disabled
    Given the following config values are set as admin:
      | messaging | 0 |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    When I set the field "instanceid" to "Test book name"
    And I set the field "roleid" to "Student"
    And I press "Go"
    Then I should not see "With selected users..."
    And "select-all-participants" "checkbox" should not exist

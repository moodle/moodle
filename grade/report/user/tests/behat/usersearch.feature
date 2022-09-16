@core @core_grades @gradereport_user @javascript
Feature: Within the User report, a teacher can search for users.
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher   | 1         | teacher1@example.com  | t1        |
      | student1  | Student   | 1         | student1@example.com  | s1        |
      | student2  | Student   | 2         | student2@example.com  | s2        |
      | student32 | Student   | 32        | student32@example.com | s32       |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
      | student32 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I change window size to "large"

  Scenario: A teacher can search for and find a user to view
    When I navigate to "View > User report" in the course gradebook
    And I click on "Click to select user" "link"
    And I confirm "Student 1" in "Select a user" search within the gradebook widget exists
    And I confirm "Student 2" in "Select a user" search within the gradebook widget exists
    And I confirm "Student 32" in "Select a user" search within the gradebook widget exists
    And I set the field "searchinput" to "2"
    And I wait "1" seconds
    Then I confirm "Student 2" in "Select a user" search within the gradebook widget exists
    And I confirm "Student 32" in "Select a user" search within the gradebook widget exists
    And I confirm "Student 1" in "Select a user" search within the gradebook widget does not exist

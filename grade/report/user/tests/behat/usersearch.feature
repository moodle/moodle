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

  Scenario: A teacher can search for and find a user to view
    Given I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    When I click on ".search-widget[data-searchtype='user']" "css_element"
    Then I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget exists
    And I confirm "Student 32" in "user" search within the gradebook widget exists
    And I set the field "Search users" to "2"
    And I wait "1" seconds
    And I confirm "Student 2" in "user" search within the gradebook widget exists
    And I confirm "Student 32" in "user" search within the gradebook widget exists
    And I confirm "Student 1" in "user" search within the gradebook widget does not exist

  Scenario: A teacher can only search for fields that he allowed to see
    Given the following "permission overrides" exist:
      | capability                         | permission | role             | contextlevel | reference |
      | moodle/course:viewhiddenuserfields | Prohibit   | editingteacher   | System       |           |
    And the following config values are set as admin:
      | hiddenuserfields | email |
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    When I click on ".search-widget[data-searchtype='user']" "css_element"
    And I set the field "Search users" to "Student"
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget exists
    And I confirm "Student 32" in "user" search within the gradebook widget exists
    # Email is not shown in results.
    And I confirm "Student" in "user" search within the gradebook widget exists
    And I confirm "example.com" in "user" search within the gradebook widget does not exist
    # Email is not searchable.
    And I set the field "Search users" to "student5@example.com"
    And I confirm "0 results found" in "user" search within the gradebook widget exists

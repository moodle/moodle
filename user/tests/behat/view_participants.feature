@core @core_user
Feature: View course participants
  In order to know who is on a course
  As a teacher
  I need to be able to view the participants on a course

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                 |
      | teacher1x  | Teacher   | 1x       | teacher1x@example.com  |
      | student0x  | Student   | 0x       | student0x@example.com  |
      | student1x  | Student   | 1x       | student1x@example.com  |
      | student2x  | Student   | 2x       | student2x@example.com  |
      | student3x  | Student   | 3x       | student3x@example.com  |
      | student4x  | Student   | 4x       | student4x@example.com  |
      | student5x  | Student   | 5x       | student5x@example.com  |
      | student6x  | Student   | 6x       | student6x@example.com  |
      | student7x  | Student   | 7x       | student7x@example.com  |
      | student8x  | Student   | 8x       | student8x@example.com  |
      | student9x  | Student   | 9x       | student9x@example.com  |
      | student10x | Student   | 10x      | student10x@example.com |
      | student11x | Student   | 11x      | student11x@example.com |
      | student12x | Student   | 12x      | student12x@example.com |
      | student13x | Student   | 13x      | student13x@example.com |
      | student14x | Student   | 14x      | student14x@example.com |
      | student15x | Student   | 15x      | student15x@example.com |
      | student16x | Student   | 16x      | student16x@example.com |
      | student17x | Student   | 17x      | student17x@example.com |
      | student18x | Student   | 18x      | student18x@example.com |
      | student19x | Student   | 19x      | student19x@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           | status | timeend |
      | teacher1x  | C1     | editingteacher |    0   |    0    |
      | student0x  | C1     | student        |    0   |    0    |
      | student1x  | C1     | student        |    0   |    0    |
      | student2x  | C1     | student        |    0   |    0    |
      | student3x  | C1     | student        |    0   |    0    |
      | student4x  | C1     | student        |    0   |    0    |
      | student5x  | C1     | student        |    0   |    0    |
      | student6x  | C1     | student        |    0   |    0    |
      | student7x  | C1     | student        |    0   |    0    |
      | student8x  | C1     | student        |    0   |    0    |
      | student9x  | C1     | student        |    0   |    0    |
      | student10x | C1     | student        |    1   |    0    |
      | student11x | C1     | student        |    0   |  100    |
      | student12x | C1     | student        |    0   |    0    |
      | student13x | C1     | student        |    0   |    0    |
      | student14x | C1     | student        |    0   |    0    |
      | student15x | C1     | student        |    0   |    0    |
      | student16x | C1     | student        |    0   |    0    |
      | student17x | C1     | student        |    0   |    0    |
      | student18x | C1     | student        |    0   |    0    |

  @javascript
  Scenario: Use select and deselect all buttons
    Given I log in as "teacher1x"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "Select all" "checkbox"
    Then the field "Select 'Teacher 1x'" matches value "1"
    And the field "Select 'Student 0x'" matches value "1"
    And the field "Select 'Student 1x'" matches value "1"
    And the field "Select 'Student 2x'" matches value "1"
    And the field "Select 'Student 3x'" matches value "1"
    And the field "Select 'Student 4x'" matches value "1"
    And the field "Select 'Student 5x'" matches value "1"
    And the field "Select 'Student 6x'" matches value "1"
    And the field "Select 'Student 7x'" matches value "1"
    And the field "Select 'Student 8x'" matches value "1"
    And the field "Select 'Student 9x'" matches value "1"
    And the field "Select 'Student 10x'" matches value "1"
    And the field "Select 'Student 11x'" matches value "1"
    And the field "Select 'Student 12x'" matches value "1"
    And the field "Select 'Student 13x'" matches value "1"
    And the field "Select 'Student 14x'" matches value "1"
    And the field "Select 'Student 14x'" matches value "1"
    And the field "Select 'Student 15x'" matches value "1"
    And the field "Select 'Student 16x'" matches value "1"
    And the field "Select 'Student 17x'" matches value "1"
    And the field "Select 'Student 18x'" matches value "1"

    And I click on "Deselect all" "checkbox"
    And the field "Select 'Teacher 1x'" matches value "0"
    And the field "Select 'Student 0x'" matches value "0"
    And the field "Select 'Student 1x'" matches value "0"
    And the field "Select 'Student 2x'" matches value "0"
    And the field "Select 'Student 3x'" matches value "0"
    And the field "Select 'Student 4x'" matches value "0"
    And the field "Select 'Student 5x'" matches value "0"
    And the field "Select 'Student 6x'" matches value "0"
    And the field "Select 'Student 7x'" matches value "0"
    And the field "Select 'Student 8x'" matches value "0"
    And the field "Select 'Student 9x'" matches value "0"
    And the field "Select 'Student 10x'" matches value "0"
    And the field "Select 'Student 11x'" matches value "0"
    And the field "Select 'Student 12x'" matches value "0"
    And the field "Select 'Student 13x'" matches value "0"
    And the field "Select 'Student 14x'" matches value "0"
    And the field "Select 'Student 14x'" matches value "0"
    And the field "Select 'Student 15x'" matches value "0"
    And the field "Select 'Student 16x'" matches value "0"
    And the field "Select 'Student 17x'" matches value "0"
    And the field "Select 'Student 18x'" matches value "0"

  @javascript
  Scenario: Sort and paginate the list of users
    Given I log in as "teacher1x"
    And the following "course enrolments" exist:
      | user      | course | role           |
      | student19x | C1     | student |
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Email address"
    When I click on "2" "link" in the "//nav[@aria-label='Page']" "xpath_element"
    Then I should not see "student0x@example.com"
    And I should not see "student19x@example.com"
    And I should see "teacher1x@example.com"
    And I follow "Email address"
    And I click on "2" "link" in the "//nav[@aria-label='Page']" "xpath_element"
    And I should not see "teacher1x@example.com"
    And I should not see "student19x@example.com"
    And I should not see "student1x@example.com"
    And I should see "student0x@example.com"

  @javascript
  Scenario: Use select all users on this page, select all users and deselect all
    Given the following "course enrolments" exist:
      | user      | course | role    |
      | student19x | C1     | student |
    When I log in as "teacher1x"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Select all" "checkbox"
    Then I should not see "Student 9x"
    And the field "Select 'Teacher 1x'" matches value "1"
    And the field "Select 'Student 0x'" matches value "1"
    And the field "Select 'Student 1x'" matches value "1"
    And the field "Select 'Student 2x'" matches value "1"
    And the field "Select 'Student 3x'" matches value "1"
    And the field "Select 'Student 4x'" matches value "1"
    And the field "Select 'Student 5x'" matches value "1"
    And the field "Select 'Student 6x'" matches value "1"
    And the field "Select 'Student 7x'" matches value "1"
    And the field "Select 'Student 8x'" matches value "1"
    And the field "Select 'Student 10x'" matches value "1"
    And the field "Select 'Student 11x'" matches value "1"
    And the field "Select 'Student 12x'" matches value "1"
    And the field "Select 'Student 13x'" matches value "1"
    And the field "Select 'Student 14x'" matches value "1"
    And the field "Select 'Student 14x'" matches value "1"
    And the field "Select 'Student 15x'" matches value "1"
    And the field "Select 'Student 16x'" matches value "1"
    And the field "Select 'Student 17x'" matches value "1"
    And the field "Select 'Student 18x'" matches value "1"
    And the field "Select 'Student 19x'" matches value "1"

    And I click on "Deselect all" "checkbox"
    And the field "Select 'Teacher 1x'" matches value "0"
    And the field "Select 'Student 0x'" matches value "0"
    And the field "Select 'Student 1x'" matches value "0"
    And the field "Select 'Student 2x'" matches value "0"
    And the field "Select 'Student 3x'" matches value "0"
    And the field "Select 'Student 4x'" matches value "0"
    And the field "Select 'Student 5x'" matches value "0"
    And the field "Select 'Student 6x'" matches value "0"
    And the field "Select 'Student 7x'" matches value "0"
    And the field "Select 'Student 8x'" matches value "0"
    And the field "Select 'Student 10x'" matches value "0"
    And the field "Select 'Student 11x'" matches value "0"
    And the field "Select 'Student 12x'" matches value "0"
    And the field "Select 'Student 13x'" matches value "0"
    And the field "Select 'Student 14x'" matches value "0"
    And the field "Select 'Student 14x'" matches value "0"
    And the field "Select 'Student 15x'" matches value "0"
    And the field "Select 'Student 16x'" matches value "0"
    And the field "Select 'Student 17x'" matches value "0"
    And the field "Select 'Student 18x'" matches value "0"
    And the field "Select 'Student 19x'" matches value "0"

    # Pressing the "Select all X users" button should select all including the 21st user (Student 9x).
    And I press "Select all 21 users"
    And I should see "Student 9x"
    And the field "Select 'Teacher 1x'" matches value "1"
    And the field "Select 'Student 0x'" matches value "1"
    And the field "Select 'Student 1x'" matches value "1"
    And the field "Select 'Student 2x'" matches value "1"
    And the field "Select 'Student 3x'" matches value "1"
    And the field "Select 'Student 4x'" matches value "1"
    And the field "Select 'Student 5x'" matches value "1"
    And the field "Select 'Student 6x'" matches value "1"
    And the field "Select 'Student 7x'" matches value "1"
    And the field "Select 'Student 8x'" matches value "1"
    And the field "Select 'Student 9x'" matches value "1"
    And the field "Select 'Student 10x'" matches value "1"
    And the field "Select 'Student 11x'" matches value "1"
    And the field "Select 'Student 12x'" matches value "1"
    And the field "Select 'Student 13x'" matches value "1"
    And the field "Select 'Student 14x'" matches value "1"
    And the field "Select 'Student 14x'" matches value "1"
    And the field "Select 'Student 15x'" matches value "1"
    And the field "Select 'Student 16x'" matches value "1"
    And the field "Select 'Student 17x'" matches value "1"
    And the field "Select 'Student 18x'" matches value "1"
    And the field "Select 'Student 19x'" matches value "1"
    And the "With selected users..." "select" should be enabled

    And I click on "Deselect all" "checkbox"
    And the field "Select 'Teacher 1x'" matches value "0"
    And the field "Select 'Student 0x'" matches value "0"
    And the field "Select 'Student 1x'" matches value "0"
    And the field "Select 'Student 2x'" matches value "0"
    And the field "Select 'Student 3x'" matches value "0"
    And the field "Select 'Student 4x'" matches value "0"
    And the field "Select 'Student 5x'" matches value "0"
    And the field "Select 'Student 6x'" matches value "0"
    And the field "Select 'Student 7x'" matches value "0"
    And the field "Select 'Student 8x'" matches value "0"
    And the field "Select 'Student 9x'" matches value "0"
    And the field "Select 'Student 10x'" matches value "0"
    And the field "Select 'Student 11x'" matches value "0"
    And the field "Select 'Student 12x'" matches value "0"
    And the field "Select 'Student 13x'" matches value "0"
    And the field "Select 'Student 14x'" matches value "0"
    And the field "Select 'Student 14x'" matches value "0"
    And the field "Select 'Student 15x'" matches value "0"
    And the field "Select 'Student 16x'" matches value "0"
    And the field "Select 'Student 17x'" matches value "0"
    And the field "Select 'Student 18x'" matches value "0"
    And the field "Select 'Student 19x'" matches value "0"

  Scenario: View the participants page as a teacher
    Given I log in as "teacher1x"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then I should see "Active" in the "student0x" "table_row"
    Then I should see "Active" in the "student1x" "table_row"
    And I should see "Active" in the "student2x" "table_row"
    And I should see "Active" in the "student3x" "table_row"
    And I should see "Active" in the "student4x" "table_row"
    And I should see "Active" in the "student5x" "table_row"
    And I should see "Active" in the "student6x" "table_row"
    And I should see "Active" in the "student7x" "table_row"
    And I should see "Active" in the "student8x" "table_row"
    And I should see "Active" in the "student9x" "table_row"
    And I should see "Suspended" in the "student10x" "table_row"
    And I should see "Not current" in the "student11x" "table_row"
    And I should see "Active" in the "student12x" "table_row"
    And I should see "Active" in the "student13x" "table_row"
    And I should see "Active" in the "student14x" "table_row"
    And I should see "Active" in the "student15x" "table_row"
    And I should see "Active" in the "student16x" "table_row"
    And I should see "Active" in the "student17x" "table_row"
    And I should see "Active" in the "student18x" "table_row"

  Scenario: View the participants page as a student
    Given I log in as "student1x"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    # Student should not see the status column.
    Then I should not see "Status" in the "participants" "table"
    # Student should be able to see the other actively-enrolled students.
    And I should see "Student 1x" in the "participants" "table"
    And I should see "Student 2x" in the "participants" "table"
    And I should see "Student 3x" in the "participants" "table"
    And I should see "Student 4x" in the "participants" "table"
    And I should see "Student 5x" in the "participants" "table"
    And I should see "Student 6x" in the "participants" "table"
    And I should see "Student 7x" in the "participants" "table"
    And I should see "Student 8x" in the "participants" "table"
    # Suspended and non-current students should not be rendered.
    And I should not see "Student 10x" in the "participants" "table"
    And I should not see "Student 11x" in the "participants" "table"

  Scenario: Check status after disabling manual enrolment
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I navigate to "Users > Enrolment methods" in current page administration
    And I click on "Disable" "link" in the "Manual enrolments" "table_row"
    Then I navigate to course participants
    And I should see "Not current" in the "student0x" "table_row"

@core @core_user
Feature: View course participants
  In order to know who is on a course
  As a teacher
  I need to be able to view the participants on a course

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                 |
      | teacher1x  | Teacher   | 1x       | teacher1x@example.com  |
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
      | student20x | Student   | 20x      | student20x@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1x  | C1     | editingteacher |
      | student1x  | C1     | student        |
      | student2x  | C1     | student        |
      | student3x  | C1     | student        |
      | student4x  | C1     | student        |
      | student5x  | C1     | student        |
      | student6x  | C1     | student        |
      | student7x  | C1     | student        |
      | student8x  | C1     | student        |
      | student9x  | C1     | student        |
      | student10x | C1     | student        |
      | student11x | C1     | student        |
      | student12x | C1     | student        |
      | student13x | C1     | student        |
      | student14x | C1     | student        |
      | student15x | C1     | student        |
      | student16x | C1     | student        |
      | student17x | C1     | student        |
      | student18x | C1     | student        |
      | student19x | C1     | student        |

  @javascript
  Scenario: Use select and deselect all buttons
    Given I log in as "teacher1x"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I press "Select all"
    Then the field with xpath "//tbody//tr[1]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[2]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[3]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[4]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[5]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[6]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[7]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[8]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[9]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[10]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[11]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[12]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[13]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[14]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[15]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[16]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[17]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[18]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[19]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[20]//input[@class='usercheckbox']" matches value "1"

    And I press "Deselect all"
    And the field with xpath "//tbody//tr[1]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[2]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[3]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[4]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[5]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[6]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[7]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[8]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[9]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[10]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[11]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[12]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[13]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[14]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[15]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[16]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[17]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[18]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[19]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[20]//input[@class='usercheckbox']" matches value "0"

  @javascript
  Scenario: Use select all users on this page, select all n users and deselect all
    Given the following "course enrolments" exist:
      | user      | course | role    |
      | student20x | C1     | student |
    When I log in as "teacher1x"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Surname"
    And I press "Select all users on this page"
    Then I should not see "Student 9x"
    And the field with xpath "//tbody//tr[1]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[2]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[3]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[4]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[5]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[6]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[7]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[8]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[9]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[10]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[11]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[12]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[13]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[14]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[15]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[16]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[17]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[18]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[19]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[20]//input[@class='usercheckbox']" matches value "1"

    And I press "Deselect all"
    And the field with xpath "//tbody//tr[1]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[2]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[3]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[4]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[5]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[6]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[7]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[8]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[9]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[10]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[11]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[12]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[13]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[14]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[15]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[16]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[17]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[18]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[19]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[20]//input[@class='usercheckbox']" matches value "0"

    And I press "Select all 21 users"
    And I should see "Student 9x"
    And the field with xpath "//tbody//tr[1]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[2]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[3]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[4]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[5]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[6]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[7]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[8]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[9]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[10]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[11]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[12]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[13]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[14]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[15]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[16]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[17]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[18]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[19]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[20]//input[@class='usercheckbox']" matches value "1"
    And the field with xpath "//tbody//tr[21]//input[@class='usercheckbox']" matches value "1"

    And I press "Deselect all"
    And the field with xpath "//tbody//tr[1]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[2]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[3]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[4]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[5]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[6]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[7]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[8]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[9]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[10]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[11]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[12]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[13]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[14]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[15]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[16]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[17]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[18]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[19]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[20]//input[@class='usercheckbox']" matches value "0"
    And the field with xpath "//tbody//tr[21]//input[@class='usercheckbox']" matches value "0"

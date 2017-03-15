@core @core_user
Feature: View course participants
  In order to know who is on a course
  As a teacher
  I need to be able to view the participants on a course

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
      | student1  | Student   | 1        | student1@example.com  |
      | student2  | Student   | 2        | student2@example.com  |
      | student3  | Student   | 3        | student3@example.com  |
      | student4  | Student   | 4        | student4@example.com  |
      | student5  | Student   | 5        | student5@example.com  |
      | student6  | Student   | 6        | student6@example.com  |
      | student7  | Student   | 7        | student7@example.com  |
      | student8  | Student   | 8        | student8@example.com  |
      | student9  | Student   | 9        | student9@example.com  |
      | student10 | Student   | 10       | student10@example.com |
      | student11 | Student   | 11       | student11@example.com |
      | student12 | Student   | 12       | student12@example.com |
      | student13 | Student   | 13       | student13@example.com |
      | student14 | Student   | 14       | student14@example.com |
      | student15 | Student   | 15       | student15@example.com |
      | student16 | Student   | 16       | student16@example.com |
      | student17 | Student   | 17       | student17@example.com |
      | student18 | Student   | 18       | student18@example.com |
      | student19 | Student   | 19       | student19@example.com |
      | student20 | Student   | 20       | student20@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
      | student3  | C1     | student        |
      | student4  | C1     | student        |
      | student5  | C1     | student        |
      | student6  | C1     | student        |
      | student7  | C1     | student        |
      | student8  | C1     | student        |
      | student9  | C1     | student        |
      | student10 | C1     | student        |
      | student11 | C1     | student        |
      | student12 | C1     | student        |
      | student13 | C1     | student        |
      | student14 | C1     | student        |
      | student15 | C1     | student        |
      | student16 | C1     | student        |
      | student17 | C1     | student        |
      | student18 | C1     | student        |
      | student19 | C1     | student        |

  @javascript
  Scenario: Use select and deselect all buttons
    Given I log in as "teacher1"
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
      | student20 | C1     | student |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Surname"
    And I press "Select all users on this page"
    Then I should not see "Student 9"
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
    And I should see "Student 9"
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

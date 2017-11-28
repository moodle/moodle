@core @core_user
Feature: View course participants
  In order to know who is on a course
  As a teacher
  I need to be able to view the participants on a course

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
      | student0  | Student   | 0        | student0@example.com  |
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
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           | status | timeend |
      | teacher1  | C1     | editingteacher |    0   |    0    |
      | student0  | C1     | student        |    0   |    0    |
      | student1  | C1     | student        |    0   |    0    |
      | student2  | C1     | student        |    0   |    0    |
      | student3  | C1     | student        |    0   |    0    |
      | student4  | C1     | student        |    0   |    0    |
      | student5  | C1     | student        |    0   |    0    |
      | student6  | C1     | student        |    0   |    0    |
      | student7  | C1     | student        |    0   |    0    |
      | student8  | C1     | student        |    0   |    0    |
      | student9  | C1     | student        |    0   |    0    |
      | student10 | C1     | student        |    1   |    0    |
      | student11 | C1     | student        |    0   |  100    |
      | student12 | C1     | student        |    0   |    0    |
      | student13 | C1     | student        |    0   |    0    |
      | student14 | C1     | student        |    0   |    0    |
      | student15 | C1     | student        |    0   |    0    |
      | student16 | C1     | student        |    0   |    0    |
      | student17 | C1     | student        |    0   |    0    |
      | student18 | C1     | student        |    0   |    0    |

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

  Scenario: Sort and paginate the list of users
    Given I log in as "teacher1"
    And the following "course enrolments" exist:
      | user      | course | role           |
      | student19 | C1     | student |
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Email address"
    When I follow "2"
    Then I should not see "student0@example.com"
    And I should not see "student19@example.com"
    And I should see "teacher1@example.com"
    And I follow "Email address"
    And I follow "2"
    And I should not see "teacher1@example.com"
    And I should not see "student19@example.com"
    And I should not see "student1@example.com"
    And I should see "student0@example.com"

  @javascript
  Scenario: Use select all users on this page, select all n users and deselect all
    Given the following "course enrolments" exist:
      | user      | course | role    |
      | student19 | C1     | student |
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

  Scenario: View the participants page as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then I should see "Active" in the "student0" "table_row"
    And I should see "Active" in the "student1" "table_row"
    And I should see "Active" in the "student2" "table_row"
    And I should see "Active" in the "student3" "table_row"
    And I should see "Active" in the "student4" "table_row"
    And I should see "Active" in the "student5" "table_row"
    And I should see "Active" in the "student6" "table_row"
    And I should see "Active" in the "student7" "table_row"
    And I should see "Active" in the "student8" "table_row"
    And I should see "Active" in the "student9" "table_row"
    And I should see "Suspended" in the "student10" "table_row"
    And I should see "Not current" in the "student11" "table_row"
    And I should see "Active" in the "student12" "table_row"
    And I should see "Active" in the "student13" "table_row"
    And I should see "Active" in the "student14" "table_row"
    And I should see "Active" in the "student15" "table_row"
    And I should see "Active" in the "student16" "table_row"
    And I should see "Active" in the "student17" "table_row"
    And I should see "Active" in the "student18" "table_row"

  Scenario: View the participants page as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    # Student should not see the status column.
    Then I should not see "Status" in the "participants" "table"
    # Student should be able to see the other actively-enrolled students.
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Student 5" in the "participants" "table"
    And I should see "Student 6" in the "participants" "table"
    And I should see "Student 7" in the "participants" "table"
    And I should see "Student 8" in the "participants" "table"
    # Suspended and non-current students should not be rendered.
    And I should not see "Student 10" in the "participants" "table"
    And I should not see "Student 11" in the "participants" "table"

@enrol @enrol_meta
Feature: Enrolments are synchronised with meta courses
  In order to simplify enrolments in parent courses
  As a teacher
  I need to be able to set up meta enrolments

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
      | student4 | Student | 4 | student4@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
      | Course 2 | C2 |
      | Course 3 | C3 |
    And the following "groups" exist:
      | name | course | idnumber |
      | Groupcourse 1 | C3 | G1 |
      | Groupcourse 2 | C3 | G2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
      | student1 | C2 | student |
      | student2 | C2 | student |
    And I log in as "admin"
    And I navigate to "Manage enrol plugins" node in "Site administration > Plugins > Enrolments"
    And I click on "Enable" "link" in the "Course meta link" "table_row"
    And I am on homepage
    And I follow "Courses"

  Scenario: Add meta enrolment instance without groups
    When I follow "Course 3"
    And I add "Course meta link" enrolment method with:
      | Link course  | Course 1 |
    And I navigate to "Enrolled users" node in "Course administration > Users"
    Then I should see "Student 1"
    And I should see "Student 4"
    And I should not see "Groupcourse" in the "table.userenrolment" "css_element"

  Scenario: Add meta enrolment instance with groups
    When I follow "Course 3"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I set the field "Add method" to "Course meta link"
    And I press "Go"
    And I set the following fields to these values:
      | Link course  | Course 1      |
      | Add to group | Groupcourse 1 |
    And I press "Add method and create another"
    And I set the following fields to these values:
      | Link course  | Course 2      |
      | Add to group | Groupcourse 2 |
    And I press "Add method"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    Then I should see "Groupcourse 1" in the "Student 1" "table_row"
    And I should see "Groupcourse 1" in the "Student 2" "table_row"
    And I should see "Groupcourse 1" in the "Student 3" "table_row"
    And I should see "Groupcourse 1" in the "Student 4" "table_row"
    And I should see "Groupcourse 2" in the "Student 1" "table_row"
    And I should see "Groupcourse 2" in the "Student 2" "table_row"
    And I should not see "Groupcourse 2" in the "Student 3" "table_row"
    And I should not see "Groupcourse 2" in the "Student 4" "table_row"

  Scenario: Add meta enrolment instance with auto-created groups
    When I follow "Course 3"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I set the field "Add method" to "Course meta link"
    And I press "Go"
    And I set the following fields to these values:
      | Link course  | Course 1      |
      | Add to group | Create new group |
    And I press "Add method"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    Then I should see "Course 1 course" in the "Student 1" "table_row"
    And I should see "Course 1 course" in the "Student 2" "table_row"
    And I should see "Course 1 course" in the "Student 3" "table_row"
    And I should see "Course 1 course" in the "Student 4" "table_row"
    And I navigate to "Groups" node in "Course administration > Users"
    And the "Groups" select box should contain "Course 1 course (4)"

  Scenario: Backup and restore of meta enrolment instance
    When I follow "Course 3"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I set the field "Add method" to "Course meta link"
    And I press "Go"
    And I set the following fields to these values:
      | Link course  | Course 1      |
      | Add to group | Groupcourse 1 |
    And I press "Add method and create another"
    And I set the following fields to these values:
      | Link course  | Course 2      |
    And I press "Add method"
    When I backup "Course 3" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I press "Continue"
    And I set the field "targetid" to "1"
    And I click on "Continue" "button" in the ".bcs-new-course" "css_element"
    And I press "Next"
    And I set the field "Course name" to "Course 4"
    And I press "Next"
    And I press "Perform restore"
    And I trigger cron
    And I am on homepage
    And I follow "Courses"
    And I follow "Course 4"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    Then I should see "Course meta link (Course 1)"
    And I should see "Course meta link (Course 2)"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    And I should see "Groupcourse 1" in the "Student 1" "table_row"
    And I should see "Groupcourse 1" in the "Student 2" "table_row"
    And I should see "Groupcourse 1" in the "Student 3" "table_row"
    And I should see "Groupcourse 1" in the "Student 4" "table_row"
    And I should see "Course 2" in the "Student 1" "table_row"
    And I should not see "Course 2" in the "Student 3" "table_row"

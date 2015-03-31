@mod @mod_assign
Feature: Grant an extension to an offline student
  In order to allow students to have an accurate due date
  As a teacher
  I need to grant students extensions at any time

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  @javascript
  Scenario: Granting an extension to an offline assignment
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | duedate    |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 0                                   | 0                             | 1388534400 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I follow "View/grade all submissions"
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And I follow "Grant extension"
    And I set the field "Enable" to "1"
    And I press "Save changes"
    Then I should see "Extension granted until:" in the "Student 1" "table_row"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "Extension due date"

  @javascript @_alert
  Scenario: Granting extensions to an offline assignment (batch action)
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | duedate    |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 0                                   | 0                             | 1388534400 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I follow "View/grade all submissions"
    And I set the field "selectall" to "1"
    And I set the field "operation" to "Grant extension"
    And I click on "Go" "button" confirming the dialogue
    And I set the field "Enable" to "1"
    And I press "Save changes"
    Then I should see "Extension granted until:" in the "Student 1" "table_row"
    And I should see "Extension granted until:" in the "Student 2" "table_row"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "Extension due date"

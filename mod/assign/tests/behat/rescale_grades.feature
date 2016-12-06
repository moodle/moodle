@mod @mod_assign @javascript
Feature: Check that the assignment grade can be rescaled when the max grade is changed
  In order to ensure that the percentages are not affected by changes to the max grade
  As a teacher
  I need to rescale all grades when updating the max grade

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student10@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "40"
    And I press "Save changes"
    And I press "Ok"
    And I follow "Edit settings"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain "40.00"
    And I follow "Test assignment name"

  Scenario: Update the max grade for an assignment without rescaling existing grades
    Given I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Rescale existing grades" to "No"
    And I set the field "Maximum grade" to "80"
    When I press "Save and display"
    And I navigate to "View all submissions" in current page administration
    Then "Student 1" row "Grade" column of "generaltable" table should contain "40.00"

  Scenario: Update an assignment without touching the max grades
    Given I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Rescale existing grades" to "No"
    And I set the field "Maximum grade" to "80"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Rescale existing grades" to "Yes"
    And I set the field "Maximum grade" to "80"
    When I press "Save and display"
    And I navigate to "View all submissions" in current page administration
    Then "Student 1" row "Grade" column of "generaltable" table should contain "40.00"

  Scenario: Update the max grade for an assignment rescaling existing grades
    Given I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Rescale existing grades" to "Yes"
    And I set the field "Maximum grade" to "50"
    When I press "Save and display"
    And I navigate to "View all submissions" in current page administration
    Then "Student 1" row "Grade" column of "generaltable" table should contain "20.00"

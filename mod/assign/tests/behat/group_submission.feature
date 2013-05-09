@mod @mod_assign
Feature: Group assignment submissions
  In order to allow students to work collaboratively on an assignment
  As a teacher
  I need to group submissions in groups

  @javascript
  Scenario: Switch between group modes
    Given the following "courses" exists:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student0 | Student | 0 | student0@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student0 | C1 | student |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exists:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Students submit in groups | Yes |
      | Group mode | No groups |
    And I follow "Test assignment name"
    When I follow "View/grade all submissions"
    Then "//tr[contains(., 'Student 0')][contains(., 'Default group')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 1')][contains(., 'Default group')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 2')][contains(., 'Default group')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 3')][contains(., 'Default group')]" "xpath_element" should exists
    And I follow "Edit settings"
    And I fill the moodle form with:
      | Group mode | Separate groups |
    And I press "Save and return to course"
    And I follow "Edit settings"
    And I fill the moodle form with:
      | Group mode | Separate groups |
    And I press "Save changes"
    And I expand "Users" node
    And I follow "Groups"
    And I add "student0" user to "Group 1" group
    And I add "student1" user to "Group 1" group
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And "//tr[contains(., 'Student 0')][contains(., 'Group 1')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 1')][contains(., 'Group 1')]" "xpath_element" should exists
    And I should not see "Student 2"
    And I select "All participants" from "Separate groups"
    And "//tr[contains(., 'Student 0')][contains(., 'Group 1')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 1')][contains(., 'Group 1')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 2')][contains(., 'Default group')]" "xpath_element" should exists
    And "//tr[contains(., 'Student 3')][contains(., 'Default group')]" "xpath_element" should exists

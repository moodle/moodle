@core @core_group
Feature: The description of a group can be viewed by students and teachers
  In order to view the description of a group
  As a teacher
  I need to create groups and add descriptions to them.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  @javascript
  Scenario: A student can see the group description when visible groups are set. Teachers can see group details.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Visible groups |
    And I press "Save and display"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group A |
      | Group description | Description for Group A |
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group B |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group A" group members
    And I add "Student 2 (student2@example.com)" user to "Group B" group members
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    And I should see "Description for Group A"
    And ".groupinfobox" "css_element" should exist
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group B"
    And I click on "Apply filters" "button"
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And I should see "Student 2" in the "participants" "table"
    And ".groupinfobox" "css_element" should not exist
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    Then I should see "Description for Group A"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And I should see "Student 2" in the "participants" "table"
    And ".groupinfobox" "css_element" should not exist

  @javascript
  Scenario: A student can not see the group description when separate groups are set. Teachers can see group details.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group A |
      | Group description | Description for Group A |
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group B |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group A" group members
    And I add "Student 2 (student2@example.com)" user to "Group B" group members
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    And I should see "Description for Group A"
    And ".groupinfobox" "css_element" should exist
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group B"
    And I click on "Apply filters" "button"
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And ".groupinfobox" "css_element" should not exist
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    And I should see "Student 1" in the "participants" "table"
    And I should not see "Description for Group A"
    And ".groupinfobox" "css_element" should not exist
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And I should see "Student 2" in the "participants" "table"
    And ".groupinfobox" "css_element" should not exist

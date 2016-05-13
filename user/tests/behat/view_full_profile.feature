@core @core_user
Feature: Access to full profiles of users
  In order to allow visibility of full profiles
  As an admin
  I need to set global permission or disable forceloginforprofiles

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student2@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C1 | editingteacher |
      | student3 | C2 | student |

  Scenario: Viewing full profiles with default settings
    When I log in as "student1"
    And I follow "Course 1"
    # Another student's full profile is not visible
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    Then I should not see "Full profile"
    # Teacher's full profile is visible
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Teacher 1"
    And I follow "Full profile"
    And I should see "First access to site"
    # Own full profile is visible
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I click on "Student 1" "link" in the "#participants" "css_element"
    And I follow "Full profile"
    And I should see "First access to site"

  @javascript
  Scenario: Viewing full profiles with forceloginforprofiles off
    Given the following config values are set as admin:
      |  forceloginforprofiles | 0 |
    When I log in as "student1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    And I follow "Full profile"
    Then I should see "First access to site"

  Scenario: Viewing full profiles with global permission
    Given I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | moodle/user:viewdetails | Allow |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    And I follow "Full profile"
    Then I should see "First access to site"

  @javascript
  Scenario: Viewing own full profile
    Given I log in as "student1"
    When I follow "Profile" in the user menu
    Then I should see "First access to site"

  @javascript
  Scenario: Viewing full profiles of someone with the course contact role
    Given I log in as "admin"
    And I navigate to "Courses" node in "Site administration > Appearance"
    And I set the following fields to these values:
      | Course creator | 1 |
    And I press "Save changes"
    And I navigate to "Assign system roles" node in "Site administration > Users > Permissions"
    And I follow "Course creator"
    And I click on "//div[@class='userselector']/descendant::option[contains(., 'Student 3')]" "xpath_element"
    And I press "Add"
    And I log out
    When I log in as "student1"
    And I follow "Messages" in the user menu
    And I set the following fields to these values:
      | Search people and messages | Student 3 |
    And I press "Search people and messages"
    And I follow "Picture of Student 3"
    Then I should see "First access to site"

  @javascript
  Scenario: View full profiles of someone in the same group in a course with separate groups.
    Given I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Group mode | Separate groups |
      | Force group mode | Yes |
    And I press "Save and display"
    And I log out
    When I log in as "student1"
    And I follow "Messages" in the user menu
    And I set the following fields to these values:
      | Search people and messages | Student 2 |
    And I press "Search people and messages"
    And I follow "Picture of Student 2"
    And I should not see "First access to site"
    And I should see "The details of this user are not available to you"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I log out
    And I log in as "student1"
    And I follow "Messages" in the user menu
    And I set the following fields to these values:
      | Search people and messages | Student 2 |
    And I press "Search people and messages"
    And I follow "Picture of Student 2"
    Then I should see "First access to site"

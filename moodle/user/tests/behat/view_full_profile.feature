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
    And I am on "Course 1" course homepage
    # Another student's full profile is not visible
    And I navigate to course participants
    And I follow "Student 2"
    Then I should not see "Full profile"
    # Teacher's full profile is visible
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Teacher 1"
    And I follow "Full profile"
    And I should see "First access to site"
    # Own full profile is visible
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1" "link" in the "#participants" "css_element"
    And I follow "Full profile"
    And I should see "First access to site"

  @javascript
  Scenario: Viewing full profiles with forceloginforprofiles off
    Given the following config values are set as admin:
      |  forceloginforprofiles | 0 |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 2"
    And I follow "Full profile"
    Then I should see "First access to site"

  Scenario: Viewing full profiles with global permission
    Given I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | moodle/user:viewdetails | Allow |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
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
    And I view the "Student 3" contact in the message area
    And I click on ".profile-picture" "css_element"
    Then I should see "First access to site"

  @javascript
  Scenario: View full profiles of someone in the same group in a course with separate groups.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | Group mode | Separate groups |
      | Force group mode | Yes |
    And I press "Save and display"
    And I log out
    When I log in as "student1"
    And I view the "Student 2" contact in the message area
    And I click on ".profile-picture" "css_element"
    And I should not see "First access to site"
    And I should see "The details of this user are not available to you"
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I log out
    And I log in as "student1"
    And I view the "Student 2" contact in the message area
    And I click on ".profile-picture" "css_element"
    Then I should see "First access to site"

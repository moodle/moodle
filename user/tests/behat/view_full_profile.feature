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
    And the following config values are set as admin:
      | messaging | 1 |

  Scenario: Viewing full profiles with default settings
    When I log in as "student1"
    # Another student's full profile is visible
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 2"
    Then I should see "Full profile"
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

  Scenario: Viewing full profiles of students as a teacher
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Full profile"
    Then I should see "First access to site"

  Scenario: Viewing own full profile
    Given I log in as "student1"
    When I follow "Profile" in the user menu
    Then I should see "First access to site"

  @javascript
  Scenario: Viewing full profiles of someone with the course contact role
    Given I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Course creator | 1 |
    And I press "Save changes"
    And I navigate to "Users > Permissions > Assign system roles" in site administration
    And I follow "Course creator"
    And I click on "//div[@class='userselector']/descendant::option[contains(., 'Student 3')]" "xpath_element"
    And I press "Add"
    And I log out
    # Message search will not return a course contact unless the searcher shares a course with them,
    # or site-wide messaging is enabled ($CFG->messagingallusers).
    When I log in as "student1"
    And I open messaging
    And I search for "Student 3" in messaging
    Then I should see "No results"

  @javascript
  Scenario: View full profiles of someone in the same group in a course with separate groups.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Separate groups |
      | Force group mode | Yes |
    And I press "Save and display"
    And I log out
    And the following "message contacts" exist:
      | user     | contact |
      | student1 | student2 |
    When I log in as "student1"
    And I view the "Student 2" contact in the message area
    And I should not see "First access to site"
    And I should see "The details of this user are not available to you"
    And I log out
    And I log in as "admin"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I log out
    And I log in as "student1"
    And I view the "Student 2" contact in the message area
    Then I should see "First access to site"

  @javascript
  Scenario: Accessibility, users can not click on profile image when on user's profile page.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then "//img[contains(@class, 'userpicture')]" "xpath_element" should exist
    And "//a/child::img[contains(@class, 'userpicture')]" "xpath_element" should exist
    When I follow "Teacher 1"
    Then I should see "Teacher 1"
    And "//img[contains(@class, 'userpicture')]" "xpath_element" should exist
    And "//a/child::img[contains(@class, 'userpicture')]" "xpath_element" should not exist
    When I follow "Full profile"
    And I should see "Teacher 1"
    Then "//img[contains(@class, 'userpicture')]" "xpath_element" should exist
    And "//a/child::img[contains(@class, 'userpicture')]" "xpath_element" should not exist

@core @core_user
Feature: Access to preferences page
  In order to view the preferences page
  As a user
  I need global permissions to view the page.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | manager1 | Manager | 1 | manager1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | parent   | Parent  | 1 | parent1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C1 | editingteacher |
   And the following "system role assigns" exist:
      | user | course | role |
      | manager1 | Acceptance test site | manager |

  Scenario: A student and teacher with normal permissions can not view another user's permissions page.
    Given I log in as "student1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    And I should not see "Preferences" in the "#region-main" "css_element"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    Then I should not see "Preferences" in the "#region-main" "css_element"

  Scenario: Administrators and Managers can view another user's permissions page.
    Given I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    And I should see "Preferences" in the "#region-main" "css_element"
    And I log out
    And I log in as "manager1"
    And I am on site homepage
    And I follow "Course 1"
    When I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    Then I should see "Preferences" in the "#region-main" "css_element"

  Scenario: A user with the appropriate permissions can view another user's permissions page.
    Given I log in as "admin"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Mentees" block
    And I navigate to "Define roles" node in "Site administration > Users > Permissions"
    And I click on "Add a new role" "button"
    And I click on "Continue" "button"
    And I set the following fields to these values:
    | Short name | Parent |
    | Custom full name | Parent |
    | contextlevel30 | 1 |
    | moodle/user:editprofile | 1 |
    | moodle/user:viewalldetails | 1 |
    | moodle/user:viewuseractivitiesreport | 1 |
    | moodle/user:viewdetails | 1 |
    And I click on "Create this role" "button"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I follow "Student 1"
    And I click on "Preferences" "link" in the ".profile_tree" "css_element"
    And I follow "Assign roles relative to this user"
    And I follow "Parent"
    And I set the field "Potential users" to "Parent 1 (parent1@example.com)"
    And I click on "Add" "button"
    And I log out
    And I log in as "parent"
    And I am on site homepage
    When I follow "Student 1"
    Then I should see "Preferences" in the "#region-main" "css_element"

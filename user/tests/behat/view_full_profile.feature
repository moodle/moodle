@core @core_user
Feature: Access to full profiles of users
  In order to allow visibility of full profiles
  As an admin
  I need to set global permission or disable forceloginforprofiles

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C1 | editingteacher |

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
    Given I log in as "admin"
    And I set the following administration settings values:
      |  Force users to log in for profiles | 0 |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 2"
    And I follow "Full profile"
    Then I should see "First access to site"

  @javascript
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

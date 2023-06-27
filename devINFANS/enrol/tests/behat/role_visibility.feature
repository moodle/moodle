@core @core_enrol
Feature: Test role visibility for the participants page
  In order to control access
  As an admin
  I need to control which roles can see each other

  Background: Add a bunch of users
    Given  the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | learner1 | Learner   | 1        | learner1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | learner1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | manager1 | C1     | manager        |

  Scenario: Check the default roles are visible
    Given I log in as "manager1"
    And I follow "Course 1"
    When I navigate to "Enrolled users" node in "Course administration > Users"
    Then "Learner 1" row "Roles" column of "participants" table should contain "Student"
    And "Teacher 1" row "Roles" column of "participants" table should contain "Teacher"
    And "Manager 1" row "Roles" column of "participants" table should contain "Manager"
    And I should not see "No Roles" in the "table#participants" "css_element"

  Scenario: Do not allow managers to view any roles but manager and check they are hidden
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Enrolled users" node in "Course administration > Users"
    Then "Learner 1" row "Roles" column of "participants" table should contain "Student"
    And "Teacher 1" row "Roles" column of "participants" table should contain "Teacher"
    And "Manager 1" row "Roles" column of "participants" table should not contain "Manager"
    And "Manager 1" row "Roles" column of "participants" table should contain "No roles"

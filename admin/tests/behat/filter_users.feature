@core @core_admin
Feature: An administrator can filter user accounts by role, cohort and other profile fields
  In order to find the user accounts I am looking for
  As an admin
  I need to filter the users account list using different filter

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | auth | confirmed |
      | user1 | User | One | one@example.com | manual | 0 |
      | user2 | User | Two | two@example.com | ldap | 1 |
      | user3 | User | Three | three@example.com | manual | 1 |
      | user4 | User | Four | four@example.com | ldap | 0 |
    And the following "cohorts" exist:
      | name | idnumber |
      | Cohort 1 | CH1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | student |
      | user2 | C1 | student |
      | user3 | C1 | student |
    And I log in as "admin"
    And I add "User Two (two@example.com)" user to "CH1" cohort members
    And I add "User Three (three@example.com)" user to "CH1" cohort members
    And I navigate to "Users > Accounts > Browse list of users" in site administration

  Scenario: Filter user accounts by role and cohort
    When I set the following fields to these values:
      | courserole_rl | Student |
      | courserole_ct | any category |
      | courserole | C1 |
    And I press "Add filter"
    Then I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should not see "User Four"
    And I set the following fields to these values:
      | cohort | CH1 |
    And I press "Add filter"
    And I should not see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should not see "User Four"
    And I press "Remove all filters"
    And I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should see "User Four"

  Scenario: Filter user accounts by confirm and authentication method
    When I set the following fields to these values:
      | Confirmed | No |
    And I press "Add filter"
    Then I should see "User One"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should see "User Four"
    And I set the following fields to these values:
      | Authentication | manual |
    And I press "Add filter"
    And I should see "User One"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should not see "User Four"

  Scenario: Filter user accounts by enrolled in any course
    When I set the following fields to these values:
      | id_anycourses | Yes |
    And I press "Add filter"
    Then I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should not see "User Four"
    And I press "Remove all filters"
    And I set the following fields to these values:
      | id_anycourses | No |
    And I press "Add filter"
    And I should not see "User One"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should see "User Four"

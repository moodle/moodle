@core @core_admin
Feature: An administrator can filter user accounts by role, cohort and other profile fields
  In order to find the user accounts I am looking for
  As an admin
  I need to filter the users account list using different filter

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email | auth | confirmed |
      | user1 | User | One | one@asd.com | manual | 0 |
      | user2 | User | Two | one@asd.com | ldap | 1 |
      | user3 | User | Three | one@asd.com | manual | 1 |
      | user4 | User | Four | one@asd.com | ldap | 0 |
    And the following "cohorts" exists:
      | name | idnumber |
      | Cohort 1 | CH1 |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exists:
      | user | course | role |
      | user1 | C1 | student |
      | user2 | C1 | student |
      | user3 | C1 | student |
    And I log in as "admin"
    And I add "user2" user to "CH1" cohort
    And I add "user3" user to "CH1" cohort
    And I follow "Browse list of users"

  @javascript
  Scenario: Filter user accounts by role and cohort
    When I fill the moodle form with:
      | courserole_rl | Student |
      | courserole_ct | any category |
      | courserole | C1 |
    And I press "Add filter"
    Then I should see "User One"
    And I should see "User Two"
    And I should see "User Three"
    And I should not see "User Four"
    And I fill the moodle form with:
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

  @javascript
  Scenario: Filter user accounts by confirm and authentication method
    When I fill the moodle form with:
      | Confirmed | No |
    And I press "Add filter"
    Then I should see "User One"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should see "User Four"
    And I fill the moodle form with:
      | Authentication | manual |
    And I press "Add filter"
    And I should see "User One"
    And I should not see "User Two"
    And I should not see "User Three"
    And I should not see "User Four"

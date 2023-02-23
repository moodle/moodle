@core @core_course @theme_boost
Feature: Role assignments can be made at the category level
  In order to grant a user different capabilities
  As a user
  I can assign roles in categories

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager   | Manager   | Manager  |
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "role assigns" exist:
      | user    | role          | contextlevel | reference |
      | manager | manager       | Category     | CAT1      |
    And I log in as "admin"

  @javascript
  Scenario: A user with a category role can assign roles
    Given I define the allowed role assignments for the "Manager" role as:
      | Teacher | Assignable |
    And I log out
    And I log in as "manager"
    And I am on course index
    When I follow "Cat 1"
    And I navigate to "Permissions" in current page administration
    Then I should see "Assign roles" in the "jump" "select"

  @javascript
  Scenario: A user with a category role cannot assign roles if there are no roles to assign
    Given I define the allowed role assignments for the "Manager" role as:
      | Manager             | Not assignable |
      | Course creator      | Not assignable |
      | Teacher             | Not assignable |
      | Non-editing teacher | Not assignable |
      | Student             | Not assignable |
    And I change window size to "large"
    And I log out
    And I log in as "manager"
    And I am on course index
    When I follow "Cat 1"
    And I navigate to "Permissions" in current page administration
    Then I should not see "Assign roles" in the "jump" "select"

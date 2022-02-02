@core @core_course
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
    Then "Assign roles" "link" should exist in current page administration

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
    Then "Assign roles" "link" should not exist in current page administration

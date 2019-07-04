@core @core_message @javascript
Feature: To be able to block users that we are able to or to see a message if we can not
  In order to attempt to block a user
  As a user
  I need to be able to block a user or to see a message if we can not

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@emample.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | C1     | teacher |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following config values are set as admin:
      | messaging | 1 |

  Scenario: Block a user
    Given I log in as "student1"
    And I select "Student 2" user in messaging
    And I open contact menu
    And I click on "Block" "link" in the "[data-region='header-container']" "css_element"
    And I should see "Are you sure you want to block Student 2?"
    And I click on "Block" "button" in the "[data-region='confirm-dialogue']" "css_element"
    And I should see "You have blocked this user."
    And I log out
    When I log in as "student2"
    And I open messaging
    And I select "Student 1" user in messaging
    Then I should see "You are unable to message this user"

  Scenario: Unable to block a user
    Given I log in as "student1"
    And I select "Teacher 1" user in messaging
    And I open contact menu
    When I click on "Block" "link" in the "[data-region='header-container']" "css_element"
    Then I should see "You are unable to block Teacher 1"

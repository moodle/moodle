@core
Feature: Menu navigation has accurate checkmarks in topic course format
  In order to correctly navigate the menu items
  As an admin
  I need to see accurate checkmarks besides the menu items I am currently on while in topics format

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
      | student1  | Student    | 1      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1  | C1      | student         |
    And the following "activities" exist:
      | activity   | name   | intro                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add menu | C1     | quiz1    |

  @javascript
  Scenario: Admin can see checkmark beside menu item they are currently on in the quiz page of a topics format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    When I navigate to "Filters" in current page administration
    Then menu item "Filters" should be active
    When I navigate to "Permissions" in current page administration
    Then menu item "Permissions" should be active
    And menu item "Filters" should not be active
    When I navigate to "Backup" in current page administration
    Then menu item "Backup" should be active
    And menu item "Filters" should not be active
    And menu item "Permissions" should not be active

  @javascript
  Scenario: Admin can see checkmark beside menu item they are currently on in the course page of a topics format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Filters" in current page administration
    Then menu item "Filters" should be active
    When I navigate to "Course reuse" in current page administration
    Then menu item "Course reuse" should be active
    And menu item "Filters" should not be active

  @javascript
  Scenario: Admin can see checkmark beside menu item they are currently on after pressing browser back button in the
  quiz page of a topics format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    When I navigate to "Filters" in current page administration
    Then menu item "Filters" should be active
    When I navigate to "Permissions" in current page administration
    Then menu item "Permissions" should be active
    And menu item "Filters" should not be active
    When I press the "back" button in the browser
    Then menu item "Filters" should be active
    And menu item "Permissions" should not be active

  @javascript
  Scenario: Admin can see checkmark beside menu item they are currently on after pressing browser back button in the
  course page of a topics format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Filters" in current page administration
    Then menu item "Filters" should be active
    When I navigate to "Course reuse" in current page administration
    Then menu item "Course reuse" should be active
    And menu item "Filters" should not be active
    When I press the "back" button in the browser
    Then menu item "Filters" should be active
    And menu item "Course reuse" should not be active

  @javascript
  Scenario: Admin should not see checkmark if link is not navigated to in current browser in course view for topics format
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I update the href of the "//*//a/following-sibling::*//a[contains(text(), 'Filters')]" "xpath" link to "#"
    And I navigate to "Question banks" in current page administration
    Then menu item "Filters" should not be active

  @javascript
  Scenario: Admin should not see checkmark if link is not navigated to in current browser in quiz view for topics format
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I update the href of the "//*//a/following-sibling::*//a[contains(text(), 'Backup')]" "xpath" link to "#"
    And I navigate to "Backup" in current page administration
    Then menu item "Backup" should not be active

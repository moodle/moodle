@core
Feature: Menu navigation has accurate checkmarks in single activity course format
  In order to correctly navigate the menu items
  As an admin
  I need to see accurate checkmarks besides the menu items I am currently on while in a single activity format course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
      | student1  | Student    | 1      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         | activitytype  |
      | Course 1 | C1        | singleactivity | quiz          |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |
    And the following "activities" exist:
      | activity   | name   | intro                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add menu | C1     | quiz1    |

  Scenario: Admin can see checkmark beside menu item they are currently on in a single activity format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Backup" in current page administration
    Then menu item "Backup" should be active
    When I navigate to "Permissions" in current page administration
    Then menu item "Permissions" should be active
    And menu item "Backup" should not be active

    When I navigate to "Participants" in current page administration
    Then menu item "Participants" should be active
    And menu item "Backup" should not be active
    And menu item "Permissions" should not be active
    When I navigate to "Grades" in current page administration
    Then menu item "Grades" should be active
    And menu item "Backup" should not be active
    And menu item "Permissions" should not be active
    And menu item "Participants" should not be active

  Scenario: Admin can see checkmark beside menu item they are currently on after pressing browser back button in a single
  activity format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Backup" in current page administration
    Then menu item "Backup" should be active
    When I navigate to "Permissions" in current page administration
    Then menu item "Permissions" should be active
    And menu item "Backup" should not be active
    When I press the "back" button in the browser
    Then menu item "Backup" should be active
    And menu item "Permissions" should not be active

  Scenario: Admin can see checkmark beside menu item they are currently on after pressing browser back button when
  jumping between course and activity menu in a single activity format course
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Backup" in current page administration
    Then menu item "Backup" should be active
    When I navigate to "Participants" in current page administration
    Then menu item "Participants" should be active
    And menu item "Backup" should not be active
    When I press the "back" button in the browser
    Then menu item "Backup" should be active
    And menu item "Participants" should not be active

  @javascript
  Scenario: Admin should not see checkmark if link is not navigated to in current browser for single activity format quiz
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I update the href of the "//*//a/following-sibling::*//a[contains(text(), 'Participants')]" "xpath" link to "#"
    When I navigate to "Participants" in current page administration
    Then menu item "Participants" should not be active
    And I update the href of the "//*//a/following-sibling::*//a[contains(text(), 'Backup')]" "xpath" link to "#"
    When I click on "//*//a[contains(text(),'Activity')]" "xpath"
    And I click on "//*//a/following-sibling::*//a[contains(text(), 'Backup')]" "xpath"
    Then menu item "Backup" should not be active

@core @core_course @communication
Feature: Course communication
  In order to create a new communication room in for course
  As an admin
  I should not have any plugins enabled by default for new and existing courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I enable communication experimental feature
    And I log in as "admin"

  Scenario: I should have matrix plugin by default for new courses
    Given I go to the courses management page
    And I click on category "Category 1" in the management interface
    And I follow "Create new course"
    And I set the following fields to these values:
      | Course full name | Course 2 |
      | Course short name | C2      |
    And I press "Save and display"
    When I navigate to "Communication" in current page administration
    Then the field "Provider" matches value "None"

  Scenario: I should have communication disabled by default for existing courses
    Given I am on "Course 1" course homepage
    When I navigate to "Communication" in current page administration
    Then the field "Provider" matches value "None"

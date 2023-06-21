@core @core_course
Feature: Course communication
  In order to create a new communication room in for course
  As an admin
  I should have matrix plugin enabled by default
  I should not have any plugins enabled by default for existing courses

  Background:
    Given the following "courses" exist:
      | fullname    | shortname |
      | Test course | C         |
    And I enable communication experimental feature
    And I log in as "admin"

  Scenario: I should have matrix plugin by default for new courses
    Given I go to the courses management page
    And I should see the "Categories" management page
    And I click on category "Category 1" in the management interface
    And I should see the "Course categories and courses" management page
    And I follow "Create new course"
    When I click on "Communication" "link"
    Then the field "Communication service" matches value "Matrix"

  Scenario: I should have communication disabled by default for existing courses
    Given I am on "Test course" course homepage
    When I navigate to "Settings" in current page administration
    And I click on "Communication" "link"
    Then the field "Communication service" matches value "None"

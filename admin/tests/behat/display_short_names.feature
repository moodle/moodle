@core @core_admin
Feature: Display extended course names
  In order to display more info about the courses
  As an admin
  I need to display courses short names along with courses full names

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course fullname | C_shortname | 0 |
    And I log in as "admin"
    And I am on site homepage

  Scenario: Courses list without extended course names (default value)
    Then I should see "Course fullname"
    And I should not see "C_shortname Course fullname"

  Scenario: Courses list with extended course names
    Given I navigate to "Appearance > Courses" in site administration
    And I set the field "Display extended course names" to "1"
    When I press "Save changes"
    And I am on site homepage
    Then I should see "C_shortname Course fullname"

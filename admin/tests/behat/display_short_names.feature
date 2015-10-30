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

  Scenario: Courses list without extended course names (default value)
    Then I should see "Course fullname"
    And I should not see "C_shortname Course fullname"

  Scenario: Courses list with extended course names
    Given I expand "Site administration" node
    And I click on "Courses" "link" in the "//div[@id='settingsnav']/descendant::li[contains(concat(' ', normalize-space(@class), ' '), ' type_setting ')][contains(., 'Appearance')]" "xpath_element"
    And I set the field "Display extended course names" to "1"
    When I press "Save changes"
    And I am on homepage
    Then I should see "C_shortname Course fullname"

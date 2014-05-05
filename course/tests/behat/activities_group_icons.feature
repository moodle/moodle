@core @core_course @_cross_browser
Feature: Toggle activities groups mode from the course page
  In order to split activities in groups
  As a teacher
  I need to change quickly the group mode of an activity

  @javascript
  Scenario: Groups mode toggle with javascript enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Group mode | No groups |
      | Force group mode | No |
    When I press "Save changes"
    Then "No groups (Click to change)" "link" should exist
    And "//a/child::img[contains(@src, 'groupn')]" "xpath_element" should exist
    And I click on "No groups (Click to change)" "link" in the "Test forum name" activity
    And "Separate groups (Click to change)" "link" should exist
    And "//a/child::img[contains(@src, 'groups')]" "xpath_element" should exist
    And I reload the page
    And "Separate groups (Click to change)" "link" should exist
    And "//a/child::img[contains(@src, 'groups')]" "xpath_element" should exist
    And I click on "Separate groups (Click to change)" "link" in the "Test forum name" activity
    And "Visible groups (Click to change)" "link" should exist
    And "//a/child::img[contains(@src, 'groupv')]" "xpath_element" should exist
    And I reload the page
    And "Visible groups (Click to change)" "link" should exist
    And "//a/child::img[contains(@src, 'groupv')]" "xpath_element" should exist
    And I click on "Visible groups (Click to change)" "link" in the "Test forum name" activity
    And "No groups (Click to change)" "link" should exist
    And "//a/child::img[contains(@src, 'groupn')]" "xpath_element" should exist

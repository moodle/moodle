@core @core_course
Feature: Edit activity name in-place
  In order to quickly edit activity name
  As a teacher
  I need to use inplace editing

  @javascript
  Scenario: Edit activity name in-place
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    # Rename activity
    And I click on "Edit title" "link" in the "//div[contains(@class,'activityinstance') and contains(.,'Test forum name')]" "xpath_element"
    And I set the field "New name for activity Test forum name" to "Good news"
    And I press key "13" in the field "New name for activity Test forum name"
    Then I should not see "Test forum name" in the ".course-content" "css_element"
    And "New name for activity Test forum name" "field" should not exist
    And I should see "Good news"
    And I am on "Course 1" course homepage
    And I should see "Good news"
    And I should not see "Test forum name"
    # Cancel renaming
    And I click on "Edit title" "link" in the "//div[contains(@class,'activityinstance') and contains(.,'Good news')]" "xpath_element"
    And I set the field "New name for activity Good news" to "Terrible news"
    And I press key "27" in the field "New name for activity Good news"
    And "New name for activity Good news" "field" should not exist
    And I should see "Good news"
    And I should not see "Terrible news"
    And I am on "Course 1" course homepage
    And I should see "Good news"
    And I should not see "Terrible news"
    And I log out

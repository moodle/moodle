@core @core_course
Feature: Allow teachers to edit the visibility of activity dates in a course
  In order to show students the activity dates in a course
  As a teacher
  I need to be able to edit activity dates settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name          | intro                   | timeopen      | timeclose     |
      | choice   | C1     | choice1  | Test choice   | Test choice description | ##yesterday## | ##tomorrow##  |

  Scenario: Activity dates setting can be enabled to display activity dates in a course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    When I set the following fields to these values:
      | Show activity dates | Yes |
    And I click on "Save and display" "button"
    And I follow "Test choice"
    Then the activity date information in "Test choice" should exist
    And the activity date in "Test choice" should contain "Opened:"
    And the activity date in "Test choice" should contain "Closes:"
    And I am on "Course 1" course homepage
    # When showactivitydates is enabled, activity dates should be shown on the course homepage.
    And the activity date information in "Test choice" should exist
    And the activity date in "Test choice" should contain "Opened:"
    And the activity date in "Test choice" should contain "Closes:"

  Scenario: Activity dates setting can be disabled to hide activity dates in a course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    When I set the following fields to these values:
      | Show activity dates | No |
    And I click on "Save and display" "button"
    And I follow "Test choice"
    # Activity dates are always shown in the module's view page.
    Then the activity date information in "Test choice" should exist
    And the activity date in "Test choice" should contain "Opened:"
    And the activity date in "Test choice" should contain "Closes:"
    And I am on "Course 1" course homepage
    # When showactivitydates is disabled, activity dates should not be shown on the course homepage.
    And the activity date information in "Test choice" should not exist

  Scenario: Default activity dates setting default value can changed to No
    Given I log in as "admin"
    And I navigate to "Courses > Course default settings" in site administration
    When I set the following fields to these values:
      | Show activity dates | No |
    And I click on "Save changes" "button"
    And I navigate to "Courses > Add a new course" in site administration
    Then the field "showactivitydates" matches value "No"

  Scenario: Default activity dates setting default value can changed to Yes
    Given I log in as "admin"
    And I navigate to "Courses > Course default settings" in site administration
    When I set the following fields to these values:
      | Show activity dates | Yes |
    And I click on "Save changes" "button"
    And I navigate to "Courses > Add a new course" in site administration
    Then the field "showactivitydates" matches value "Yes"

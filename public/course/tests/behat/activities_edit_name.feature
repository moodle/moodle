@core @core_course
Feature: Edit activity name in-place
  In order to quickly edit activity name
  As a teacher
  I need to use inplace editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | course      | C1                     |
      | activity    | forum                  |
      | name        | Test forum name        |
      | idnumber    | forum1                 |

  @javascript
  Scenario: Edit activity name in-place
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    # Rename activity
    And I set the field "Edit title" in the "Test forum name" "activity" to "Good news"
    Then I should not see "Test forum name" in the ".course-content" "css_element"
    And "New name for activity Test forum name" "field" should not exist
    And I should see "Good news"
    And I am on "Course 1" course homepage
    And I should see "Good news"
    And I should not see "Test forum name"
    # Cancel renaming
    And I click on "Edit title" "link" in the "[data-value='Good news']" "css_element"
    And I type "Terrible news"
    And I press the escape key
    And "New name for activity Good news" "field" should not exist
    And I should see "Good news"
    And I should not see "Terrible news"
    And I am on "Course 1" course homepage
    And I should see "Good news"
    And I should not see "Terrible news"

  @javascript
  Scenario: Edit activity name in-place ensuring correct encoding
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I set the field "Edit title" in the "Test forum name" "activity" to "Good & bad news"
    Then I should not see "Test forum name" in the ".course-content" "css_element"
    And I should see "Good & bad news" in the ".course-content" "css_element"

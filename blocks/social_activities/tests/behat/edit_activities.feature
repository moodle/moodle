@block @block_social_activities @format_social
Feature: Edit activities in social activities block
  In order to use social activities block
  As a teacher
  I need to add and edit activities there

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | social |
    And the following "users" exist:
      | username | firstname | lastname |
      | user1 | User | One |
      | student1 | Student | One |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Edit name of activity in-place in social activities block
    Given I log in as "user1"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Forum" "link" in the "Add an activity or resource" "dialogue"
    And I set the field "Forum name" to "My forum name"
    And I press "Save and return to course"
    When I set the field "Edit title" in the "My forum name" "block_social_activities > Activity" to "New forum name"
    Then I should not see "My forum name" in the "Social activities" "block"
    And I should see "New forum name"
    And I follow "New forum name"
    And I should not see "My forum name"
    And I should see "New forum name"

  @javascript
  Scenario: Activities in social activities block can be made available but not visible on a course page
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And I log in as "user1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Recent activity" block
    And I press "Add an activity or resource"
    And I click on "Add a new Forum" "link" in the "Add an activity or resource" "dialogue"
    And I set the field "Forum name" to "My forum name"
    And I press "Save and return to course"
    And "My forum name" activity in social activities block should have "Hide" editing icon
    And "My forum name" activity in social activities block should not have "Show" editing icon
    And "My forum name" activity in social activities block should not have "Make available" editing icon
    And "My forum name" activity in social activities block should not have "Make unavailable" editing icon
    And I wait until the page is ready
    And I open "My forum name" actions menu in social activities block
    And I click on "Hide" "link" in the "My forum name" activity in social activities block
    And "My forum name" activity in social activities block should be hidden
    And "My forum name" activity in social activities block should not have "Hide" editing icon
    And "My forum name" activity in social activities block should have "Show" editing icon
    And "My forum name" activity in social activities block should have "Make available" editing icon
    And "My forum name" activity in social activities block should not have "Make unavailable" editing icon
    And I open "My forum name" actions menu in social activities block
    And I click on "Make available" "link" in the "My forum name" activity in social activities block
    And "My forum name" activity in social activities block should be available but hidden from course page
    And "My forum name" activity in social activities block should not have "Hide" editing icon
    And "My forum name" activity in social activities block should have "Show" editing icon
    And "My forum name" activity in social activities block should not have "Make available" editing icon
    And "My forum name" activity in social activities block should have "Make unavailable" editing icon
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "My forum name" actions menu in social activities block
    And I click on "Edit settings" "link" in the "My forum name" activity in social activities block
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should contain "Hide from students"
    And the field "Availability" matches value "Make available but not shown on course page"
    And I press "Save and return to course"
    And "My forum name" activity in social activities block should be available but hidden from course page
    And I turn editing mode off
    And "My forum name" activity in social activities block should be available but hidden from course page
    And I log out
    # Student will not see the module on the course page but can access it from other reports and blocks:
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "My forum name" in the "Social activities" "block"
    And I click on "My forum name" "link" in the "Recent activity" "block"
    And I should see "My forum name" in the ".breadcrumb" "css_element"

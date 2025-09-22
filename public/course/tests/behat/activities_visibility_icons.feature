@core @core_course @_cross_browser
Feature: Toggle activities visibility from the course page
  In order to delay activities availability
  As a teacher
  I need to quickly change the visibility of an activity

  Background:
    Given the following "users" exist:
      | username           | firstname           | lastname | email                          |
      | teacher1           | Teacher             | 1        | teacher1@example.com           |
      | noneditingteacher1 | Non-Editing Teacher | 1        | noneditingteacher1@example.com |
      | student1           | Student             | 1        | student1@example.com           |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1        | topics | 2           |
    And the following "course enrolments" exist:
      | user               | course | role           |
      | teacher1           | C1     | editingteacher |
      | noneditingteacher1 | C1     | teacher        |
      | student1           | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | idnumber | name                 | intro                       | id_visible |
      | assign   | C1     | 1       | 1        | Test assignment name | Test assignment description | 1          |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | recent_activity | Course       | C1        | course-view-*   | side-pre      |

  @javascript
  Scenario: Hide/Show toggle with javascript enabled
    Given the following "activity" exists:
      | activity | forum                  |
      | course   | C1                     |
      | idnumber | C1F1                   |
      | name     | Test forum name        |
      | visible  | 1                      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open "Test forum name" actions menu
    And I choose "Hide" in the open action menu
    Then "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And I choose "Show" in the open action menu
    And "Test forum name" activity should be visible
    And I open "Test forum name" actions menu
    And I choose "Hide" in the open action menu
    And "Test forum name" activity should be hidden
    And I reload the page
    And "Test forum name" activity should be hidden
    # Make sure that "Availability" dropdown in the edit menu has two options: Show/Hide.
    And I open "Test forum name" actions menu
    And I click on "Edit settings" "link" in the "Test forum name" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should not contain "Make available but don't show on course page"
    And the field "Availability" matches value "Hide on course page"
    And I press "Save and return to course"
    And "Test forum name" activity should be hidden
    # Non-editing teacher should see this activity.
    And I am on the "Course 1" course page logged in as noneditingteacher1
    And I should see "Test forum name" in the "region-main" "region"
    # Student should not see this activity.
    And I am on the "Course 1" course page logged in as student1
    And I should not see "Test forum name"

  @javascript
  Scenario: Activities can be made available and unavailable inside a hidden section
    Given the following "activity" exists:
      | activity | forum                  |
      | course   | C1                     |
      | idnumber | C1F1                   |
      | section  | 2                      |
      | name     | Test forum name        |
      | visible  | 1                      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I hide section "2"
    And "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    Then "Test forum name" activity should be available but hidden from course page
    And I open "Test forum name" actions menu
    And I choose "Availability > Hide on course page" in the open action menu
    And "Test forum name" activity should be hidden
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "Test forum name" actions menu
    And I click on "Edit settings" "link" in the "Test forum name" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Hide on course page"
    And the "Availability" select box should contain "Make available but don't show on course page"
    And the "Availability" select box should not contain "Show on course page"
    And I set the field "Availability" to "Make available but don't show on course page"
    And I press "Save and return to course"
    And "Test forum name" activity should be available but hidden from course page
    And I turn editing mode off
    And "Test forum name" activity should be available but hidden from course page
    # Student will not see the module on the course page but can access it from other reports and blocks:
    And I am on the "Course 1" course page logged in as student1
    And "Test forum name" activity should be hidden
    And I click on "Test forum name" "link" in the "Recent activity" "block"
    And I should see "Test forum name"
    And I should see "There are no discussion topics yet in this forum."

  @javascript
  Scenario: Activities can be made available but not visible on a course page
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open "Test assignment name" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    Then "Test assignment name" activity should be available but hidden from course page
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "Test assignment name" actions menu
    And I click on "Edit settings" "link" in the "Test assignment name" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should contain "Hide on course page"
    And the field "Availability" matches value "Make available but don't show on course page"
    And I press "Save and return to course"
    And "Test assignment name" activity should be available but hidden from course page
    And I turn editing mode off
    And "Test assignment name" activity should be available but hidden from course page
    # Non-editing teacher will see the module on the course page:
    And I am on the "Course 1" course page logged in as noneditingteacher1
    And I should see "Test assignment name" in the "region-main" "region"
    # Student will not see the module on the course page but can access it from other reports and blocks:
    And I am on the "Course 1" course page logged in as student1
    And "Test assignment name" activity should be hidden
    And I click on "Test assignment name" "link" in the "Recent activity" "block"
    And I should see "Test assignment name"
    And I should see "Submission status"

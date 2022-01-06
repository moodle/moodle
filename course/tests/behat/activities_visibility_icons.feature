@core @core_course @_cross_browser
Feature: Toggle activities visibility from the course page
  In order to delay activities availability
  As a teacher
  I need to quickly change the visibility of an activity

  @javascript
  Scenario: Hide/Show toggle with javascript enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Availability | Show on course page |
    When I open "Test forum name" actions menu
    Then "Test forum name" actions menu should not have "Show" item
    And "Test forum name" actions menu should not have "Make available" item
    And "Test forum name" actions menu should not have "Make unavailable" item
    And I click on "Hide" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And "Test forum name" actions menu should not have "Hide" item
    # Stealth behaviour is not available by default:
    And "Test forum name" actions menu should not have "Make available" item
    And "Test forum name" actions menu should not have "Make unavailable" item
    And I click on "Show" "link" in the "Test forum name" activity
    And "Test forum name" activity should be visible
    And I open "Test forum name" actions menu
    And "Test forum name" actions menu should not have "Show" item
    And "Test forum name" actions menu should not have "Make available" item
    And "Test forum name" actions menu should not have "Make unavailable" item
    And I click on "Hide" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden
    And I reload the page
    And "Test forum name" activity should be hidden
    # Make sure that "Availability" dropdown in the edit menu has two options: Show/Hide.
    And I open "Test forum name" actions menu
    And I click on "Edit settings" "link" in the "Test forum name" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should not contain "Make available but not shown on course page"
    And the field "Availability" matches value "Hide from students"
    And I press "Save and return to course"
    And "Test forum name" activity should be hidden
    And I turn editing mode off
    And "Test forum name" activity should be hidden
    And I log out
    # Student should not see this activity.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Test forum name"
    And I log out

  @javascript
  Scenario: Activities can be made available and unavailable inside a hidden section
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1 | topics | 2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Recent activity" block
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Availability | Show on course page |
    When I hide section "2"
    Then "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And "Test forum name" actions menu should not have "Show" item
    And "Test forum name" actions menu should not have "Hide" item
    And "Test forum name" actions menu should not have "Make unavailable" item
    And I click on "Make available" "link" in the "Test forum name" activity
    And "Test forum name" activity should be available but hidden from course page
    And I open "Test forum name" actions menu
    And "Test forum name" actions menu should not have "Show" item
    And "Test forum name" actions menu should not have "Hide" item
    And "Test forum name" actions menu should not have "Make available" item
    And I click on "Make unavailable" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "Test forum name" actions menu
    And I click on "Edit settings" "link" in the "Test forum name" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Hide from students"
    And the "Availability" select box should contain "Make available but not shown on course page"
    And the "Availability" select box should not contain "Show on course page"
    And I set the field "Availability" to "Make available but not shown on course page"
    And I press "Save and return to course"
    And "Test forum name" activity should be available but hidden from course page
    And I turn editing mode off
    And "Test forum name" activity should be available but hidden from course page
    And I log out
    # Student will not see the module on the course page but can access it from other reports and blocks:
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And "Test forum name" activity should be hidden
    And I click on "Test forum name" "link" in the "Recent activity" "block"
    And I should see "Test forum name"
    And I should see "(There are no discussion topics yet in this forum)"
    And I log out

  @javascript
  Scenario: Activities can be made available but not visible on a course page
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1        | topics | 2           |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | allowstealth | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Recent activity" block
    And I add a "Assignment" to section "2" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Availability | Show on course page |
    When I open "Test assignment name" actions menu
    Then "Test assignment name" actions menu should not have "Show" item
    And "Test assignment name" actions menu should have "Hide" item
    And "Test assignment name" actions menu should not have "Make available" item
    And "Test assignment name" actions menu should not have "Make unavailable" item
    And I click on "Hide" "link" in the "Test assignment name" activity
    And "Test assignment name" activity should be hidden
    And I open "Test assignment name" actions menu
    And "Test assignment name" actions menu should have "Show" item
    And "Test assignment name" actions menu should not have "Hide" item
    And "Test assignment name" actions menu should not have "Make unavailable" item
    And I click on "Make available" "link" in the "Test assignment name" activity
    And "Test assignment name" activity should be available but hidden from course page
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "Test assignment name" actions menu
    And I click on "Edit settings" "link" in the "Test assignment name" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should contain "Hide from students"
    And the field "Availability" matches value "Make available but not shown on course page"
    And I press "Save and return to course"
    And "Test assignment name" activity should be available but hidden from course page
    And I change window size to "large"
    And I turn editing mode off
    And "Test assignment name" activity should be available but hidden from course page
    And I log out
    # Student will not see the module on the course page but can access it from other reports and blocks:
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And "Test assignment name" activity should be hidden
    And I click on "Test assignment name" "link" in the "Recent activity" "block"
    And I should see "Test assignment name"
    And I should see "Submission status"
    And I log out

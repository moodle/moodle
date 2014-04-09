@core @core_course @_cross_browser
Feature: Toggle activities visibility from the course page
  In order to delay activities availability
  As a teacher
  I need to quickly change the visibility of an activity

  @javascript
  Scenario: Hide/Show toggle with javascript enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Visible | Show |
    When I open "Test forum name" actions menu
    And I click on "Hide" "link" in the "Test forum name" activity
    Then "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And I click on "Show" "link" in the "Test forum name" activity
    And "Test forum name" activity should be visible
    And I open "Test forum name" actions menu
    And I click on "Hide" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden
    And I reload the page
    And "Test forum name" activity should be hidden
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And "Test forum name" activity should be hidden

  @javascript
  Scenario: Activities can be shown and hidden inside a hidden section
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1 | topics | 2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Visible | Show |
    When I hide section "2"
    Then "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And I click on "Show" "link" in the "Test forum name" activity
    And "Test forum name" activity should be visible
    And I open "Test forum name" actions menu
    And I click on "Hide" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden

  @javascript
  Scenario: Activities can be shown and hidden inside an orphaned section
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1 | topics | 2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Visible | Show |
    When I click on ".reduce-sections" "css_element"
    Then "Test forum name" activity should be visible
    And I open "Test forum name" actions menu
    And I click on "Hide" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden
    And I open "Test forum name" actions menu
    And I click on "Show" "link" in the "Test forum name" activity
    And "Test forum name" activity should be visible

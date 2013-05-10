@core @core_course @_cross_browser
Feature: Toggle activities visibility from the course page
  In order to delay activities availability
  As a teacher
  I need to quickly change the visibility of an activity

  @javascript
  Scenario: Hide/Show toggle with javascript enabled
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
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
    When I click on "Hide" "link" in the "Test forum name" activity
    Then "Test forum name" activity should be hidden
    And I click on "Show" "link" in the "Test forum name" activity
    And "Test forum name" activity should be visible
    And I click on "Hide" "link" in the "Test forum name" activity
    And "Test forum name" activity should be hidden
    And I reload the page
    And "Test forum name" activity should be hidden
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And "Test forum name" activity should be hidden

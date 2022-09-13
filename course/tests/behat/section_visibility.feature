@core @core_course @_cross_browser
Feature: Show/hide course sections
  In order to delay sections availability
  As a teacher
  I need to show or hide sections

  @javascript
  Scenario: Show / hide section icon functions correctly
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
    And the following "activities" exist:
      | activity | course | section | name                      | visible |
      | forum    | C1     | 1       | Test hidden forum 11 name | 0       |
      | forum    | C1     | 1       | Test hidden forum 12 name | 1       |
      | forum    | C1     | 2       | Test hidden forum 21 name | 0       |
      | forum    | C1     | 2       | Test hidden forum 22 name | 1       |
      | forum    | C1     | 3       | Test hidden forum 31 name | 0       |
      | forum    | C1     | 3       | Test hidden forum 32 name | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I hide section "1"
    Then section "1" should be hidden
    And section "2" should be visible
    And section "3" should be visible
    And I hide section "2"
    And section "2" should be hidden
    And I show section "2"
    And section "2" should be visible
    And I hide section "3"
    And I show section "3"
    And I hide section "3"
    And section "3" should be hidden
    And I reload the page
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And section "2" should be visible
    And section "3" should be hidden
    And all activities in section "1" should be hidden
    And I am on the "Course 1" course page logged in as student1
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And section "2" should be visible
    And section "3" should be hidden
    And all activities in section "1" should be hidden

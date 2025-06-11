@core @core_course @_cross_browser @javascript
Feature: Topic's course sections highlighting
  In order to highlight parts of the course to students
  As a teacher
  I need to highlight one specific section

  @javascript
  Scenario Outline: Highlight a course section with course paged mode and without it
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay |
      | Course 1 | C1 | topics | <coursedisplay> |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I turn section "1" highlighting on
    Then section "1" should be highlighted
    And I turn section "2" highlighting on
    And section "2" should be highlighted
    And section "1" should not be highlighted
    And I am on "Course 1" course homepage
    And section "2" should be highlighted
    And section "1" should not be highlighted
    And I turn section "2" highlighting off
    And I wait until the page is ready
    And section "2" should not be highlighted
    And I reload the page
    And section "2" should not be highlighted
    And I am on "Course 1" course homepage
    And section "2" should not be highlighted
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And section "1" should not be highlighted
    And section "2" should not be highlighted

    Examples:
      | coursedisplay |
      | 0            |
      | 1            |

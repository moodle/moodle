@core @core_course @_cross_browser
Feature: Topic's course sections highlighting
  In order to highlight parts of the course to students
  As a teacher
  I need to highlight one specific section

  @javascript
  Scenario Outline: Highlight a topic's course section with course paged mode and without it
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format | coursedisplay |
      | Course 1 | C1 | topics | <coursedisplay> |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    When I turn section "1" highlighting on
    Then section "1" should be highlighted
    And I turn section "2" highlighting on
    And section "2" should be highlighted
    And section "1" should not be highlighted
    And I am on homepage
    And I follow "Course 1"
    And section "2" should be highlighted
    And section "1" should not be highlighted
    And I turn section "2" highlighting off
    And section "2" should not be highlighted
    And I reload the page
    And section "2" should not be highlighted
    And I am on homepage
    And I follow "Course 1"
    And section "2" should not be highlighted
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And section "1" should not be highlighted
    And section "2" should not be highlighted

    Examples:
      | coursedisplay |
      | 0            |
      | 1            |

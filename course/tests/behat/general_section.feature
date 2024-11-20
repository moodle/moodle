@format @format_topics
Feature: General section does not show in navigation when empty
  In order to keep my navigation links relevant
  As a teacher
  The general section links should not appear in the navigation when the section is empty

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1        | topics | 0             | 5           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name            | course | idnumber | section |
      | forum    | Test forum name | C1     | forum1   | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present

  Scenario: General section is visible in navigation when it is not empty
    When I move "Test forum name" activity to section "0"
    And I am on "Course 1" course homepage
    Then I should see "General" in the "Navigation" "block"

  Scenario: General section is not visible in navigation when it is empty
    When I move "Test forum name" activity to section "3"
    And I am on "Course 1" course homepage
    Then I should not see "General" in the "Navigation" "block"

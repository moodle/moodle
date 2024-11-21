@block @block_xp
Feature: A student earns experience points for participating in a course
  In order to earn experience points in a course
  As a student
  I need to participate

  Scenario: Basic participation in the course
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | Student   | One      | s1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | s1       | c1     | student |
    And the following "activity" exists:
      | course    | c1                             |
      | section   | 1                              |
      | activity  | forum                          |
      | name      | Test forum name                |
      | intro     | Test forum description         |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add the "Level Up XP" block
    And I log out
    When I log in as "s1"
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Post with text    |
      | Message | This is the body  |
    And I reply "Post with text" post from "Test forum name" forum with:
      | Subject | Reply with text   |
      | Message | This is the body  |
    And I am on "Course 1" course homepage
    And I click on "Leaderboard" "link" in the "Level up!" "block"
    Then the following should exist in the "block_xp-table" table:
      | Participant | Level | Total |
      | Student One | 1     | 117   |

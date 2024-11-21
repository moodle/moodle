@block @block_xp @javascript
Feature: A student is shown a notification popup when they level up
  In order to motivate students
  The system
  Notifies students when they level up

  Scenario: Notification for leveling up
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
    And I follow "Edit"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I wait until ".modal-open" "css_element" exists
    Then I should see "You levelled up!"
    And "[aria-label='Level #2']" "css_element" should exist
    And I wait "4" seconds
    And I press "Cool, thanks"
    And I click on "Leaderboard" "link" in the "Level up!" "block"
    And the following should exist in the "block_xp-table" table:
      | Participant | Level | Total |
      | Student One | 2     | 120   |

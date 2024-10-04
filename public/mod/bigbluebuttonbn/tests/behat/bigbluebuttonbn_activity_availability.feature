@mod @mod_bigbluebuttonbn
Feature: Manage BigBlueButton session timings
  As a teacher
  I want to set and manage the open and close times for BigBlueButton sessions
  So that I can control when students can join the sessions

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "blocks" exist:
      | blockname         | contextlevel | reference | pagetypepattern | defaultregion |
      | calendar_upcoming | System       | 1         | my-index        | side-post     |

  Scenario Outline: Setting and verifying BBB activity open and close times
    Given the following "activities" exist:
      | course | activity        | name  | openingtime   | closingtime   |
      | C1     | bigbluebuttonbn | BBB 1 | <openingtime> | <closingtime> |
    When I am on the "BBB 1" "bigbluebuttonbn activity" page logged in as student1
    And "Join session" "link" <buttonvisibility> exist
    And I should see "Open:"
    And I should see "<openingtime>%A, %d %B %Y##"
    And I should see "Close:"
    And I should see "<closingtime>%A, %d %B %Y##"
    And I am viewing calendar in "month" view
    And I <calendarvisibility> see "BBB 1"
    And I am on site homepage
    And I follow "Dashboard"
    And I <upcomingeventvisibility> see "BBB 1" in the "Upcoming events" "block"

    Examples:
      | openingtime       | closingtime            | calendarvisibility | buttonvisibility | upcomingeventvisibility |
      | ##now +1 minute## | ##now +5 minutes##     | should             | should not       | should                  |
      | ##1 hour ago##    | ##+2 hours##           | should             | should           | should not              |
      | ##yesterday##     | ##yesterday +3 hours## | should not         | should not       | should not              |

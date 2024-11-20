@tool @tool_behat
Feature: Frozen clock in Behat
  In order to write tests that depend on the current system time
  As a test writer
  I need to set the time using a Behat step

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activities" exist:
      | activity | course | name      | idnumber | externalurl                                                 |
      | url      | C1     | Fixture   | url1     | #wwwroot#/admin/tool/behat/tests/fixtures/core/showtime.php |
      | forum    | C1     | TestForum | forum1   |                                                             |

  Scenario: Time has been frozen
    # Set up 2 forum discussions at different times. This tests the clock in the Behat CLI process.
    Given the time is frozen at "2024-03-01 12:34:56"
    And the following "mod_forum > discussions" exist:
      | user  | forum  | name     | message  |
      | admin | forum1 | Subject1 | Message1 |
    And the time is frozen at "2024-08-01 12:34:56"
    And the following "mod_forum > discussions" exist:
      | user  | forum  | name     | message  |
      | admin | forum1 | Subject2 | Message2 |
    When I am on the "TestForum" "forum activity" page logged in as admin
    Then I should see "1 Mar 2024" in the "Subject1" "table_row"
    And I should see "1 Aug 2024" in the "Subject2" "table_row"
    # Also view time on the fixture page. This tests the clock for Behat web server requests.
    And I am on the "Fixture" "url activity" page
    And I should see "Behat time is not the same as real time"
    # This Unix time corresponds to 12:34:56 in Perth time zone.
    And I should see "Unix time 1722486896"
    And I should see "Date-time 2024-08-01 12:34:56"

  # This scenario is second, to verify that the clock automatically goes back to normal after test.
  Scenario: Time is normal
    Given the following "mod_forum > discussions" exist:
      | user  | forum  | name     | message  |
      | admin | forum1 | Subject1 | Message1 |
    When I am on the "TestForum" "forum activity" page logged in as admin
    # The time should be the real current time, not the frozen time.
    Then I should see "## today ##%d %b %Y##" in the "Subject1" "table_row"
    And I am on the "Fixture" "url activity" page
    And I should see "Behat time is the same as real time"

  Scenario: Time is frozen and then unfrozen
    Given the time is frozen at "2024-03-01 12:34:56"
    And the following "mod_forum > discussions" exist:
      | user  | forum  | name     | message  |
      | admin | forum1 | Subject1 | Message1 |
    And the time is no longer frozen
    And the following "mod_forum > discussions" exist:
      | user  | forum  | name     | message  |
      | admin | forum1 | Subject2 | Message2 |
    When I am on the "TestForum" "forum activity" page logged in as admin
    Then I should see "1 Mar 2024" in the "Subject1" "table_row"
    # The time should be the real current time, not the frozen time for this entry.
    And I should see "## today ##%d %b %Y##" in the "Subject2" "table_row"
    And I am on the "Fixture" "url activity" page
    And I should see "Behat time is the same as real time"

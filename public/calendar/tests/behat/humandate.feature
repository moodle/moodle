@core @core_calendar
Feature: Confirm dates are human readable
  In order to ensure the calendar dates are human readable
  As an admin
  I need to create calendar events in near days

  Background:
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "blocks" exist:
      | blockname         | contextlevel | reference | pagetypepattern | defaultregion |
      | calendar_upcoming | System       | 1        | my-index   | content      |

  @javascript
  Scenario: Create human readable events
    Given I log in as "admin"
    And the following "events" exist:
      | name                             | eventtype | timestart                     | timeduration |
#      Starts today and ends today in the future
      | This day one hour event          | user      | ##today midnight +1 seconds## | 86398        |
#      Starts today, ends tomorrow
      | This day and 1 day event         | user      | ##today midnight +1 seconds## | 87000        |
#      Starts yesterday, ends today in the future
      | Last day one day event           | user      | ##today noon -1 days##        | 129598       |
#      Starts yesterday, ends in the past
      | Last day less than one day event | user      | ##today noon -1 days##        | 86400        |
#      Starts tomorrow
      | Next day event                   | user      | ##today midnight +1 days##    | 86400        |
#      Far in the future
      | Future event                     | user      | ##today noon +2 days##        | 86400        |
    When I am on homepage
    And I click on "This day one hour event" "link"
    Then I should see "Today" in the "This day one hour event" "dialogue"
    And I should not see "Yesterday" in the "This day one hour event" "dialogue"
    And I should not see "Tomorrow" in the "This day one hour event" "dialogue"
    And "Warning" "icon" should exist in the "This day one hour event" "dialogue"
    And I click on "Close" "button" in the "This day one hour event" "dialogue"
    And I click on "This day and 1 day event" "link"
    And I should see "Today" in the "This day and 1 day event" "dialogue"
    And I should not see "Yesterday" in the "This day and 1 day event" "dialogue"
    And I should see "Tomorrow" in the "This day and 1 day event" "dialogue"
    And "Warning" "icon" should exist in the "This day and 1 day event" "dialogue"
    And I click on "Close" "button" in the "This day and 1 day event" "dialogue"
    And I click on "Last day one day event" "link"
    And I should see "Today" in the "Last day one day event" "dialogue"
    And I should see "Yesterday" in the "Last day one day event" "dialogue"
    And I should not see "Tomorrow" in the "Last day one day event" "dialogue"
    And "Warning" "icon" should exist in the "Last day one day event" "dialogue"
    And I click on "Close" "button" in the "Last day one day event" "dialogue"
    And I click on "Next day event" "link"
    And I should not see "Today" in the "Next day event" "dialogue"
    And I should not see "Yesterday" in the "Next day event" "dialogue"
    And I should see "Tomorrow" in the "Next day event" "dialogue"
    And "Warning" "icon" should exist in the "Next day event" "dialogue"
    And I click on "Close" "button" in the "Next day event" "dialogue"
    And I click on "Future event" "link"
    And I should not see "Today" in the "Future event" "dialogue"
    And I should not see "Yesterday" in the "Future event" "dialogue"
    And I should not see "Tomorrow" in the "Future event" "dialogue"
    And "Warning" "icon" should not exist in the "Future event" "dialogue"
    And I click on "Close" "button" in the "Future event" "dialogue"

@core @core_block
Feature: Show hidden blocks in a docked block region when editing
  In order to edit blocks in a hidden region
  As a teacher
  I need to be able to see the blocks when editing is on

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Search forums" block
    And I add the "Latest announcements" block
    And I add the "Upcoming events" block
    And I add the "Recent activity" block
    # Hide all the blocks in the non-default region
    And I configure the "Search forums" block
    And I set the following fields to these values:
      | Visible | No |
    And I click on "Save changes" "button"
    And I configure the "Latest announcements" block
    And I set the following fields to these values:
      | Visible | No |
    And I click on "Save changes" "button"
    And I configure the "Upcoming events" block
    And I set the following fields to these values:
      | Visible | No |
    And I click on "Save changes" "button"
    And I configure the "Recent activity" block
    And I set the following fields to these values:
      | Visible | No |
    When I click on "Save changes" "button"
    # Editing is on so they should be visible
    Then I should see "Search forums"
    And I should see "Latest announcements"
    And I should see "Upcoming events"
    And I should see "Recent activity"
    And I turn editing mode off
    # Editing is off, so they should no longer be visible
    And I should not see "Search forums"
    And I should not see "Latest announcements"
    And I should not see "Upcoming events"
    And I should not see "Recent activity"

  @javascript
  Scenario: Check that a region with only hidden blocks is not docked in editing mode (javascript enabled)

  Scenario: Check that a region with only hidden blocks is not docked in editing mode (javascript disabled)

@theme_boost
Feature: Using the help popover
  As a user who wants to use the help popover
  The help popover must be accessible

  @javascript @accessibility
  Scenario: Checking the policies link in the footer popover
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name             | Revision | Content    | Summary     | Status |
      | This site policy |          | full text2 | short text2 | active |
    And I am on site homepage
    And I click on "Continue" "link"
    When I click on "Show footer" "button" in the "page-footer" "region"
    Then I should see "Policies" in the "page-footer" "region"
    And the page should meet accessibility standards with "best-practice" extra tests

  @javascript
  Scenario: Navigate to a link in a form help popover using the keyboard
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
    And the following "activity" exists:
      | activity | quiz      |
      | course   | C1        |
      | name     | Test quiz |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I am on the "Test quiz" "quiz activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I click on "Timing" "link"
    When I click on "#fitem_id_timelimit .help-icon" "css_element"
    Then ".help-popover a[href*='/mod/quiz/timing'][target='_blank']" "css_element" should be visible
    And ".help-popover[role='dialog'][aria-label='Help']" "css_element" should be visible
    And "#fitem_id_timelimit .help-icon[aria-haspopup='dialog']" "css_element" should be visible
    And I press the tab key
    Then the focused element is "More help" "link"
    And "More help" "link" should be visible
    When I press the shift tab key
    Then the focused element is "#fitem_id_timelimit .help-icon" "css_element"
    When I press the tab key
    Then the focused element is "More help" "link"
    When I press the tab key
    Then the focused element is "#id_timelimit_enabled" "css_element"
    When I press the tab key
    Then the focused element is "#fitem_id_overduehandling .help-icon" "css_element"
    When I click on "#fitem_id_timelimit .help-icon" "css_element"
    And I press the tab key
    And I press the enter key
    And I switch to a second window
    And I close all opened windows
    And I switch to the main window
    And I click on "#fitem_id_timelimit .help-icon" "css_element"
    And I press the escape key
    Then ".help-popover" "css_element" should not be visible
    And the focused element is "#fitem_id_timelimit .help-icon" "css_element"

  @javascript
  Scenario: Use a form help popover without a More help link
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | category  | 0        |
    And the following "activity" exists:
      | activity | quiz      |
      | course   | C1        |
      | name     | Test quiz |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I am on the "Test quiz" "quiz activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I click on "Timing" "link"
    And I set the field "When time expires" to "There is a grace period when open attempts can be submitted, but no more questions answered"
    When I click on "#fitem_id_graceperiod .help-icon" "css_element"
    Then ".help-popover" "css_element" should be visible
    And "More help" "link" should not exist in the ".help-popover" "css_element"
    When I press the tab key
    Then the focused element is not "#fitem_id_graceperiod .help-icon" "css_element"
    And ".help-popover" "css_element" should not be visible

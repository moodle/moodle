@tool_behat
Feature: Confirm that we can open multiple browser tabs
  In order to use multiple browser tabs
  As a test writer
  I need the relevant Behat steps to work

  @javascript @_switch_window
  Scenario: Open multiple browser tabs
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
      | Course 3 | C3        |
    And I am on the "C1" "Course" page logged in as "admin"

    # Open a new tab on the same page.
    When I open a tab named "CourseViewer1" on the current page
    And I should see "Course 1" in the "h1" "css_element"
    And I am on the "C2" "Course" page

    # Open new tab for specified page with identifier.
    And I open a tab named "CourseViewer2" on the "C3" "Course" page

    # And for a specified page without identifier.
    And I open a tab named "CourseViewer4" on the "My courses" page

    # Switch between all the tabs and confirm their different contents.
    Then I should see "You're not enrolled in any courses."
    And I switch to "CourseViewer2" tab
    And "Course 3" "heading" should exist
    And I switch to "CourseViewer1" tab
    And "Course 2" "heading" should exist
    And I switch to the main tab
    And "Course 1" "heading" should exist

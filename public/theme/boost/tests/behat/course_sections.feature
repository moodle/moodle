@theme_boost
Feature: Course sections selector is not available on boost theme
  In order to view course sections selector
  As admin
  I need to use the classic theme

  # Please note: This is a sister test to "Use the course section selector in classic theme" to confirm
  # that the "Jump to" menu does not exist on the section page.
  Scenario Outline: Course sections selector is not available on boost theme regardless of the course format
    Given the following "courses" exist:
      | fullname | shortname | format         | coursedisplay | initsections |
      | Course 1 | C1        | <courseformat> | 1             | 1            |
    # Add activities in different sections to test that only the selected section's activity is visible.
    And the following "activities" exist:
      | activity | course | name     | section |
      | forum    | C1     | Forum 1  | 1       |
      | assign   | C1     | Assign 1 | 2       |
    And I am on the "Course 1" course page logged in as admin
    When I click on "Go to section Section 1" "link"
    # Confirm that only the selected section's activity is visible.
    Then I should see "Forum 1"
    And I should not see "Assign 1"
    # Confirm that the "Jump to" menu does not exist on the view section page.
    And "jump" "select" should not exist

    Examples:
      | courseformat |
      | topics       |
      | weeks        |

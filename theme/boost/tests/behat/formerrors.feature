@javascript @theme_boost
Feature: Form errors only display after submit or change in Boost theme
  In order to have a more accessible way to display form errors
  As anyone
  I need to see errors only after I submit the form or change a field

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |

  Scenario: Form error appears after submit, but not after just tabbing through
    When I log in as "admin"
    And I navigate to "Courses > Add a new course" in site administration
    And I click on "Course full name" "field"
    And I press the tab key
    Then I should not see "Missing full name"
    And I press "Save and display"
    And I should see "Missing full name"
    And the focused element is "Course full name" "field"

  Scenario: Form error appears immediately after change
    When I am on the "C1" "course editing" page logged in as "admin"
    And I set the field "Course full name" to ""
    And I press the tab key
    Then I should see "Missing full name"
    And the focused element is "Course full name" "field"

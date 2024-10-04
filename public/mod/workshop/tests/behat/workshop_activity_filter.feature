@mod @mod_workshop
Feature: View work shop activity submissions report.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Money    | student1@example.com |
      | student2 | Beth      | Velvet   | student2@example.com |
      | student3 | Beth      | Moe      | student3@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name          | intro                     | course |
      | workshop | Music history | Test workshop description | C1     |

  Scenario Outline: Filter submissions report by last name/first name
    Given I am on the "Music history" "workshop activity" page logged in as teacher1
    When I change phase in workshop "Music history" to "<phase>"
    Then ".firstinitial" "css_element" should exist
    And ".lastinitial" "css_element" should exist
    And "grading-report" "table" should exist
    And I should see "Beth Moe" in the "grading-report" "table"
    And I should see "Vinnie Money" in the "grading-report" "table"
    And I should see "Beth Velvet" in the "grading-report" "table"

    Examples:
      | phase                    |
      | Submission phase         |
      | Assessment phase         |
      | Grading evaluation phase |
      | Closed                   |

  Scenario: Filter submissions report by last name/first name is hidden in the Setup phase.
    When I am on the "Music history" "workshop activity" page logged in as teacher1
    Then ".firstinitial" "css_element" should not exist
    And ".lastinitial" "css_element" should not exist
    And "grading-report" "table" should not exist

  Scenario: Filter submissions report by first name as a teacher.
    Given I am on the "Music history" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Music history" to "Submission phase"
    When I click on "B" "link" in the ".firstinitial" "css_element"
    Then I should see "Beth Moe" in the "grading-report" "table"
    And I should see "Beth Velvet" in the "grading-report" "table"
    And I should not see "Vinnie Money" in the "grading-report" "table"

  Scenario: Filter submissions report by last name name as a teacher.
    Given I am on the "Music history" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Music history" to "Submission phase"
    When I click on "V" "link" in the ".lastinitial" "css_element"
    Then I should see "Beth Velvet" in the "grading-report" "table"
    And I should not see "Beth Moe" in the "grading-report" "table"
    And I should not see "Vinnie Money" in the "grading-report" "table"

  Scenario: Filter submissions report by first name and last name as a teacher.
    Given I am on the "Music history" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Music history" to "Submission phase"
    When I click on "V" "link" in the ".firstinitial" "css_element"
    And I click on "M" "link" in the ".lastinitial" "css_element"
    Then I should see "Vinnie Money" in the "grading-report" "table"
    And I should not see "Beth Moe" in the "grading-report" "table"
    And I should not see "Beth Velvet" in the "grading-report" "table"

  Scenario: Filter submissions report and see nothing.
    Given I am on the "Music history" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Music history" to "Submission phase"
    When I click on "Z" "link" in the ".firstinitial" "css_element"
    Then I should see "Nothing to display"

  Scenario: Filter submissions report using All by first name
    Given I am on the "Music history" "workshop activity" page logged in as teacher1
    And I change phase in workshop "Music history" to "Submission phase"
    When I click on "V" "link" in the ".firstinitial" "css_element"
    And I click on "M" "link" in the ".lastinitial" "css_element"
    And I click on "All" "link" in the ".firstinitial" "css_element"
    Then I should see "Vinnie Money" in the "grading-report" "table"
    And I should see "Beth Moe" in the "grading-report" "table"
    And I should not see "Beth Velvet" in the "grading-report" "table"

@mod @mod_subsection @report
Feature: Subsections are shown in reports
  In order to use reports
  As an teacher
  I need to see sections and subsections structure in reports

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname  | email                 |
      | teacher1 | Teacher    | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  | numsections | initsections |
      | Course 1 | C1         | 0         | 1           | 1            |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name                 | course | idnumber     | section |
      | page       | First page           | C1     | page1        | 1       |
      | subsection | Subsection 1         | C1     | subsection1  | 1       |
      | page       | Last page            | C1     | last         | 1       |
      | page       | Page in Subsection 1 | C1     | subpage      | 2       |
    And I log in as "teacher1"

  @report_outline
  Scenario: Course Activity report show subsections' information
    Given I am on "Course 1" course homepage
    When I navigate to "Reports > Activity report" in current page administration
    Then I should see "First page" in the "generaltable" "table"
    And "Subsection" "table_row" should appear before "Last page" "table_row"
    And "Page in Subsection 1" "table_row" should appear before "Last page" "table_row"

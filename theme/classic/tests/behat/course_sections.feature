@theme_classic
Feature: Select course sections using classic theme
  In order to view course sections when using the classic theme
  As a teacher
  I need to select the section from the section selector

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | numsections      | 3        |
      | initsections     | 1        |
    And the following "activities" exist:
      | course | activity | name         | idnumber | section |
      | C1     | assign   | Assignment 1 | assign1  | 1       |
      | C1     | assign   | Assignment 2 | assign2  | 2       |

  @javascript
  Scenario: Use the course section selector in classic theme
    Given I am on the "C1" "Course" page logged in as "admin"
    And I turn editing mode on
    When I choose the "View" item in the "Edit" action menu of the "Section 1" "section"
    Then I should see "Section 1"
    And I should see "Assignment 1"
    And I should not see "Assignment 2"
    And I select "Section 2" from the "jump" singleselect
    And I should see "Section 2"
    And I should not see "Assignment 1"
    And I should see "Assignment 2"
    And the "jump" select box should contain "Section 3"

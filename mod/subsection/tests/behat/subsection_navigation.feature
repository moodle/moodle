@mod @mod_subsection
Feature: Teachers navigate to subsections
  In order to use subsections
  As an teacher
  I need to navigate to subsections

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "users" exist:
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
      | subsection | Subsection 1         | C1     | subsection1  | 1       |
      | page       | Page in Subsection 1 | C1     | page1        | 2       |
    And I log in as "teacher1"

  Scenario: Subsection section page shows parent section in the breadcrumb
    When I am on the "C1 > Subsection 1" "course > section" page
    Then "C1" "link" should exist in the ".breadcrumb" "css_element"
    And "Section 1" "link" should exist in the ".breadcrumb" "css_element"
    And "Subsection 1" "text" should exist in the ".breadcrumb" "css_element"

  Scenario: Activity page shows subsection and its parent section in the breadcrumb
    When I am on the "page1" "Activity" page
    Then "C1" "link" should exist in the ".breadcrumb" "css_element"
    And "Section 1" "link" should exist in the ".breadcrumb" "css_element"
    And "Subsection 1" "link" should exist in the ".breadcrumb" "css_element"
    And "Page in Subsection 1" "text" should exist in the ".breadcrumb" "css_element"

  Scenario: Sections and Subsections are displayed in the navigation block
    Given the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I turn editing mode on
    When I am on the "page1" "Activity" page
    And I add the "Navigation" block if not present
    Then "Section 1" "link" should appear before "Subsection 1" "link" in the "Navigation" "block"
    And "Subsection 1" "link" should appear before "Page in Subsection 1" "link" in the "Navigation" "block"

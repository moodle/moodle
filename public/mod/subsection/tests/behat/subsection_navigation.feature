@mod @mod_subsection
Feature: Teachers navigate to subsections
  In order to use subsections
  As an teacher
  I need to navigate to subsections

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
      | activity   | name                 | course | idnumber    | section |
      | assign     | Assignment 1         | C1     | assignment1 | 1       |
      | subsection | Subsection 1         | C1     | subsection1 | 1       |
      | page       | Page in Subsection 1 | C1     | page1       | 2       |
      | assign     | Assignment 2         | C1     | assignment2 | 1       |
    And I log in as "teacher1"

  Scenario: Subsection section page shows parent section in the breadcrumb
    When I am on the "C1 > Subsection 1" "course > section" page
    Then "C1" "link" should exist in the ".breadcrumb" "css_element"
    And "Section 1" "text" should exist in the ".breadcrumb" "css_element"

  Scenario: Activity page shows subsection and its parent section in the breadcrumb
    When I am on the "page1" "Activity" page
    Then "C1" "link" should exist in the ".breadcrumb" "css_element"
    And "Section 1" "link" should exist in the ".breadcrumb" "css_element"
    And "Subsection 1" "link" should exist in the ".breadcrumb" "css_element"
    And "Page in Subsection 1" "text" should exist in the ".breadcrumb" "css_element"

  Scenario: Activity page shows Sections and Subsections in the navigation block
    Given the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I turn editing mode on
    When I am on the "page1" "Activity" page
    And I add the "Navigation" block if not present
    Then "Section 1" "link" should appear before "Assignment 1" "link" in the "Navigation" "block"
    And "Assignment 1" "link" should appear before "Subsection 1" "link" in the "Navigation" "block"
    And "Subsection 1" "link" should appear before "Page in Subsection 1" "link" in the "Navigation" "block"
    And "Page in Subsection 1" "link" should appear before "Assignment 2" "link" in the "Navigation" "block"

  @javascript
  Scenario: Section page shows Sections and Subsections in the navigation block
    Given the following config values are set as admin:
      | unaddableblocks |  | theme_boost |
    And I turn editing mode on
    When I am on the "C1 > Section 1" "course > section" page
    And I add the "Navigation" block if not present
    And I click on "Subsection 1" "link" in the "Navigation" "block"
    Then "Section 1" "link" should appear before "Assignment 1" "link" in the "Navigation" "block"
    And "Assignment 1" "link" should appear before "Subsection 1" "link" in the "Navigation" "block"
    And "Subsection 1" "link" should appear before "Page in Subsection 1" "link" in the "Navigation" "block"
    And "Page in Subsection 1" "link" should appear before "Assignment 2" "link" in the "Navigation" "block"

  @javascript
  Scenario: The navigation block can load subsections via ajax
    Given the following config values are set as admin:
      | unaddableblocks |  | theme_boost |
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    # Open all navigation nodes via keyboard because it does not use buttons/links chevrons.
    And I click on "Actions menu" "menuitem" in the "Navigation" "block"
    And I press the escape key
    And I press the tab key
    And I press the multiply key
    Then "Section 1" "link" should appear before "Assignment 1" "link" in the "Navigation" "block"
    And "Assignment 1" "link" should appear before "Subsection 1" "link" in the "Navigation" "block"
    And "Subsection 1" "link" should appear before "Page in Subsection 1" "link" in the "Navigation" "block"
    And "Page in Subsection 1" "link" should appear before "Assignment 2" "link" in the "Navigation" "block"

  Scenario: Subsections are displayed inline when layout is set to single page
    Given I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Course layout" to "Show one section per page"
    And I click on "Save and display" "button"
    When I am on the "C1 > Section 1" "course > section" page
    Then I should see "Assignment 1" in the "page-content" "region"
    And I should see "Subsection 1" in the "page-content" "region"
    And I should see "Page in Subsection 1" in the "page-content" "region"
    And I should see "Assignment 2" in the "page-content" "region"

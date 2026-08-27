@mod @mod_subsection @core_courseformat
Feature: Users view the course index with subsections
  In order to effectively navigate the course with subsections
  As an user
  I need to be able to effectively use the course index in an accessible way

  Background:
    Given the following "users" exist:
      | username | firstname    | lastname  | email                 |
      | teacher1 | Teacher      | 1         | teacher1@example.com  |
      | student1 | Student      | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname    | category  | numsections   | initsections  |
      | Course 1 | C1           | 0         | 3             | 1             |
    And the following "course enrolments" exist:
      | user        | course    | role              |
      | teacher1    | C1        | editingteacher    |
      | student1    | C1        | student           |
    And the following "activities" exist:
      | activity   | name                 | course | idnumber | section |
      | subsection | Subsection1          | C1     | sub1     | 1       |
      | page       | Page1 in Subsection1 | C1     | page11   | 4       |
      | subsection | Subsection2          | C1     | sub2     | 1       |
      | data       | New database         | C1     | data1    | 3       |
      | page       | New page             | C1     | page1    | 3       |

  @javascript
  Scenario: A subsection is a single course index tree item with an accessible name
    Given I am on the "C1" "Course" page logged in as "teacher1"
    # The course index is hidden by default in small devices.
    And I change window size to "large"
    And I should see "Subsection1" in the "courseindex-content" "region"
    # A tree item can only contain other tree items inside a group.
    Then "#course-index [role='treeitem'] > [role='treeitem']" "css_element" should not exist
    And the "role" attribute of "#course-index [data-for='section'][data-number='4']" "css_element" should not be set
    And the "role" attribute of "#course-index [data-for='cm'][aria-labelledby='courseindexsection4-title']" "css_element" should contain "treeitem"
    And I should see "Subsection1" in the "#courseindexsection4-title" "css_element"
    # Empty subsections are exposed the same way.
    And the "role" attribute of "#course-index [data-for='section'][data-number='5']" "css_element" should not be set
    And the "role" attribute of "#course-index [data-for='cm'][aria-labelledby='courseindexsection5-title']" "css_element" should contain "treeitem"
    And I should see "Subsection2" in the "#courseindexsection5-title" "css_element"

  @javascript
  Scenario: A subsection is a single course index tree item for students
    Given I am on the "C1" "Course" page logged in as "student1"
    And I change window size to "large"
    And I should see "Subsection1" in the "courseindex-content" "region"
    Then "#course-index [role='treeitem'] > [role='treeitem']" "css_element" should not exist
    And the "role" attribute of "#course-index [data-for='cm'][aria-labelledby='courseindexsection4-title']" "css_element" should contain "treeitem"
    And I should see "Subsection1" in the "#courseindexsection4-title" "css_element"

  @javascript
  Scenario: A subsection course index tree item exposes the current expanded state
    Given I am on the "C1" "Course" page logged in as "student1"
    And I change window size to "large"
    And the "aria-expanded" attribute of "#course-index [data-for='cm'][aria-labelledby='courseindexsection4-title']" "css_element" should contain "true"
    When I click on "Collapse" "link" in the "#course-index [data-for='section'][data-number='4'] [data-for='section_item']" "css_element"
    Then I should not see "Page1 in Subsection1" in the "courseindex-content" "region"
    And the "aria-expanded" attribute of "#course-index [data-for='cm'][aria-labelledby='courseindexsection4-title']" "css_element" should contain "false"
    And I click on "Expand" "link" in the "#course-index [data-for='section'][data-number='4'] [data-for='section_item']" "css_element"
    And I should see "Page1 in Subsection1" in the "courseindex-content" "region"
    And the "aria-expanded" attribute of "#course-index [data-for='cm'][aria-labelledby='courseindexsection4-title']" "css_element" should contain "true"

  @javascript
  Scenario: Course index keyboard navigation goes through a subsection only once
    Given I am on the "C1" "Course" page logged in as "student1"
    And I change window size to "large"
    And I click on "Close course index" "button"
    And I click on "Open course index" "button"
    And I press the tab key
    And the focused element is "#course-index [data-for='section'][data-number='0']" "css_element"
    And I press the down key
    And the focused element is "#course-index [data-for='section'][data-number='1']" "css_element"
    When I press the down key
    Then the focused element is "#course-index [data-for='cm'][aria-labelledby='courseindexsection4-title']" "css_element"
    And I press the down key
    And the focused element is "#course-index [data-for='section'][data-number='4'] [data-for='cm']" "css_element"
    And I press the down key
    And the focused element is "#course-index [data-for='cm'][aria-labelledby='courseindexsection5-title']" "css_element"

  @javascript @accessibility
  Scenario: The course index with subsections meets accessibility standards
    Given I am on the "C1" "Course" page logged in as "student1"
    And I change window size to "large"
    And I should see "Subsection1" in the "courseindex-content" "region"
    Then the "#course-index" "css_element" should meet accessibility standards with "best-practice" extra tests

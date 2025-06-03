@format @format_topics
Feature: Custom sections are created with the system default number of sections
  In order to create courses
  As a course creator
  I need my courses to be created as the system default number of sections

  @javascript
  Scenario: Default number of sections in course creation
    Given the following config values are set as admin:
      | numsections | 5 | moodlecourse |
    When I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "Create new course" "link"
    And I expand all fieldsets
    And I set the field "Course full name" to "Course 1"
    And I set the field "Course short name" to "C1"
    And I set the field "Format" to "Custom sections"
    Then I should not see "Number of sections"
    And I click on "Save and display" "button"
    And "[data-for='section'][data-number='1']" "css_element" should exist
    And "[data-for='section'][data-number='2']" "css_element" should exist
    And "[data-for='section'][data-number='3']" "css_element" should exist
    And "[data-for='section'][data-number='4']" "css_element" should exist
    And "[data-for='section'][data-number='5']" "css_element" should exist
    And "[data-for='section'][data-number='6']" "css_element" should not exist

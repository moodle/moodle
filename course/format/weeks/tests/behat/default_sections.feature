@format @format_weeks
Feature: Weeks format courses are created with the system default number of sections
  In order to create courses
  As a course creator
  I need my week courses to be created as the system default number of sections

  @javascript
  Scenario: Weeks formats cannot be created with more sections than the format max
    Given the following config values are set as admin:
      | maxinitialsections | 5 | format_weeks |
      | numsections        | 40 | moodlecourse |
    When I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "Create new course" "link"
    And I expand all fieldsets
    And I set the field "Course full name" to "Course 1"
    And I set the field "Course short name" to "C1"
    And I set the field "Format" to "Weekly sections"
    Then the "Number of sections" select box should contain "5"
    And the "Number of sections" select box should not contain "6"
    And I expand all fieldsets
    And I set the field "Number of sections" to "5"
    And I click on "Save and display" "button"
    And "[data-for='section'][data-number='1']" "css_element" should exist
    And "[data-for='section'][data-number='2']" "css_element" should exist
    And "[data-for='section'][data-number='3']" "css_element" should exist
    And "[data-for='section'][data-number='4']" "css_element" should exist
    And "[data-for='section'][data-number='5']" "css_element" should exist
    And "[data-for='section'][data-number='6']" "css_element" should not exist

  @javascript
  Scenario: Weeks formats will be created with the system default
    Given the following config values are set as admin:
      | numsections        | 5 | moodlecourse |
    When I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "Create new course" "link"
    And I expand all fieldsets
    And I set the field "Course full name" to "Course 1"
    And I set the field "Course short name" to "C1"
    And I set the field "Format" to "Weekly sections"
    Then the "Number of sections" select box should contain "52"
    And the "Number of sections" select box should not contain "53"
    And I click on "Save and display" "button"
    And "[data-for='section'][data-number='1']" "css_element" should exist
    And "[data-for='section'][data-number='2']" "css_element" should exist
    And "[data-for='section'][data-number='3']" "css_element" should exist
    And "[data-for='section'][data-number='4']" "css_element" should exist
    And "[data-for='section'][data-number='5']" "css_element" should exist
    And "[data-for='section'][data-number='6']" "css_element" should not exist

  @javascript
  Scenario: Weeks formats can be created with a specific number of sections
    Given the following config values are set as admin:
      | numsections        | 4  | moodlecourse |
      | maxinitialsections | 10 | format_weeks |
    When I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "Create new course" "link"
    And I expand all fieldsets
    And I set the field "Course full name" to "Course 1"
    And I set the field "Course short name" to "C1"
    And I set the field "Format" to "Weekly sections"
    Then the "Number of sections" select box should contain "10"
    And the "Number of sections" select box should not contain "11"
    And I expand all fieldsets
    And I set the field "Number of sections" to "5"
    And I click on "Save and display" "button"
    And "[data-for='section'][data-number='1']" "css_element" should exist
    And "[data-for='section'][data-number='2']" "css_element" should exist
    And "[data-for='section'][data-number='3']" "css_element" should exist
    And "[data-for='section'][data-number='4']" "css_element" should exist
    And "[data-for='section'][data-number='5']" "css_element" should exist
    And "[data-for='section'][data-number='6']" "css_element" should not exist

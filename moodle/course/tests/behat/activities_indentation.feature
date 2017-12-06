@core @core_course @_cross_browser
Feature: Indent items on the course page
  In order to create a structured view of activities
  As a teacher
  I need to move activities and resources to left and right

  @javascript
  Scenario: Indent course items with Javascript enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
    When I indent right "Test glossary name" activity
    Then "#section-1 li.glossary div.mod-indent-1" "css_element" should exist
    And I indent right "Test glossary name" activity
    And "//li[@id='section-1']/descendant::li[contains(concat(' ', @class, ' '), ' glossary ')]/descendant::a[not(contains(concat(' ', @class, ' '), ' hidden '))]/descendant::span[normalize-space(.)='Move left']" "xpath_element" should exist
    And "#section-1 li.glossary div.mod-indent-2" "css_element" should exist
    And I reload the page
    And "#section-1 li.glossary div.mod-indent-2" "css_element" should exist
    And I indent left "Test glossary name" activity
    And I indent left "Test glossary name" activity
    And "#section-1 li.glossary div.mod-indent-2" "css_element" should not exist
    And "#section-1 li.glossary div.mod-indent-1" "css_element" should not exist
    And "//li[@id='section-1']/descendant::li[contains(concat(' ', @class, ' '), ' glossary ')]/descendant::a[not(contains(concat(' ', @class, ' '), ' hidden '))]/descendant::span[normalize-space(.)='Move left']" "xpath_element" should not exist

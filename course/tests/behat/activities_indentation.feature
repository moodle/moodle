@core_course @_cross_browser
Feature: Indent items on the course page
  In order to create a structured view of activities
  As a moodle teacher
  I need to move activities and resources to left and right 

  @javascript
  Scenario: Indent course items with Javascript enabled
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
    When I click on "Move right" "link" in the "#section-1 li.glossary" "css_element"
    And I wait "2" seconds
    Then "#section-1 li.glossary div.mod-indent-1" "css_element" should exists
    And I click on "Move right" "link" in the "#section-1 li.glossary" "css_element"
    And I wait "2" seconds
    And "//*[@id='section-1']/descendant::li[contains(concat(' ', @class, ' '), ' glossary ')]/descendant::a[@title='Move left']" "xpath_element" should exists
    And "#section-1 li.glossary div.mod-indent-2" "css_element" should exists
    And I reload the page
    And "#section-1 li.glossary div.mod-indent-2" "css_element" should exists
    And I click on "Move left" "link" in the "#section-1 li.glossary" "css_element"
    And I wait "2" seconds
    And I click on "Move left" "link" in the "#section-1 li.glossary" "css_element"
    And I wait "2" seconds
    And "#section-1 li.glossary div.mod-indent-2" "css_element" should not exists
    And "#section-1 li.glossary div.mod-indent-1" "css_element" should not exists
    And "//*[@id='section-1']/descendant::li[contains(concat(' ', @class, ' '), ' glossary ')]/descendant::a[@title='Move left']" "xpath_element" should not exists

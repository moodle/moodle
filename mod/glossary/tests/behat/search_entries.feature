@mod @mod_glossary
Feature: Glossary entries can be searched or browsed by alphabet, category, date or author
  In order to find entries in a glossary
  As a user
  I need to search the entries list by keyword, alphabet, category, date and author

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
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
    And I follow "Test glossary name"
    And I add a glossary entries category named "The ones I like"
    And I add a glossary entries category named "All for you"
    And I add a glossary entry with the following data:
      | Concept | Eggplant |
      | Definition | Sour eggplants |
      | Categories | All for you |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test glossary name"
    And I add a glossary entry with the following data:
      | Concept | Cucumber |
      | Definition | Sweet cucumber |
      | Categories | The ones I like |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test glossary name"

  @javascript
  Scenario: Search by keyword and browse by alphabet
    When I fill in "hook" with "cucumber"
    And I press "Search"
    Then I should see "Sweet cucumber"
    And I should see "Search: cucumber"
    And I follow "Browse by alphabet"
    And I click on "E" "link" in the ".entrybox" "css_element"
    And I should see "Sour eggplants"
    And I should not see "Sweet cucumber"
    And I click on "X" "link" in the ".entrybox" "css_element"
    And I should not see "Sweet cucumber"
    And I should see "No entries found in this section"

  @javascript
  Scenario: Browse by category
    When I follow "Browse by category"
    And I select "The ones I like" from "Categories"
    Then I should see "Sweet cucumber"
    And I should not see "Sour eggplants"
    And I select "All for you" from "Categories"
    And I should see "Sour eggplants"
    And I should not see "Sweet cucumber"

  @javascript
  Scenario: Browse by date
    When I follow "Browse by date"
    And I follow "By creation date"
    Then "Delete: Eggplant" "link" should appear before "Delete: Cucumber" "link"
    And I follow "By last update"
    And I follow "By last update change to descending"
    And "Delete: Cucumber" "link" should appear before "Delete: Eggplant" "link"

  @javascript
  Scenario: Browse by author
    When I follow "Browse by Author"
    And I click on "T" "link" in the ".entrybox" "css_element"
    Then I should see "Teacher 1"
    And I should see "Sour eggplants"
    And I should not see "Sweet cucumber"
    And I click on "S" "link" in the ".entrybox" "css_element"
    And I should see "Student 1"
    And I should see "Sweet cucumber"
    And I should not see "Sour eggplants"

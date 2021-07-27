@mod @mod_glossary
Feature: Glossary entries can be searched or browsed by alphabet, category, date or author
  In order to find entries in a glossary
  As a user
  I need to search the entries list by keyword, alphabet, category, date and author

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name               | intro                     | displayformat  | course | idnumber |
      | glossary | Test glossary name | Test glossary description | fullwithauthor | C1     | g1       |
    And I am on the "Test glossary name" "glossary activity" page logged in as teacher1
    And I add a glossary entries category named "The ones I like"
    And I add a glossary entries category named "All for you"
    And I add a glossary entry with the following data:
      | Concept | Eggplant |
      | Definition | Sour eggplants |
      | Categories | All for you |
    And I log out
    And I am on the "Test glossary name" "glossary activity" page logged in as student1
    And I add a glossary entry with the following data:
      | Concept | Cucumber |
      | Definition | Sweet cucumber |
      | Categories | The ones I like |
    And I log out
    And I am on the "Test glossary name" "glossary activity" page logged in as teacher1

  @javascript
  Scenario: Search by keyword and browse by alphabet
    When I set the field "hook" to "cucumber"
    And I press "Search"
    Then I should see "Sweet cucumber"
    And I should see "Search: cucumber"
    And I click on "E" "link" in the ".entrybox" "css_element"
    And I should see "Sour eggplants"
    And I should not see "Sweet cucumber"
    And I click on "X" "link" in the ".entrybox" "css_element"
    And I should not see "Sweet cucumber"
    And I should see "No entries found in this section"

  @javascript
  Scenario: Browse by category
    When I follow "Browse by category"
    And I set the field "Categories" to "The ones I like"
    Then I should see "Sweet cucumber"
    And I should not see "Sour eggplants"
    And I set the field "Categories" to "All for you"
    And I should see "Sour eggplants"
    And I should not see "Sweet cucumber"

  @javascript
  Scenario: Browse by date
    When I follow "Browse by date"
    And I follow "By creation date"
    Then "Delete entry: Eggplant" "link" should appear before "Delete entry: Cucumber" "link"
    And I follow "By last update"
    And I follow "By last update change to descending"
    And "Delete entry: Cucumber" "link" should appear before "Delete entry: Eggplant" "link"

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

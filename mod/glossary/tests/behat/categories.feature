@mod @mod_glossary
Feature: Glossary entries can be organised in categories
  In order to organise glossary entries
  As a teacher
  I need to be able to create, edit and delete categories

  @javascript
  Scenario: Glossary entries can be organised in categories and categories can be autolinked
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
      | activity | name       | intro                     | displayformat | course | idnumber  |
      | glossary | MyGlossary | Test glossary description | encyclopedia  | C1     | glossary1 |
    And the following "activities" exist:
      | activity | name       | intro                                                           | course | idnumber  |
      | label    | name       | check autolinking of CategoryAutoLinks and CategoryNoLinks text | C1     | label1    |
    And the "glossary" filter is "on"
# Log in as a teacher and make sure nothing is yet autolinked
    When I am on the "Course 1" course page logged in as teacher1
    Then I should see "CategoryAutoLinks"
    And I should see "CategoryNoLinks"
    And "a.glossary.autolink" "css_element" should not exist
# Create, edit and delete categories
    And I am on the MyGlossary "glossary activity" page
    And I follow "Browse by category"
    And I press "Edit categories"
    And I press "Add category"
    And I set the field "name" to "CategoryNoLinks"
    And I press "Save changes"
    And I should see "0 Entries" in the "CategoryNoLinks" "table_row"
    And I press "Add category"
    And I set the field "name" to "CategoryAutoLinks"
    And I set the field "usedynalink" to "Yes"
    And I press "Save changes"
    And I should see "0 Entries" in the "CategoryAutoLinks" "table_row"
    And I press "Add category"
    And I set the field "name" to "Category2"
    And I press "Save changes"
    And I click on "Edit" "link" in the "Category2" "table_row"
    And I set the field "name" to "Category3"
    And I press "Save changes"
    And I should see "Category3"
    And I should not see "Category2"
    And I click on "Delete" "link" in the "Category3" "table_row"
    And I press "No"
    And I should see "Category3"
    And I click on "Delete" "link" in the "Category3" "table_row"
    And I press "Yes"
    And I should not see "Category3"
    And I press "Back"
# Add glossary entries in categories and outside
    And I add a glossary entry with the following data:
      | Concept    | EntryNoCategory |
      | Definition | Definition      |
    And I add a glossary entry with the following data:
      | Concept    | EntryCategoryNL |
      | Definition | Definition      |
      | Categories | CategoryNoLinks |
    And I add a glossary entry with the following data:
      | Concept    | EntryCategoryAL   |
      | Definition | Definition        |
      | Categories | CategoryAutoLinks |
    And I press "Add a new entry"
    And I set the following fields to these values:
      | Concept    | EntryCategoryBoth                 |
      | Definition | Definition                        |
      | Categories | CategoryAutoLinks,CategoryNoLinks |
    And I press "Save changes"
# Make sure entries appear in their categories
    And I follow "Browse by category"
    And "//h3[contains(.,'CATEGORYAUTOLINKS')]" "xpath_element" should appear before "//h3[contains(.,'CATEGORYNOLINKS')]" "xpath_element"
    And "//h4[contains(.,'EntryCategoryAL')]" "xpath_element" should appear before "//h3[contains(.,'CATEGORYNOLINKS')]" "xpath_element"
    And "(//h4[contains(.,'EntryCategoryBoth')])[1]" "xpath_element" should appear before "//h3[contains(.,'CATEGORYNOLINKS')]" "xpath_element"
    And "//h3[contains(.,'CATEGORYNOLINKS')]" "xpath_element" should appear before "(//h4[contains(.,'EntryCategoryBoth')])[2]" "xpath_element"
    And "//h4[contains(.,'EntryCategoryNL')]" "xpath_element" should appear after "//h3[contains(.,'CATEGORYNOLINKS')]" "xpath_element"
    And I should not see "EntryNoCategory"
    And I set the field "hook" to "Not categorised"
    And I set the field "Categories" to "Not categorised"
    And I should see "EntryNoCategory"
    And I should not see "EntryCategoryNL"
    And I should not see "EntryCategoryAL"
    And I should not see "EntryCategoryBoth"
# Check that category is autolinked from the text in the course
    And I am on "Course 1" course homepage
    And I should see "CategoryAutoLinks"
    And I should see "CategoryAutoLinks" in the "a.glossary.autolink" "css_element"
    And I should see "CategoryNoLinks"
    And "//a[contains(.,'CategoryNoLinks')]" "xpath_element" should not exist
# Delete a category with entries
    And I am on the MyGlossary "glossary activity" page
    And I follow "Browse by category"
    And I press "Edit categories"
    And I should see "2 Entries" in the "CategoryNoLinks" "table_row"
    And I should see "2 Entries" in the "CategoryAutoLinks" "table_row"
    And I click on "Delete" "link" in the "CategoryAutoLinks" "table_row"
    And I press "Yes"
    And I wait to be redirected
    And I am on the MyGlossary "glossary activity" page
    And I follow "Browse by category"
    And I should see "EntryCategoryNL"
    And I should not see "EntryNoCategory"
    And I should not see "EntryCategoryAL"
    And I should see "EntryCategoryBoth"
    And I set the field "Categories" to "Not categorised"
    And I should see "EntryNoCategory"
    And I should see "EntryCategoryAL"
    And I should not see "EntryCategoryBoth"

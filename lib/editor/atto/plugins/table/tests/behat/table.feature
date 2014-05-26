@atto @atto_table
Feature: Atto tables
  To format text in Atto, I need to create tables

  @javascript
  Scenario: Create a table
    Given I log in as "admin"
    And I am on homepage
    And I expand "My profile" node
    And I expand "Blogs" node
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    And I set the field "Caption" to "Dinner"
    And I press "Create table"
    And I select the text in the "Blog entry body" field
    And I click on "Table" "button"
    And I click on "Insert column after" "link"
    And I press "Save changes"
    Then ".blog_entry table caption" "css_element" should be visible


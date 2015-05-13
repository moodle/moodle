@editor @editor_atto @atto @atto_table @_bug_phantomjs
Feature: Atto tables
  To format text in Atto, I need to create tables

  @javascript
  Scenario: Create a table
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    And I set the field "Caption" to "Dinner"
    And I press "Create table"
    And I press "Save changes"
    Then ".blog_entry table caption" "css_element" should be visible

  @javascript
  Scenario: Edit a table
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    And I click on "Edit table" "link"
    And I set the field "Caption" to "Dinner"
    And I press "Update table"
    And I press "Save changes"
    Then ".blog_entry table caption" "css_element" should be visible



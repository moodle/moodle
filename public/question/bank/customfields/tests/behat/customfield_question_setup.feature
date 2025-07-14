@core @core_question @core_customfield @qbank_customfields @javascript
Feature: Site administrators can manage categories for question custom fields
  In order to have additional data in questions
  As a site site administrator
  I need to create, edit, remove and sort question custom field categories

  Scenario: Create a category for custom question fields
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I press "Add a new category"
    And I wait until the page is ready
    Then I should see "Other fields" in the "#customfield_catlist" "css_element"

  Scenario: Edit a category name for custom question fields
    Given the following "custom field categories" exist:
      | name              | component          | area     | itemid |
      | Category for test | qbank_customfields | question | 0      |
    And I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I set the field "Edit category name" in the "//div[contains(@class,'categoryinstance') and contains(.,'Category for test')]" "xpath_element" to "Good fields"
    Then I should not see "Category for test" in the "#customfield_catlist" "css_element"
    And "New value for Category for test" "field" should not exist
    And I should see "Good fields" in the "#customfield_catlist" "css_element"

  Scenario: Delete a category for custom question fields
    Given the following "custom field categories" exist:
      | name              | component          | area     | itemid |
      | Category for test | qbank_customfields | question | 0      |
    And the following "custom fields" exist:
      | name    | category          | type | shortname |
      | Field 1 | Category for test | text | f1        |
    And I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I click on "[data-role='deletecategory']" "css_element"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I wait until "Test category" "text" does not exist
    Then I should not see "Test category" in the "#customfield_catlist" "css_element"

  Scenario: Move field in the question custom fields to another category
    Given the following "custom field categories" exist:
      | name      | component          | area     | itemid |
      | Category1 | qbank_customfields | question | 0      |
      | Category2 | qbank_customfields | question | 0      |
      | Category3 | qbank_customfields | question | 0      |
    And the following "custom fields" exist:
      | name   | category  | type | shortname |
      | Field1 | Category1 | text | f1        |
      | Field2 | Category2 | text | f2        |
    When I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    Then "Field1" "text" should appear after "Category1" "text"
    And "Category2" "text" should appear after "Field1" "text"
    And "Field2" "text" should appear after "Category2" "text"
    And "Category3" "text" should appear after "Field2" "text"
    And I press "Move \"Field1\""
    And I follow "To the top of category Category2"
    And "Category2" "text" should appear after "Category1" "text"
    And "Field1" "text" should appear after "Category2" "text"
    And "Field2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Field2" "text"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And "Category2" "text" should appear after "Category1" "text"
    And "Field1" "text" should appear after "Category2" "text"
    And "Field2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Field2" "text"
    And I press "Move \"Field1\""
    And I follow "After field Field2"
    And "Field1" "text" should appear after "Field2" "text"

  Scenario: Reorder question custom field categories
    Given the following "custom field categories" exist:
      | name      | component          | area     | itemid |
      | Category1 | qbank_customfields | question | 0      |
      | Category2 | qbank_customfields | question | 0      |
      | Category3 | qbank_customfields | question | 0      |
    And the following "custom fields" exist:
      | name   | category  | type | shortname |
      | Field1 | Category1 | text | f1        |
    When I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    Then "Field1" "text" should appear after "Category1" "text"
    And "Category2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Category2" "text"
    And I press "Move \"Category2\""
    And I follow "After \"Category3\""
    And "Field1" "text" should appear after "Category1" "text"
    And "Category3" "text" should appear after "Field1" "text"
    And "Category2" "text" should appear after "Category3" "text"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And "Field1" "text" should appear after "Category1" "text"
    And "Category3" "text" should appear after "Field1" "text"
    And "Category2" "text" should appear after "Category3" "text"
    And I press "Move \"Category2\""
    And I follow "After \"Category1\""
    And "Field1" "text" should appear after "Category1" "text"
    And "Category2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Category2" "text"

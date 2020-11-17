@core @core_course @core_customfield @javascript
Feature: Managers can manage categories for course custom fields
  In order to have additional data on the course
  As a manager
  I need to create, edit, remove and sort custom field's categories

  Scenario: Create a category for custom course fields
    Given I log in as "admin"
    When I navigate to "Courses > Course custom fields" in site administration
    And I press "Add a new category"
    And I wait until the page is ready
    Then I should see "Other fields" in the "#customfield_catlist" "css_element"
    And I navigate to "Reports > Logs" in site administration
    And I press "Get these logs"
    And I log out

  Scenario: Edit a category name for custom course fields
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Edit category name" "link" in the "//div[contains(@class,'categoryinstance') and contains(.,'Category for test')]" "xpath_element"
    And I set the field "New value for Category for test" to "Good fields"
    And I press the enter key
    Then I should not see "Category for test" in the "#customfield_catlist" "css_element"
    And "New value for Category for test" "field" should not exist
    And I should see "Good fields" in the "#customfield_catlist" "css_element"
    And I navigate to "Reports > Logs" in site administration
    And I press "Get these logs"
    And I log out

  Scenario: Delete a category for custom course fields
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    And the following "custom fields" exist:
      | name    | category          | type | shortname |
      | Field 1 | Category for test | text | f1        |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "[data-role='deletecategory']" "css_element"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I wait until "Test category" "text" does not exist
    Then I should not see "Test category" in the "#customfield_catlist" "css_element"
    And I navigate to "Reports > Logs" in site administration
    And I press "Get these logs"
    And I log out

  Scenario: Move field in the course custom fields to another category
    Given the following "custom field categories" exist:
      | name      | component   | area   | itemid |
      | Category1 | core_course | course | 0      |
      | Category2 | core_course | course | 0      |
      | Category3 | core_course | course | 0      |
    And the following "custom fields" exist:
      | name   | category  | type | shortname |
      | Field1 | Category1 | text | f1        |
      | Field2 | Category2 | text | f2        |
    When I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
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
    And I navigate to "Courses > Course custom fields" in site administration
    And "Category2" "text" should appear after "Category1" "text"
    And "Field1" "text" should appear after "Category2" "text"
    And "Field2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Field2" "text"
    And I press "Move \"Field1\""
    And I follow "After field Field2"
    And "Field1" "text" should appear after "Field2" "text"
    And I log out

  Scenario: Reorder course custom field categories
    Given the following "custom field categories" exist:
      | name      | component   | area   | itemid |
      | Category1 | core_course | course | 0      |
      | Category2 | core_course | course | 0      |
      | Category3 | core_course | course | 0      |
    And the following "custom fields" exist:
      | name   | category  | type | shortname |
      | Field1 | Category1 | text | f1        |
    When I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    Then "Field1" "text" should appear after "Category1" "text"
    And "Category2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Category2" "text"
    And I press "Move \"Category2\""
    And I follow "After \"Category3\""
    And "Field1" "text" should appear after "Category1" "text"
    And "Category3" "text" should appear after "Field1" "text"
    And "Category2" "text" should appear after "Category3" "text"
    And I navigate to "Courses > Course custom fields" in site administration
    And "Field1" "text" should appear after "Category1" "text"
    And "Category3" "text" should appear after "Field1" "text"
    And "Category2" "text" should appear after "Category3" "text"
    And I press "Move \"Category2\""
    And I follow "After \"Category1\""
    And "Field1" "text" should appear after "Category1" "text"
    And "Category2" "text" should appear after "Field1" "text"
    And "Category3" "text" should appear after "Category2" "text"
    And I log out

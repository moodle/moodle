@core @core_course @core_customfield @javascript
Feature: Create shared categories and fields
  In order to have shared custom fields
  As a manager
  I need to create, edit, remove and sort shared custom field's categories

  Scenario: Create and delete a category for shared custom fields
    Given I log in as "admin"
    When I navigate to "Custom fields > Shared custom fields" in site administration
    And I press "Add a new category"
    And I wait until the page is ready
    Then I should see "Other fields" in the "#customfield_catlist" "css_element"
    And I click on "[data-role='deletecategory']" "css_element"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I wait until "Other fields" "text" does not exist

  Scenario: Shared customfields are displayed in other entities
    Given the following "custom field categories" exist:
      | name               | component        | area   | itemid |
      | My shared category | core_customfield | shared | 0      |
    And the following "custom fields" exist:
      | name            | category           | type | shortname |
      | Shared field 1  | My shared category | text | f1        |
    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    Then I should see "My shared category" in the "#customfield_catlist" "css_element"
    And I should see "Shared field 1" in the "#customfield_catlist" "css_element"
    And I navigate to "Users > Accounts > Cohort custom fields" in site administration
    And I should see "My shared category" in the "#customfield_catlist" "css_element"
    And I should see "Shared field 1" in the "#customfield_catlist" "css_element"

  Scenario: Shared custom fields cannot be reordered, edited or deleted from other entities
    Given the following "custom field categories" exist:
      | name               | component        | area   | itemid |
      | My shared category | core_customfield | shared | 0      |
      | My course category | core_course      | course | 0      |
    And the following "custom fields" exist:
      | name            | category           | type | shortname |
      | Shared field 1  | My shared category | text | f1        |
      | Course field 1  | My course category | text | f2        |
    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    # Check that the delete category link exists for course categories but not for shared categories.
    Then "Delete custom field category: My course category" "button" should exist
    And "Delete custom field category: My shared category" "button" should not exist
    # Check that the inplaceeditable exists for course categories but not for shared categories.
    And "//div[contains(@class,'categoryinstance') and contains(.,'My course category') and .//span[contains(@class,'inplaceeditable')]]" "xpath_element" should exist
    And "//div[contains(@class,'categoryinstance') and contains(.,'My shared category') and .//span[contains(@class,'inplaceeditable')]]" "xpath_element" should not exist
    # There should be no move button for lone custom field categories.
    And "Move \"My course category\"" "button" should not exist
    # There should be no move button for lone custom fields within a single custom field category.
    And "Move \"Course field 1\"" "button" should not exist
    And I press "Add a new category"
    # There should be no move button for shared categories and custom fields.
    And "Move \"My shared category\"" "button" should not exist
    And "Move \"Shared field 1\"" "button" should not exist
    # TODO. We should not need to reload the page, but behat fails to find the move buttons otherwise.
    And I reload the page
    # With more than one category there should be move buttons for course categories and fields.
    And "Move \"My course category\"" "button" should exist
    And "Move \"Course field 1\"" "button" should exist

  Scenario: Select which shared custom fields categories are used in the course entity
    Given the following "custom field categories" exist:
      | name                 | component        | area   | itemid |
      | My shared category 1 | core_customfield | shared | 0      |
      | My shared category 2 | core_customfield | shared | 0      |
      | My course category   | core_course      | course | 0      |
    And the following "custom fields" exist:
      | name            | category             | type | shortname |
      | Shared field 1  | My shared category 1 | text | f1        |
      | Shared field 2  | My shared category 2 | text | f2        |
      | Course field 1  | My course category   | text | f3        |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And I log in as "admin"
    When I am on the "C1" "Course" page
    And I navigate to "Settings" in current page administration
    Then I should see "My course category"
    And I should not see "My shared category 1"
    And I should not see "My shared category 2"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I toggle the "Enable My shared category 1" admin switch "on"
    And I am on the "C1" "Course" page
    And I navigate to "Settings" in current page administration
    And I should see "My course category"
    And I should see "My shared category 1"
    And I should not see "My shared category 2"

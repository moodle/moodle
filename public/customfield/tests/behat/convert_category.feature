@core @core_course @core_customfield @javascript
Feature: Managers can convert categories for any entity custom field category to shared custom field
  In order to use custom fields across the site
  As a manager
  I need to convert any entity custom field's categories to shared custom fields

  Background:
    Given the following "custom field categories" exist:
      | name                  | component        | area   | itemid |
      | Category for course 1 | core_course      | course | 0      |
      | Category for course 2 | core_course      | course | 0      |
      | Shared category       | core_customfield | shared | 0      |
    And the following "custom fields" exist:
      | name    | category              | type   | shortname | description |
      | Field 1 | Category for course 1 | text   | f1        | d1          |
      | Field 2 | Category for course 1 | text   | f2        | d2          |
      | Field 3 | Category for course 2 | text   | shf1      | d3          |
      | Field 4 | Shared category       | text   | shf1      | shd1        |

  Scenario: Convert a course custom field category to shared custom fields
    Given I log in as "admin"
    When I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Convert to shared custom fields" "button" in the "Category for course 1" "core_customfield > Category"
    And I should see "Are you sure you want to convert this category" in the "Convert to shared custom fields?" "dialogue"
    And I click on "Proceed" "button" in the "Convert to shared custom fields?" "dialogue"
    Then "The category has been successfully converted to shared custom fields" "toast_message" should be visible
    And "Enable Category for course 1" "checkbox" should exist in the "Category for course 1" "core_customfield > Category"
    And I reload the page
    And I navigate to "Custom fields > Shared custom fields" in site administration
    And "Category for course 1" "core_customfield > Category" should exist
    And "Convert to shared custom fields" "button" should not exist

  Scenario: Cannot convert category with duplicate custom field short names in shared category
    Given I log in as "admin"
    When I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Convert to shared custom fields" "button" in the "Category for course 2" "core_customfield > Category"
    Then I should see "One or more short names in this category already exist as shared custom fields." in the "Cannot convert to shared custom fields" "dialogue"
    And I click on "Close" "button" in the "Cannot convert to shared custom fields" "dialogue"
    And "Enable Category for course 2" "checkbox" should not exist in the "Category for course 2" "core_customfield > Category"

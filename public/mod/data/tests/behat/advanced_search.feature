@mod @mod_data
Feature: Database entries can be searched using an advanced search form.
  In order to find an entry
  As a user
  I need to have an advanced search form

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name        | description         | param1 |
      | data1    | text | My Field    | Field 1 description |        |
      | data1    | text | Their field | Field 2 description |        |
      | data1    | menu | My menu     | Field 3 description | 0\nFoo |
    And the following "mod_data > entries" exist:
      | database | user     | My Field       | Their field      | My menu |
      | data1    | teacher1 | First content  | 0                | 0       |
      | data1    | teacher1 | Second content | Authored content | Foo     |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I should see "First content"
    And I should see "Second content"

  @javascript
  Scenario: Content can be searched using advanced search
    Given I click on "Advanced search" "checkbox"
    And I should see "My Field" in the "data_adv_form" "region"
    And I should see "Their field" in the "data_adv_form" "region"
    When I set the field "My Field" in the "data_adv_form" "region" to "First"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "First content"
    And I should not see "Second content"
    And I set the following fields in the "data_adv_form" "region" to these values:
      | My Field    |   |
      | Their field | 0 |
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should see "First content"
    And I should not see "Second content"
    And I set the following fields in the "data_adv_form" "region" to these values:
      | My Field      |   |
      | Their field   |   |
      # My menu.
      | Dropdown list | 0 |
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should see "First content"
    And I should not see "Second content"

  @javascript
  Scenario: Advanced search template can use field information tags
    Given I navigate to "Templates" in current page administration
    And I set the field "Templates tertiary navigation" to "Advanced search template"
    And I set the following fields to these values:
      | Advanced search template | The test is on [[My Field#name]], [[My Field#description]], and the input [[My Field]] |
    And I click on "Save" "button"
    And I navigate to "Database" in current page administration
    And I should see "First content"
    And I should see "Second content"
    And I click on "Advanced search" "checkbox"
    And I should see "The test is on My Field, Field 1 description, and the input" in the "data_adv_form" "region"
    And I should not see "Their field" in the "data_adv_form" "region"
    When I set the field "My Field" to "First"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "First content"
    And I should not see "Second content"

  @javascript
  Scenario: Advanced search can use otherfields tag
    Given I navigate to "Templates" in current page administration
    And I set the field "Templates tertiary navigation" to "Advanced search template"
    And I set the following fields to these values:
      | Advanced search template | Main search [[My Field]], Other fields ##otherfields## |
    And I click on "Save" "button"
    And I navigate to "Database" in current page administration
    And I should see "First content"
    And I should see "Second content"
    And I click on "Advanced search" "checkbox"
    And I should see "Main search" in the "data_adv_form" "region"
    And I should see "Other fields" in the "data_adv_form" "region"
    And I should see "Their field" in the "data_adv_form" "region"
    When I set the field "My Field" to "First"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "First content"
    And I should not see "Second content"
    And I set the field "My Field" to ""
    And I set the field "Their field" to "Authored content"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should not see "First content"
    And I should see "Second content"

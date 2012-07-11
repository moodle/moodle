@mod @mod_data
Feature: Users can be required to specify certain fields when adding entries to database activities
  In order to populate databases
  As a user
  I need to specify certain fields when I add entries to databases

  Scenario: Students can add entries to a database
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Base Text input |
      | Field description | Base Text input |
    And I add a "Checkbox" field to "Test database name" database and I fill the form with:
      | Field name | Required Checkbox |
      | Field description | Required Checkbox |
      | Required | yes |
      | Options | Option 1 |
    And I add a "Latlong" field to "Test database name" database and I fill the form with:
      | Field name | Required Latlong |
      | Field description | Required Latlong |
      | Required | yes |
    And I add a "Menu" field to "Test database name" database and I fill the form with:
      | Field name | Required Menu |
      | Field description | Required Menu |
      | Required | yes |
      | Options | Option 1 |
    And I add a "Number" field to "Test database name" database and I fill the form with:
      | Field name | Required Number |
      | Field description | Required Number |
      | Required | yes |
    And I add a "Radio button" field to "Test database name" database and I fill the form with:
      | Field name | Required Radio |
      | Field description | Required Radio |
      | Required | yes |
      | Options | Option 1 |
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Required Text input |
      | Field description | Required Text input |
      | Required | yes |
    And I add a "Text area" field to "Test database name" database and I fill the form with:
      | Field name | Required Text area |
      | Field description | Required Text area |
      | Required | yes |
    And I add a "URL" field to "Test database name" database and I fill the form with:
      | Field name | Required URL |
      | Field description | Required URL |
      | Required | yes |
    And I add a "Checkbox" field to "Test database name" database and I fill the form with:
      | Field name | Not required Checkbox |
      | Field description | Not required Checkbox |
      | Options | Option 1 |
    And I add a "Latlong" field to "Test database name" database and I fill the form with:
      | Field name | Not required Latlong |
      | Field description | Not required Latlong |
    And I add a "Menu" field to "Test database name" database and I fill the form with:
      | Field name | Not required Menu |
      | Field description | Not required Menu |
      | Options | Option 1 |
    And I add a "Number" field to "Test database name" database and I fill the form with:
      | Field name | Not required Number |
      | Field description | Not required Number |
    And I add a "Radio button" field to "Test database name" database and I fill the form with:
      | Field name | Not required Radio |
      | Field description | Not required Radio |
      | Options | Option 1 |
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Not required Text input |
      | Field description | Not required Text input |
    And I add a "Text area" field to "Test database name" database and I fill the form with:
      | Field name | Not required Text area |
      | Field description | Not required Text area |
    And I add a "URL" field to "Test database name" database and I fill the form with:
      | Field name | Not required URL |
      | Field description | Not required URL |
    And I follow "Templates"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I add an entry to "Test database name" database with:
       | Base Text input | Some input to allow us to submit the for otherwise empty |
    And I press "Save and view"
    Then ".alert.alert-error" "css_element" should exist in the "Required Checkbox" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required Latlong" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required Menu" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required Number" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required Radio" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required Text input" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required Text area" "table_row"
    Then ".alert.alert-error" "css_element" should exist in the "Required URL" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Checkbox" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Latlong" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Menu" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Number" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Radio" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Text input" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required Text area" "table_row"
    Then ".alert.alert-error" "css_element" should not exist in the "Not required URL" "table_row"
    And I follow "View list"
    And I should see "No entries in database"

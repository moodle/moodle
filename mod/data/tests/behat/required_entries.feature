@mod @mod_data
Feature: Users can be required to specify certain fields when adding entries to database activities
  In order to constrain user input
  As a teacher
  I need to specify certain fields as required when I add entries to databases

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
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
      | Required | yes |
      | Field description | Base Text input |
    And I add a "Checkbox" field to "Test database name" database and I fill the form with:
      | Field name | Required Checkbox |
      | Field description | Required Checkbox |
      | Required | yes |
      | Options | Required Checkbox Option 1 |
    And I follow "Fields"
    And I set the field "newtype" to "Checkbox"
    And I click on "Go" "button" in the ".fieldadd" "css_element"
    And I set the following fields to these values:
      | Field name | Required Two-Option Checkbox |
      | Field description | Required Two-Option Checkbox |
      | Required | yes |
    And I set the field "Options" to multiline
    """
    RTOC Option 1
    RTOC Option 2
    """
    And I press "Add"
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
      | Options | Required Radio Option 1 |
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
    And I add a "Multimenu" field to "Test database name" database and I fill the form with:
      | Field name | Required Multimenu |
      | Field description | Required Multimenu |
      | Required | yes |
      | Options | Option 1 |
    And I follow "Fields"
    And I set the field "newtype" to "Multimenu"
    And I click on "Go" "button" in the ".fieldadd" "css_element"
    And I set the following fields to these values:
      | Field name | Required Two-Option Multimenu |
      | Field description | Required Two-Option Multimenu |
      | Required | yes |
    And I set the field "Options" to multiline
    """
    Option 1
    Option 2
    """
    And I press "Add"
    And I add a "Checkbox" field to "Test database name" database and I fill the form with:
      | Field name | Not required Checkbox |
      | Field description | Not required Checkbox |
      | Options | Not required Checkbox Option 1 |
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
      | Options | Not required Radio Option 1 |
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Not required Text input |
      | Field description | Not required Text input |
    And I add a "Text area" field to "Test database name" database and I fill the form with:
      | Field name | Not required Text area |
      | Field description | Not required Text area |
    And I add a "URL" field to "Test database name" database and I fill the form with:
      | Field name | Not required URL |
      | Field description | Not required URL |
    And I add a "Multimenu" field to "Test database name" database and I fill the form with:
      | Field name | Not required Multimenu |
      | Field description | Not required Multimenu |
      | Options | Option 1 |
    And I follow "Templates"
    And I log out

  Scenario: Students receive errors for empty required fields but not for optional fields
    When I log in as "student1"
    And I follow "Course 1"
    And I add an entry to "Test database name" database with:
       | Base Text input | Some input to allow us to submit the otherwise empty form |
    And I press "Save and view"
    Then ".alert.alert-error" "css_element" should exist in the "Required Checkbox" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Two-Option Checkbox" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Latlong" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Menu" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Number" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Radio" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Text input" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Text area" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required URL" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Multimenu" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Required Two-Option Multimenu" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Checkbox" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Latlong" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Menu" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Number" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Radio" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Text input" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Text area" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required URL" "table_row"
    And ".alert.alert-error" "css_element" should not exist in the "Not required Multimenu" "table_row"
    And I follow "View list"
    And I should see "No entries in database"

  Scenario: Students recieve no error for filled in required fields
    When I log in as "student1"
    And I follow "Course 1"
    And I add an entry to "Test database name" database with:
       | Base Text input               | Some input to allow us to submit the otherwise empty form |
       | Required Checkbox Option 1    | 1                                                         |
       | RTOC Option 1                 | 1                                                         |
       | Latitude                      | 0                                                         |
       | Longitude                     | 0                                                         |
       | Required Menu                 | 1                                                         |
       | Required Number               | 1                                                         |
       | Required Radio Option 1       | 1                                                         |
       | Required Text input           | New entry text                                            |
       | Required Text area            | More text                                                 |
       | Required URL                  | http://example.com/                                       |
       | Required Multimenu            | 1                                                         |
       | Required Two-Option Multimenu | 1                                                         |
    And I press "Save and view"
    And I follow "View list"
    Then I should not see "No entries in database"
    And I should see "New entry text"

  Scenario: Fields refill with data after having an error
    When I log in as "student1"
    And I follow "Course 1"
    And I add an entry to "Test database name" database with:
       | RTOC Option 1                 | 1                   |
       | Latitude                      | 0                   |
       | Longitude                     | 0                   |
       | Required Menu                 | 1                   |
       | Required Number               | 1                   |
       | Required Radio Option 1       | 1                   |
       | Required Text input           | New entry text      |
       | Required Text area            | More text           |
       | Required URL                  | http://example.com/ |
       | Required Multimenu            | 1                   |
       | Required Two-Option Multimenu | 1                   |
    And I press "Save and view"
    Then the following fields match these values:
       | Base Text input               |                     |
       | Latitude                      | 0                   |
       | Longitude                     | 0                   |
       | Required Menu                 | Option 1            |
       | Required Number               | 1                   |
       | Required Radio Option 1       | 1                   |
       | Required Text input           | New entry text      |
       | Required Text area            | More text           |
       | Required URL                  | http://example.com/ |
       | Required Multimenu            | Option 1            |
       | Required Two-Option Multimenu | Option 1            |

  Scenario: A student fills in Latitude but not Longitude will see an error
    Given I log in as "student1"
    And I follow "Course 1"
    When I add an entry to "Test database name" database with:
       | Base Text input               | Some input to allow us to submit the otherwise empty form |
       | Required Checkbox Option 1    | 1                                                         |
       | RTOC Option 1                 | 1                                                         |
       | Latitude                      | 24                                                        |
       | Required Menu                 | 1                                                         |
       | Required Number               | 1                                                         |
       | Required Radio Option 1       | 1                                                         |
       | Required Text input           | New entry text                                            |
       | Required Text area            | More text                                                 |
       | Required URL                  | http://example.com/                                       |
       | Required Multimenu            | 1                                                         |
       | Required Two-Option Multimenu | 1                                                         |
    And I set the field with xpath "//div[@title='Not required Latlong']//tr[td/label[normalize-space(.)='Latitude']]/td/input" to "20"
    And I press "Save and view"
    Then ".alert.alert-error" "css_element" should exist in the "Required Latlong" "table_row"
    And ".alert.alert-error" "css_element" should exist in the "Not required Latlong" "table_row"

@mod @mod_customcert
Feature: Being able to set a site setting to determine whether or not to display the position X and Y fields
  In order to ensure the show position X and Y fields setting works as expected
  As an admin
  I need to ensure teachers can see the position X and Y fields depending on the site setting

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 |

  Scenario: Adding an element with the show position X and Y setting disabled
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I navigate to "Edit certificate" in current page administration
    And I add the element "Code" to page "1" of the "Custom certificate 1" certificate template
    And I should not see "Position X"
    And I should not see "Position Y"

  Scenario: Adding an element with the show position X and Y setting enabled
    And I log in as "admin"
    And I navigate to "Plugins" in site administration
    And I follow "Manage activities"
    And I click on "Settings" "link" in the "Custom certificate" "table_row"
    And I set the field "Show position X and Y" to "1"
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I navigate to "Edit certificate" in current page administration
    And I add the element "Code" to page "1" of the "Custom certificate 1" certificate template
    And I should see "Position X"
    And I should see "Position Y"
    And I set the following fields to these values:
      | Position X | 5  |
      | Position Y | 10 |
    And I press "Save changes"
    And I click on ".edit-icon" "css_element" in the "Code" "table_row"
    And the following fields match these values:
      | Position X | 5  |
      | Position Y | 10 |

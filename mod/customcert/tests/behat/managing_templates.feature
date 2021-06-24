@mod @mod_customcert
Feature: Being able to manage site templates
  In order to ensure managing site templates works as expected
  As an admin
  I need to manage and load site templates

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
    And I log in as "admin"

  Scenario: Adding a site template and loading it into a course certificate
    And I navigate to "Plugins" in site administration
    And I follow "Manage activities"
    And I click on "Settings" "link" in the "Custom certificate" "table_row"
    And I follow "Manage templates"
    And I press "Create template"
    And I set the field "Name" to "Site template"
    And I press "Save changes"
    And I add the element "Border" to page "1" of the "Site template" certificate template
    And I set the following fields to these values:
      | Width  | 5 |
      | Colour | #045ECD |
    And I press "Save changes"
    And I add the element "Category name" to page "1" of the "Site template" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I navigate to "Edit certificate" in current page administration
    And I set the field "ltid" to "Site template"
    And I click on "Load" "button" in the "#loadtemplateform" "css_element"
    And I should see "Are you sure you wish to load this template"
    And I press "Cancel"
    And "elementstable" "table" should not exist
    And I set the field "ltid" to "Site template"
    And I click on "Load" "button" in the "#loadtemplateform" "css_element"
    And I should see "Are you sure you wish to load this template"
    And I press "Continue"
    And I should see "Border" in the "elementstable" "table"
    And I should see "Category name" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Border" "table_row"
    And the following fields match these values:
      | Width  | 5 |
      | Colour | #045ECD |
    And I press "Save changes"
    And I click on ".edit-icon" "css_element" in the "Category name" "table_row"
    And the following fields match these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |

  Scenario: Deleting a site template
    And I navigate to "Plugins" in site administration
    And I follow "Manage activities"
    And I click on "Settings" "link" in the "Custom certificate" "table_row"
    And I follow "Manage templates"
    And I press "Create template"
    And I set the field "Name" to "Site template"
    And I press "Save changes"
    And I follow "Manage templates"
    And I click on ".delete-icon" "css_element" in the "Site template" "table_row"
    And I press "Cancel"
    And I should see "Site template"
    And I click on ".delete-icon" "css_element" in the "Site template" "table_row"
    And I press "Continue"
    And I should not see "Site template"

  Scenario: Duplicating a site template
    And I navigate to "Plugins" in site administration
    And I follow "Manage activities"
    And I click on "Settings" "link" in the "Custom certificate" "table_row"
    And I follow "Manage templates"
    And I press "Create template"
    And I set the field "Name" to "Site template"
    And I press "Save changes"
    And I follow "Manage templates"
    And I click on ".duplicate-icon" "css_element" in the "Site template" "table_row"
    And I press "Cancel"
    And I should see "Site template"
    And I should not see "Site template (duplicate)"
    And I click on ".duplicate-icon" "css_element" in the "Site template" "table_row"
    And I press "Continue"
    And I should see "Site template"
    And I should see "Site template (duplicate)"

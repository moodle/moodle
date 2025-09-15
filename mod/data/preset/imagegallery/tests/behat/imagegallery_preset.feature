@mod @mod_data @datapreset @data_preset_imagegallery
Feature: Users can use the Image gallery preset
  In order to create an Image gallery database
  As a user
  I need to apply and use the Image gallery preset

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Alice     | Student  | student1@example.com |
      | teacher1 | Pau       | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name                | intro          | course | idnumber |
      | data     | Mountain landscapes | Database intro | C1     | data1    |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I click on "fullname" "radio" in the "Image gallery" "table_row"
    And I click on "Use this preset" "button"
    And the following "mod_data > entries" exist:
      | database | user      | title           | description                                  | image             |
      | data1    | student1  | First image     | This is the description text for image 1     | first.png         |
      | data1    | teacher1  | Second image    | And this is the description text for image 2 | second.png        |

  @javascript
  Scenario: Users view entries
    When I am on the "Mountain landscapes" "data activity" page logged in as student1
    Then I should see "First image"
    And I should not see "This is the description text for image 1"
    And I should not see "first.png"
    And I should not see "Alice Student" in the "#imagegallery-list" "css_element"
    And "//a/child::img[contains(@src, 'first.png')]" "xpath_element" should exist
    And "Actions" "button" should exist in the "#imagegallery-list" "css_element"
    And I should see "Second image"
    And I should not see "And this is the description text for image 2"
    And I should not see "second.png"
    And I should not see "Pau Teacher"
    And "//a/child::img[contains(@src, 'second.png')]" "xpath_element" should exist
    # Single view.
    And I select "Single view" from the "jump" singleselect
    And I should see "First image"
    And I should see "Alice Student" in the ".imagegallery-single" "css_element"
    And I should see "This is the description text for image 1"
    And "//a/child::img[contains(@src, 'first.png')]" "xpath_element" should exist
    And "Actions" "button" should exist in the ".imagegallery-single" "css_element"
    And I should not see "Second image"
    And I should not see "And this is the description text for image 2"
    And I should not see "Pau Teacher"
    And "//a/child::img[contains(@src, 'second.png')]" "xpath_element" should not exist
    And I follow "Next"
    And I should see "Second image"
    And I should see "Pau Teacher"
    And I should see "And this is the description text for image 2"
    And "//a/child::img[contains(@src, 'second.png')]" "xpath_element" should exist
    # This student can't edit or delete this entry, so the Actions menu shouldn't be displayed.
    And "Actions" "button" should not exist in the ".imagegallery-single" "css_element"
    And I should not see "First image"
    And I should not see "Alice Student" in the ".imagegallery-single" "css_element"
    And I should not see "This is the description text for image 1"
    And "//a/child::img[contains(@src, 'first.png')]" "xpath_element" should not exist

  @javascript
  Scenario: Users can search entries
    Given I am on the "Mountain landscapes" "data activity" page logged in as student1
    And "First image" "text" should appear before "Second image" "text"
    When I click on "Advanced search" "checkbox"
    And I should see "First name"
    And I should see "Last name"
    And I set the field "title" to "First image"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "First image"
    And I should not see "Second image"
    But I set the field "title" to "image"
    And I set the field "Order" to "Descending"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And "Second image" "text" should appear before "First image" "text"

  @javascript
  Scenario: Users can add entries
    Given I am on the "Mountain landscapes" "data activity" page logged in as student1
    When I press "Add entry"
    And I set the field "title" to "New image"
    And I set the field "description" to "This is the description for the new image."
    And I press "Save"
    Then I should see "New image"
    And I should see "This is the description for the new image."

  @javascript
  Scenario: Image gallery. Renaming a field should affect the template
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I navigate to "Fields" in current page administration
    And I open the action menu in "title" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Field name" to "Edited field name"
    And I press "Save"
    And I should see "Field updated"
    When I navigate to "Database" in current page administration
    Then I click on "Advanced search" "checkbox"
    And I should see "Edited field name"
    And I click on "Add entry" "button"
    And I should see "Edited field name"

  @javascript
  Scenario: Image gallery. Has otherfields tag
    Given the following "mod_data > fields" exist:
      | database | type | name        | description            |
      | data1    | text | Extra field | Test field description |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I select "Single view" from the "jump" singleselect
    Then I should see "Extra field"
    And I click on "Add entry" "button"
    And I should see "Extra field"

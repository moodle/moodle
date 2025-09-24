@mod @mod_data @datapreset @datapreset_resources
Feature: Users can use the Resources preset
  In order to create a Resources database
  As a user
  I need to apply and use the Resources preset

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
      | data     | Student resources    | Database intro | C1     | data1    |
    And I am on the "Student resources" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I click on "fullname" "radio" in the "Resources" "table_row"
    And I click on "Use this preset" "button"
    And the following "mod_data > entries" exist:
      | database | user     | Title                | Description    | Type  | Author             | Web link                      | Cover      |
      | data1    | student1 | My favourite book    | Book content   | Type1 | The book author    | http://myfavouritebook.cat    | first.png  |
      | data1    | teacher1 | My favourite podcast | Podcast content| Type2 | The podcast author | http://myfavouritepodcast.cat | second.png |

  @javascript
  Scenario: Resources. Users view entries
    When I am on the "Student resources" "data activity" page logged in as student1
    Then I should see "My favourite book"
    And I should see "Type1"
    And I should see "The book author"
    And I should see "http://myfavouritebook.cat"
    And I should not see "Book content"
    And "Actions" "button" should exist in the "#resources-list" "css_element"
    And I should see "My favourite podcast"
    And I should see "Type2"
    And I should see "The podcast author"
    And I should see "http://myfavouritepodcast.cat"
    And I should not see "Podcast content"
    # Single view.
    And I select "Single view" from the "jump" singleselect
    And I should see "My favourite book"
    And I should see "Type1"
    And I should see "The book author"
    And I should see "http://myfavouritebook.cat"
    And I should see "Book content"
    And "Actions" "button" should exist in the ".resources-single" "css_element"
    And I should not see "My favourite podcast"
    And I should not see "Type2"
    And I should not see "The podcast author"
    And I should not see "http://myfavouritepodcast.cat"
    And I should not see "Podcast content"
    And I follow "Next"
    And I should see "My favourite podcast"
    And I should see "Type2"
    And I should see "The podcast author"
    And I should see "http://myfavouritepodcast.cat"
    And I should see "Podcast content"
    # This student can't edit or delete this entry, so the Actions menu shouldn't be displayed.
    And "Actions" "button" should not exist in the ".resources-single" "css_element"
    And I should not see "My favourite book"
    And I should not see "Type1"
    And I should not see "The book author"
    And I should not see "http://myfavouritebook.cat"
    And I should not see "Book content"

  @javascript
  Scenario: Resources. Users can search entries
    Given I am on the "Student resources" "data activity" page logged in as student1
    And "My favourite book" "text" should appear before "My favourite podcast" "text"
    When I click on "Advanced search" "checkbox"
    And I should see "First name"
    And I should see "Last name"
    And I set the field "Title" to "book"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "My favourite book"
    And I should not see "My favourite podcast"
    But I set the field "Title" to "favourite"
    And I set the field "Order" to "Descending"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And "My favourite podcast" "text" should appear before "My favourite book" "text"

  @javascript
  Scenario: Resources. Users can add entries
    Given I am on the "Student resources" "data activity" page logged in as student1
    When I press "Add entry"
    And I set the field "Title" to "This is the title"
    And I set the field "Author" to "This is the author"
    And I set the field "Description" to "This is description."
    And I set the field "Web link" to "https://thisisthelink.cat"
    And I set the field "Type" to "Type2"
    And I press "Save"
    Then I should see "This is the title"
    And I should see "This is the author"
    And I should see "https://thisisthelink.cat"
    And I should see "Type2"

  @javascript
  Scenario: Resources. Renaming a field should affect the template
    Given I am on the "Student resources" "data activity" page logged in as teacher1
    And I navigate to "Fields" in current page administration
    And I open the action menu in "Type" "table_row"
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
  Scenario: Resources. Has otherfields tag
    Given the following "mod_data > fields" exist:
      | database | type | name        | description            |
      | data1    | text | Extra field | Test field description |
    And I am on the "Student resources" "data activity" page logged in as teacher1
    When I select "Single view" from the "jump" singleselect
    Then I should see "Extra field"
    And I click on "Add entry" "button"
    And I should see "Extra field"

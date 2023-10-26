@mod @mod_data @datapreset @datapreset_journal
Feature: Users can use the Journal preset
  In order to create a Journal database
  As a user
  I need to apply and use the Journal preset

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
      | activity | name                   | intro          | course | idnumber |
      | data     | Student reflections    | Database intro | C1     | data1    |
    And I am on the "Student reflections" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I click on "fullname" "radio" in the "Journal" "table_row"
    And I click on "Use this preset" "button"
    And the following "mod_data > entries" exist:
      | database | user      | Title                           | Content                                  |
      | data1    | student1  | Reflection created by student   | This is the content for the entry 1      |
      | data1    | teacher1  | Reflection created by teacher   | And this is the content for the entry 2  |

  @javascript
  Scenario: Journal. Users view entries
    When I am on the "Student reflections" "data activity" page logged in as student1
    Then I should see "Reflection created by student"
    And I should see "This is the content for the entry 1"
    And "Actions" "icon" should exist in the "#journal-list" "css_element"
    And I should see "Reflection created by teacher"
    And I should see "And this is the content for the entry 2"
    # Single view.
    And I select "Single view" from the "jump" singleselect
    And I should see "Reflection created by student"
    And I should see "This is the content for the entry 1"
    And "Actions" "icon" should exist in the ".journal-single" "css_element"
    And I should not see "Reflection created by teacher"
    And I should not see "And this is the content for the entry 2"
    And I follow "Next"
    And I should see "Reflection created by teacher"
    And I should see "And this is the content for the entry 2"
    # This student can't edit or delete this entry, so the Actions menu shouldn't be displayed.
    And "Actions" "icon" should not exist in the ".journal-single" "css_element"
    And I should not see "Reflection created by student"
    And I should not see "This is the content for the entry 1"

  @javascript
  Scenario: Journal. Users can search entries
    Given I am on the "Student reflections" "data activity" page logged in as student1
    And "Reflection created by student" "text" should appear before "Reflection created by teacher" "text"
    When I click on "Advanced search" "checkbox"
    And I should see "First name"
    And I should see "Last name"
    And I set the field "Title" to "student"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "Reflection created by student"
    And I should not see "Reflection created by teacher"
    But I set the field "Title" to "Reflection"
    And I set the field "Order" to "Descending"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And "Reflection created by teacher" "text" should appear before "Reflection created by student" "text"

  @javascript
  Scenario: Journal. Users can add entries
    Given I am on the "Student reflections" "data activity" page logged in as student1
    When I press "Add entry"
    And I set the field "Title" to "This is the title"
    And I set the field "Content" to "This is the content for the new entry."
    And I press "Save"
    Then I should see "This is the title"
    And I should see "This is the content for the new entry."

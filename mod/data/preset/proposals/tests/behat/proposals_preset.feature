@mod @mod_data @datapreset @datapreset_proposals
Feature: Users can use the Proposals preset
  In order to create a Proposals database
  As a user
  I need to apply and use the Proposals preset

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
      | data     | Student projects    | Database intro | C1     | data1    |
    And I am on the "Student projects" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I click on "fullname" "radio" in the "Proposals" "table_row"
    And I click on "Use this preset" "button"
    And the following "mod_data > entries" exist:
      | database | user      | Title                           | Summary    | Content                  | Status    |
      | data1    | student1  | Project created by student      | Summary 1  | Content for entry 1      | Pending   |
      | data1    | teacher1  | Project created by teacher      | Summary 2  | And content for entry 2  | Rejected  |

  @javascript
  Scenario: Proposals. Users view entries
    When I am on the "Student projects" "data activity" page logged in as student1
    Then I should see "Project created by student"
    And "Summary 1" "text" should exist
    And "Actions" "button" should exist in the "#proposals-list" "css_element"
    And I should see "Project created by teacher"
    And "Summary 2" "text" should exist
    And I click on "Project created by student" "link"
    And I click on "Project created by teacher" "link"
    And I should see "Summary 1"
    And I should not see "Content for entry 1"
    And I should see "Summary 2"
    And I should not see "And content for entry 2"
    # Single view.
    And I select "Single view" from the "jump" singleselect
    And I should see "Project created by student"
    And I should see "Summary 1"
    And I should see "Content for entry 1"
    And I should see "Pending"
    And "Actions" "button" should exist in the ".proposals-single" "css_element"
    And I should not see "Project created by teacher"
    And I should not see "Summary 2"
    And I should not see "And content for entry 2"
    And I should not see "Rejected"
    And I follow "Next"
    And I should see "Project created by teacher"
    And I should see "Summary 2"
    And I should see "And content for entry 2"
    And I should see "Rejected"
    # This student can't edit or delete this entry, so the Actions menu shouldn't be displayed.
    And "Actions" "button" should not exist in the ".proposals-single" "css_element"
    And I should not see "Project created by student"
    And I should not see "Summary 1"
    And I should not see "Content for entry 1"
    And I should not see "Pending"

  @javascript
  Scenario: Proposals. Users can search entries
    Given I am on the "Student projects" "data activity" page logged in as student1
    And "Project created by student" "text" should appear before "Project created by teacher" "text"
    When I click on "Advanced search" "checkbox"
    And I should see "First name"
    And I should see "Last name"
    And I set the field "Title" to "student"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "Project created by student"
    And I should not see "Project created by teacher"
    But I set the field "Title" to "Project"
    And I set the field "Order" to "Descending"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And "Project created by teacher" "text" should appear before "Project created by student" "text"

  @javascript
  Scenario: Proposals. Users can add entries
    Given I am on the "Student projects" "data activity" page logged in as student1
    When I press "Add entry"
    And I set the field "Title" to "This is the title"
    And I set the field "Summary" to "This is the summary for the new entry."
    And I set the field "Content" to "This is the content for the new entry."
    And I set the field "Status" to "Approved"
    And I press "Save"
    Then I should see "This is the title"
    And I should see "Approved"
    And I should see "This is the summary for the new entry."
    And I should see "This is the content for the new entry."

  @javascript
  Scenario: Proposals. Renaming a field should affect the template
    Given I am on the "Student projects" "data activity" page logged in as teacher1
    And I navigate to "Fields" in current page administration
    And I open the action menu in "Summary" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Field name" to "Edited field name"
    And I press "Save"
    And I should see "Field updated"
    When I navigate to "Database" in current page administration
    Then I click on "Advanced search" "checkbox"
    And I should see "Edited field name"
    And I select "Single view" from the "jump" singleselect
    And I should see "Edited field name"
    And I click on "Add entry" "button"
    And I should see "Edited field name"

  @javascript
  Scenario: Proposals. Has otherfields tag
    Given the following "mod_data > fields" exist:
      | database | type | name        | description            |
      | data1    | text | Extra field | Test field description |
    And I am on the "Student projects" "data activity" page logged in as teacher1
    When I select "Single view" from the "jump" singleselect
    Then I should see "Extra field"
    And I click on "Add entry" "button"
    And I should see "Extra field"

@mod @mod_data
Feature: Users can edit the database templates
  In order to use custom templates for entries
  As a teacher
  I need to edit the templates html

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro          | course | idnumber |
      | data     | Test database name | Database intro | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field1 | Test field description   |
      | data1    | text | field2 | Test field 2 description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | field1          | field2         |
      | data1    | Student entry 1 | Some content 1 |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I navigate to "Templates" in current page administration

  @javascript
  Scenario: Edit list template
    Given I set the following fields to these values:
      | Header         | New header!                |
      | Repeated entry | [[field1]] and [[field2]]! |
      | Footer         | New footer!                |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    Then I should see "New header!"
    And I should see "Student entry 1 and Some content 1!"
    And I should see "New footer!"

  @javascript
  Scenario: Edit single template
    Given I set the field "Templates tertiary navigation" to "Single template"
    And I set the following fields to these values:
      | Single template | [[field1]] and [[field2]] details! |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    And I set the field "View mode tertiary navigation" to "Single view"
    Then I should see "Student entry 1 and Some content 1 details!"

  @javascript
  Scenario: Edit add entry template
    Given I set the field "Templates tertiary navigation" to "Add entry template"
    And I set the following fields to these values:
      | Add entry template | [[field1]] [[field2]] Form extra! |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    And I click on "Add entry" "button"
    Then I should see "Form extra!"

  @javascript
  Scenario: Edit advanced search template
    Given I set the field "Templates tertiary navigation" to "Advanced search template"
    And I set the following fields to these values:
      | Advanced search template | New advanced search template! |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    And I click on "Advanced search" "checkbox"
    Then I should see "New advanced search template!"

  @javascript
  Scenario: Edit without the wysiwyg editor
    Given I click on "Enable editor" "checkbox"
    And I set the following fields to these values:
      | Repeated entry | <span class="d-none">Nope</span>Yep! |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    Then I should not see "Nope"
    And I should see "Yep!"

  @javascript
  Scenario: Edit CSS teamplate
    Given I click on "Enable editor" "checkbox"
    And I set the following fields to these values:
      | Repeated entry | <span class="hideme">Nope</span>Yep! |
    And I click on "Save template" "button"
    And I set the field "Templates tertiary navigation" to "CSS template"
    And I set the following fields to these values:
      | CSS template | .hideme {display: none;} |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    Then I should not see "Nope"
    And I should see "Yep!"

  @javascript
  Scenario: Edit Javascript template
    Given I click on "Enable editor" "checkbox"
    And I set the following fields to these values:
      | Repeated entry | <span id="hideme">Nope</span>Yep! |
    And I click on "Save template" "button"
    And I set the field "Templates tertiary navigation" to "Javascript template"
    And I set the following fields to these values:
      | Javascript template | window.onload = () => document.querySelector('#hideme').style.display = 'none'; |
    And I click on "Save template" "button"
    When I navigate to "Database" in current page administration
    Then I should not see "Nope"
    And I should see "Yep!"

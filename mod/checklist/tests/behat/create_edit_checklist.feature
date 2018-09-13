@mod @mod_checklist @checklist
Feature: I can create and update a checklist

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Checklist" to section "1" and I fill the form with:
      | Checklist    | Test checklist      |
      | Introduction | This is a checklist |
    And I log out

  Scenario: When I add no items to a checklist a student sees no items
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "Test checklist"
    And I should see "This is a checklist"
    And I should see "No items in the checklist"

  Scenario: When I add some items to a checklist a student should see them
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    And I click on "Insert new item after this one" "link" in the "Another list item" "list_item"
    And I set the field "displaytext" to "Extra inserted item"
    And I press "Add"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "The first list item" "text" should appear before "Another list item" "text"
    And "Another list item" "text" should appear before "Extra inserted item" "text"
    And "Extra inserted item" "text" should appear before "Third list item" "text"
    And I should see "All items"
    And I should not see "Required items"

  Scenario: When I press the 'move down' icon the checklist is correctly reordered
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    And I click on "Move item down" "link" in the "The first list item" "list_item"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "Another list item" "text" should appear before "The first list item" "text"
    And "The first list item" "text" should appear before "Third list item" "text"

  Scenario: When I press the 'move up' icon the checklist is correctly reordered
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    And I click on "Move item up" "link" in the "Third list item" "list_item"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "The first list item" "text" should appear before "Third list item" "text"
    And "Third list item" "text" should appear before "Another list item" "text"

  @javascript
  Scenario: When I press the 'indent' icon the checklist is correctly indented
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    # Indent second item once.
    And I click on "Indent item" "link" in the "Another list item" "list_item"
    # Indent third item twice.
    And I click on "Indent item" "link" in the "Third list item" "list_item"
    And I click on "Indent item" "link" in the "Third list item" "list_item"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    # The following CSS selectors do not work properly if the @javascript tag is removed.
    Then "The first list item" "text" in the "form > ol.checklist > li" "css_element" should be visible
    And "Another list item" "text" in the "form > ol.checklist > ol.checklist > li" "css_element" should be visible
    And "Third list item" "text" in the "form > ol.checklist > ol.checklist > ol.checklist > li" "css_element" should be visible

  Scenario: When I edit a checklist item's text it is saved correctly
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    And I click on "Edit this item" "link" in the "The first list item" "list_item"
    And the field "displaytext" matches value "The first list item"
    And I set the field "displaytext" to "Updated first item"
    And I press "Update"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "Updated first item"
    # Not working as the 'I set the field' step leaves the original text behind.
    #And I should not see "The first list item"

  Scenario: When I delete a checklist item it disappears
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    And I click on "Delete this item" "link" in the "Another list item" "list_item"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "The first list item" "text" should appear before "Third list item" "text"
    And I should not see "Another list item"

  Scenario: When I click on the change colour icons for items they change colour
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "The first list item"
    And I press "Add"
    And I set the field "displaytext" to "Another list item"
    And I press "Add"
    And I set the field "displaytext" to "Third list item"
    And I press "Add"
    And I click on "Next text colour" "link" in the "The first list item" "list_item"
    And I click on "Next text colour" "link" in the "Another list item" "list_item"
    And I click on "Next text colour" "link" in the "Another list item" "list_item"
    And I click on "Next text colour" "link" in the "Third list item" "list_item"
    And I click on "Next text colour" "link" in the "Third list item" "list_item"
    And I click on "Next text colour" "link" in the "Third list item" "list_item"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "label.itemred" "css_element" should appear before "label.itemorange" "css_element"
    And "label.itemorange" "css_element" should appear before "label.itemgreen" "css_element"

  Scenario: When I click on the 'item required' icon the item should toggle between required/optional/heading
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the field "displaytext" to "Heading item"
    And I press "Add"
    And I set the field "displaytext" to "You must tick this"
    And I press "Add"
    And I set the field "displaytext" to "You can tick this"
    And I press "Add"
    And I click on "This item is required" "link" in the "Heading item" "list_item"
    And I click on "This item is optional" "link" in the "Heading item" "list_item"
    And I click on "This item is required" "link" in the "You must tick this" "list_item"
    And I click on "This item is optional" "link" in the "You must tick this" "list_item"
    And I click on "This item is a heading" "link" in the "You must tick this" "list_item"
    And I click on "This item is required" "link" in the "You can tick this" "list_item"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "All items"
    And I should see "Required items"
    And "label.itemheading" "css_element" should appear before "You must tick this" "text"
    And "You must tick this" "text" should appear before "label.itemoptional" "css_element"
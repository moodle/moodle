@core @javascript @core_form
Feature: Autocomplete functionality in forms
  For forms including autocomplete elements
  As a user
  I need to use the autocomplete form element

  Scenario: Use autocomplete element which accepts a single value
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Jane      | Jones    |
      | user2    | Sam       | Smith    |
    And I log in as "admin"

    When I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "New request"
    And I open the autocomplete suggestions list
    And I click on "Jane Jones" item in the autocomplete list

    Then "Jane Jones" "autocomplete_selection" should exist
    # Change selection
    And I open the autocomplete suggestions list
    And I click on "Sam Smith" item in the autocomplete list
    And "Sam Smith" "autocomplete_selection" should exist
    And "Jane Jones" "autocomplete_selection" should not exist
    # Remove selection
    And I click on "Sam Smith" "autocomplete_selection"
    And "Sam Smith" "autocomplete_selection" should not exist
    And I should see "No selection" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Single-select autocomplete can be cleared after being set
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I am on the "autocomplete-disabledif" "core_form > Fixture" page logged in as "admin"
    When I set the field "Controls the rest" to "Course 1"
    And "Course 1" "autocomplete_selection" should be visible
    And I click on "Course 1" "autocomplete_selection"
    Then "frog" "autocomplete_selection" should not exist

  @javascript
  Scenario: Single-select autocomplete can be cleared immediately after page load
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I am on the "autocomplete-disabledif" "core_form > Fixture" page logged in as "admin"
    When I set the field "Controls the rest" to "Course 1"
    And I press "Save changes"
    And "Course 1" "autocomplete_selection" should be visible
    And I click on "Course 1" "autocomplete_selection"
    Then "frog" "autocomplete_selection" should not exist

  @javascript
  Scenario: Autocomplete can control other form fields via disabledIf
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I am on the "autocomplete-disabledif" "core_form > Fixture" page logged in as "admin"
    When I set the field "Controls the rest" to "Course 1"
    Then the "Single select will be enabled if the control is blank" "field" should be disabled
    And the "Single select will be disabled if the control is blank" "field" should be enabled
    And I click on "Course 1" "autocomplete_selection"
    And the "Single select will be enabled if the control is blank" "field" should be enabled
    And the "Single select will be disabled if the control is blank" "field" should be disabled

  @javascript
  Scenario: Single-select autocomplete can be cleared after being set and suggestion list reloaded
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Jane      | Jones    |
      | user2    | Sam       | Smith    |
      | user3    | Mark      | Davis    |
    And I log in as "admin"

    When I navigate to "Server > Web services > Manage tokens" in site administration
    And I press "Create token"
    And I open the autocomplete suggestions list
    And I click on "Jane Jones" item in the autocomplete list
    Then "Jane Jones" "autocomplete_selection" should exist
    # Only reload de sugestion list
    And I open the autocomplete suggestions list
    # Remove selection
    And I click on "Jane Jones" "autocomplete_selection"
    And "Jane Jones" "autocomplete_selection" should not exist
    And I should see "No selection" in the ".form-autocomplete-selection" "css_element"

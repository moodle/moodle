@core @javascript @core_form
Feature: Autocomplete functionality in forms
  For forms including autocomplete elements
  As a user
  I need to use the autocomplete form element

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Jane      | Jones    |
      | user2    | Sam       | Smith    |
    And I log in as "admin"

  Scenario: Use autocomplete element which accepts a single value
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
@core @core_search
Feature: Use global search interface
  In order to search for things
  As a user
  I need to be able to type search queries and see results

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | 1 |
    And the following "activities" exist:
      | activity | name       | intro      | course               | idnumber |
      | page     | PageName1  | PageDesc1  | Acceptance test site | PAGE1    |
      | forum    | ForumName1 | ForumDesc1 | Acceptance test site | FORUM1   |
    And I log in as "admin"

  @javascript
  Scenario: Search from header search box with one result
    Given global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    When I search for "frogs" using the header global search box
    Then I should see "PageName1"
    And I should see "PageDesc1"

    # Check the link works.
    And I follow "PageName1"
    And I should see "PageName1" in the ".breadcrumb" "css_element"

  @javascript
  Scenario: Search from search page with two results
    Given global search expects the query "zombies" and will return:
      | nothing |
    When I search for "zombies" using the header global search box
    Then I should see "No results"
    And I set the field "id_q" to "Toads"
    And global search expects the query "Toads" and will return:
      | type     | idnumber |
      | activity | FORUM1   |
      | activity | PAGE1    |
    # You cannot press "Search" because there's a fieldset with the same name that gets in the way.
    And I press "id_submitbutton"
    And I should see "ForumName1"
    And I should see "ForumDesc1"
    And I should see "PageName1"
    And I should see "PageDesc1"

    # Check the link works.
    And I follow "ForumName1"
    And I should see "ForumName1" in the ".breadcrumb" "css_element"

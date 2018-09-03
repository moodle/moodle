@core @core_search
Feature: Use global search interface
  In order to search for things
  As a user
  I need to be able to type search queries and see results

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | 1 |
      | searchengine | solr |
    And the following "courses" exist:
      | shortname | fullname   |
      | F1        | Amphibians |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | page     | PageName1  | PageDesc1  | F1     | PAGE1    |
      | forum    | ForumName1 | ForumDesc1 | F1     | FORUM1   |
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

  @javascript
  Scenario: Search starting from site context (no within option)
    Given global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    When I search for "frogs" using the header global search box
    And I expand all fieldsets
    Then I should not see "Search within"
    And I should see "Courses" in the "region-main" "region"

  @javascript
  Scenario: Search starting from course context (within option lists course)
    Given global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    When I am on "Amphibians" course homepage
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    Then I should see "Search within"
    And I select "Everywhere you can access" from the "Search within" singleselect
    And I should see "Courses" in the "region-main" "region"
    And I select "Course: Amphibians" from the "Search within" singleselect
    And I should not see "Courses" in the "region-main" "region"

  @javascript
  Scenario: Search starting from forum context (within option lists course and forum)
    Given global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    When I am on "Amphibians" course homepage
    And I follow "ForumName1"
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    And I should see "Search within"
    And I select "Everywhere you can access" from the "Search within" singleselect
    And I should see "Courses" in the "region-main" "region"
    And I select "Course: Amphibians" from the "Search within" singleselect
    And I should not see "Courses" in the "region-main" "region"
    And I select "Forum: ForumName1" from the "Search within" singleselect
    And I should not see "Courses" in the "region-main" "region"

  @javascript
  Scenario: Check that groups option in search form appears when intended
    Given the following "groups" exist:
      | name    | course | idnumber |
      | A Group | F1     | G1       |
      | B Group | F1     | G2       |
    And the following "activities" exist:
      | activity | name    | intro      | course | idnumber | groupmode |
      | forum    | ForumSG | ForumDesc1 | F1     | FORUM2   | 1         |
    When I am on "Amphibians" course homepage
    And I follow "ForumSG"
    And global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    Then I should not see "All groups" in the "region-main" "region"
    And I select "Course: Amphibians" from the "Search within" singleselect
    And I should see "All groups" in the "region-main" "region"
    And I set the field "Groups" to "A Group"
    And I select "Forum: ForumSG" from the "Search within" singleselect
    And I should see "A Group" in the "region-main" "region"
    And I am on "Amphibians" course homepage
    And I follow "ForumName1"
    And global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    Then I should not see "All groups" in the "region-main" "region"
    And I select "Course: Amphibians" from the "Search within" singleselect
    And I should see "All groups" in the "region-main" "region"
    And I select "Forum: ForumName1" from the "Search within" singleselect
    And I should not see "All groups" in the "region-main" "region"

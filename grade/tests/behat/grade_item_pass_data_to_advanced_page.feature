@core @core_grades @javascript @testtt
Feature: We carry over data from modal to advanced grade item settings
  In order to setup grade items quickly
  As an teacher
  I need to ensure data is carried over from modal to advanced grade item settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "grade categories" exist:
      | fullname                 | course |
      | Some cool grade category | C1     |
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    And the following "scales" exist:
      | name       | scale                                     |
      | Test Scale | Disappointing, Good, Very good, Excellent |
    And the following "grade outcomes" exist:
      | fullname  | shortname | course | scale      |
      | Outcome 1 | OT1       | C1     | Test Scale |
      | Outcome 2 | OT2       | C1     | Test Scale |
    And the following "activities" exist:
      | activity | course | idnumber | name               | intro             |
      | assign   | C1     | a1       | Test assignment 1  | Submit something! |
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_aggregations_visible | Mean of grades,Weighted mean of grades,Simple weighted mean of grades,Mean of grades (with extra credits),Median of grades,Lowest grade,Highest grade,Mode of grades,Natural |
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I choose the "Add grade item" item in the "Add" action menu

  Scenario: Defaults are used when creating a new grade item
    Given I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the following fields match these values:
      | Item name         |          |
      | Minimum grade     | 0        |
      | Maximum grade     | 100      |
      | Weight adjusted   | 0        |
      | aggregationcoef2  | 0        |
      | Grade category    | Course 1 |
    And I press "Cancel"
    And I wait until the page is ready
    And I choose the "Add category" item in the "Add" action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And the following fields match these values:
      | Category name                |          |
      | Aggregation                  | Natural  |
      | Weight adjusted              | 0        |
      | grade_item_aggregationcoef2  | 0        |
      | Parent category              | Course 1 |
    And I press "Cancel"
    And I wait until the page is ready
    And I choose the "Add outcome item" item in the "Add" action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And the following fields match these values:
      | Item name         |                          |
      | Outcome           | Outcome 1                |
      | Linked activity   | None                     |
      | Grade category    | Course 1                 |

  Scenario: We carry over data from modal to advanced grade item settings
    Given I set the following fields to these values:
      | Item name         | Manual item 1            |
      | Minimum grade     | 1                        |
      | Maximum grade     | 99                       |
      | Weight adjusted   | 1                        |
      | aggregationcoef2  | 100                      |
      | Grade category    | Some cool grade category |
    When I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the following fields match these values:
      | Item name         | Manual item 1            |
      | Minimum grade     | 1                        |
      | Maximum grade     | 99                       |
      | Weight adjusted   | 1                        |
      | aggregationcoef2  | 100                      |
      | Grade category    | Some cool grade category |
    And I press "Cancel"
    And I wait until the page is ready
    And I choose the "Add category" item in the "Add" action menu
    And I set the following fields to these values:
      | Category name                | Category 1               |
      | Aggregation                  | Mean of grades           |
      | Minimum grade                | 1                        |
      | Maximum grade                | 99                       |
      | Weight adjusted              | 1                        |
      | grade_item_aggregationcoef2  | 100                      |
      | Parent category              | Some cool grade category |
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And the following fields match these values:
      | Category name                | Category 1               |
      | Aggregation                  | Mean of grades           |
      | Minimum grade                | 1                        |
      | Maximum grade                | 99                       |
      | Weight adjusted              | 1                        |
      | grade_item_aggregationcoef2  | 100                      |
      | Parent category              | Some cool grade category |
    And I press "Cancel"
    And I choose the "Add category" item in the "Add" action menu
    # Confirm that the form values are carried over even if some mandatory fields are missing (e.g. Category name).
    And I set the following fields to these values:
      | Aggregation                  | Mean of grades           |
      | Minimum grade                | 1                        |
      | Maximum grade                | 99                       |
      | Weight adjusted              | 1                        |
      | grade_item_aggregationcoef2  | 100                      |
      | Parent category              | Some cool grade category |
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And the following fields match these values:
      | Category name                |                          |
      | Aggregation                  | Mean of grades           |
      | Minimum grade                | 1                        |
      | Maximum grade                | 99                       |
      | Weight adjusted              | 1                        |
      | grade_item_aggregationcoef2  | 100                      |
      | Parent category              | Some cool grade category |
    And I press "Cancel"
    And I wait until the page is ready
    And I choose the "Add outcome item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name         | Outcome item 1           |
      | Outcome           | Outcome 2                |
      | Linked activity   | Test assignment 1        |
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And the following fields match these values:
      | Item name         | Outcome item 1           |
      | Outcome           | Outcome 2                |
      | Linked activity   | Test assignment 1        |

@core @core_search
Feature: Select users when searching for user-created content
  In order to search for content by specific users
  As a user
  I need to be able to add users to the select list in the search form

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | 1    |
      | searchengine       | solr |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Frogs    |
      | C2        | Zombies  |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    |
    And the following "users" exist:
      | username | firstname | lastname   |
      | s1       | Anne      | Other      |
      | s2       | Anne      | Additional |
      | t        | Anne      | Ditin      |
    And the following "course enrolments" exist:
      | user | course | role    |
      | s1   | C1     | student |
      | s2   | C2     | student |
      | t    | C1     | teacher |

  @javascript
  Scenario: As administrator, search for users from home page
    Given I log in as "admin"
    And global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    And I set the field with xpath "//select[@id='id_userids']/..//input[@type='text']" to "Anne"
    # Alphabetical surname order.
    Then "Anne Additional" "text" should appear before "Anne Ditin" "text"
    And "Anne Ditin" "text" should appear before "Anne Other" "text"

  @javascript
  Scenario: As administrator, search for users within course
    Given I log in as "admin"
    And I am on "Frogs" course homepage
    And global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    And I select "Course: Frogs" from the "Search within" singleselect
    And I set the field with xpath "//select[@id='id_userids']/..//input[@type='text']" to "Anne"
    # Users in selected course appear first.
    And "Anne Additional" "text" should appear after "Anne Other" "text"

  @javascript
  Scenario: As student, cannot see users on other courses
    Given I log in as "s1"
    And I am on "Frogs" course homepage
    And global search expects the query "frogs" and will return:
      | type     | idnumber |
      | activity | PAGE1    |
    And I search for "frogs" using the header global search box
    And I expand all fieldsets
    And I set the field with xpath "//select[@id='id_userids']/..//input[@type='text']" to "A"
    Then "Anne Ditin" "text" should appear before "Anne Other" "text"
    And "Anne Additional" "text" should not exist

@mod @mod_wiki
Feature: Users can search wikis
  In order to find information in wiki
  As a user
  I need to be able to search individual and collaborative wikis

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  @javascript
  Scenario: Searching collaborative wiki
    Given the following "activity" exists:
      | activity       | wiki                    |
      | course         | C1                      |
      | name           | Collaborative wiki name |
      | wikimode       | collaborative           |
      | firstpagetitle | First page              |
    And I am on the "Collaborative wiki name" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Collaborative teacher1 page [[new page]] |
    And I press "Save"
    And I am on the "Collaborative wiki name" "wiki activity" page logged in as student1
    And I follow "new page"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | New page created by student1 |
    And I press "Save"
    When I set the field "searchstring" to "page"
    And I press "Search wikis"
    Then I should see "New page created by student1"
    And I should see "Collaborative teacher1 page"
    And I set the field "searchstring" to "teacher1"
    And I press "Search wikis"
    And I should not see "New page created by student1"
    And I should see "Collaborative teacher1 page"
    And I set the field "searchstring" to "teacher1 page"
    And I press "Search wikis"
    And I should not see "New page created by student1"
    And I should see "Collaborative teacher1 page"

  @javascript
  Scenario: Searching individual wiki
    Given the following "activity" exists:
      | activity       | wiki                 |
      | course         | C1                   |
      | name           | Individual wiki name |
      | wikimode       | individual           |
      | firstpagetitle | First page           |
    And I am on the "Individual wiki name" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual teacher1 page |
    And I press "Save"
    And I am on the "Individual wiki name" "wiki activity" page logged in as student1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual student1 page |
    And I press "Save"
    When I set the field "searchstring" to "page"
    And I press "Search wikis"
    Then I should see "Individual student1 page"
    And I should not see "Individual teacher1 page"
    And I am on the "Individual wiki name" "wiki activity" page logged in as student2
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual student2 page |
    And I press "Save"
    And I set the field "searchstring" to "page"
    And I press "Search wikis"
    And I should see "Individual student2 page"
    And I should not see "Individual student1 page"
    And I should not see "Individual teacher1 page"
    And I am on the "Individual wiki name" "wiki activity" page logged in as teacher1
    And I set the field "searchstring" to "page"
    And I press "Search wikis"
    And I should see "Individual teacher1 page"
    And I should not see "Individual student1 page"
    And I should not see "Individual student2 page"
    And I set the field "uid" to "Student 1"
    And I should not see "Individual teacher1 page"
    And I should see "Individual student1 page"
    And I should not see "Individual student2 page"
    And I set the field "uid" to "Student 2"
    And I should not see "Individual teacher1 page"
    And I should not see "Individual student1 page"
    And I should see "Individual student2 page"

  @javascript
  Scenario: Searching group wiki
    Given the following "groups" exist:
      | name   | course | idnumber |
      | Group1 | C1     | G1       |
      | Group2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
    Given the following "activity" exists:
      | activity       | wiki            |
      | course         | C1              |
      | name           | Group wiki name |
      | wikimode       | collaborative   |
      | firstpagetitle | Groups pag      |
      | groupmode      | 1               |
    And I am on the "Group wiki name" "wiki activity" page logged in as teacher1
    And I set the field "Group" to "All participants"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | All participants teacher1 page |
    And I press "Save"
    And I set the field "group" to "Group1"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Group1 teacher1 page [[new page1]] |
    And I press "Save"
    And I set the field "group" to "Group2"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Group2 teacher1 page [[new page2]] |
    And I press "Save"
    And I am on the "Group wiki name" "wiki activity" page logged in as student1
    And I follow "new page1"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Group1 student1 new page |
    And I press "Save"
    When I set the field "searchstring" to "page"
    And I press "Search wikis"
    Then I should see "Group1 teacher1 page"
    And I should not see "Group2 teacher1 page"
    And I should see "Group1 student1 new page"
    And I should not see "All participants teacher1 page"
    And I am on the "Group wiki name" "wiki activity" page logged in as student2
    And I follow "new page2"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Group2 student2 new page |
    And I press "Save"
    And I set the field "searchstring" to "page"
    And I press "Search wikis"
    And I should not see "Group1 teacher1 page"
    And I should see "Group2 teacher1 page"
    And I should not see "Group1 student1 new page"
    And I should not see "All participants teacher1 page"
    And I should see "Group2 student2 new page"

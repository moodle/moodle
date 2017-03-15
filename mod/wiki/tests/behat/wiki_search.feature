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
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Collaborative wiki name |
      | Description | Collaborative wiki description |
      | First page name | Collaborative index |
      | Wiki mode | Collaborative wiki |
    And I follow "Collaborative wiki name"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Collaborative teacher1 page [[new page]] |
    And I press "Save"
    And I am on "Course 1" course homepage
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Collaborative wiki name"
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
    And I log out

  @javascript
  Scenario: Searching individual wiki
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Individual wiki name |
      | Description | Individual wiki description |
      | First page name | Individual index |
      | Wiki mode | Individual wiki |
    And I follow "Individual wiki name"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual teacher1 page |
    And I press "Save"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Individual wiki name"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual student1 page |
    And I press "Save"
    When I set the field "searchstring" to "page"
    And I press "Search wikis"
    Then I should see "Individual student1 page"
    And I should not see "Individual teacher1 page"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Individual wiki name"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual student2 page |
    And I press "Save"
    And I set the field "searchstring" to "page"
    And I press "Search wikis"
    And I should see "Individual student2 page"
    And I should not see "Individual student1 page"
    And I should not see "Individual teacher1 page"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Individual wiki name"
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
    And I log out

  @javascript
  Scenario: Searching group wiki
    Given the following "groups" exist:
      | name | course | idnumber |
      | Group1 | C1 | G1 |
      | Group2 | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G2 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Group wiki name |
      | Description | Wiki description |
      | First page name | Groups index |
      | Wiki mode | Collaborative wiki |
      | Group mode | Separate groups |
    And I follow "Group wiki name"
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
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Group wiki name"
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
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Group wiki name"
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
    And I should see "Group2 student2 new page"
    And I should not see "All participants teacher1 page"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Group wiki name"

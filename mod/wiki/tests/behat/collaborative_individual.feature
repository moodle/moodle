@mod @mod_wiki
Feature: A teacher can set a wiki to be collaborative or individual
  In order to allow both collaborative wikis and individual journals with history register
  As a teacher
  I need to select whether the wiki is collaborative or individual

  @javascript
  Scenario: Collaborative and individual wikis
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
    And the following "activity" exists:
      | course         | C1                      |
      | activity       | wiki                    |
      | name           | Collaborative wiki name |
      | firstpagetitle | Collaborative index     |
      | wikimode       | collaborative           |
    And the following "activity" exists:
      | course         | C1                      |
      | activity       | wiki                    |
      | name           | Individual wiki name |
      | firstpagetitle | Individual index     |
      | wikimode       | individual           |
    And I am on the "Collaborative wiki name" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Collaborative teacher1 edition |
    And I press "Save"
    And I am on the "Individual wiki name" "wiki activity" page
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual teacher1 edition |
    And I press "Save"
    And I log out
    And I am on the "Collaborative wiki name" "wiki activity" page logged in as student1
    Then I should see "Collaborative teacher1 edition"
    And I select "Edit" from the "jump" singleselect
    And I set the following fields to these values:
      | HTML format | Collaborative student1 edition |
    And I press "Save"
    And I should not see "Collaborative teacher1 edition"
    And I should see "Collaborative student1 edition"
    And I am on the "Individual wiki name" "wiki activity" page
    And I should not see "Individual teacher1 edition"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual student1 edition |
    And I press "Save"
    And I log out
    And I am on the "Individual wiki name" "wiki activity" page logged in as student2
    And I should not see "Individual teacher1 edition"
    And I should not see "Individual student1 edition"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Individual student2 edition |
    And I press "Save"
    And I log out
    And I am on the "Collaborative wiki name" "wiki activity" page logged in as teacher1
    And I should see "Collaborative student1 edition"
    And I am on the "Individual wiki name" "wiki activity" page
    And I should see "Individual teacher1 edition"
    And I should not see "Individual student1 edition"
    And I set the field "uid" to "Student 1"
    And I should see "Individual student1 edition"
    And I should not see "Individual teacher1 edition"
    And I set the field "uid" to "Student 2"
    And I should see "Individual student2 edition"
    And I should not see "Individual teacher1 edition"

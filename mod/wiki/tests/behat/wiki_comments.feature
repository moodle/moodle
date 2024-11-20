@mod @mod_wiki
Feature: Users can comment on wiki pages
  In order to discuss wiki pages
  As a user
  I need to be able to comment on wiki pages as well as editing and deleting comments

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
    And the following "activity" exists:
      | activity       | wiki                  |
      | course         | C1                    |
      | name           | Test wiki name        |
      | firstpagetitle | First page            |
      | wikimode       | collaborative         |
    And I am on the "Test wiki name" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | First edition |
    And I press "Save"
    And I am on the "Test wiki name" "wiki activity" page logged in as student1
    And I select "Comments" from the "jump" singleselect
    And I follow "Add comment"
    And I set the following fields to these values:
      | Comment | student 1 original comment |
    And I press "Save"
    And I wait to be redirected

  @javascript
  Scenario: Student can edit and delete their own comment
    When I click on "Edit" "link" in the "wiki-comments" "table"
    And I set the following fields to these values:
      | Comment | student 1 updated comment |
    And I press "Save"
    Then I should see "student 1 updated comment"
    And "Edit" "link" should exist in the "wiki-comments" "table"
    And "Delete" "link" should exist in the "wiki-comments" "table"
    And I click on "Delete" "link" in the "wiki-comments" "table"
    And I press "Continue"
    And I should not see "student 1 updated comment"

  @javascript
  Scenario: Student cannot edit another student's comment
    When I am on the "Test wiki name" "wiki activity" page logged in as student2
    And I select "Comments" from the "jump" singleselect
    Then "Edit" "link" should not exist in the "wiki-comments" "table"
    And "Delete" "link" should not exist in the "wiki-comments" "table"

  @javascript
  Scenario: Teacher can delete a student comment
    When I am on the "Test wiki name" "wiki activity" page logged in as teacher1
    And I select "Comments" from the "jump" singleselect
    Then "Edit" "link" should not exist in the "wiki-comments" "table"
    And "Delete" "link" should exist in the "wiki-comments" "table"

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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Test wiki name |
      | Description | Test wiki description |
      | First page name | First page |
      | Wiki mode | Collaborative wiki |
    And I follow "Test wiki name"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | First edition |
    And I press "Save"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test wiki name"
    And I follow "Comments"
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
    And I press "Yes"
    And I should not see "student 1 updated comment"

  @javascript
  Scenario: Student cannot edit another student's comment
    When I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test wiki name"
    And I follow "Comments"
    Then "Edit" "link" should not exist in the "wiki-comments" "table"
    And "Delete" "link" should not exist in the "wiki-comments" "table"

  @javascript
  Scenario: Teacher can delete a student comment
    When I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test wiki name"
    And I follow "Comments"
    Then "Edit" "link" should not exist in the "wiki-comments" "table"
    And "Delete" "link" should exist in the "wiki-comments" "table"

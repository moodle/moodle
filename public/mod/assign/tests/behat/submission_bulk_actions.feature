@mod @mod_assign
Feature: In an assignment, teachers can perform bulk actions on submissions
  In order to manage submissions in bulk
  As a teacher
  I need the appropriate options available in the sticky footer

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
      | manager1  | Manager    | 1         | manager1@example.com  |
    And the following "role assigns" exist:
      | user      | role          | contextlevel | reference |
      | manager1  | coursecreator | system       |           |
    And the following "role capability" exists:
      | role                           | manager |
      | mod/assign:editothersubmission | allow   |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |
      | manager1  | C1      | manager         |

  @javascript
  Scenario Outline: Appropriate bulk actions should be available when there are submissions.
    Given the following "activity" exists:
      | activity                            | assign               |
      | course                              | C1                   |
      | name                                | Test assignment name |
      | assignsubmission_onlinetext_enabled | 1                    |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                       |
      | Test assignment name  | student1  | I'm the student first submission |
    And I am on the "Test assignment name" Activity page logged in as <user>
    And I change window size to "large"
    When I navigate to "Submissions" in current page administration
    And I click on "Select all" "checkbox"
    Then I should <download_visibility> "Download" in the "sticky-footer" "region"
    And I should <lock_visibility> "Lock" in the "sticky-footer" "region"
    And I should <unlock_visibility> "Unlock" in the "sticky-footer" "region"
    And I should <delete_visibility> "Delete" in the "sticky-footer" "region"

    Examples:
      | user     | download_visibility  | lock_visibility  | unlock_visibility  | delete_visibility  |
      | manager1 | see                  | see              | see                | see                |
      | teacher1 | see                  | see              | see                | not see            |

  @javascript
  Scenario Outline: Appropriate bulk actions should be available if no submissions have been made.
    Given the following "activity" exists:
      | activity                            | assign               |
      | course                              | C1                   |
      | name                                | Test assignment name |
      | assignsubmission_onlinetext_enabled | <onlinetext_enabled> |
      | assignsubmission_file_enabled       | <file_enabled>       |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    When I navigate to "Submissions" in current page administration
    And I click on "Select all" "checkbox" in the "#submissions" "css_element"
    Then I should <download_visibility> "Download" in the "sticky-footer" "region"
    And I should <lock_visibility> "Lock" in the "sticky-footer" "region"
    And I should <unlock_visibility> "Unlock" in the "sticky-footer" "region"
    And I should <delete_visibility> "Delete" in the "sticky-footer" "region"

    Examples:
      | onlinetext_enabled | file_enabled | download_visibility  | lock_visibility  | unlock_visibility  | delete_visibility  |
      | 1                  | 0            | not see              | see              | see                | not see            |
      | 0                  | 0            | not see              | not see          | not see            | not see            |

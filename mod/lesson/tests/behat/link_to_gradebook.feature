@mod @mod_lesson
Feature: link to gradebook on the end of lesson page
  In order to allow students to see their lesson grades
  As a teacher
  I need to provide a link to gradebook on the end of lesson page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name        | course | idnumber    |
      | lesson     | Test lesson | C1     | lesson1     |
    And I am on the "Test lesson" "lesson activity" page logged in as teacher1
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | Next page |
      | id_jumpto_1 | Next page |
    And I press "Save page"

  Scenario: Link to gradebook for non practice lesson
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should see "View grades"
    And I follow "View grades"
    And I should see "User report" in the "page-header" "region"
    And I should see "Student 1" in the "region-main" "region"
    And I should see "Test lesson"

  Scenario: No link to gradebook for non graded lesson
    Given I am on the "Test lesson" "lesson activity editing" page
    And I set the following fields to these values:
        | Type | None |
    And I press "Save and display"
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

  Scenario: No link to gradebook for practice lesson
    Given I am on the "Test lesson" "lesson activity editing" page
    And I set the following fields to these values:
        | Practice lesson | Yes |
    And I press "Save and display"
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

  Scenario: No link if Show gradebook to student disabled
    Given I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Show gradebook to students | No |
    And I press "Save and display"
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

  Scenario: No link to gradebook if no gradereport/user:view capability
    Given the following "role capability" exists:
      | role                  | student |
      | gradereport/user:view | prevent |
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

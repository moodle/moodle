@core @core_courseformat
Feature: Validate some section editing has a non-ajax alternative
  In order to edit the course sections faster
  As a teacher
  I need to be able use some edit tools without ajax.

  Background:
    Given the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | numsections  | 3        |
      | initsections | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Section settings can be accessed without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Edit settings" "link" in the "Section 1" "core_courseformat > Section actions menu"
    Then I should see "Section name"
    And I set the field "Section name" to "New name"
    And I press "Save changes"
    And I should see "New name"

  Scenario: Hide and show a section can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should not see "Show" in the "Section 1" "core_courseformat > Section actions menu"
    When I click on "Hide" "link" in the "Section 1" "core_courseformat > Section actions menu"
    Then I should see "Show" in the "Section 1" "core_courseformat > Section actions menu"
    And I should not see "Hide" in the "Section 1" "core_courseformat > Section actions menu"
    And I click on "Show" "link" in the "Section 1" "core_courseformat > Section actions menu"
    And I should not see "Show" in the "Section 1" "core_courseformat > Section actions menu"
    And I should see "Hide" in the "Section 1" "core_courseformat > Section actions menu"

  Scenario: Delete a section can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Delete" "link" in the "Section 1" "core_courseformat > Section actions menu"
    Then I should see "Delete section?"
    And I should see "This will delete Section 1 and all the activities it contains."
    And I click on "Delete" "button"
    And I should not see "Section 1"

  Scenario: Adding a section at the end of the course can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Add section" "link" in the "course-addsection" "region"
    Then I should see "New section" in the "New section" "section"
    And "Section 3" "text" should appear before "New section" "text"

  Scenario: Adding a section between sections can be done without ajax
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Add section" "link" in the "Section 2" "section"
    Then I should see "New section" in the "New section" "section"
    And "Section 2" "text" should appear before "New section" "text"
    And "New section" "text" should appear before "Section 3" "text"

@mod @mod_subsection
Feature: Subsections visibility badges
  In order to use subsections
  As an teacher
  I need to see edit visibility from the section card badges when possible.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 2           | 1            |
    And the following "activity" exists:
      | activity | subsection  |
      | name     | Subsection1 |
      | course   | C1          |
      | idnumber | subsection1 |
      | section  | 1           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"

  @javascript
  Scenario: Hide/Show subsection badge on the course page
    Given I am on "Course 1" course homepage with editing mode on
    And I hide section "Subsection1"
    And I should see "Hidden from students" in the "Subsection1" "core_courseformat > Section visibility"
    When I click on "Hidden from students" "button" in the "Subsection1" "core_courseformat > Section visibility"
    And I click on "Show on course page" "link" in the "Subsection1" "core_courseformat > Section visibility"
    Then I should not see "Hidden from students" in the "Subsection1" "activity"

  @javascript
  Scenario: Hide/Show subsection badge on the parent section page
    Given I am on "Course 1" course homepage with editing mode on
    And I hide section "Subsection1"
    When I am on the "Course 1 > Section 1" "course > section" page
    And I should see "Hidden from students" in the "Subsection1" "core_courseformat > Section visibility"
    And I click on "Hidden from students" "button" in the "Subsection1" "core_courseformat > Section visibility"
    And I click on "Show on course page" "link" in the "Subsection1" "core_courseformat > Section visibility"
    Then I should not see "Hidden from students" in the "Subsection1" "activity"

  @javascript
  Scenario: Hide/Show subsection badge on the subsection page
    Given I am on "Course 1" course homepage with editing mode on
    And I hide section "Subsection1"
    When I am on the "Course 1 > Subsection1" "course > section" page
    And I should see "Hidden from students" in the "Subsection1" "core_courseformat > Section visibility"
    And I click on "Hidden from students" "button" in the "Subsection1" "core_courseformat > Section visibility"
    And I click on "Show on course page" "link" in the "Subsection1" "core_courseformat > Section visibility"
    Then I should not see "Hidden from students"

  @javascript
  Scenario: Subsection visibility badge is not editable when parent section is hidden
    Given I am on "Course 1" course homepage with editing mode on
    When I hide section "Section 1"
    And I should see "Hidden from students" in the "Section 1" "core_courseformat > Section visibility"
    And I should see "Hidden from students" in the "Subsection1" "activity"
    Then "Hidden from students" "button" should not exist in the "Subsection1" "activity"
    And I am on the "Course 1 > Section 1" "course > section" page
    And I should see "Hidden from students" in the "Subsection1" "activity"
    And "Hidden from students" "button" should not exist in the "Subsection1" "activity"
    And I am on the "Course 1 > Subsection1" "course > section" page
    And I should see "Hidden from students"
    And "Hidden from students" "button" should not exist

@mod @mod_subsection
Feature: Subsection visibility should work as a module
  In order to hide and show subsections
  As a teacher
  I need subsections to behave as any other module

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | numsections  | 2        |
      | initsections | 1        |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Subsections created on a hidden section behave like a normal module when parent visibility is toggled
    Given I should not see "Hidden from student"
    When I hide section "Section 1"
    And I should see "Hidden from students" in the "Section 1" "section"
    # We cannot use generators because they don't check the parent section visibility.
    And I add a subsection activity to course "Course 1" section "1"
    And I set the field "Name" to "Subsection 1"
    And I press "Save and return to course"
    And I add a assign activity to course "Course 1" section "1" and I fill the form with:
      | Assignment name                     | Hidden assignment name        |
      | ID number                           | assign1                       |
      | Description                         | Hidden assignment description |
      | assignsubmission_onlinetext_enabled | 1                             |
    Then I should see "Hidden from students" in the "Subsection 1" "section"
    And I should see "Hidden from students" in the "Hidden assignment name" "activity"
    And I show section "Section 1"
    And I should see "Hidden from students" in the "Subsection 1" "section"
    And I should see "Hidden from students" in the "Hidden assignment name" "activity"

  @javascript
  Scenario: Subsections created on a visible section behave like a normal module when parent visibility is toggled
    Given I should not see "Hidden from student"
    And the following "activities" exist:
      | activity   | name         | course | idnumber    | section |
      | subsection | Subsection 1 | C1     | subsection1 | 1       |
      | page       | Page 1       | C1     | page1       | 1       |
    When I am on "Course 1" course homepage
    And I should not see "Hidden from students" in the "Subsection 1" "section"
    And I should not see "Hidden from students" in the "Page 1" "activity"
    Then I hide section "Section 1"
    And I should see "Hidden from students" in the "Subsection 1" "section"
    And I should see "Hidden from students" in the "Page 1" "activity"
    And I show section "Section 1"
    And I should not see "Hidden from students" in the "Subsection 1" "section"
    And I should not see "Hidden from students" in the "Page 1" "activity"

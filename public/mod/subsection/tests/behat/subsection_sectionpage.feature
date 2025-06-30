@mod @mod_subsection
Feature: Teachers create and destroy subsections on section page
  In order to use subsections on section page
  As an teacher
  I need to create and destroy subsections

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | numsections |
      | Course 1 | C1 | 0 | 2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"

  Scenario: Activities can be created in a subsection
    Given the following "activities" exist:
      | activity   | name        | course | idnumber | section |
      | subsection | Subsection1 | C1     | forum1   | 1       |
    When I add an "assign" activity to course "Course 1" section "3" and I fill the form with:
      | Assignment name | Test assignment name        |
      | ID number       | Test assignment name        |
      | Description     | Test assignment description |
    And I am on "Course 1" course homepage
    And I click on "Subsection1" "link" in the "region-main" "region"
    Then I should see "Test assignment name" in the "region-main" "region"

  @javascript
  Scenario: Teacher can create activities in a subsection page with the activity chooser
    Given the following "activities" exist:
      | activity   | name         | course | idnumber | section |
      | subsection | Subsection1  | C1     | forum1   | 1       |
    When I am on the "C1 > Subsection1" "course > section" page
    And I turn editing mode on
    And I click on "Add an activity or resource" "button"
    And I click on "Add a new Assignment" "link"
    And I click on "Add selected activity" "button" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
      | Assignment name | Test assignment name        |
      | ID number       | Test assignment name        |
      | Description     | Test assignment description |
    And I press "Save and return to course"
    Then I should see "Test assignment name" in the "region-main" "region"

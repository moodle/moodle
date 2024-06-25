@mod @mod_subsection
Feature: Teachers create and destroy subsections
  In order to use subsections
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

  Scenario: Subsections are not listed as regular sections
    Given the following "activities" exist:
      | activity   | name         | course | idnumber | section |
      | subsection | Subsection1  | C1     | forum1   | 1       |
      | data       | Subactivity1 | C1     | data1    | 3       |
    When I am on "Course 1" course homepage
    Then "Subsection1" "section" should not exist
    And I should not see "Subactivity1" in the "region-main" "region"
    And I click on "Subsection1" "link" in the "region-main" "region"
    And I should see "Subsection1" in the "page" "region"
    And I should see "Subactivity1" in the "region-main" "region"

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
    And I am on "Course 1" course homepage
    And I should not see "Test assignment name" in the "region-main" "region"

  @javascript
  Scenario: Teacher can create activities in a subsection page with the activity chooser
    Given the following "activities" exist:
      | activity   | name         | course | idnumber | section |
      | subsection | Subsection1  | C1     | forum1   | 1       |
    When I am on "Course 1" course homepage with editing mode on
    And I click on "Subsection1" "link" in the "region-main" "region"
    And I add a "Assignment" to section "3" using the activity chooser
    And I set the following fields to these values:
      | Assignment name | Test assignment name        |
      | ID number       | Test assignment name        |
      | Description     | Test assignment description |
    And I press "Save and return to course"
    Then I should see "Test assignment name" in the "region-main" "region"
    And I am on "Course 1" course homepage
    And I should not see "Test assignment name" in the "region-main" "region"

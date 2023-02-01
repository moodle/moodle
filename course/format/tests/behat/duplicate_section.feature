@core @core_course @core_courseformat
Feature: Duplicate a section
  In order to set up my course contents quickly
  As a teacher
  I need to duplicate sections inside the same course

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 4        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | book     | Activity sample 2 | Test book description       | C1     | sample2  | 1       |
      | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 2       |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Duplicate section
    Given I open section "1" edit menu
    When I click on "Duplicate" "link" in the "Topic 1" "section"
    Then I should see "Activity sample 2" in the "Topic 2" "section"

  @javascript
  Scenario: Duplicate a named section
    Given I set the field "Edit topic name" in the "Topic 1" "section" to "New name"
    And I should see "New name" in the "New name" "section"
    When I open section "1" edit menu
    And I click on "Duplicate" "link" in the "New name" "section"
    Then I should see "Activity sample 2" in the "New name (copy)" "section"

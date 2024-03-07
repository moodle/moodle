@core @core_course @core_courseformat
Feature: Duplicate a section
  In order to set up my course contents quickly
  As a teacher
  I need to duplicate sections inside the same course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion | numsections | initsections |
      | Course 1 | C1        | 0        | 1                | 1           | 0            |
      | Course 2 | C2        | 0        | 1                | 4           | 1            |
    And the following "activities" exist:
      | activity | name                | intro                       | course | idnumber | section |
      | assign   | Activity sample 1.1 | Test assignment description | C1     | sample11 | 1       |
      | book     | Activity sample 1.2 | Test book description       | C1     | sample12 | 1       |
      | assign   | Activity sample 2.1 | Test assignment description | C2     | sample21 | 1       |
      | book     | Activity sample 2.2 | Test book description       | C2     | sample22 | 1       |
      | choice   | Activity sample 2.3 | Test choice description     | C2     | sample23 | 2       |
    And I log in as "admin"

  @javascript
  Scenario: Duplicate unnamed section
    Given the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Section links" block
    When I open section "1" edit menu
    And I click on "Duplicate" "link" in the "New section" "section"
    # As the section names are the same, the only way to access the section is through the section links block.
    And I click on "2" "link" in the "Section links" "block"
    Then I should see "Activity sample 1.2"
    And I should see "New section"

  @javascript
  Scenario: Duplicate a named section
    Given I am on "Course 2" course homepage with editing mode on
    And I set the field "Edit section name" in the "Section 1" "section" to "New name"
    And I should see "New name" in the "New name" "section"
    When I open section "1" edit menu
    And I click on "Duplicate" "link" in the "New name" "section"
    Then I should see "Activity sample 2.2" in the "New name (copy)" "section"

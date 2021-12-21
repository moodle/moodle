@format @format_weeks
Feature: Sections can be edited and deleted in weeks format
  In order to rearrange my course contents
  As a teacher
  I need to edit and Delete weeks

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | startdate |
      | Course 1 | C1        | weeks  | 0             | 5           | 957139200 |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    | section |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     | 0       |
      | book       | Test book name         | Test book description         | C1     | book1       | 1       |
      | chat       | Test chat name         | Test chat description         | C1     | chat1       | 4       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     | 5       |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: View the default name of the general section in weeks format
    When I edit the section "0"
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "General"

  Scenario: Edit the default name of the general section in weeks format
    When I edit the section "0" and I fill the form with:
      | Custom | 1                      |
      | New value for Section name      | This is the general section |
    Then I should see "This is the general section" in the "This is the general section" "section"

  Scenario: View the default name of the second section in weeks format
    When I edit the section "2"
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "8 May - 14 May"

  Scenario: Edit section summary in weeks format
    When I edit the section "2" and I fill the form with:
      | Summary | Welcome to section 2 |
    Then I should see "Welcome to section 2" in the "8 May - 14 May" "section"

  Scenario: Edit section default name in weeks format
    Given I should see "8 May - 14 May" in the "8 May - 14 May" "section"
    When I edit the section "2" and I fill the form with:
      | Custom | 1                  |
      | New value for Section name      | This is the second week |
    Then I should see "This is the second week" in the "This is the second week" "section"
    And I should not see "8 May - 14 May"

  @javascript
  Scenario: Inline edit section name in weeks format
    When I set the field "Edit week name" in the "1 May - 7 May" "section" to "Midterm evaluation"
    Then I should not see "1 May - 7 May" in the "region-main" "region"
    And "New name for week" "field" should not exist
    And I should see "Midterm evaluation" in the "Midterm evaluation" "section"
    And I am on "Course 1" course homepage
    And I should not see "1 May - 7 May" in the "region-main" "region"
    And I should see "Midterm evaluation" in the "Midterm evaluation" "section"

  Scenario: Deleting the last section in weeks format
    Given I should see "29 May - 4 June" in the "29 May - 4 June" "section"
    When I delete section "5"
    Then I should see "Are you absolutely sure you want to completely delete \"29 May - 4 June\" and all the activities it contains?"
    And I press "Delete"
    And I should not see "29 May - 4 June"
    And I should see "22 May - 28 May"

  Scenario: Deleting the middle section in weeks format
    Given I should see "29 May - 4 June" in the "29 May - 4 June" "section"
    When I delete section "4"
    And I press "Delete"
    Then I should not see "29 May - 4 June"
    And I should not see "Test chat name"
    And I should see "Test choice name" in the "22 May - 28 May" "section"
    And I should see "22 May - 28 May"

  @javascript
  Scenario: Adding sections in weeks format
    When I follow "Add week"
    Then I should see "5 June - 11 June" in the "5 June - 11 June" "section"

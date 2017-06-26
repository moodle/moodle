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
    When I click on "Edit section" "link" in the "li#section-0" "css_element"
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "General"

  Scenario: Edit the default name of the general section in weeks format
    When I click on "Edit section" "link" in the "li#section-0" "css_element"
    And I set the following fields to these values:
      | Custom | 1                      |
      | New value for Section name      | This is the general section |
    And I press "Save changes"
    Then I should see "This is the general section" in the "li#section-0" "css_element"

  Scenario: View the default name of the second section in weeks format
    When I click on "Edit week" "link" in the "li#section-2" "css_element"
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "8 May - 14 May"

  Scenario: Edit section summary in weeks format
    When I click on "Edit week" "link" in the "li#section-2" "css_element"
    And I set the following fields to these values:
      | Summary | Welcome to section 2 |
    And I press "Save changes"
    Then I should see "Welcome to section 2" in the "li#section-2" "css_element"

  Scenario: Edit section default name in weeks format
    Given I should see "8 May - 14 May" in the "li#section-2" "css_element"
    When I click on "Edit week" "link" in the "li#section-2" "css_element"
    And I set the following fields to these values:
      | Custom | 1                  |
      | New value for Section name      | This is the second week |
    And I press "Save changes"
    Then I should see "This is the second week" in the "li#section-2" "css_element"
    And I should not see "8 May - 14 May" in the "li#section-2" "css_element"

  @javascript
  Scenario: Inline edit section name in weeks format
    When I click on "Edit week name" "link" in the "li#section-1" "css_element"
    And I set the field "New name for week 1 May - 7 May" to "Midterm evaluation"
    And I press key "13" in the field "New name for week 1 May - 7 May"
    Then I should not see "1 May - 7 May" in the "region-main" "region"
    And "New name for week" "field" should not exist
    And I should see "Midterm evaluation" in the "li#section-1" "css_element"
    And I am on "Course 1" course homepage
    And I should not see "1 May - 7 May" in the "region-main" "region"
    And I should see "Midterm evaluation" in the "li#section-1" "css_element"

  Scenario: Deleting the last section in weeks format
    Given I should see "29 May - 4 June" in the "li#section-5" "css_element"
    When I delete section "5"
    Then I should see "Are you absolutely sure you want to completely delete \"29 May - 4 June\" and all the activities it contains?"
    And I press "Delete"
    And I should not see "29 May - 4 June"
    And I should see "22 May - 28 May"

  Scenario: Deleting the middle section in weeks format
    Given I should see "29 May - 4 June" in the "li#section-5" "css_element"
    When I delete section "4"
    And I press "Delete"
    Then I should not see "29 May - 4 June"
    And I should not see "Test chat name"
    And I should see "Test choice name" in the "li#section-4" "css_element"
    And I should see "22 May - 28 May"

  @javascript
  Scenario: Adding sections in weeks format
    When I follow "Add weeks"
    Then the field "Number of sections" matches value "1"
    And I press "Add weeks"
    And I should see "5 June - 11 June" in the "li#section-6" "css_element"
    And "li#section-7" "css_element" should not exist
    And I follow "Add weeks"
    And I set the field "Number of sections" to "3"
    And I press "Add weeks"
    And I should see "12 June - 18 June" in the "li#section-7" "css_element"
    And I should see "19 June - 25 June" in the "li#section-8" "css_element"
    And I should see "26 June - 2 July" in the "li#section-9" "css_element"
    And "li#section-10" "css_element" should not exist

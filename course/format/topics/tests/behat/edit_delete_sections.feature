@format @format_topics
Feature: Sections can be edited and deleted in topics format
  In order to rearrange my course contents
  As a teacher
  I need to edit and Delete topics

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1        | topics | 0             | 5           |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    | section |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     | 0       |
      | book       | Test book name         |                               | C1     | book1       | 1       |
      | chat       | Test chat name         | Test chat description         | C1     | chat1       | 4       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     | 5       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: View the default name of the general section in topics format
    When I edit the section "0"
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "General"

  Scenario: Edit the default name of the general section in topics format
    Given I should see "General" in the "General" "section"
    When I edit the section "0" and I fill the form with:
      | Custom | 1                     |
      | New value for Section name      | This is the general section |
    Then I should see "This is the general section" in the "This is the general section" "section"

  Scenario: View the default name of the second section in topics format
    When I edit the section "2"
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "Topic 2"

  Scenario: Edit section summary in topics format
    When I edit the section "2" and I fill the form with:
      | Summary | Welcome to section 2 |
    Then I should see "Welcome to section 2" in the "Topic 2" "section"

  Scenario: Edit section default name in topics format
    When I edit the section "2" and I fill the form with:
      | Custom | 1                      |
      | New value for Section name      | This is the second topic |
    Then I should see "This is the second topic" in the "This is the second topic" "section"
    And I should not see "Topic 2" in the "region-main" "region"

  @javascript
  Scenario: Inline edit section name in topics format
    When I set the field "Edit topic name" in the "Topic 1" "section" to "Midterm evaluation"
    Then I should not see "Topic 1" in the "region-main" "region"
    And "New name for topic" "field" should not exist
    And I should see "Midterm evaluation" in the "Midterm evaluation" "section"
    And I am on "Course 1" course homepage
    And I should not see "Topic 1" in the "region-main" "region"
    And I should see "Midterm evaluation" in the "Midterm evaluation" "section"

  Scenario: Deleting the last section in topics format
    When I delete section "5"
    Then I should see "Are you absolutely sure you want to completely delete \"Topic 5\" and all the activities it contains?"
    And I press "Delete"
    And I should not see "Topic 5"
    And I should see "Topic 4"

  Scenario: Deleting the middle section in topics format
    When I delete section "4"
    And I press "Delete"
    Then I should not see "Topic 5"
    And I should not see "Test chat name"
    And I should see "Test choice name" in the "Topic 4" "section"
    And I should see "Topic 4"

  @javascript
  Scenario: Adding sections at the end of a topics format
    When I click on "Add topic" "link" in the "Topic 5" "section"
    Then I should see "Topic 6" in the "Topic 6" "section"
    And I should see "Test choice name" in the "Topic 5" "section"

  @javascript
  Scenario: Adding sections between topics in topics format
    When I click on "Add topic" "link" in the "Topic 4" "section"
    Then I should see "Topic 6" in the "Topic 6" "section"
    And I should not see "Test choice name" in the "Topic 5" "section"
    And I should see "Test choice name" in the "Topic 6" "section"

  @javascript
  Scenario: Add a topic and then add an activity in it
    When I click on "Add topic" "link" in the "Topic 5" "section"
    And I add a "Assignment" to section "6" and I fill the form with:
      | Assignment name | Very new activity |
      | Description     | Test              |
    Then I should see "Very new activity" in the "Topic 6" "section"

  @javascript
  Scenario: Copy section permalink URL to clipboard
    When I open section "1" edit menu
    And I click on "Permalink" "link" in the "Topic 1" "section"
    And I click on "Copy to clipboard" "link" in the "Permalink" "dialogue"
    Then I should see "Text copied to clipboard"

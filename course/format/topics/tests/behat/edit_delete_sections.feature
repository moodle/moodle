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
      | book       | Test book name         | Test book description         | C1     | book1       | 1       |
      | chat       | Test chat name         | Test chat description         | C1     | chat1       | 4       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     | 5       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  Scenario: Edit section summary in topics format
    When I click on "Edit summary" "link" in the "li#section-2" "css_element"
    And I set the following fields to these values:
      | Summary | Welcome to section 2 |
    And I press "Save changes"
    Then I should see "Welcome to section 2" in the "li#section-2" "css_element"

  Scenario: Edit section default name in topics format
    When I click on "Edit summary" "link" in the "li#section-2" "css_element"
    And I set the following fields to these values:
      | Use default section name | 0                        |
      | name                     | This is the second topic |
    And I press "Save changes"
    Then I should see "This is the second topic" in the "li#section-2" "css_element"
    And I should not see "Topic 2" in the "li#section-2" "css_element"

  Scenario: Deleting the last section in topics format
    When I click on "Delete topic" "link" in the "li#section-5" "css_element"
    Then I should see "Are you absolutely sure you want to completely delete \"Topic 5\" and all the activities it contains?"
    And I press "Delete"
    And I should not see "Topic 5"
    And I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    And the field "Number of sections" matches value "4"

  Scenario: Deleting the middle section in topics format
    When I click on "Delete topic" "link" in the "li#section-4" "css_element"
    And I press "Delete"
    Then I should not see "Topic 5"
    And I should not see "Test chat name"
    And I should see "Test choice name" in the "li#section-4" "css_element"
    And I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    And the field "Number of sections" matches value "4"

  Scenario: Deleting the orphaned section in topics format
    When I follow "Reduce the number of sections"
    Then I should see "Orphaned activities (section 5)" in the "li#section-5" "css_element"
    And I click on "Delete topic" "link" in the "li#section-5" "css_element"
    And I press "Delete"
    And I should not see "Topic 5"
    And I should not see "Orphaned activities"
    And "li#section-5" "css_element" should not exist
    And I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    And the field "Number of sections" matches value "4"

  Scenario: Deleting a section when orphaned section is present in topics format
    When I follow "Reduce the number of sections"
    Then I should see "Orphaned activities (section 5)" in the "li#section-5" "css_element"
    And "li#section-5.orphaned" "css_element" should exist
    And "li#section-4.orphaned" "css_element" should not exist
    And I click on "Delete topic" "link" in the "li#section-1" "css_element"
    And I press "Delete"
    And I should not see "Test book name"
    And I should see "Orphaned activities (section 4)" in the "li#section-4" "css_element"
    And "li#section-5" "css_element" should not exist
    And "li#section-4.orphaned" "css_element" should exist
    And "li#section-3.orphaned" "css_element" should not exist

@core @core_course
Feature: Sections can be moved
  In order to rearrange my course contents
  As a teacher
  I need to move sections up and down

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1 | topics | 0 | 5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  Scenario: Move up and down a section with Javascript disabled in a single page course
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    When I move down section "1"
    Then I should see "Test forum name" in the "Topic 2" "section"
    And I move up section "2"
    And I should see "Test forum name" in the "Topic 1" "section"

  Scenario: Move up and down a section with Javascript disabled in the course home of a course using paged mode
    Given I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    When I move down section "1"
    Then I should see "Test forum name" in the "Topic 2" "section"
    And I move up section "2"
    And I should see "Test forum name" in the "Topic 1" "section"

  Scenario: Sections can not be moved with Javascript disabled in a section page of a course using paged mode
    Given I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    When I follow "Topic 2"
    Then "Topic 1" "section" should not exist
    And "Topic 3" "section" should not exist
    And "Move down" "link" should not exist
    And "Move up" "link" should not exist

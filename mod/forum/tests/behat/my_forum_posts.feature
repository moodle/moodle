@mod @mod_forum @javascript
Feature: A user can view their posts and discussions
  In order to ensure a user can view their posts and discussions
  As a student
  I need to view my post and discussions

  Scenario: View the student's posts and discussions
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Forum discussion 1 |
      | Message | How awesome is this forum discussion? |
    And I reply "Forum discussion 1" post from "Test forum name" forum with:
      | Message | Actually, I've seen better. |
    When I follow "Profile" in the user menu
    And I follow "Forum posts"
    Then I should see "How awesome is this forum discussion?"
    And I should see "Actually, I've seen better."
    And I follow "Profile" in the user menu
    And I follow "Forum discussions"
    And I should see "How awesome is this forum discussion?"
    And I should not see "Actually, I've seen better."

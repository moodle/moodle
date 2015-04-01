@mod @mod_forum
Feature: Set a certain number of discussions as a completion condition for a forum
  In order to ensure students are participating on forums
  As a teacher
  I need to set a minimum number of discussions to mark the forum activity as completed

  @javascript
  Scenario: Set X number of discussions as a condition
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | enablecompletion   | 1 |
      | enableavailability | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
      | Completion tracking | Show activity as complete when conditions are met |
      | completiondiscussionsenabled | 1 |
      | completiondiscussions | 2 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_forum ')]/descendant::img[@alt='Not completed: Test forum name']" "xpath_element"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Post 1 subject |
      | Message | Body 1 content |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Post 2 subject |
      | Message | Body 2 content |
    And I follow "Course 1"
    And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_forum ')]/descendant::img[contains(@alt, 'Completed: Test forum name')]" "xpath_element"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And "Student 1" user has completed "Test forum name" activity

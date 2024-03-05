@mod @mod_chat
Feature: Chat calendar entries
  In order to notify students of upcoming chat sessons
  As a teacher
  I need to create a chat activity and publish the event times

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I enable "chat" "mod" plugin

  Scenario Outline: Create a chat activity with repeated chat times set
    # Create an activity with repeated chat times
    Given the following "activities" exist:
      | activity | course | name   | schedule      |
      | chat     | C1     | Chat 1 | <scheduleset> |
    And I log in as "teacher1"
    # Confirm Chat activity visibility based on schedule
    When I am viewing calendar in "upcoming" view
    Then I <chatvisibility> see "Chat 1"

    Examples:
      | scheduleset | chatvisibility |
      | 0           | should not     |
      | 1           | should         |
      | 2           | should         |
      | 3           | should         |

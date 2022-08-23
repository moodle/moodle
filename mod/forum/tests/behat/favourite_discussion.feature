@mod @mod_forum @javascript
Feature: A student can favourite a discussion via the forum settings menu

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activity" exists:
      | course   | C1              |
      | activity | forum           |
      | name     | Test forum name |
    And the following forum discussions exist in course "Course 1":
      | user  | forum           | name         | message                              |
      | admin | Test forum name | Discussion 1 | Discussion contents 1, first message |
    And the following forum replies exist in course "Course 1":
      | user  | forum           | discussion   | name                    | message                               |
      | admin | Test forum name | Discussion 1 | Reply 1 to discussion 1 | Discussion contents 1, second message |

  Scenario: Student can favourite a discussion from within an individual discussion
    Given I am on the "Course 1" Course page logged in as student1
    And I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 1 |
      | Message | Discussion contents 1, third message |
    And I wait until the page is ready
    When I open the action menu in "[data-container='discussion-tools']" "css_element"
    And I click on "[title='Star this discussion']" "css_element"
    And I wait "3" seconds
    And I open the action menu in "[data-container='discussion-tools']" "css_element"
    And I click on "[title='Unstar this discussion']" "css_element"

  Scenario: Student can favourite a discussion from the discussion list
    Given I am on the "Course 1" Course page logged in as student1
    And I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 1 |
      | Message | Discussion contents 1, third message |
    When I am on the "Test forum name" "forum activity" page
    And I click on "Discussion 1" action menu
    And I click on "[title='Star this discussion']" "css_element"
    And I click on "Discussion 1" action menu
    And I click on "[title='Unstar this discussion']" "css_element"

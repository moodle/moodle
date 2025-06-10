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
      | idnumber | forum1          |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name         | message                              |
      | admin    | forum1 | Discussion 1 | Discussion contents 1, first message |
    And the following "mod_forum > posts" exist:
      | user     | parentsubject | subject                 | message                               |
      | admin    | Discussion 1  | Reply 1 to discussion 1 | Discussion contents 1, second message |
      | student1 | Discussion 1  | Reply 2 to discussion 1 | Discussion contents 1, third message  |

  Scenario: Student can favourite a discussion from within an individual discussion
    Given I am on the "Test forum name" "forum activity" page logged in as student1
    When I open the action menu in "[data-container='discussion-tools']" "css_element"
    And I click on "[title='Star this discussion']" "css_element"
    And I wait "3" seconds
    And I open the action menu in "[data-container='discussion-tools']" "css_element"
    And I click on "[title='Unstar this discussion']" "css_element"

  Scenario: Student can favourite a discussion from the discussion list
    When I am on the "Test forum name" "forum activity" page logged in as student1
    And I click on "Discussion 1" action menu
    And I click on "[title='Star this discussion']" "css_element"
    And I click on "Discussion 1" action menu
    And I click on "[title='Unstar this discussion']" "css_element"

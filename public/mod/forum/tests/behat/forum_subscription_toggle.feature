@mod @mod_forum
Feature: Forum subscription toggle in the forum action bar
  In order to quickly manage my forum subscription without page reloads
  As a user
  I need the forum action bar to show a toggle that subscribes and unsubscribes via AJAX

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   |
      | student1 | Student   | One      | student.one@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |

  @javascript
  Scenario: The subscription toggle is shown unchecked when a user is not subscribed to an optional forum
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 0               |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to forum" "field" should exist
    And the field "Subscribe to forum" matches value "0"

  @javascript
  Scenario: The subscription toggle is shown checked when a user is auto-subscribed to a forum
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 2               |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to forum" "field" should exist
    And the field "Subscribe to forum" matches value "1"

  Scenario: The subscription toggle is not shown when subscriptions are forced
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 1               |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to forum" "field" should not exist

  Scenario: The subscription toggle is not shown when subscriptions are disabled
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 3               |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to forum" "field" should not exist

  @javascript
  Scenario: Subscribing via the forum action bar toggle shows a visible toast notification
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 0               |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    And I click on "Subscribe to forum" "field"
    Then I should see "You will be notified of new posts in the forum 'Test forum name'."

  @javascript
  Scenario: Unsubscribing via the forum action bar toggle shows a visible toast notification
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 2               |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    And I click on "Subscribe to forum" "field"
    Then I should see "Student One will NOT be notified of new posts in 'Test forum name'"

  @javascript
  Scenario: Subscribing via the forum action bar toggle updates all discussions
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 0               |
    And the following "mod_forum > discussions" exist:
      | forum  | course | user  | name                  | message               |
      | forum1 | C1     | admin | Test post subject one | Test post message one |
      | forum1 | C1     | admin | Test post subject two | Test post message two |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    When I click on "Subscribe to forum" "field"
    Then "Unsubscribe from this discussion" "field" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "field" should exist in the "Test post subject two" "table_row"

  @javascript
  Scenario: Discussion toggle works after subscribing all via the forum action bar toggle
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 0               |
    And the following "mod_forum > discussions" exist:
      | forum  | course | user  | name                  | message               |
      | forum1 | C1     | admin | Test post subject one | Test post message one |
      | forum1 | C1     | admin | Test post subject two | Test post message two |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    When I click on "Subscribe to forum" "field"
    And "Unsubscribe from this discussion" "field" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "field" should exist in the "Test post subject two" "table_row"
    When I click on "Unsubscribe from this discussion" "field" in the "Test post subject one" "table_row"
    Then "Subscribe to this discussion" "field" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "field" should exist in the "Test post subject two" "table_row"

  @javascript
  Scenario: Unsubscribing via the forum action bar toggle updates all discussions
    Given the following "activity" exists:
      | activity       | forum           |
      | course         | C1              |
      | idnumber       | forum1          |
      | name           | Test forum name |
      | type           | general         |
      | forcesubscribe | 2               |
    And the following "mod_forum > discussions" exist:
      | forum  | course | user  | name                  | message               |
      | forum1 | C1     | admin | Test post subject one | Test post message one |
      | forum1 | C1     | admin | Test post subject two | Test post message two |
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Unsubscribe from this discussion" "checkbox" should exist in the "Test post subject two" "table_row"
    When I click on "Subscribe to forum" "field"
    Then "Subscribe to this discussion" "checkbox" should exist in the "Test post subject one" "table_row"
    And "Subscribe to this discussion" "checkbox" should exist in the "Test post subject two" "table_row"

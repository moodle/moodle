@mod @mod_forum @javascript
Feature: As a teacher, you can manually lock individual discussions when viewing the discussion

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 |
      | Message | Discussion contents 1, first message |
    And I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 1 |
      | Message | Discussion contents 1, second message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 |
      | Message | Discussion contents 2, first message |
    And I reply "Discussion 2" post from "Test forum name" forum with:
      | Subject | Reply 1 to discussion 2 |
      | Message | Discussion contents 2, second message |
    And I log out

  Scenario: Lock a discussion and view
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to post "Discussion 1" in "Test forum name" forum
    And I press "Settings"
    Then "Lock" "link" should be visible
    And I follow "Lock"
    Then I should see "This discussion has been locked so you can no longer reply to it."
    And I press "Settings"
    Then "a[@title='Lock']" "css_element" should not be visible
    Then "Locked" "link" should be visible
    And I press "Settings"
    And I follow "Discussion 2"
    Then I should not see "This discussion has been locked so you can no longer reply to it."
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to post "Discussion 1" in "Test forum name" forum
    Then I should see "This discussion has been locked so you can no longer reply to it."
    And "Reply" "link" should not be visible

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
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

  Scenario: Student can favourite a discussion from within an individual discussion
    Given I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 1 |
      | Message | Discussion contents 1, third message |
    And I wait until the page is ready
    And I open the action menu in "[data-container='discussion-tools']" "css_element"
    And I click on "[title='Star this discussion']" "css_element"
    And I wait "3" seconds
    And I open the action menu in "[data-container='discussion-tools']" "css_element"
    And I click on "[title='Unstar this discussion']" "css_element"

  Scenario: Student can favourite a discussion from the discussion list
    Given I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 1 |
      | Message | Discussion contents 1, third message |
    And I follow "Test forum name"
    And I click on "Discussion 1" action menu
    And I click on "[title='Star this discussion']" "css_element"
    And I click on "Discussion 1" action menu
    And I click on "[title='Unstar this discussion']" "css_element"
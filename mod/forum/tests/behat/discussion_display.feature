@mod @mod_forum
Feature: Students can choose from 4 discussion display options and their choice is remembered
  In order to read forum posts in a suitable view
  As a user
  I need to select which display method I want to use

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
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
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
    And I log in as "student1"
    And I follow "Course 1"

  Scenario: Display replies flat, with oldest first
    Given I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 1 |
      | Message | Discussion contents 1, third message |
    When I select "Display replies flat, with oldest first" from the "mode" singleselect
    Then I should see "Discussion contents 1, first message" in the "div.firstpost.starter" "css_element"
    And I should see "Discussion contents 1, second message" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ') and not(contains(@class, 'starter'))]" "xpath_element"
    And I reply "Discussion 2" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 2 |
      | Message | Discussion contents 2, third message |
    And the field "Display mode" matches value "Display replies flat, with oldest first"
    And I should see "Discussion contents 2, first message" in the "div.firstpost.starter" "css_element"
    And I should see "Discussion contents 2, second message" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ') and not(contains(@class, 'starter'))]" "xpath_element"

  Scenario: Display replies flat, with newest first
    Given I reply "Discussion 1" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 1 |
      | Message | Discussion contents 1, third message |
    When I select "Display replies flat, with newest first" from the "mode" singleselect
    Then I should see "Discussion contents 1, first message" in the "div.firstpost.starter" "css_element"
    And I should see "Discussion contents 1, third message" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ') and not(contains(@class, 'starter'))]" "xpath_element"
    And I reply "Discussion 2" post from "Test forum name" forum with:
      | Subject | Reply 2 to discussion 2 |
      | Message | Discussion contents 2, third message |
    And the field "Display mode" matches value "Display replies flat, with newest first"
    And I should see "Discussion contents 2, first message" in the "div.firstpost.starter" "css_element"
    And I should see "Discussion contents 2, third message" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ') and not(contains(@class, 'starter'))]" "xpath_element"

  Scenario: Display replies in threaded form
    Given I follow "Test forum name"
    And I follow "Discussion 1"
    When I select "Display replies in threaded form" from the "mode" singleselect
    Then I should see "Discussion contents 1, first message"
    And I should see "Reply 1 to discussion 1" in the "span.forumthread" "css_element"
    And I follow "Test forum name"
    And I follow "Discussion 2"
    And the field "Display mode" matches value "Display replies in threaded form"
    And I should see "Discussion contents 2, first message"
    And I should see "Reply 1 to discussion 2" in the "span.forumthread" "css_element"

  Scenario: Display replies in nested form
    Given I follow "Test forum name"
    And I follow "Discussion 1"
    When I select "Display replies in nested form" from the "mode" singleselect
    Then I should see "Discussion contents 1, first message" in the "div.firstpost.starter" "css_element"
    And I should see "Discussion contents 1, second message" in the "div.indent div.forumpost" "css_element"
    And I follow "Test forum name"
    And I follow "Discussion 2"
    And the field "Display mode" matches value "Display replies in nested form"
    And I should see "Discussion contents 2, first message" in the "div.firstpost.starter" "css_element"
    And I should see "Discussion contents 2, second message" in the "div.indent div.forumpost" "css_element"

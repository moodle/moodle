@mod @mod_forum
Feature: A user can control their default discussion subscription settings
  In order to automatically subscribe to discussions
  As a user
  I can choose my default subscription preference

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                   | autosubscribe |
      | student1 | Student   | One      | student.one@example.com | 1             |
      | student2 | Student   | Two      | student.one@example.com | 0             |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity   | name                   | intro                  | course | idnumber | type    | section |
      | forum      | Test forum name        | Test forum description | C1     | forump1  | general | 1       |
    And I am on the "Test forum name" "forum activity editing" page logged in as admin
    And I set the following fields to these values:
      | Subscription mode | Optional subscription |
    And I press "Save and return to course"
    And I log out

  Scenario: Creating a new discussion in an optional forum follows user preferences
    Given I am on the "Test forum name" "forum activity" page logged in as student1
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then "input[name=discussionsubscribe][checked=checked]" "css_element" should exist
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student2
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And "input[name=discussionsubscribe]:not([checked=checked])" "css_element" should exist

  Scenario: Replying to an existing discussion in an optional forum follows user preferences
    Given I am on the "Test forum name" "forum activity" page logged in as admin
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    And I follow "Test post subject"
    When I follow "Reply"
    Then "input[name=discussionsubscribe][checked=checked]" "css_element" should exist
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student2
    And I follow "Test post subject"
    And I follow "Reply"
    And "input[name=discussionsubscribe]:not([checked=checked])" "css_element" should exist

  Scenario: Creating a new discussion in an automatic forum follows forum subscription
    Given I am on the "Test forum name" "forum activity editing" page logged in as admin
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then "input[name=discussionsubscribe][checked=checked]" "css_element" should exist
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student2
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And "input[name=discussionsubscribe][checked=checked]" "css_element" should exist

  Scenario: Replying to an existing discussion in an automatic forum follows forum subscription
    Given I am on the "Test forum name" "forum activity editing" page logged in as admin
    And I set the following fields to these values:
      | Subscription mode | Optional subscription |
    And I press "Save and return to course"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    And I follow "Test post subject"
    When I follow "Reply"
    Then "input[name=discussionsubscribe][checked=checked]" "css_element" should exist
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student2
    And I follow "Test post subject"
    And I follow "Reply"
    And "input[name=discussionsubscribe]:not([checked=checked])" "css_element" should exist

  @javascript
  Scenario: Replying to an existing discussion in an automatic forum which has been unsubscribed from follows user preferences
    Given I am on the "Test forum name" "forum activity editing" page logged in as admin
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save and return to course"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject" "table_row"
    And I follow "Test post subject"
    When I follow "Reply"
    And I click on "Advanced" "button"
    And "input[name=discussionsubscribe][checked]" "css_element" should exist
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student2
    And I click on "input[id^=subscription-toggle]" "css_element" in the "Test post subject" "table_row"
    And I follow "Test post subject"
    And I follow "Reply"
    And I click on "Advanced" "button"
    And "input[name=discussionsubscribe]:not([checked])" "css_element" should exist

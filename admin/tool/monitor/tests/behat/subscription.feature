@javascript @tool @tool_monitor @tool_monitor_subscriptions
Feature: tool_monitor_subscriptions
  In order to monitor events and receive notifications
  As an user
  I need to create a new rule, subscribe to it, receive notification and delete subscription

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And   I log in as "admin"
    And   I follow "Course 1"
    And   I navigate to "Event monitoring rules" node in "Course administration > Reports"
    And   I press "Add a new rule"
    And   I set the following fields to these values:
      | name              | New rule course level                             |
      | plugin            | Core                                              |
      | eventname         | Course viewed                                     |
      | id_description    | I want a rule to monitor when a course is viewed. |
      | frequency         | 1                                                 |
      | minutes           | 1                                                 |
      | Message template  | The course was viewed. {modulelink}               |
    And   I press "Save changes"
    And   I navigate to "Event monitoring rules" node in "Site administration > Reports"
    And   I press "Add a new rule"
    And   I set the following fields to these values:
      | name              | New rule site level                               |
      | plugin            | Core                                              |
      | eventname         | Course viewed                                     |
      | id_description    | I want a rule to monitor when a course is viewed. |
      | frequency         | 1                                                 |
      | minutes           | 1                                                 |
      | Message template  | The course was viewed. {modulelink}               |
    And  I press "Save changes"
    And  I log out

  Scenario: Subscribe to a rule on course level
    Given I log in as "teacher1"
    And   I follow "Course 1"
    And   I navigate to "Event monitoring" node in "My profile settings"
    And   I set the field "courseid" to "Course 1"
    When  I set the field "cmid" to "All events"
    Then  I should see "Subscription successfully created"
    And   "#toolmonitorsubs_r0" "css_element" should exist

  Scenario: Delete a subscription on course level
    Given I log in as "teacher1"
    And   I follow "Course 1"
    And   I navigate to "Event monitoring" node in "My profile settings"
    And   I set the field "courseid" to "Course 1"
    And   I set the field "cmid" to "All events"
    And   I should see "Subscription successfully created"
    When  I click on "Delete subscription" "link"
    And   I should see "Are you sure you want to delete this subscription for the rule \"New rule course level\"?"
    And   I press "Yes"
    Then  I should see "Subscription successfully removed"
    And   "#toolmonitorsubs_r0" "css_element" should not exist

  Scenario: Subscribe to a rule on site level
    Given I log in as "admin"
    And   I navigate to "Event monitoring" node in "My profile settings"
    And   I set the field "courseid" to "Site"
    When  I set the field "cmid" to "All events"
    Then  I should see "Subscription successfully created"
    And   "#toolmonitorsubs_r0" "css_element" should exist

  Scenario: Delete a subscription on site level
    Given I log in as "admin"
    And   I navigate to "Event monitoring" node in "My profile settings"
    And   I set the field "courseid" to "Site"
    And   I set the field "cmid" to "All events"
    And   I should see "Subscription successfully created"
    And   "#toolmonitorsubs_r0" "css_element" should exist
    When  I click on "Delete subscription" "link"
    And   I should see "Are you sure you want to delete this subscription for the rule \"New rule site level\"?"
    And   I press "Yes"
    Then  I should see "Subscription successfully removed"
    And   "#toolmonitorsubs_r0" "css_element" should not exist

  Scenario: Receiving notification on site level
    Given I log in as "admin"
    And   I navigate to "Messaging" node in "My profile settings"
    And   I click on "input[name^=tool_monitor_notification_loggedin]" "css_element"
    And   I press "Update profile"
    And   I am on homepage
    And   I follow "Course 1"
    And   I navigate to "Event monitoring" node in "My profile settings"
    And   I set the field "courseid" to "Site"
    And   I set the field "cmid" to "All events"
    And   I should see "Subscription successfully created"
    And   "#toolmonitorsubs_r0" "css_element" should exist
    And   I am on homepage
    And   I trigger cron
    And   I am on homepage
    And   I expand "My profile" node
    When  I follow "Messages"
    And   I follow "Do not reply to this email (1)"
    Then  I should see "The course was viewed."

  Scenario: Receiving notification on course level
    Given I log in as "teacher1"
    And   I navigate to "Messaging" node in "My profile settings"
    And   I click on "input[name^=tool_monitor_notification_loggedin]" "css_element"
    And   I press "Update profile"
    And   I am on homepage
    And   I follow "Course 1"
    And   I navigate to "Event monitoring" node in "My profile settings"
    And   I set the field "courseid" to "Course 1"
    And   I set the field "cmid" to "All events"
    And   I should see "Subscription successfully created"
    And   "#toolmonitorsubs_r0" "css_element" should exist
    And   I am on homepage
    And   I follow "Course 1"
    And   I trigger cron
    And   I am on homepage
    And   I expand "My profile" node
    When  I follow "Messages"
    And   I follow "Do not reply to this email (1)"
    Then  I should see "The course was viewed."

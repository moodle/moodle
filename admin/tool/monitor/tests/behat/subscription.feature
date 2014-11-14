@javascript @tool @tool_monitor @tool_monitor_subscriptions
Feature: tool_monitor_subscriptions
  In order to monitor events and receive notifications
  As an user
  I need to create a new rule, subscribe to it, receive notification and delete subscription

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | teacher2 | Teacher | 2 | teacher2@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | teacher |
      | teacher2 | C1 | teacher |
      | teacher2 | C2 | editingteacher |
    And I log in as "admin"
    And I navigate to "Event monitoring rules" node in "Site administration > Reports"
    And I click on "Enable" "link"
    And I am on homepage
    And I follow "Course 1"
    And I navigate to "Event monitoring rules" node in "Course administration > Reports"
    And I press "Add a new rule"
    And I set the following fields to these values:
      | name                 | New rule course level                             |
      | plugin               | Core                                              |
      | eventname            | Course viewed                                     |
      | id_description       | I want a rule to monitor when a course is viewed. |
      | frequency            | 1                                                 |
      | minutes              | 1                                                 |
      | Notification message | The course was viewed. {modulelink}               |
    And I press "Save changes"
    And I navigate to "Event monitoring rules" node in "Site administration > Reports"
    And I press "Add a new rule"
    And I set the following fields to these values:
      | name                 | New rule site level                               |
      | plugin               | Core                                              |
      | eventname            | Course viewed                                     |
      | id_description       | I want a rule to monitor when a course is viewed. |
      | frequency            | 1                                                 |
      | minutes              | 1                                                 |
      | Notification message | The course was viewed. {modulelink}               |
    And I press "Save changes"
    And I navigate to "Define roles" node in "Site administration > Users > Permissions"
    And I follow "Non-editing teacher"
    And I press "Edit"
    And I click on "tool/monitor:managerules" "checkbox"
    And I press "Save changes"
    And I log out

  Scenario: Subscribe to a rule on course level
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I set the field "Select a course" to "Course 1"
    When I follow "Subscribe to rule \"New rule course level\""
    Then I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist

  Scenario: Delete a subscription on course level
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I set the field "Select a course" to "Course 1"
    And I follow "Subscribe to rule \"New rule course level\""
    And I should see "Subscription successfully created"
    When I click on "Delete subscription" "link" in the "New rule course level" "table_row"
    And I should see "Are you sure you want to delete the subscription to the rule \"New rule course level\"?"
    And I press "Continue"
    Then I should see "Subscription successfully removed"
    And "#toolmonitorsubs_r0" "css_element" should not exist

  Scenario: Subscribe to a rule on site level
    Given I log in as "admin"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I set the field "Select a course" to "Site"
    When I follow "Subscribe to rule \"New rule site level\""
    Then I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist

  Scenario: Delete a subscription on site level
    Given I log in as "admin"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I set the field "Select a course" to "Site"
    And I follow "Subscribe to rule \"New rule site level\""
    And I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist
    When I click on "Delete subscription" "link" in the "New rule site level" "table_row"
    And I should see "Are you sure you want to delete the subscription to the rule \"New rule site level\"?"
    And I press "Continue"
    Then I should see "Subscription successfully removed"
    And "#toolmonitorsubs_r0" "css_element" should not exist

  Scenario: Receiving notification on site level
    Given I log in as "admin"
    And I navigate to "Messaging" node in "My profile settings"
    And I click on "input[name^=tool_monitor_notification_loggedin]" "css_element"
    And I press "Update profile"
    And I am on homepage
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I set the field "Select a course" to "Site"
    And I follow "Subscribe to rule \"New rule site level\""
    And I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist
    And I am on homepage
    And I trigger cron
    And I am on homepage
    When I navigate to "Messages" node in "My profile"
    And I follow "Do not reply to this email (1)"
    Then I should see "The course was viewed."

  Scenario: Receiving notification on course level
    Given I log in as "teacher1"
    And I navigate to "Messaging" node in "My profile settings"
    And I click on "input[name^=tool_monitor_notification_loggedin]" "css_element"
    And I press "Update profile"
    And I am on homepage
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I set the field "Select a course" to "Course 1"
    And I follow "Subscribe to rule \"New rule course level\""
    And I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist
    And I am on homepage
    And I follow "Course 1"
    And I trigger cron
    And I am on homepage
    When I navigate to "Messages" node in "My profile"
    And I follow "Do not reply to this email (1)"
    Then I should see "The course was viewed."

  Scenario: Navigating via quick link to rules
    Given I log in as "admin"
    When I navigate to "Event monitoring" node in "My profile settings"
    Then I should see "You can manage rules from the Event monitoring rules page."
    And I follow "Event monitoring rules"
    And I should see "You can subscribe to rules from the Event monitoring page."
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I should see "You can manage rules from the Event monitoring rules page."
    And I follow "Event monitoring rules"
    And I should see "You can subscribe to rules from the Event monitoring page."
    And I click on "//a[text()='Event monitoring']" "xpath_element"
    And the field "courseid" matches value "Course 1"
    And I set the field "courseid" to "Site"
    And I should not see "You can manage rules from the Event monitoring rules page."
    And I log out
    And I log in as "teacher2"
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I should not see "You can manage rules the from the Event monitoring rules page."

  Scenario: No manage rules link when user does not have permission
    Given I log in as "teacher1"
    When I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    Then I should see "You can manage rules from the Event monitoring rules page."
    And I log out
    And I log in as "teacher2"
    And I follow "Course 1"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I should not see "You can manage rules from the Event monitoring rules page."
    And I follow "Home"
    And I follow "Course 2"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I should see "You can manage rules from the Event monitoring rules page."
    And I log out
    And I log in as "teacher1"
    And I follow "Course 2"
    And I navigate to "Event monitoring" node in "My profile settings"
    And I should not see "You can manage rules from the Event monitoring rules page."

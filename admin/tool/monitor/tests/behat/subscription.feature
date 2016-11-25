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
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | teacher |
      | teacher2 | C1 | teacher |
      | teacher2 | C2 | editingteacher |
    And I log in as "admin"
    And I navigate to "Event monitoring rules" node in "Site administration > Reports"
    And I click on "Enable" "link"
    And I am on site homepage
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
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Course 1"
    When I follow "Subscribe to rule \"New rule course level\""
    Then I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist

  Scenario: Delete a subscription on course level
    Given I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
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
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Acceptance test site"
    When I follow "Subscribe to rule \"New rule site level\""
    Then I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist

  Scenario: Delete a subscription on site level
    Given I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Acceptance test site"
    And I follow "Subscribe to rule \"New rule site level\""
    And I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist
    When I click on "Delete subscription" "link" in the "New rule site level" "table_row"
    And I should see "Are you sure you want to delete the subscription to the rule \"New rule site level\"?"
    And I press "Continue"
    Then I should see "Subscription successfully removed"
    And "#toolmonitorsubs_r0" "css_element" should not exist

  @_bug_phantomjs
  Scenario: Receiving notification on site level
    Given I log in as "admin"
    And I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    And I click on ".preference-state" "css_element" in the "Notifications of rule subscriptions" "table_row"
    And I wait until the page is ready
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Acceptance test site"
    And I follow "Subscribe to rule \"New rule site level\""
    And I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist
    And I am on site homepage
    And I trigger cron
    And I am on site homepage
    When I click on ".popover-region-notifications" "css_element"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    Then I should see "New rule site level"
    And I should see "The course was viewed"

  @_bug_phantomjs
  Scenario: Receiving notification on course level
    Given I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    And I click on ".preference-state" "css_element" in the "Notifications of rule subscriptions" "table_row"
    And I wait until the page is ready
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Course 1"
    And I follow "Subscribe to rule \"New rule course level\""
    And I should see "Subscription successfully created"
    And "#toolmonitorsubs_r0" "css_element" should exist
    And I am on site homepage
    And I follow "Course 1"
    And I trigger cron
    And I am on site homepage
    When I click on ".popover-region-notifications" "css_element"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    Then I should see "New rule course level"
    And I should see "The course was viewed"

  Scenario: Navigating via quick link to rules
    Given I log in as "admin"
    And I follow "Preferences" in the user menu
    When I follow "Event monitoring"
    And I set the field "Select a course" to "Course 1"
    Then I should see "You can manage rules from the Event monitoring rules page."
    And I click on "Event monitoring rules" "link" in the "region-main" "region"
    And I should see "You can subscribe to rules from the Event monitoring page."
    And I log out
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Course 1"
    And I should see "You can manage rules from the Event monitoring rules page."
    And I click on "Event monitoring rules" "link" in the "region-main" "region"
    And I should see "You can subscribe to rules from the Event monitoring page."
    And I click on "//a[text()='Event monitoring']" "xpath_element"
    And the field "courseid" matches value "Course 1"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I should not see "You can manage rules from the Event monitoring rules page."
    And I log out
    And I log in as "teacher2"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Course 1"
    And I should not see "You can manage rules the from the Event monitoring rules page."

  Scenario: No manage rules link when user does not have permission
    Given I log in as "admin"
    And I set the following system permissions of "Non-editing teacher" role:
      | tool/monitor:managerules | Prohibit |
    And I log out
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    When I set the field "Select a course" to "Course 1"
    Then I should see "You can manage rules from the Event monitoring rules page."
    And I set the field "Select a course" to "Course 2"
    And I should not see "You can manage rules from the Event monitoring rules page."
    And I log out
    And I log in as "teacher2"
    And I follow "Preferences" in the user menu
    And I follow "Event monitoring"
    And I set the field "Select a course" to "Course 1"
    And I should not see "You can manage rules from the Event monitoring rules page."
    And I set the field "Select a course" to "Course 2"
    And I should see "You can manage rules from the Event monitoring rules page."

@enrol @enrol_self
Feature: A course welcome message will be sent to the user when they auto-enrol themself in a course
  In order to let the user know they have been auto-enrol themself in a course successfully
  As a teacher
  I want the user to receive a welcome message when they auto-enrol themself in a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | manager  | Manager   | User     | manager@example.com |
      | teacher  | Teacher   | User     | teacher@example.com |
      | user1    | First     | User     | first@example.com   |
      | user2    | Second    | User     | second@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | manager | C1     | manager        |
      | teacher | C1     | editingteacher |
      | teacher | C2     | editingteacher |
    And I log in as "admin"
    And I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
    And I add "Self enrolment" enrolment method in "Course 2" with:
      | Custom instance name | Test student enrolment |

  @javascript
  Scenario: Manager should see the new settings for course welcome message
    Given I am on the "C1" "Enrolled users" page logged in as manager
    And I set the field "Participants tertiary navigation" to "Enrolment methods"
    When I click on "Edit" "link" in the "Test student enrolment" "table_row"
    Then I should see "Send course welcome message"
    And the field "Send course welcome message" matches value "From the course contact"
    And I should see "Custom welcome message"
    And the field "Custom welcome message" matches value "Dear {$a->fullname}, you have successfully been enrolled to course {$a->coursename}"
    And I should see "Accepted formats: Plain text or Moodle-auto format. HTML tags and multi-lang tags are also accepted, as well as the following placeholders:"
    And I set the field "Send course welcome message" to "No"
    And I should not see "Custom welcome message"
    And I should not see "Accepted formats: Plain text or Moodle-auto format. HTML tags and multi-lang tags are also accepted, as well as the following placeholders:"

  @javascript
  Scenario: Student should not receive a welcome message if the setting is disabled
    Given I am on the "C1" "Enrolled users" page logged in as manager
    And I set the field "Participants tertiary navigation" to "Enrolment methods"
    And I click on "Edit" "link" in the "Test student enrolment" "table_row"
    And I set the field "Send course welcome message" to "No"
    And I press "Save changes"
    And I log in as "user1"
    And I am on "Course 1" course homepage
    When I press "Enrol me"
    Then I should not see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"

  @javascript
  Scenario: Students should receive a welcome message if the setting is enabled - Default message
    # Login as first user and check the notification.
    Given I log in as "user1"
    And I am on "Course 1" course homepage
    When I press "Enrol me"
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 1"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear First User, you have successfully been enrolled to course Course 1"
    # Login as second user and check the notification.
    And I log in as "user2"
    And I am on "Course 2" course homepage
    And I press "Enrol me"
    And I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 2"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear Second User, you have successfully been enrolled to course Course 2"

  @javascript
  Scenario: Students should receive a welcome message if the setting is enabled - Custom message
    Given I am on the "C1" "Enrolled users" page logged in as manager
    And I set the field "Participants tertiary navigation" to "Enrolment methods"
    And I click on "Edit" "link" in the "Test student enrolment" "table_row"
    And I set the field "Custom welcome message" to multiline:
    """
    Dear {$a->fullname}, you have successfully been enrolled to course {$a->coursename}.
    Your email address: {$a->email}
    Your first name: {$a->firstname}
    Your last name: {$a->lastname}
    Your course role: {$a->courserole}
    """
    And I press "Save changes"
    # Login as first user and check the notification.
    And I log in as "user1"
    And I am on "Course 1" course homepage
    When I press "Enrol me"
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 1"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear First User, you have successfully been enrolled to course Course 1"
    And I should see "Your email address: first@example.com"
    And I should see "Your first name: First"
    And I should see "Your last name: User"
    And I should see "Your course role: student"
    # Login as second user and check the notification.
    And I log in as "user2"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 1"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear Second User, you have successfully been enrolled to course Course 1"
    And I should see "Your email address: second@example.com"
    And I should see "Your first name: Second"
    And I should see "Your last name: User"
    And I should see "Your course role: student"

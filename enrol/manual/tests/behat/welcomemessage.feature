@enrol @enrol_manual
Feature: A course welcome message will be sent to the user when they are enrolled in a course
  In order to let the user know they have been enrolled in a course
  As a teacher
  I want the user to receive a welcome message when they are enrolled in a course

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

  @javascript
  Scenario: Manager should see the new settings for course welcome message
    Given I am on the "C1" "Enrolled users" page logged in as manager
    And I set the field "Participants tertiary navigation" to "Enrolment methods"
    When I click on "Edit" "link" in the "Manual enrolments" "table_row"
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
    And I click on "Edit" "link" in the "Manual enrolments" "table_row"
    And I set the field "Send course welcome message" to "No"
    And I press "Save changes"
    And I am on the "C1" "Enrolled users" page logged in as teacher
    And I press "Enrol users"
    And I set the field "Select users" to "First User"
    And I should see "First User"
    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
    And I should see "Active" in the "First User" "table_row"
    When I am on the "C1" "course" page logged in as user1
    Then I should not see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"

  @javascript
  Scenario: Students should receive a welcome message if the setting is enabled - Default message
    Given I am on the "C1" "Enrolled users" page logged in as teacher
    # Enrol first user to Course 1.
    And I press "Enrol users"
    And I set the field "Select users" to "First User"
    And I should see "First User"
    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
    # Enrol second user to Course 2.
    And I am on the "C2" "Enrolled users" page
    And I press "Enrol users"
    And I set the field "Select users" to "Second User"
    And I should see "Second User"
    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
    # Login as first user and check the notification.
    When I am on the "C1" "course" page logged in as user1
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 1"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear First User, you have successfully been enrolled to course Course 1"
    # Login as second user and check the notification.
    And I am on the "C1" "course" page logged in as user2
    And I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 2"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear Second User, you have successfully been enrolled to course Course 2"

  @javascript
  Scenario: Students should receive a welcome message if the setting is enabled - Custom message
    Given I am on the "C1" "Enrolled users" page logged in as manager
    And I set the field "Participants tertiary navigation" to "Enrolment methods"
    And I click on "Edit" "link" in the "Manual enrolments" "table_row"
    And I set the field "Custom welcome message" to multiline:
    """
    Dear {$a->fullname}, you have successfully been enrolled to course {$a->coursename}.
    Your email address: {$a->email}
    Your first name: {$a->firstname}
    Your last name: {$a->lastname}
    Your course role: {$a->courserole}
    """
    And I press "Save changes"
    # Enrol first user and second user to Course 1.
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |
    # Login as first user and check the notification.
    When I am on the "C1" "course" page logged in as user1
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
    When I am on the "C1" "course" page logged in as user2
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    And I open the notification popover
    And I should see "Welcome to Course 1"
    And I click on "View full notification" "link" in the ".popover-region-notifications" "css_element"
    And I should see "Dear Second User, you have successfully been enrolled to course Course 1"
    And I should see "Your email address: second@example.com"
    And I should see "Your first name: Second"
    And I should see "Your last name: User"
    And I should see "Your course role: student"

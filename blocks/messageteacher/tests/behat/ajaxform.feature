@block @block_messageteacher @block_messageteacher_ajaxform @javascript
Feature: AJAX message form
    In order to message my teacher easily
    As a student
    I need to access a messaging form without leaving the current page

    Background:
        Given the following "users" exist:
            | username     | email                    | firstname | lastname |
            | teststudent  | teststudent@example.com  | Test      | Student  |
            | testteacher1 | testteacher1@example.com | Test      | Teacher1 |
        And the following "categories" exist:
            | name       | category | idnumber |
            | Category 1 | 0        | CAT1     |
        And the following "courses" exist:
            | fullname | shortname | category | format |
            | Course 1 | course1   | CAT1     | topics |
        And the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course1 | editingteacher | manual |
        And messageteacher has the following settings:
            | roles | 3 |
        And there is an instance of messageteacher on "Course 1"

    Scenario: User accesses form
        Given I log in as "teststudent"
        And I am on "Course 1" course homepage
        When I follow "Test Teacher1"
        Then "Enter your message for Test Teacher1" "fieldset" should exist
        And "Message text" "field" should exist
        And "Send" "button" should exist

    Scenario: Student sends a message and returns to the page
        Given I log in as "teststudent"
        And I am on "Course 1" course homepage
        And I follow "Test Teacher1"
        And I set the following fields to these values:
            | Message text | Test Message |
        When I press "Send"
        Then I wait "5" seconds
        And I should see "Message Sent!" in the ".messageteacher_confirm" "css_element"
        And "Enter your message for Test Teacher1" "fieldset" should not exist
        When I press "Close"
        Then I should see "Course 1" in the "h1" "css_element"

    Scenario: Teacher recieves a message sent from the custom form
        Given I log in as "teststudent"
        And I am on "Course 1" course homepage
        And I follow "Test Teacher1"
        And I set the following fields to these values:
            | Message text | Test Message |
        And I press "Send"
        And I press "Close"
        And I log out
        And I log in as "testteacher1"
        And I follow "Messages" in the user menu
        Then I should see "Test Student" in the "conversations-tab-panel" "region"
        And I should see "Test Message"

    Scenario: Teacher recieves a message sent from the custom form and appendurl is enabled
        Given I log in as "admin"
        And I set the following administration settings values:
            | Append Referring URL | 1 |
        And I log out
        And I log in as "teststudent"
        And I am on "Course 1" course homepage
        And I follow "Test Teacher1"
        And I set the following fields to these values:
            | Message text | Test Message |
        And I press "Send"
        And I press "Close"
        And I log out
        And I log in as "testteacher1"
        And I follow "Messages" in the user menu
        Then I should see "Test Student" in the "conversations-tab-panel" "region"
        And I should see "Test Message"
        And I should see "/course/view.php?id="

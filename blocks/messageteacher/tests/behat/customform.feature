@block @block_messageteacher @block_messageteacher_customform
Feature: Custom Form
    In order to send my teacher a message
    As a student
    I need to access a custom messaging form which returns me to the page I came from

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
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        And I am on "Course 1" course homepage

    Scenario: User accesses form
        When I follow "Test Teacher1"
        Then "Enter your message for Test Teacher1" "fieldset" should exist
        And "Message text" "field" should exist
        And "Send" "button" should exist

    Scenario: Student sends a message and returns to the page
        Given I follow "Test Teacher1"
        And I set the following fields to these values:
            | Message text | Test Message |
        When I press "Send"
        Then I should see "Course 1" in the "h1" "css_element"

    Scenario: Teacher recieves a message sent from the custom form
        Given I follow "Test Teacher1"
        And I set the following fields to these values:
            | Message text | Test Message |
        And I press "Send"
        And I log out
        And I log in as "testteacher1"
        And I expand "My profile" node
        When I follow "Messages"
        Then I should see "Test Student" in the "conversations-tab-panel" "region"
        And I should see "Test Message"

    Scenario: Teacher recieves a message sent from the custom form and appendurl is enabled
        Given the following config values are set as admin:
            | appendurl | 1 | block_messageteacher |
        Given I follow "Test Teacher1"
        And I set the following fields to these values:
            | Message text | Test Message |
        And I press "Send"
        And I log out
        And I log in as "testteacher1"
        And I expand "My profile" node
        When I follow "Messages"
        Then I should see "Test Student" in the "conversations-tab-panel" "region"
        And I should see "Test Message"
        And I should see "/course/view.php?id="

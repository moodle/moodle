@theme @theme_snap
Feature: Testing core_communication in theme_snap

    Background:

        Given the following "courses" exist:
            | fullname    | shortname | category | selectedcommunication |
            | Test course | C1        | 0        | none                  |
        And the following "users" exist:
            | username | firstname | lastname | email                |
            | teacher1 | Teacher   | 1        | teacher1@example.com |
            | teacher2 | Teacher   | 2        | teacher2@example.com |
            | student1 | Student   | 1        | student1@example.com |
        And the following "course enrolments" exist:
            | user     | course | role           |
            | teacher1 | C1     | editingteacher |
            | teacher2 | C1     | teacher        |
            | student1 | C1     | student        |
        And the following config values are set as admin:
            | enablecommunicationsubsystem | 1 |

    Scenario: A teacher with the correct capability can access the communication configuration page
        Given I am on the "C1" "Course" page logged in as "teacher1"
        And I click on "#admin-menu-trigger" "css_element"
        And I follow "Communication"
        Then I should see "Communication"

    Scenario: A teacher without the correct capability can access the communication configuration page
        Given I am on the "C1" "Course" page logged in as "teacher2"
        And I click on "#admin-menu-trigger" "css_element"
        Then I should not see "Communication"

    Scenario: I cannot see the communication link when communication provider is disabled
        Given I disable communication experimental feature
        And I am on the "C1" "Course" page logged in as "teacher2"
        And I click on "#admin-menu-trigger" "css_element"
        Then I should not see "Communication"

    @javascript
    Scenario: As a teacher I can configure a custom communication provider for my course
        Given I am on the "C1" "Course" page logged in as "teacher1"
        And "Chat to course participants" "button" should not be visible
        And I click on "#admin-menu-trigger" "css_element"
        And I follow "Communication"
        And the "Provider" select box should contain "Custom link"
        And I should not see "Custom link URL"
        And I select "Custom link" from the "Provider" singleselect
        And I should see "Custom link URL"
        And I set the following fields to these values:
            | communication_customlinkroomname | Test URL                                                                                   |
            | customlinkurl                    | #wwwroot#/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php |
        And I press "Save changes"
        Then "Chat to course participants" "button" should be visible
        And I click on "Chat to course participants" "button"
        And I switch to a second window
        And I should see "Example messaging service - teacher1" in the "region-main" "region"
        And I close all opened windows
        And I run all adhoc tasks
        And I am on the "C1" course page
        And "Chat to course participants" "button" should be visible
        And I click on "Chat to course participants" "button"
        And I switch to a second window
        And I should see "Example messaging service - teacher1" in the "region-main" "region"
        And I close all opened windows
        And I log out
        And I am on the "C1" "Course" page logged in as "student1"
        And "Chat to course participants" "button" should be visible
        And I click on "Chat to course participants" "button"
        And I switch to a second window
        And I should see "Example messaging service - student1" in the "region-main" "region"

    @javascript
    Scenario: As a teacher I can disable and re-enable a custom communication provider for my course
        Given I am on the "C1" "Course" page logged in as "teacher1"
        And "Chat to course participants" "button" should not be visible
        And I click on "#admin-menu-trigger" "css_element"
        And I follow "Communication"
        And I select "Custom link" from the "Provider" singleselect
        And I set the following fields to these values:
            | communication_customlinkroomname | Test URL                                                                                   |
            | customlinkurl                    | #wwwroot#/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php |
        And I press "Save changes"
        And "Chat to course participants" "button" should be visible
        And I run all adhoc tasks
        And I click on "#admin-menu-trigger" "css_element"
        And I follow "Communication"
        And I select "None" from the "Provider" singleselect
        And I press "Save changes"
        And "Chat to course participants" "button" should not be visible
        And I run all adhoc tasks
        And I am on the "C1" course page
        And "Chat to course participants" "button" should not be visible
        And I click on "#admin-menu-trigger" "css_element"
        And I follow "Communication"
        And I select "Custom link" from the "Provider" singleselect
        And I set the following fields to these values:
            | communication_customlinkroomname | Test URL                                                                                   |
            | customlinkurl                    | #wwwroot#/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php |
        And I press "Save changes"
        And "Chat to course participants" "button" should be visible
        And I run all adhoc tasks
        And I am on the "C1" course page
        And "Chat to course participants" "button" should be visible
        And I click on "Chat to course participants" "button"
        And I switch to a second window
        And I should see "Example messaging service - teacher1" in the "region-main" "region"
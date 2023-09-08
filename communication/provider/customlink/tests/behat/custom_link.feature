@communication @communication_customlink @javascript
Feature: Communication custom link
  In order to facilitate easy access to an existing communication platform
  As a teacher
  I need to be able to make a custom communication link available in my course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | enablecommunicationsubsystem | 1 |

  Scenario: As a teacher I can configure a custom communication provider for my course
    Given I am on the "Course 1" "Course" page logged in as "teacher1"
    And "Chat to course participants" "button" should not be visible
    When I navigate to "Communication" in current page administration
    And the "Provider" select box should contain "Custom link"
    And I should not see "Custom link URL"
    And I select "Custom link" from the "Provider" singleselect
    And I should see "Custom link URL"
    And I set the following fields to these values:
      | communicationroomname | Test URL                                                                                   |
      | customlinkurl         | #wwwroot#/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php |
    And I press "Save changes"
    Then "Chat to course participants" "button" should be visible
    And I click on "Chat to course participants" "button"
    # Check the link hits the expected destination.
    And I switch to a second window
    And I should see "Example messaging service - teacher1" in the "region-main" "region"
    And I close all opened windows
    # Ensure any communication subsystem tasks have no impact on availability.
    And I run all adhoc tasks
    And I am on the "Course 1" course page
    And "Chat to course participants" "button" should be visible
    And I click on "Chat to course participants" "button"
    And I switch to a second window
    And I should see "Example messaging service - teacher1" in the "region-main" "region"
    And I close all opened windows
    And I log out
    # Confirm student also has access to the custom link.
    And I am on the "Course 1" "Course" page logged in as "student1"
    And "Chat to course participants" "button" should be visible
    And I click on "Chat to course participants" "button"
    And I switch to a second window
    And I should see "Example messaging service - student1" in the "region-main" "region"

  Scenario: As a teacher I can disable and re-enable a custom communication provider for my course
    Given I am on the "Course 1" "Course" page logged in as "teacher1"
    And "Chat to course participants" "button" should not be visible
    When I navigate to "Communication" in current page administration
    And I select "Custom link" from the "Provider" singleselect
    And I set the following fields to these values:
      | communicationroomname | Test URL                                                                                   |
      | customlinkurl         | #wwwroot#/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php |
    And I press "Save changes"
    And "Chat to course participants" "button" should be visible
    And I run all adhoc tasks
    And I navigate to "Communication" in current page administration
    And I select "None" from the "Provider" singleselect
    And I press "Save changes"
    And "Chat to course participants" "button" should not be visible
    And I run all adhoc tasks
    And I am on the "Course 1" course page
    And "Chat to course participants" "button" should not be visible
    And I navigate to "Communication" in current page administration
    And I select "Custom link" from the "Provider" singleselect
    And I set the following fields to these values:
      | communicationroomname | Test URL                                                                                   |
      | customlinkurl         | #wwwroot#/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php |
    And I press "Save changes"
    And "Chat to course participants" "button" should be visible
    And I run all adhoc tasks
    And I am on the "Course 1" course page
    And "Chat to course participants" "button" should be visible
    And I click on "Chat to course participants" "button"
    And I switch to a second window
    And I should see "Example messaging service - teacher1" in the "region-main" "region"

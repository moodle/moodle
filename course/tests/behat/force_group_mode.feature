@core @core_course @_cross_browser
Feature: Force group mode in a course
  In order to use the same group mode all over the course
  As a teacher
  I need to force the group mode of all course's activities

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Chat" to section "1" and I fill the form with:
      | Name of this chat room | Chat room |
      | Description | Chat description |
    And I click on "Edit settings" "link" in the "Administration" "block"

  @javascript
  Scenario: Forced group mode using separate groups
    Given I set the following fields to these values:
      | Group mode | Separate groups |
      | Force group mode | Yes |
    When I press "Save and display"
    Then "//a/child::img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" should not exist
    And "//img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" should not exist

  @javascript
  Scenario: Forced group mode using visible groups
    Given I set the following fields to these values:
      | Group mode | Visible groups |
      | Force group mode | Yes |
    And I press "Save and display"
    Then "//a/child::img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" should not exist
    And "//img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" should not exist

  @javascript
  Scenario: Forced group mode without groups
    Given I set the following fields to these values:
      | Group mode | No groups |
      | Force group mode | Yes |
    And I press "Save and display"
    Then "//a/child::img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" should not exist
    And "//img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" should not exist


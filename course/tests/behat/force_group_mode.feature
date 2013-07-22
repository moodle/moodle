@core @core_course @_cross_browser
Feature: Force group mode in a course
  In order to use the same group mode all over the course
  As a teacher
  I need to force the group mode of all course's activities

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Chat" to section "1" and I fill the form with:
      | Name of this chat room | Chat room |
      | Description | Chat description |
    And I follow "Edit settings"

  @javascript
  Scenario: Forced group mode using separate groups
    Given I fill the moodle form with:
      | Group mode | Separate groups |
      | Force group mode | Yes |
    When I press "Save changes"
    Then "//a/child::img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" should not exists
    And "//img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" should exists
    And I click on "//img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" in the "li.activity.chat" "css_element"
    And "//a/child::img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" should not exists
    And "//img[contains(@alt, 'Separate groups (forced mode)')]" "xpath_element" should exists

  @javascript
  Scenario: Forced group mode using visible groups
    Given I fill the moodle form with:
      | Group mode | Visible groups |
      | Force group mode | Yes |
    And I press "Save changes"
    Then "//a/child::img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" should not exists
    And "//img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" should exists
    And I click on "//img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" in the "li.activity.chat" "css_element"
    And "//a/child::img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" should not exists
    And "//img[contains(@alt, 'Visible groups (forced mode)')]" "xpath_element" should exists

  @javascript
  Scenario: Forced group mode without groups
    Given I fill the moodle form with:
      | Group mode | No groups |
      | Force group mode | Yes |
    And I press "Save changes"
    Then "//a/child::img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" should not exists
    And "//img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" should exists
    And I click on "//img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" in the "li.activity.chat" "css_element"
    And "//a/child::img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" should not exists
    And "//img[contains(@alt, 'No groups (forced mode)')]" "xpath_element" should exists


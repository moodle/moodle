@block @block_social_activities
Feature: Edit activities in social activities block
  In order to use social activities block
  As a teacher
  I need to add and edit activities there

  @javascript
  Scenario: Edit name of acitivity in-place in social activities block
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | social |
    And the following "users" exist:
      | username | firstname | lastname |
      | user1 | User | One |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | editingteacher |
    Given I log in as "user1"
    And I follow "Course 1"
    And I turn editing mode on
    And I set the field "Add an activity to section 'section 0'" to "Forum"
    And I set the field "Forum name" to "My forum name"
    And I press "Save and return to course"
    And I click on "Edit title" "link" in the "//div[contains(@class,'block_social_activities')]//li[contains(.,'My forum name')]" "xpath_element"
    And I set the field "New name for activity My forum name" to "New forum name"
    And I press key "13" in the field "New name for activity My forum name"
    Then I should not see "My forum name" in the "Social activities" "block"
    And I should see "New forum name"
    And I follow "New forum name"
    And I should not see "My forum name"
    And I should see "New forum name"

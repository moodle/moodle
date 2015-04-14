@core @core_course
Feature: Restrict activities availability
  In order to prevent the use of some activities
  As an admin
  I need to control which activities can be used in courses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: Activities can be added with the default permissions
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
    And I add a "Chat" to section "1" and I fill the form with:
      | Name of this chat room | Test chat name |
      | Description | Test chat description |
    Then I should see "Test glossary name"
    And I should see "Test chat name"

  @javascript
  Scenario: Activities can not be added when the admin restricts the permissions
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | mod/chat:addinstance | Prohibit |
    And I am on site homepage
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Permissions"
    And I override the system permissions of "Teacher" role with:
      | mod/glossary:addinstance | Prohibit |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I turn editing mode on
    And I follow "Activity chooser off"
    Then the "Add an activity to section 'Topic 1'" select box should not contain "Chat"
    Then the "Add an activity to section 'Topic 1'" select box should not contain "Glossary"

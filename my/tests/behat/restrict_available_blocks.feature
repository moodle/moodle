@core @core_my
Feature: Restrict which blocks can be added to Dashboard
  In order to restrict which blocks can be added
  As a student I need to ensure I can add the blocks
  As an admin I need to remove the capability to add a blocks
  As a student I need to ensure I can't add the blocks any more

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |

  Scenario: The comments block can be added to Dashboard by default
    And I log in as "student1"
    And I turn editing mode on
    Then the add block selector should contain "Comments" block
    And the add block selector should contain "Text" block
    And the add block selector should contain "Tags" block

  Scenario: Remove the ability to add the comments block to Dashboard
    Given the following "role capability" exists:
      | role                            | user     |
      | block/comments:myaddinstance    | prohibit |
      | block/course_list:myaddinstance | prohibit |
      | block/html:myaddinstance        | prohibit |
    When I log in as "student1"
    And I turn editing mode on
    Then the add block selector should not contain "Comments" block
    And the add block selector should not contain "Courses" block
    And the add block selector should not contain "Text" block
    And the add block selector should contain "Tags" block

@core @core_my
Feature: Restrict which blocks can be added to My home
  In order to restrict which blocks can be added
  As a student I need to ensure I can add the blocks
  As an admin I need to remove the capability to add a blocks
  As a student I need to ensure I can't add the blocks any more

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |

  Scenario: The comments block can be added to My home by default
    And I log in as "student1"
    And I click on "My home" "link" in the "Navigation" "block"
    And I press "Customise this page"
    Then the "Add a block" select box should contain "Comments"
    And the "Add a block" select box should contain "Courses"
    And the "Add a block" select box should contain "HTML"
    And the "Add a block" select box should contain "Tags"

  @javascript
  Scenario: Remove the ability to add the comments block to My home
    When I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | block/comments:myaddinstance | Prohibit |
      | block/course_list:myaddinstance | Prohibit |
      | block/html:myaddinstance | Prohibit |
    And I log out
    And I log in as "student1"
    And I click on "My home" "link" in the "Navigation" "block"
    And I press "Customise this page"
    Then the "Add a block" select box should not contain "Comments"
    And the "Add a block" select box should not contain "Courses"
    And the "Add a block" select box should not contain "HTML"
    And the "Add a block" select box should contain "Tags"
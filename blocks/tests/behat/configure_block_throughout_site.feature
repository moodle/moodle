@core @core_block
Feature: Add and configure blocks throughout the site
  In order to maintain some patterns across all the site
  As a manager
  I need to set and configure blocks throughout the site

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
      | teacher1 | teacher | 1 | teacher@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "system role assigns" exist:
      | user | course | role |
      | manager1 | Acceptance test site | manager |
    # Allow at least one role assignment in the block context:
    And I log in as "admin"
    And I navigate to "Define roles" node in "Site administration > Users > Permissions"
    And I follow "Edit Non-editing teacher role"
    And I set the following fields to these values:
      | Block | 1 |
    And I press "Save changes"
    And I log out

  Scenario: Add and configure a block throughtout the site
    Given I log in as "manager1"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Comments" block
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    When I follow "Course 1"
    Then I should see "Comments" in the "Comments" "block"
    And I should see "Save comment" in the "Comments" "block"
    And I am on site homepage
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Default weight | -10 (first) |
    And I press "Save changes"
    And I follow "Course 1"
    # The first block matching the pattern should be top-left block
    And I should see "Comments" in the "//*[@id='region-pre' or @id='block-region-side-pre']/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' block ')]" "xpath_element"

  Scenario: Blocks on the dashboard page can have roles assigned to them
    Given I log in as "manager1"
    And I click on "Dashboard" "link" in the "Navigation" "block"
    When I press "Customise this page"
    Then I should see "Assign roles in Navigation block"

  Scenario: Blocks on courses can have roles assigned to them
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Turn editing on"
    Then I should see "Assign roles in Search forums block"

  @javascript
  Scenario: Blocks can safely be customised
    Given I log in as "admin"
    And I click on "Dashboard" "link" in the "Navigation" "block"
    And I press "Customise this page"
    And I add the "HTML" block
    And I configure the "(new HTML block)" block
    And I set the following fields to these values:
      | Block title | Foo " onload="document.getElementsByTagName('body')[0].remove()" alt="
      | Content     | Example
    When I press "Save changes"
    Then I should see "Course overview"

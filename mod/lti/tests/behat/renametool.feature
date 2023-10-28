@mod @mod_lti
Feature: Rename external tools via inline editing
  In order to keep track of my activities
  As a teacher
  I need to be able to rename the LTI tool and have it's name change in the gradebook

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "activities" exist:
      | activity | course | name                 |
      | lti      | C1     | Test tool activity 1 |

  @javascript
  Scenario: Add a tool and inline edit
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I set the field "Edit title" in the "Test tool activity 1" "activity" to "Test tool activity renamed"
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    Then I should not see "Test tool activity 1"
    And I should see "Test tool activity renamed"

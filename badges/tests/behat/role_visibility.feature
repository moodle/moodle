@core @core_badges
Feature: Test role visibility for the badge administration page
  In order to control access
  As an admin
  I need to control which roles can see each other

  Background: Add a bunch of users
    Given  the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | C1     | editingteacher |
      | manager1 | C1     | manager        |

  @javascript @_file_upload
  Scenario: Check the default roles are visible
    Given I log in as "manager1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    Then I should see "Teacher"
    And I should see "Manager"

  @javascript @_file_upload
  Scenario: Check hidden roles are not visible
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the following fields to these values:
      | Add badge criteria | Manual issue by role |
    Then I should see "Teacher"
    And I should not see "Manager"

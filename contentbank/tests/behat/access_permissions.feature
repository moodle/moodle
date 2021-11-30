@core @core_contentbank
Feature: Access permission to content Bank
  In order to control access to content bank
  As an admin
  I need to be able to configure users' permissions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | teacher1 | Teacher   | 1        | user1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Admins access content bank
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    Then "Content bank" "link" should exist

  Scenario: Editing teachers can access content bank at course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    Then "Content bank" "link" should exist

  Scenario: Editing teachers can't access content bank at system level
    Given I log in as "teacher1"
    Then "Content bank" "link" should not exist

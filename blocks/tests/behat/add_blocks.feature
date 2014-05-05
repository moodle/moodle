@core @core_block
Feature: Add blocks
  In order to add more functionality to pages
  As a teacher
  I need to add blocks to pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    When I add the "Blog menu" block
    Then I should see "View my entries about this course"

  @javascript
  Scenario: Add a block to a course with Javascript enabled

  Scenario: Add a block to a course with Javascript disabled

@mod @mod_forum
Feature: Add forum activities and discussions
  In order to discuss topics with other users
  As a teacher
  I need to add forum activities to moodle courses

  @javascript
  Scenario: Add a forum and a discussion
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
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
    When I add a new discussion to "Test forum name" forum with:
      | Subject | Forum post 1 |
      | Message | This is the body |
    And I wait "6" seconds
    Then I should see "Test forum name"

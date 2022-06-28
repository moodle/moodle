@mod @mod_moodleoverflow @javascript
Feature: Add moodleoverflow activities and discussions
  In order to discuss topics with other users
  As a teacher
  I need to add forum activities to moodle courses

  Scenario: Add a moodleoverflow and a discussion
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Moodleoverflow" to section "1" and I fill the form with:
      | Moodleoverflow name | Test moodleoverflow name |
      | Description | Test forum description |
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Forum post 1 |
      | Message | This is the body |
    And I log out

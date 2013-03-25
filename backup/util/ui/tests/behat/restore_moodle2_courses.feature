@backup
Feature: Restore Moodle 2 course backups
  In order to continue using my stored course contents
  As a moodle teacher and as a moodle admin
  I need to restore them inside other Moodle courses or in new courses

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I add the "Community finder" block

  @javascript
  Scenario: Restore a course in another existing course
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
    Then I should see "Course 2"
    And I should see "Community finder"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a course in a new course
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Course name | Course 1 restored in a new course |
    Then I should see "Course 1 restored in a new course"
    And I should see "Community finder"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a backup into the same course
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I merge "test_backup.mbz" backup into the current course using this options:
      | setting_section_section_5_included | 0 |
      | setting_section_section_5_userinfo | 0 |
    Then I should see "Course 1"
    And I should not see "Section 3"
    And I should see "Community finder"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a backup into the same course removing it's contents before that
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I follow "Course 1"
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum post backup name |
      | Description | Test forum post backup description |
    And I follow "Restore"
    And I merge "test_backup.mbz" backup into the current course after deleting it's contents using this options:
      | setting_section_section_5_userinfo | 0 |
      | setting_section_section_5_included | 0 |
    Then I should see "Course 1"
    And I should not see "Section 3"
    And I should not see "Test forum post backup name"
    And I should see "Community finder"
    And I should see "Test forum name"

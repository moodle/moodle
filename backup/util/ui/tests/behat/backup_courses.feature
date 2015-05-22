@core @core_backup
Feature: Backup Moodle courses
  In order to save and store course contents
  As an admin
  I need to create backups of courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | numsections |
      | Course 1 | C1 | 0 | 10 |
      | Course 2 | C2 | 0 | 2 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | section |
      | assign | C2 | assign1 | Test assign | Assign description | 1 |
      | data | C2 | data1 | Test data | Database description | 2 |
    And I log in as "admin"

  @javascript
  Scenario: Backup a course providing options
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    Then I should see "Restore"
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I should see "URL of backup"
    And I should see "Anonymize user information"

  @javascript
  Scenario: Backup a course with default options
    When I backup "Course 1" course using this options:
      | Initial | Include calendar events | 0 |
      | Initial | Include course logs | 1 |
      | Schema | Topic 5 | 0 |
      | Confirmation | Filename | test_backup.mbz |
    Then I should see "Restore"
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I should not see "Section 3"
    And I press "Continue"
    And I click on "Continue" "button" in the ".bcs-current-course" "css_element"
    And "//div[contains(concat(' ', normalize-space(@class), ' '), ' fitem ')][contains(., 'Include calendar events')]/descendant::img" "xpath_element" should exist
    And "Include course logs" "checkbox" should exist
    And I press "Next"

  @javascript
  Scenario: Backup a course without blocks
    When I backup "Course 1" course using this options:
      | 1 | setting_root_blocks | 0 |
    Then I should see "Course backup area"

  @javascript
  Scenario: Backup selecting just one section
    When I backup "Course 2" course using this options:
      | Schema | Test data | 0 |
      | Schema | Topic 2 | 0 |
      | Confirmation | Filename | test_backup.mbz |
    Then I should see "Course backup area"
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I should not see "Section 2"
    And I press "Continue"
    And I click on "Continue" "button" in the ".bcs-current-course" "css_element"
    And I press "Next"
    And I should see "Test assign"
    And I should not see "Test data"

  @javascript
  Scenario: Backup a course using the one click backup button
    When I perform a quick backup of course "Course 2"
    Then I should see "Restore course"
    And I should see "Course backup area"
    And I should see "backup-moodle2-course-"

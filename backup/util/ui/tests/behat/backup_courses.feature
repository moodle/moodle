@core @core_backup
Feature: Backup Moodle courses
  In order to save and store course contents
  As an admin
  I need to create backups of courses

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category | numsections |
      | Course 1 | C1 | 0 | 10 |
      | Course 2 | C2 | 0 | 2 |
    And the following "activities" exists:
      | activity | course | idnumber | name | intro | section |
      | assign | C2 | assign1 | Test assign | Assign description | 1 |
      | data | C2 | data1 | Test data | Database description | 2 |
    And I log in as "admin"

  @javascript
  Scenario: Backup a course providing options
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    Then I should see "Restore"
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I should see "URL of backup"
    And I should see "Anonymize user information"

  @javascript
  Scenario: Backup a course with default options
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
      | Include calendar events | 0 |
      | Include course logs | 1 |
      | setting_section_section_5_userinfo | 0 |
      | setting_section_section_5_included | 0 |
    Then I should see "Restore"
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I should not see "Section 3"
    And I press "Continue"
    And I click on "Continue" "button" in the ".bcs-current-course" "css_element"
    And "//div[contains(concat(' ', normalize-space(@class), ' '), ' fitem ')][contains(., 'Include calendar events')]/descendant::img" "xpath_element" should exists
    And I check "Include course logs"
    And I press "Next"

  @javascript
  Scenario: Backup a course without blocks
    When I backup "Course 1" course using this options:
      | id_setting_root_blocks | 0 |
    Then I should see "Course backup area"

  @javascript
  Scenario: Backup selecting just one section
    When I backup "Course 2" course using this options:
      | Filename | test_backup.mbz |
      | setting_section_section_2_userinfo | 0 |
      | setting_section_section_2_included | 0 |
      | setting_section_section_4_userinfo | 0 |
      | setting_section_section_4_included | 0 |
    Then I should see "Course backup area"
    And I click on "Restore" "link" in the "test_backup.mbz" "table_row"
    And I should not see "Section 2"
    And I press "Continue"
    And I click on "Continue" "button" in the ".bcs-current-course" "css_element"
    And I press "Next"
    And I should see "Test assign"
    And I should not see "Test data"

@core @core_backup
Feature: Restore Moodle 2 course backups
  In order to continue using my stored course contents
  As a teacher and an admin
  I need to restore them inside other Moodle courses or in new courses

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category | format | numsections | coursedisplay |
      | Course 1 | C1 | 0 | topics | 15 | 1 |
      | Course 2 | C2 | 0 | topics | 5 | 0 |
      | Course 3 | C3 | 0 | topics | 2 | 0 |
    And the following "activities" exists:
      | activity | course | idnumber | name | intro | section |
      | assign | C3 | assign1 | Test assign name | Assign description | 1 |
      | data | C3 | data1 | Test database name | Database description | 2 |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I add the "Community finder" block

  @javascript
  Scenario: Restore a course in another existing course
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
    Then I should see "Course 2"
    And I should see "Community finder" in the "Community finder" "block"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a course in a new course
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Course name | Course 1 restored in a new course |
    Then I should see "Course 1 restored in a new course"
    And I should see "Community finder" in the "Community finder" "block"
    And I should see "Test forum name"
    And I follow "Edit settings"
    And I expand all fieldsets
    And the "id_format" field should match "Topics format" value
    And the "Number of sections" field should match "15" value
    And the "Course layout" field should match "Show one section per page" value
    And I press "Cancel"

  @javascript
  Scenario: Restore a backup into the same course
    When I backup "Course 3" course using this options:
      | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
      | setting_section_section_3_included | 0 |
      | setting_section_section_3_userinfo | 0 |
      | setting_section_section_5_included | 0 |
      | setting_section_section_5_userinfo | 0 |
    Then I should see "Course 2"
    And I should see "Test assign name"
    And I should not see "Test database name"

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
    And I should see "Community finder" in the "Community finder" "block"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a backup into a new course changing the course format afterwards
    Given I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
    Then I should see "Topic 1"
    And I should see "Test forum name"
    And I follow "Edit settings"
    And I expand all fieldsets
    And the "id_format" field should match "Topics format" value
    And I fill the moodle form with:
      | id_startdate_day | 1 |
      | id_startdate_month | January |
      | id_startdate_year | 2020 |
      | id_format | Weekly format |
    And I press "Save changes"
    And I should see "1 January - 7 January"
    And I should see "Test forum name"
    And I follow "Edit settings"
    And I expand all fieldsets
    And the "id_format" field should match "Weekly format" value
    And I fill the moodle form with:
      | id_format | Social format |
    And I press "Save changes"
    And I should see "An open forum for chatting about anything you want to"
    And I follow "Edit settings"
    And I expand all fieldsets
    And the "id_format" field should match "Social format" value
    And I fill the moodle form with:
      | id_format | SCORM format |
    And I press "Save changes"
    And I should see "Adding a new SCORM package"
    And I follow "Edit settings"
    And I expand all fieldsets
    And the "id_format" field should match "SCORM format" value
    And I press "Cancel"

  @javascript
  Scenario: Restore a backup in an existing course retaining the backup course settings
    Given I add a "URL" to section "3" and I fill the form with:
      | Name | Test URL name |
      | Description | Test URL description |
      | id_externalurl | http://www.moodle.org |
    And I hide section "3"
    And I hide section "7"
    When I backup "Course 1" course using this options:
      | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
      | Overwrite course configuration | Yes |
    And I follow "Edit settings"
    And I expand all fieldsets
    Then the "id_format" field should match "Topics format" value
    And the "Number of sections" field should match "15" value
    And the "Course layout" field should match "Show one section per page" value
    And I press "Cancel"
    And section "3" should be hidden
    And section "7" should be hidden
    And section "15" should be visible
    And I should see "Test URL name" in the "#section-3" "css_element"
    And I should see "Test forum name" in the "#section-1" "css_element"

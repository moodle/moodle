@core @core_backup
Feature: Schema form selectors
  In order to quickly select schema elements
  As an admin
  I need to use the selectors UI to toggle selection of schema elements

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 2           | 1            |
    And the following "activities" exist:
      | activity | course | idnumber | name          | intro                | section |
      | assign   | C1     | assign1  | Test assign 1 | Assign description   | 1       |
      | data     | C1     | data1    | Test data 1   | Database description | 1       |
      | assign   | C1     | assign2  | Test assign 2 | Assign description   | 2       |
      | data     | C1     | data2    | Test data 2   | Database description | 2       |
    And I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Course reuse" in current page administration
    And I follow "Backup"
    And I click on "Next" "button" in the "page-content" "region"

  @javascript
  Scenario: Select all and none should toggle backup schema checkboxes
    Given the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test select none.
    When I click on "None" "link" in the "backup_selectors_included" "region"
    Then the field "Section 1" matches value ""
    And the field "Test assign 1" matches value ""
    And the field "Test data 1" matches value ""
    And the field "Section 2" matches value ""
    And the field "Test assign 2" matches value ""
    And the field "Test data 2" matches value ""
    And the "Section 1: User data" "checkbox" should be disabled
    And the "Include Test assign 1 user data" "checkbox" should be disabled
    And the "Include Test data 1 user data" "checkbox" should be disabled
    And the "Section 2: User data" "checkbox" should be disabled
    And the "Include Test assign 2 user data" "checkbox" should be disabled
    And the "Include Test data 2 user data" "checkbox" should be disabled
    # Test select all.
    And I click on "All" "link" in the "backup_selectors_included" "region"
    And the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled

  @javascript
  Scenario: The type options panell allow to select all and none of one activity type
    Given the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test select none assignment.
    When I click on "Show type options" "link" in the "backup_selectors_included" "region"
    And I click on "None" "link" in the "backup_selectors_mod_assign" "region"
    Then the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value ""
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value ""
    And the field "Test data 2" matches value "1"
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be disabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be disabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test select all assignments.
    And I click on "All" "link" in the "backup_selectors_mod_assign" "region"
    And the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled

  @javascript
  Scenario: Select all or none in user data should toggle backup schema checkboxes
    Given the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the field "Section 1: User data" matches value "1"
    And the field "Include Test assign 1 user data" matches value "1"
    And the field "Include Test data 1 user data" matches value "1"
    And the field "Section 2: User data" matches value "1"
    And the field "Include Test assign 2 user data" matches value "1"
    And the field "Include Test data 2 user data" matches value "1"
    # Test select none.
    When I click on "None" "link" in the "backup_selectors_userdata" "region"
    Then the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the field "Section 1: User data" matches value ""
    And the field "Include Test assign 1 user data" matches value ""
    And the field "Include Test data 1 user data" matches value ""
    And the field "Section 2: User data" matches value ""
    And the field "Include Test assign 2 user data" matches value ""
    And the field "Include Test data 2 user data" matches value ""
    # Test select all.
    And I click on "All" "link" in the "backup_selectors_userdata" "region"
    And the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the field "Section 1: User data" matches value "1"
    And the field "Include Test assign 1 user data" matches value "1"
    And the field "Include Test data 1 user data" matches value "1"
    And the field "Section 2: User data" matches value "1"
    And the field "Include Test assign 2 user data" matches value "1"
    And the field "Include Test data 2 user data" matches value "1"

  @javascript
  Scenario: The type options panell allow to select all and none user data for an activity type
    Given the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the field "Section 1: User data" matches value "1"
    And the field "Include Test assign 1 user data" matches value "1"
    And the field "Include Test data 1 user data" matches value "1"
    And the field "Section 2: User data" matches value "1"
    And the field "Include Test assign 2 user data" matches value "1"
    And the field "Include Test data 2 user data" matches value "1"
    # Test select none assignment.
    When I click on "Show type options" "link" in the "backup_selectors_included" "region"
    And I click on "None" "link" in the "backup_selectors_userdata-mod_assign" "region"
    Then the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the field "Section 1: User data" matches value "1"
    And the field "Include Test assign 1 user data" matches value ""
    And the field "Include Test data 1 user data" matches value "1"
    And the field "Section 2: User data" matches value "1"
    And the field "Include Test assign 2 user data" matches value ""
    And the field "Include Test data 2 user data" matches value "1"
    # Test select all assignments.
    And I click on "All" "link" in the "backup_selectors_userdata-mod_assign" "region"
    And the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the field "Section 1: User data" matches value "1"
    And the field "Include Test assign 1 user data" matches value "1"
    And the field "Include Test data 1 user data" matches value "1"
    And the field "Section 2: User data" matches value "1"
    And the field "Include Test assign 2 user data" matches value "1"
    And the field "Include Test data 2 user data" matches value "1"

  @javascript
  Scenario: Select or unselect a section schema disable the activities checkboxes
    Given the field "Section 1" matches value "1"
    And the field "Test assign 1" matches value "1"
    And the field "Test data 1" matches value "1"
    And the field "Section 2" matches value "1"
    And the field "Test assign 2" matches value "1"
    And the field "Test data 2" matches value "1"
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test unselect section 1.
    When I set the field "Section 1" to ""
    Then the field "Section 1" matches value ""
    And the "Test assign 1" "checkbox" should be disabled
    And the "Test data 1" "checkbox" should be disabled
    And the "Section 2" "checkbox" should be enabled
    And the "Test assign 2" "checkbox" should be enabled
    And the "Test data 2" "checkbox" should be enabled
    And the "Section 1: User data" "checkbox" should be disabled
    And the "Include Test assign 1 user data" "checkbox" should be disabled
    And the "Include Test data 1 user data" "checkbox" should be disabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test select section 1.
    And I set the field "Section 1" to "1"
    And the field "Section 1" matches value "1"
    And the "Test assign 1" "checkbox" should be enabled
    And the "Test data 1" "checkbox" should be enabled
    And the "Section 2" "checkbox" should be enabled
    And the "Test assign 2" "checkbox" should be enabled
    And the "Test data 2" "checkbox" should be enabled
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled

  @javascript
  Scenario: Select or unselect a section user data disable the activities checkboxes
    Given the "Section 1" "checkbox" should be enabled
    And the "Test assign 1" "checkbox" should be enabled
    And the "Test data 1" "checkbox" should be enabled
    And the "Section 2" "checkbox" should be enabled
    And the "Test assign 2" "checkbox" should be enabled
    And the "Test data 2" "checkbox" should be enabled
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test unselect section 1.
    When I set the field "Section 1: User data" to ""
    Then the "Section 1" "checkbox" should be enabled
    And the "Test assign 1" "checkbox" should be enabled
    And the "Test data 1" "checkbox" should be enabled
    And the "Section 2" "checkbox" should be enabled
    And the "Test assign 2" "checkbox" should be enabled
    And the "Test data 2" "checkbox" should be enabled
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be disabled
    And the "Include Test data 1 user data" "checkbox" should be disabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled
    # Test select section 1.
    And I set the field "Section 1: User data" to "1"
    And the "Section 1" "checkbox" should be enabled
    And the "Test assign 1" "checkbox" should be enabled
    And the "Test data 1" "checkbox" should be enabled
    And the "Section 2" "checkbox" should be enabled
    And the "Test assign 2" "checkbox" should be enabled
    And the "Test data 2" "checkbox" should be enabled
    And the "Section 1: User data" "checkbox" should be enabled
    And the "Include Test assign 1 user data" "checkbox" should be enabled
    And the "Include Test data 1 user data" "checkbox" should be enabled
    And the "Section 2: User data" "checkbox" should be enabled
    And the "Include Test assign 2 user data" "checkbox" should be enabled
    And the "Include Test data 2 user data" "checkbox" should be enabled

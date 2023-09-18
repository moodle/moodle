@core @core_backup @core_h5p @mod_h5pactivity @_switch_iframe @javascript
Feature: Backup xAPI states
  In order to save and restore xAPI states
  As an admin
  I need to create backups with xAPI states and restore them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity        | h5pactivity                             |
      | course          | C1                                      |
      | name            | Awesome H5P package                     |
      | packagefilepath | h5p/tests/fixtures/filltheblanks.h5p    |
    # Save state for the student user.
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I switch to the main frame
    And I am on the "Course 1" course page
    And I am on the "Awesome H5P package" "h5pactivity activity" page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" matches value "Narnia"
    And I log out

  Scenario: Content state is backup/restored when user data is included
    # Backup and restore the course.
    Given I log in as "admin"
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Settings | Include enrolled users | 1        |
      | Schema   | User data              | 1        |
      | Schema   | Course name            | Course 2 |
      | Schema   | Course short name      | C2       |
    # Login as student and confirm xAPI state has been restored.
    When I am on the "Course 2" course page logged in as student1
    And I click on "Awesome H5P package" "link" in the "region-main" "region"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" matches value "Narnia"

  Scenario: Content state is not restored when user data is not included in the backup
    # Backup course without user data and then restore it.
    When I log in as "admin"
    And I backup "Course 1" course using this options:
      | Initial      | Include enrolled users | 0               |
      | Confirmation | Filename               | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema   | Course name            | Course 2 |
      | Schema   | Course short name      | C2       |
    # Enrol student to the new course.
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C2     | student        |
    # Login as student and confirm xAPI state hasn't been restored.
    And I am on the "Course 2" course page logged in as student1
    And I click on "Awesome H5P package" "link" in the "region-main" "region"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"

  Scenario: Content state is not restored when user data is included in the backup but xAPI state is not restored
    # Backup with user data and restore it without user data the course.
    Given I log in as "admin"
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Settings | Include user's state in content such as H5P activities  | 0        |
      | Schema   | Course name                                             | Course 2 |
      | Schema   | Course short name                                       | C2       |
    # Login as student and confirm xAPI state hasn't been restored.
    When I am on the "Course 2" course page logged in as student1
    And I click on "Awesome H5P package" "link" in the "region-main" "region"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"

  Scenario: Content state is not restored when it is not included explicitly in the backup
    # Backup course with user data but without xAPI state and then restore it.
    When I log in as "admin"
    And I backup "Course 1" course using this options:
      | Initial      | Include user's state in content such as H5P activities  | 0               |
      | Confirmation | Filename                                                | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema   | Course name            | Course 2 |
      | Schema   | Course short name      | C2       |
    And I should see "Awesome H5P package"
    # Login as student and confirm xAPI state hasn't been restored.
    And I am on the "Course 2" course page logged in as student1
    And I click on "Awesome H5P package" "link" in the "region-main" "region"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"

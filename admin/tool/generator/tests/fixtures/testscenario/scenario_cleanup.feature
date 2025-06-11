Feature: Fixture to test cleanup of a testing scenario

  Scenario: Create course content to cleanup later
    Given the following config values are set as admin:
      | sendcoursewelcomemessage | 0 | enrol_manual |
    And the following "course" exists:
      | fullname         | Course cleanup |
      | shortname        | Cleanup        |
      | category         | 0              |
      | numsections      | 3              |
      | initsections     | 1              |
    Given the following "users" exist:
      | username     | firstname | lastname | email                     |
      | cleanteacher | Teacher   | Test1    | samplecleanup@example.com |
    And the following "course enrolments" exist:
      | user         | course  | role           |
      | cleanteacher | Cleanup | editingteacher |

  @cleanup
  Scenario: remove fixture to test cleanup of a testing scenario
    Given the course "Course cleanup" is deleted
    And the user "cleanteacher" is deleted

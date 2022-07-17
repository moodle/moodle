@local @local_codechecker
Feature: Codechecker UI works as expected
  In order to verify coding style
  As an admin
  I need to be able to use codechecker UI with success

  Scenario Outline: Verify that specified paths are checked
    Given I log in as "admin"
    And I navigate to "Development > Code checker" in site administration
    And I set the field "Path(s) to check" to "<path>"
    When I press "Check code"
    Then I should see "<seen>"
    And I should not see "<notseen>"
    And I log out

    Examples:
      | path                                                                     | seen                               | notseen        |
      | index.php                                                                | Files found: 1                     | Invalid path   |
      | index2.php                                                               | Invalid path index2.php            | Files found: 1 |
      | local/codechecker/version.php                                            | Well done!                         | Invalid path   |
      | local/codechecker/tests/                                                 | local_codechecker_testcase.php     | Invalid path   |
      | local/codechecker/tests/                                                 | Files found: 6                     | Invalid path   |
      | local/codechecker/tests/                                                 | Well done!                         | Invalid path   |
      | local/codechecker/moodle/tests/fixtures/files/moodleinternal/problem.php | Files found: 1                     | Invalid path   |
      | local/codechecker/moodle/tests/fixtures/files/moodleinternal/problem.php | Total: 2 error(s) and 1 warning(s) | Well done!     |
      | local/codechecker/moodle/tests/fixtures/files/moodleinternal/problem.php | Inline comments must end           | Well done!     |
      | local/codechecker/moodle/tests/fixtures/files/moodleinternal/problem.php | Expected MOODLE_INTERNAL check     | Well done!     |

  Scenario Outline: Verify that specified exclusions are performed
    Given I log in as "admin"
    And I navigate to "Development > Code checker" in site administration
    And I set the field "Path(s) to check" to "<path>"
    And I set the field "Exclude" to "<exclude>"
    When I press "Check code"
    Then I should see "<seen>"
    And I should not see "<notseen>"
    And I log out

    Examples:
      | path                            | exclude            | seen                          | notseen      |
      | local/codechecker/moodle/tests  | */tests/fixtures/* | Files found: 8                | Invalid path |
      | local/codechecker/moodle/tests  | */tests/fixtures/* | moodlestandard_test.php       | Invalid path |
      | local/codechecker/moodle/tests/ | *PHPC*, *moodle_*  | Files found: 70               | Invalid path |
      | local/codechecker/moodle/tests/ | *PHPC*, *moodle_*  | Line 1 of the opening comment | moodle_php   |
      | local/codechecker/moodle/tests/ | *PHPC*, *moodle_*  | Inline comments must end      | /phpcompat   |
      | local/codechecker/moodle/tests/ | *PHPC*, *moodle_*  | Inline comments must end      | /phpcompat   |
      | local/codechecker/moodle/tests/ | *moodle_*          | fixtures/phpcompat            | /moodle_php  |

  # We use the @javascript tag here because of MDL-53083, causing non-javascript to fail unchecking checkboxes
  @javascript
  Scenario: Verify that the warnings toggle has effect
    Given I log in as "admin"
    And I navigate to "Development > Code checker" in site administration
    And I set the field "Path(s) to check" to "local/codechecker/moodle/tests/fixtures/squiz_php_commentedoutcode.php"
    And I set the field "Exclude" to "dont_exclude_anything"
    # Warnings enabled
    And I set the field "Include warnings" to "1"
    When I press "Check code"
    Then I should see "Inline comments must start"
    And I should see "is this commented out code"
    And I should not see "0 warning(s)"
    # Warnings disabled
    And I set the field "Include warnings" to ""
    And I press "Check code"
    And I should see "0 warning(s)"
    And I should not see "Inline comments must start"
    And I should not see "is this commented out code"
    And I log out

  Scenario: Verify that multiple paths work
    Given I log in as "admin"
    And I navigate to "Development > Code checker" in site administration
    And I set the field "Path(s) to check" to "local/codechecker/version.php\nlocal/codechecker/index.php"
    When I press "Check code"
    Then I should see "index.php"
    And I should see "version.php"

  Scenario: Optionally output PHPCS standard
    Given I log in as "admin"
    And I navigate to "Development > Code checker" in site administration
    And I set the field "Path(s) to check" to "local/codechecker/moodle/tests/fixtures/files/moodleinternal/problem.php"
    And I set the field "Display phpcs standard associated with a problem" to "1"
    When I press "Check code"
    Then I should see "moodle.Files.BoilerplateComment.WrongWhitespace"
    And I set the field "Display phpcs standard associated with a problem" to "0"
    And I press "Check code"
    And I should not see "moodle.Files.BoilerplateComment.WrongWhitespace"
    And I log out

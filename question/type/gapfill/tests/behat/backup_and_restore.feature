@qtype @qtype_gapfill @qtype_gapfill_backup_and_restore
Feature: Testing backup_and_restore in qtype_gapfill
    As a teacher
    In order re-use my courses containing Gapfill questions
    I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
          | fullname | shortname | category |
          | Course 1 | C1        | 0        |
    And the following "question categories" exist:
          | contextlevel | reference | name           |
          | Course       | C1        | Test questions |
    And the following "questions" exist:
          | questioncategory | qtype   | name        | questiontext               |
          | Test questions   | gapfill | Gapfill-001 | The [cat] sat on the [mat] |

    And the following "activities" exist:
          | activity | name      | course | idnumber |
          | quiz     | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
          | Gapfill-001 | 1 |
    And the following config values are set as admin:
          | enableasyncbackup | 0 |

  @javascript
  Scenario: Backup and restore a course containing a Gapfill question
    When I am on the "Course 1" course page logged in as admin

    And I backup "Course 1" course using this options:
          | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
          | Schema | Course name       | Course 2 |
          | Schema | Course short name | C2       |

    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "Gapfill-001" in the question bank
    And I click on "Close course index" "button"

    And the field "Question name" matches value "Gapfill-001"
    And the field "Question text" matches value "The [cat] sat on the [mat]"
    #The values for these feields are added in the helper.php file.
    And the field "For any correct response" matches value "Correct feedback"
    And the field "For any incorrect response" matches value "Incorrect feedback"
    And the field "General feedback" matches value "General feedback"


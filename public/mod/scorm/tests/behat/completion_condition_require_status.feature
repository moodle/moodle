@mod @mod_scorm @_file_upload @_switch_iframe
Feature: Scorm multi-sco completion
  In order to let students access a scorm package
  As a teacher
  I need to add scorm activity to a course
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | enablecompletion | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario: Test completion with a single sco completion.
    Given the following "activity" exists:
      | activity                 | scorm                                                    |
      | course                   | C1                                                       |
      | name                     | Basic Multi-sco SCORM package                            |
      | completion               | 2                                                        |
      # Add requirements
      | completionstatusallscos  | 0                                                        |
      | packagefilepath          | mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip |
      | completionstatusrequired | 4                                                        |
    And I am on the "Basic Multi-sco SCORM package" "scorm activity" page logged in as student1
    And I should see "Enter"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    Then I should see "Basic Multi-sco SCORM package" in the "page" "region"
    And I am on homepage
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    Then "Student 1" user has completed "Basic Multi-sco SCORM package" activity

  @javascript
  Scenario: Test completion with all scos and correct sco load on re-entry.
    Given the following "activity" exists:
      | activity                | scorm                                                    |
      | course                  | C1                                                       |
      | name                    | ADV Multi-sco SCORM package                              |
      | completion              | 2                                                        |
      # Add requirements
      | packagefilepath         | mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip |
      | completionstatusallscos | 1                                                        |
    And I am on the "ADV Multi-sco SCORM package" "scorm activity" page logged in as student1
    And I should see "Enter"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    Then I should see "ADV Multi-sco SCORM package" in the "page" "region"
    And I am on homepage
    And I log out

    And I am on the "Course 1" course page logged in as teacher1
    Then "Student 1" user has not completed "ADV Multi-sco SCORM package" activity
    And I log out
    And I am on the "ADV Multi-sco SCORM package" "scorm activity" page logged in as student1
    And I should see "Enter"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Par"

    And I switch to the main frame
    And I click on "Keeping Score" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Scoring"

    And I switch to the main frame
    And I click on "Other Scoring Systems" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Other Scoring Systems"

    And I switch to the main frame
    And I click on "The Rules of Golf" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "The Rules of Golf"

    And I switch to the main frame
    And I click on "Playing Golf Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Knowledge Check"

    And I switch to the main frame
    And I click on "Taking Care of the Course" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Etiquette - Care For the Course"

    And I switch to the main frame
    And I click on "Avoiding Distraction" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Etiquette - Avoiding Distraction"

    And I switch to the main frame
    And I click on "Playing Politely" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Etiquette - Playing the Game"

    And I switch to the main frame
    And I click on "Etiquette Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Knowledge Check"

    And I switch to the main frame
    And I click on "Handicapping Overview" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Handicapping"

    And I switch to the main frame
    And I click on "Calculating a Handicap" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Calculating a Handicap"

    And I switch to the main frame
    And I click on "Calculating a Handicapped Score" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Calculating a Score"

    And I switch to the main frame
    And I click on "Handicapping Example" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Calculating a Score"

    And I switch to the main frame
    And I click on "Handicapping Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Knowledge Check"

    And I switch to the main frame
    And I click on "How to Have Fun Playing Golf" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "How to Have Fun Golfing"

    And I switch to the main frame
    And I click on "How to Make Friends Playing Golf" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "How to Make Friends on the Golf Course"

    And I switch to the main frame
    And I click on "Having Fun Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I should see "Knowledge Check"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    Then I should see "ADV Multi-sco SCORM package" in the "page" "region"
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    And "Student 1" user has completed "ADV Multi-sco SCORM package" activity

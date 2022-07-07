@mod @mod_scorm @_file_upload @_switch_iframe
Feature: Scorm multi-sco review mode.
  Check review mode and attempt handling.
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Test review mode with a single sco completion.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Basic Multi-sco SCORM package |
      | Description | Description |
      | Force new attempt | No |
    And I set the field "Completed" to "1"
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "Basic Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    Then I should not see "Review mode"

  @javascript
  Scenario: Test review mode with all scos completed.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | ADV Multi-sco SCORM package |
      | Description | Description |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require all scos to return completion status | 1 |
      | Force new attempt | No |
    And I set the field "Completed" to "1"
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "ADV Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "ADV Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"

    And I switch to the main frame
    And I click on "Par?" "list_item"
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
    And I should see "ADV Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "ADV Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    Then I should see "Review mode"

  @javascript
  Scenario: Test force completed set to Always.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Basic Multi-sco SCORM package |
      | Description | Description |
      | Force new attempt | Always |
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12-mini.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "Basic Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I should not see "Start a new attempt"
    And I press "Enter"
    Then I should not see "Review mode"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"

  @javascript
  Scenario: Test force completed set to when previous complete/passed/failed.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Basic Multi-sco SCORM package |
      | Description | Description |
      | Force new attempt | When previous attempt completed, passed or failed |
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12-mini.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "Basic Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I should not see "Start a new attempt"
    And I press "Enter"
    And I should not see "Review mode"
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
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should see "Normal"
    And I press "Enter"
    Then I should not see "Review mode"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"

  @javascript
  Scenario: Test force completed set to Always and student skipview
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Basic Multi-sco SCORM package |
      | Description | Description |
      | Force new attempt | Always |
      | Student skip content structure page | Always |
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12-mini.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "Basic Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    Then I should not see "Review mode"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"

  @javascript
  Scenario: Test force completed set to when previous complete/passed/failed.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Basic Multi-sco SCORM package |
      | Description | Description |
      | Force new attempt | When previous attempt completed, passed or failed |
      | Student skip content structure page | Always |
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12-mini.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I should see "Basic Multi-sco SCORM package"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"
    And I switch to the main frame
    And I follow "Exit activity"
    And I wait until the page is ready
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    And I should not see "Review mode"
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
    And I should see "Basic Multi-sco SCORM package"
    And I am on "Course 1" course homepage
    And I follow "Basic Multi-sco SCORM package"
    Then I should not see "Review mode"
    And I switch to "scorm_object" iframe
    And I should see "Play of the game"

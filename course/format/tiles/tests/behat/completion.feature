@format @format_tiles @format_tiles_completion @javascript
Feature: Progress indicators can be used to change progress status and changes are reflected in database

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | enablecompletion |
      | Course 1 | C1        | tiles  | 0             | 7           | 1                |
    And the following "activities" exist:
    # completion 1 is manual completion, 2 is automatic then add an extra column completionpass = 1
      | activity | name         | intro           | course | idnumber | section | visible | completion |
      | page     | Test page 1a | Test page intro | C1     | page1a   | 1       | 1       | 1          |
      | page     | Test page 1b | Test page intro | C1     | page1b   | 1       | 1       | 1          |
      | page     | Test page 2a | Test page intro | C1     | page2a   | 2       | 1       | 1          |
      | page     | Test page 2b | Test page intro | C1     | page2b   | 2       | 1       | 1          |
      | page     | Test page 2c | Test page intro | C1     | page2c   | 2       | 1       | 1          |
      | page     | Test page 2d | Test page intro | C1     | page2d   | 2       | 1       | 1          |
      | page     | Test page 3a | Test page intro | C1     | page3a   | 3       | 1       | 1          |
      | page     | Test page 3b | Test page intro | C1     | page3b   | 3       | 1       | 1          |
      | page     | Test page 3c | Test page intro | C1     | page3c   | 3       | 1       | 1          |
      | page     | Test page 3d | Test page intro | C1     | page3d   | 3       | 1       | 1          |
      | page     | Test page 3e | Test page intro | C1     | page3e   | 3       | 1       | 1          |
      | page     | Test page 4a | Test page intro | C1     | page4a   | 4       | 1       | 1          |
      | page     | Test page 4b | Test page intro | C1     | page4b   | 4       | 1       | 1          |
      | page     | Test page 5a | Test page intro | C1     | page5a   | 5       | 1       | 1          |
      | page     | Test page 5b | Test page intro | C1     | page5b   | 5       | 1       | 1          |
      | page     | Test page 6a | Test page intro | C1     | page6a   | 6       | 1       | 1          |
      | page     | Test page 6b | Test page intro | C1     | page6b   | 6       | 1       | 1          |
      | page     | Test page 7a | Test page intro | C1     | page7a   | 7       | 1       | 1          |
      | page     | Test page 7b | Test page intro | C1     | page7b   | 7       | 1       | 1          |
      | page     | Test page 7c | Test page intro | C1     | page7c   | 7       | 1       | 1          |

    #Activity counts for each tile based on above
    #    Tile    | Activity count
    #    Tile 1  | 2
    #    Tile 2  | 4
    #    Tile 3  | 5
    #    Tile 4  | 2
    #    Tile 5  | 2
    #    Tile 6  | 2
    #    Tile 7  | 3
    #    Total   | 20

    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |

  @javascript
  Scenario: Log in as student and check/uncheck activities - results correctly reach database
    When I log in as "student1"
    And format_tiles progress indicator is showing as "percent" for course "Course 1"
    And I am on "Course 1" course homepage
    And format_tiles subtiles are off for course "Course 1"
    And I wait "1" seconds
    And format_tiles progress indicator for tile "1" is "0" out of "2"
    And I click on tile "1"
    And I wait until the page is ready

    And I click format tiles progress indicator for "Test page 1a"
    Then format_tiles progress for "Test page 1a" in "Course 1" is "1" in the database
    And I wait until the page is ready
    And format_tiles progress indicator for tile "1" is "1" out of "2"

    And I click format tiles progress indicator for "Test page 1b"
    Then format_tiles progress for "Test page 1b" in "Course 1" is "1" in the database
    And I wait until the page is ready
    And format_tiles progress indicator for tile "1" is "2" out of "2"

    And I click format tiles progress indicator for "Test page 1a"
    Then format_tiles progress for "Test page 1a" in "Course 1" is "0" in the database
    And I wait until the page is ready
    And format_tiles progress indicator for tile "1" is "1" out of "2"

    And I click format tiles progress indicator for "Test page 1b"
    Then format_tiles progress for "Test page 2b" in "Course 1" is "0" in the database
    And I wait until the page is ready
    And format_tiles progress indicator for tile "1" is "0" out of "2"

  @javascript
  Scenario: Log in as student and check/uncheck activities - results correctly shown in UI
    When I log in as "student1"
    And format_tiles progress indicator is showing as "percent" for course "Course 1"
    And I am on "Course 1" course homepage
    And format_tiles subtiles are off for course "Course 1"
    And format_tiles progress indicator for tile "1" is "0" out of "2"
    And I click on tile "1"
    And I wait until the page is ready

    And I click format tiles progress indicator for "Test page 1a"
    Then format_tiles progress for "Test page 1a" in "Course 1" is "1" in the database
    And format_tiles progress indicator for tile "1" is "1" out of "2"

    And I click format tiles progress indicator for "Test page 1b"
    Then format_tiles progress for "Test page 1b" in "Course 1" is "1" in the database
    And format_tiles progress indicator for tile "1" is "2" out of "2"

    And I click format tiles progress indicator for "Test page 1a"
    Then format_tiles progress for "Test page 1a" in "Course 1" is "0" in the database
    And format_tiles progress indicator for tile "1" is "1" out of "2"

    And I click format tiles progress indicator for "Test page 1b"
    Then format_tiles progress for "Test page 1b" in "Course 1" is "0" in the database
    And format_tiles progress indicator for tile "1" is "0" out of "2"

#    TODO check that the completion values shown on the tile and overall are complete when these items change

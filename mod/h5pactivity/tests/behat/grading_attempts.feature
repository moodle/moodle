@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Change grading options in an H5P activity
  In order to let students do a H5P attempt
  As a teacher
  I need to define what students attempts are used for grading

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And the following "activity" exists:
      | activity        | h5pactivity                                |
      | course          | C1                                         |
      | section         | 1                                          |
      | name            | Awesome H5P package                        |
      | intro           | Description                                |
      | packagefilepath | h5p/tests/fixtures/multiple-choice-2-6.h5p |
    And the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity         | attempt | interactiontype   | rawscore | maxscore | duration | completion | success |
      | student1 | Awesome H5P package | 1       | choice            | 0        | 1        | 4        | 1          | 0       |
      | student1 | Awesome H5P package | 2       | choice            | 1        | 1        | 4        | 1          | 1       |
      | student1 | Awesome H5P package | 3       | choice            | 0        | 1        | 4        | 1          | 0       |

  @javascript
  Scenario: Default grading is max attempt grade
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    And I expand all fieldsets
    And the field "Grading method" matches value "Highest grade"
    And I click on "Save and return to course" "button"
    When I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade  | Percentage |
      | Awesome H5P package | 100.00 | 100.00 %   |

  @javascript
  Scenario: Change setting to first attempt
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Grading method | First attempt |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Percentage |
      | Awesome H5P package | 0.00  | 0.00 %     |

  @javascript
  Scenario: Change setting to last attempt
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Grading method | Last attempt |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Percentage |
      | Awesome H5P package | 0.00  | 0.00 %     |

  @javascript
  Scenario: Change setting to average attempt
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Grading method | Average grade |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Percentage  |
      | Awesome H5P package | 33.33 | 33.33 %     |

  @javascript
  Scenario: Change setting to manual grading
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Grading method | Don't calculate a grade |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Percentage |
      | Awesome H5P package | -     | -          |

  @javascript
  Scenario: Disable tracking
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Enable attempt tracking | No |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Percentage |
      | Awesome H5P package | -     | -          |

  @javascript
  Scenario: Reescale existing grades changing the maximum grade
    # First we set to average and recalculate grades.
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Grading method | Average grade |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Range | Percentage  |
      | Awesome H5P package | 33.33 | 0–100 | 33.33 %     |

    # Now we modify the maximum grade with rescaling.
    And I am on the "Awesome H5P package" "h5pactivity activity editing" page
    And I set the following fields to these values:
      | Rescale existing grades | Yes |
      | Maximum grade           | 50  |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Range | Percentage  |
      | Awesome H5P package | 16.67 | 0–50  | 33.33 %     |

  @javascript
  Scenario: Change maximum grade without rescaling grade
    # First we set to average and recalculate grades.
    Given I am on the "Awesome H5P package" "h5pactivity activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | Grading method | Average grade |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Range | Percentage  |
      | Awesome H5P package | 33.33 | 0–100 | 33.33 %     |

    # Now we modify the maximum grade with rescaling.
    When I am on the "Awesome H5P package" "h5pactivity activity editing" page
    And I set the following fields to these values:
      | Rescale existing grades | No |
      | Maximum grade           | 50 |
    And I click on "Save and return to course" "button"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Range | Percentage  |
      | Awesome H5P package | 33.33 | 0–50  | 66.67 %     |

@mod @mod_assign @javascript
Feature: Allocate marks to student submissions
  In order to assess a submission with multiple markers
  As a teacher
  I need to allocate marks to student submissions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | teacher2 | Teacher   | Two      | teacher2@example.com |
      | teacher3 | Teacher   | Three    | teacher3@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | teacher3 | C1     | editingteacher |
    And the following "permission overrides" exist:
      | capability                         | permission | role           | contextlevel | reference |
      | mod/assign:managemarkedallocations | Allow      | editingteacher | Course       | C1        |
    And the following "activity" exists:
      | activity                        | assign       |
      | course                          | C1           |
      | idnumber                        | A1           |
      | name                            | Assignment 1 |
      | section                         | 1            |
      | completion                      | 1            |
      | markingworkflow                 | 1            |
      | markingallocation               | 1            |
      | markercount                     | 2            |
      | multimarkmethod                 | maximum      |
      | grade[modgrade_type]            | point        |
      | grade[modgrade_point]           | 100          |
      | assignfeedback_comments_enabled | 1            |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user     | marker   |
      | Assignment 1 | student1 | teacher1 |
      | Assignment 1 | student1 | teacher2 |
      | Assignment 1 | student2 | teacher1 |
      | Assignment 1 | student2 | teacher2 |

  Scenario: Allocate marks to students via the Quick Grading page
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I click on "Quick grading" "checkbox"
    When I set the field "User mark" in the "Student One" "table_row" to "99"
    And I click on "Save" "button" in the "sticky-footer" "region"
    And I press "Continue"
    And I click on "Quick grading" "checkbox"
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1 |
      | Student One | 99       |

  Scenario: Allocate marks to students via the Advanced Marker window
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I go to "Student One" "Assignment 1" activity advanced marking page
    When I set the field "Mark out of 100" to "50"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1 |
      | Student One | 50       |

  Scenario: Set workflow state for an allocated mark via Advanced Marker window
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I go to "Student One" "Assignment 1" activity advanced marking page
    When I set the field "Mark out of 100" to "42"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1          | Marker 2   | Status     |
      | Student One | Marking completed | Not marked | In marking |
      | Student Two |                   |            | Not marked |

  Scenario: Bulk set workflow state as allocated markers
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the ".modal-footer" "css_element"
    And I select "Mark" from the "Workflow context" singleselect
    When I select "In marking" from the "Marking workflow state" singleselect
    And I press "Save changes"
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1   | Marker 2   | Status     |
      | Student One | In marking | Not marked | In marking |
      | Student Two | In marking | Not marked | In marking |
    # Log in as the other teacher and change their workflow status too.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the ".modal-footer" "css_element"
    And I select "Mark" from the "Workflow context" singleselect
    And I select "In marking" from the "Marking workflow state" singleselect
    And I press "Save changes"
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1   | Marker 2   | Status     |
      | Student One | In marking | In marking | In marking |
      | Student Two | In marking | In marking | In marking |

  Scenario: Bulk set workflow state can only set marking completed when a marker already has a mark
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I go to "Student One" "Assignment 1" activity advanced marking page
    When I set the field "Mark out of 100" to "42"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1   | Marker 2   | Status     |
      | Student One | Not marked | Not marked | Not marked |
      | Student Two | Not marked | Not marked | Not marked |
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the ".modal-footer" "css_element"
    And I select "Mark" from the "Workflow context" singleselect
    When I select "Marking completed" from the "Marking workflow state" singleselect
    And I press "Save changes"
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1          | Marker 2   | Status     |
      | Student One | Marking completed | Not marked | In marking |
      | Student Two | Not marked        | Not marked | Not marked |

  Scenario: Grades are only calculated after all marks are given
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I go to "Student One" "Assignment 1" activity advanced marking page
    When I set the field "Mark out of 100" to "42"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1          | Marker 2   | Status     |
      | Student One | Marking completed | Not marked | In marking |
    And the following should not exist in the "submissions" table:
      | Grade |
      | 42    |
    # Now, provide marks as teacher2.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "55"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name  | Marker 1          | Marker 2          | Status            | Grade |
      | Student One | Marking completed | Marking completed | Marking completed | 55    |

  Scenario: Grade updates when new allocated marker provides mark
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I go to "Student One" "Assignment 1" activity advanced marking page
    When I set the field "Mark out of 100" to "50"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Now, provide marks as teacher2.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "51"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then the following should exist in the "submissions" table:
      | First name  | Marker 1          | Marker 2          | Status            | Grade |
      | Student One | Marking completed | Marking completed | Marking completed | 51    |
    # Swap teacher2 for teacher3 and provide a new mark.
    And I am on the "A1" "assign activity" page logged in as teacher3
    And I go to "Student One" "Assignment 1" activity advanced grading page
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Marker 2" to "Teacher Three"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "52"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name  | Marker 1          | Marker 2          | Status            | Grade |
      | Student One | Marking completed | Marking completed | Marking completed | 52    |

@core_form
Feature: Using the activity grade form element
  In order to ensure validation is provided to the teacher
  As a teacher
  I need to know why I can not add/edit values in the form element

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Being able to change the grade type, scale and maximum grade when there are no grades
    Given I log in as "admin"
    And I navigate to "Grades > Scales" in site administration
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | ABCDEF |
      | Scale | F,E,D,C,B,A |
    And I press "Save changes"
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | Letter scale |
      | Scale | Disappointing, Good, Very good, Excellent |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
      | Aggregate type | Average of ratings  |
      | scale[modgrade_type] | Point |
      | scale[modgrade_point] | 100 |
      | Group mode | No groups |
    And I follow "Test forum name"
    And I navigate to "Edit settings" in current page administration
    When I expand all fieldsets
    Then I should not see "Some grades have already been awarded, so the grade type"
    And I set the field "scale[modgrade_type]" to "Scale"
    And I set the field "scale[modgrade_scale]" to "ABCDEF"
    And I press "Save and display"
    And I should not see "You cannot change the type, as grades already exist for this item"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I should not see "Some grades have already been awarded, so the grade type"
    And I set the field "scale[modgrade_scale]" to "Letter scale"
    And I press "Save and display"
    And I should not see "You cannot change the scale, as grades already exist for this item"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I should not see "Some grades have already been awarded, so the grade type"
    And I set the field "scale[modgrade_type]" to "Point"
    And I set the field "Maximum grade" to "50"
    And I press "Save and display"
    And I should not see "You must choose whether to rescale existing grades or not"

  @javascript
  Scenario: Attempting to change the scale when grades already exist in rating activity
    Given I log in as "admin"
    And I navigate to "Grades > Scales" in site administration
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | ABCDEF |
      | Scale | F,E,D,C,B,A |
    And I press "Save changes"
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | Letter scale |
      | Scale | Disappointing, Good, Very good, Excellent |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
      | Aggregate type | Average of ratings  |
      | scale[modgrade_type] | Scale |
      | scale[modgrade_scale] | ABCDEF |
      | Group mode | No groups |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I click on "Add a new discussion topic" "link"
    And I set the following fields to these values:
      | Subject  | Discussion subject |
      | Message | Discussion message |
    And I press "Post to forum"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Discussion subject"
    And I set the field "rating" to "D"
    And I follow "Test forum name"
    And I navigate to "Edit settings" in current page administration
    When I expand all fieldsets
    Then I should see "Some grades have already been awarded, so the grade type and scale cannot be changed"
    # Try saving the form and visiting it back to verify that everything is working ok.
    And I press "Save and display"
    And I should not see "When selecting a ratings aggregate type you must also select"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And the field "Aggregate type" matches value "Average of ratings"
    And the field "scale[modgrade_type]" matches value "Scale"
    And the field "scale[modgrade_scale]" matches value "ABCDEF"

  @javascript
  Scenario: Attempting to change the scale when grades already exist in non-rating activity
    Given I log in as "admin"
    And I navigate to "Grades > Scales" in site administration
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | ABCDEF |
      | Scale | F,E,D,C,B,A |
    And I press "Save changes"
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | Letter scale |
      | Scale | Disappointing, Good, Very good, Excellent |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | grade[modgrade_type] | Scale |
      | grade[modgrade_scale] | ABCDEF |
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade" to "C"
    And I press "Save changes"
    And I press "Ok"
    And I follow "Edit settings"
    When I expand all fieldsets
    Then I should see "Some grades have already been awarded, so the grade type and scale cannot be changed"
    # Try saving the form and visiting it back to verify everything is working ok.
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And the field "grade[modgrade_type]" matches value "Scale"
    And the field "grade[modgrade_scale]" matches value "ABCDEF"

  Scenario: Attempting to change the maximum grade when ratings exist
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
      | Aggregate type | Average of ratings  |
      | scale[modgrade_type] | Point |
      | scale[modgrade_point] | 100 |
      | Group mode | No groups |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I click on "Add a new discussion topic" "link"
    And I set the following fields to these values:
      | Subject  | Discussion subject |
      | Message | Discussion message |
    And I press "Post to forum"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Discussion subject"
    And I set the field "rating" to "100"
    And I press "Rate"
    And I follow "Test forum name"
    And I navigate to "Edit settings" in current page administration
    When I expand all fieldsets
    Then I should see "You cannot change the type, as grades already exist for this item."
    And I set the field "Maximum grade" to "50"
    And I press "Save and display"
    And I should see "You cannot change the maximum grade when grades already exist for an activity with ratings"

  @javascript
  Scenario: Attempting to change the maximum grade when no rescaling option has been chosen
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I press "Save changes"
    And I press "Ok"
    And I follow "Edit settings"
    When I expand all fieldsets
    Then I should see "Some grades have already been awarded, so the grade type cannot be changed. If you wish to change the maximum grade, you must first choose whether or not to rescale existing grades."

@core_form
Feature: Using the activity grade form element
  In order to ensure validation is provided to the teacher
  As a teacher
  I need to know why I can not add/edit values in the form element

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "scales" exist:
      | name         | scale                                     |
      | ABCDEF       | F,E,D,C,B,A                               |
      | Letter scale | Disappointing, Good, Very good, Excellent |
    And the following "activities" exist:
      | activity | course | section | name                 | intro                       | idnumber | type    | groupmode |
      | assign   | C1     | 1       | Test assignment name | Test assignment description |          |         |           |
      | forum    | C1     | 1       | Test forum name      |                             | forum1   | general | 0         |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name               | message            |
      | student1 | forum1 | Discussion subject | Discussion message |

  @javascript
  Scenario: Being able to change the grade type, scale and maximum grade when there are no grades
    Given I am on the "Test forum name" "forum activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Ratings > Aggregate type        | Average of ratings |
      | id_scale_modgrade_type          | Point              |
      | Ratings > scale[modgrade_point] | 60                 |
    And I press "Save and return to course"
    And I am on the "Test forum name" "forum activity editing" page
    When I expand all fieldsets
    Then I should not see "Some grades have already been awarded, so the grade type"
    And I set the field "id_scale_modgrade_type" to "Scale"
    And I set the field "Ratings > scale[modgrade_scale]" to "ABCDEF"
    And I press "Save and display"
    And I should not see "You cannot change the type, as grades already exist for this item"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I should not see "Some grades have already been awarded, so the grade type"
    And I set the field "Ratings > scale[modgrade_scale]" to "Letter scale"
    And I press "Save and display"
    And I should not see "You cannot change the scale, as grades already exist for this item"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I should not see "Some grades have already been awarded, so the grade type"
    And I set the field "id_scale_modgrade_type" to "Point"
    And I set the field "Ratings > Maximum grade" to "50"
    And I press "Save and display"
    And I should not see "You must choose whether to rescale existing grades or not"

  @javascript
  Scenario: Attempting to change the scale when grades already exist in rating activity
    Given I am on the "Test forum name" "forum activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Ratings > Aggregate type        | Average of ratings |
      | id_scale_modgrade_type          | Scale              |
      | Ratings > scale[modgrade_scale] | ABCDEF             |
    And I press "Save and display"
    And I follow "Discussion subject"
    And I set the field "rating" to "D"
    And I am on the "Test forum name" "forum activity editing" page
    When I expand all fieldsets
    Then I should see "Some grades have already been awarded, so the grade type and scale cannot be changed"
    # Try saving the form and visiting it back to verify that everything is working ok.
    And I press "Save and display"
    And I should not see "When selecting a ratings aggregate type you must also select"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "Ratings > Aggregate type" matches value "Average of ratings"
    And the field "id_scale_modgrade_type" matches value "Scale"
    And the field "Ratings > scale[modgrade_scale]" matches value "ABCDEF"

  @javascript
  Scenario: Attempting to change the scale when grades already exist in non-rating activity
    Given I am on the "Test assignment name" "assign activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | grade[modgrade_type] | Scale |
      | grade[modgrade_scale] | ABCDEF |
    And I press "Save and display"
    And I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade" to "C"
    And I press "Save changes"
    And I follow "Edit settings"
    When I expand all fieldsets
    Then I should see "Some grades have already been awarded, so the grade type and scale cannot be changed"
    # Try saving the form and visiting it back to verify everything is working ok.
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "grade[modgrade_type]" matches value "Scale"
    And the field "grade[modgrade_scale]" matches value "ABCDEF"

  @javascript
  Scenario: Attempting to change the maximum grade when ratings exist
    Given I am on the "Test forum name" "forum activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Ratings > Aggregate type        | Average of ratings |
      | id_scale_modgrade_type          | Point              |
      | Ratings > scale[modgrade_point] | 100                |
    And I press "Save and display"
    And I follow "Discussion subject"
    And I set the field "rating" to "100"
    And I am on the "Test forum name" "forum activity editing" page
    When I expand all fieldsets
    Then I should see "You cannot change the type, as grades already exist for this item."
    And the "Maximum grade" "field" should be disabled

  @javascript
  Scenario: Attempting to change the maximum grade when no rescaling option has been chosen
    Given I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I press "Save changes"
    And I follow "Edit settings"
    When I expand all fieldsets
    Then I should see "Some grades have already been awarded, so the grade type cannot be changed. If you wish to change the maximum grade, you must first choose whether or not to rescale existing grades."

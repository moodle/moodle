@core @core_grades
Feature: Control the aggregation of the scales
  In order to use control the aggregation of the scales
  As an admin
  I can change use administration setting

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
      | student1 | Student   | 1        | student1@example.com | s1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_aggregations_visible | Mean of grades,Weighted mean of grades,Simple weighted mean of grades,Mean of grades (with extra credits),Median of grades,Lowest grade,Highest grade,Mode of grades,Natural |
      | grade_report_user_showcontributiontocoursetotal | 1 |
    And I navigate to "Scales" node in "Site administration > Grades"
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | Letterscale |
      | Scale | F,D,C,B,A   |
    And I press "Save changes"
    And the following "grade items" exist:
      | itemname | course |
      | Grade me | C1     |
    And the following "grade items" exist:
      | itemname | course | scale       |
      | Scale me | C1     | Letterscale |
    And the following config values are set as admin:
      | grade_includescalesinaggregation | 0 |
    And I log out

  Scenario Outline: Scales can be excluded from aggregation
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    When I give the grade "10" to the user "Student 1" for the grade item "Grade me"
    And I give the grade "B" to the user "Student 1" for the grade item "Scale me"
    And I press "Save changes"
    And I set the following settings for grade item "Course 1":
      | Aggregation | <aggregation> |
    And I navigate to "View > User report" in the course gradebook
    And I select "Student 1" from the "Select all or one user" singleselect
    Then the following should exist in the "user-grade" table:
      | Grade item             | Grade          | Percentage  | Contribution to course total |
      | Grade me               | 10.00          | 10.00 %     | <gradecontrib>               |
      | Scale me               | B              | 75.00 %     | <scalecontrib>               |
      | Course total<totalstr> | <coursetotal>  | <coursepc>  | -                            |
    And I log out
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_includescalesinaggregation | 1 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > User report" in the course gradebook
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item             | Grade          | Percentage  | Contribution to course total |
      | Grade me               | 10.00          | 10.00 %     | <gradecontrib2>              |
      | Scale me               | B              | 75.00 %     | <scalecontrib2>              |
      | Course total<totalstr> | <coursetotal2> | <coursepc2> | -                            |

    Examples:
      | aggregation                         | totalstr                             | coursetotal | coursepc | gradecontrib | scalecontrib | coursetotal2 | coursepc2 | gradecontrib2 | scalecontrib2 |
      | Natural                             |                                      | 10.00       | 10.00 %  | 10.00        | 0.00         | 14.00        | 13.33 %   | 9.52 %        | 3.81 %        |
      | Mean of grades                      | Mean of grades.                      | 10.00       | 10.00 %  | 10.00        | 0.00         | 42.50        | 42.50 %   | 5.00 %        | 37.50 %       |
      | Weighted mean of grades             | Weighted mean of grades.             | 10.00       | 10.00 %  | 10.00        | 0.00         | 42.50        | 42.50 %   | 5.00 %        | 37.50 %       |
      | Simple weighted mean of grades      | Simple weighted mean of grades.      | 10.00       | 10.00 %  | 10.00        | 0.00         | 12.50        | 12.50 %   | 9.62 %        | 2.88 %        |
      | Mean of grades (with extra credits) | Mean of grades (with extra credits). | 10.00       | 10.00 %  | 10.00        | 0.00         | 42.50        | 42.50 %   | 5.00 %        | 37.50 %       |
      | Median of grades                    | Median of grades.                    | 10.00       | 10.00 %  | 10.00        | 0.00         | 42.50        | 42.50 %   | 5.00 %        | 37.50 %       |
      | Lowest grade                        | Lowest grade.                        | 10.00       | 10.00 %  | 10.00        | 0.00         | 10.00        | 10.00 %   | 10.00 %       | 0.00 %        |
      | Highest grade                       | Highest grade.                       | 10.00       | 10.00 %  | 10.00        | 0.00         | 75.00        | 75.00 %   | 0.00 %        | 75.00 %       |
      | Mode of grades                      | Mode of grades.                      | 10.00       | 10.00 %  | 10.00        | 0.00         | 75.00        | 75.00 %   | 0.00 %        | 75.00 %       |

  @javascript
  Scenario: Weights of scales cannot be edited when they are not aggregated
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    When I set the following settings for grade item "Course 1":
      | Aggregation | Natural |
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Override weight of Grade me" to "1"
    Then the field "Override weight of Grade me" matches value "100.00"
    And I open the action menu in "Scale me" "table_row"
    And I click on "Edit settings" "link" in the "Scale me" "table_row"
    And I follow "Show more..."
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And the following config values are set as admin:
      | grade_includescalesinaggregation | 1 |
    And I am on "Course 1" course homepage
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Override weight of Grade me" to "1"
    And the field "Override weight of Grade me" matches value "95.238"
    And I set the field "Override weight of Scale me" to "1"
    And the field "Override weight of Scale me" matches value "4.8"
    And I open the action menu in "Scale me" "table_row"
    And I click on "Edit settings" "link" in the "Scale me" "table_row"
    And I follow "Show more..."
    And I should see "Weight adjusted"
    And I should see "Weight"

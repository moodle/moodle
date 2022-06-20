@core
Feature: Initials bar
  In order to filter users from user list
  As an admin
  I need to be able to use letter filters

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher   | Ateacher  | Teacher  | teacher@example.com   |
      | student1  | Astudent  | Astudent | student1@example.com  |
      | student2  | Bstudent  | Astudent | student2@example.com  |
      | student3  | Cstudent  | Cstudent | student3@example.com  |
      | student4  | Cstudent  | Cstudent | student4@example.com  |
      | student5  | Cstudent  | Cstudent | student5@example.com  |
      | student6  | Cstudent  | Cstudent | student6@example.com  |
      | student7  | Cstudent  | Cstudent | student7@example.com  |
      | student8  | Cstudent  | Cstudent | student8@example.com  |
      | student9  | Cstudent  | Cstudent | student9@example.com  |
      | student10 | Cstudent  | Cstudent | student10@example.com |
      | student11 | Cstudent  | Cstudent | student11@example.com |
      | student12 | Cstudent  | Cstudent | student12@example.com |
      | student13 | Cstudent  | Cstudent | student13@example.com |
      | student14 | Cstudent  | Cstudent | student14@example.com |
      | student15 | Cstudent  | Cstudent | student15@example.com |
      | student16 | Cstudent  | Cstudent | student16@example.com |
      | student17 | Cstudent  | Cstudent | student17@example.com |
      | student18 | Cstudent  | Cstudent | student18@example.com |
      | student19 | Cstudent  | Cstudent | student19@example.com |
      | student20 | Cstudent  | Cstudent | student20@example.com |
      | student21 | Cstudent  | Cstudent | student21@example.com |
      | student22 | Cstudent  | Cstudent | student22@example.com |
      | student23 | Cstudent  | Cstudent | student23@example.com |
      | student24 | Cstudent  | Cstudent | student24@example.com |

    And the following "courses" exist:
      | fullname | shortname | category |enablecompletion |
      | Course 1 | C1        | 0        | 1               |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher   | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
      | student3  | C1     | student        |
      | student4  | C1     | student        |
      | student5  | C1     | student        |
      | student6  | C1     | student        |
      | student7  | C1     | student        |
      | student8  | C1     | student        |
      | student9  | C1     | student        |
      | student10 | C1     | student        |
      | student11 | C1     | student        |
      | student12 | C1     | student        |
      | student13 | C1     | student        |
      | student14 | C1     | student        |
      | student15 | C1     | student        |
      | student16 | C1     | student        |
      | student17 | C1     | student        |
      | student18 | C1     | student        |
      | student19 | C1     | student        |
      | student20 | C1     | student        |
      | student21 | C1     | student        |
      | student22 | C1     | student        |
      | student23 | C1     | student        |
      | student24 | C1     | student        |

  Scenario: Filter users on assignment submission page
    Given the following "activities" exist:
      | activity | course | idnumber | name           | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled |
      | assign   | C1     | assign1  | TestAssignment | Test assignment description | 0                                   | 0                             |
    And I am on the "assign1" "Activity" page logged in as "teacher"
    When I follow "View all submissions"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"
    And I click on "A" "link" in the ".initialbar.lastinitial .page-item.A" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "B" "link" in the ".initialbar.firstinitial .page-item.B" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I am on the "assign1" "Activity" page
    When I follow "View all submissions"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.lastinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"

  Scenario: Filter users on view gradebook page
    Given the following "activities" exist:
      | activity | course | idnumber | name           | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled |
      | assign   | C1     | assign1  | TestAssignment | Test assignment description | 0                                   | 0                             |
    And I am on the "assign1" "Activity" page logged in as "teacher"
    When I follow "View all submissions"
    And I select "View gradebook" from the "jump" singleselect
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"
    And I click on "A" "link" in the ".initialbar.lastinitial .page-item.A" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "B" "link" in the ".initialbar.firstinitial .page-item.B" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I am on the "assign1" "Activity" page
    When I follow "View all submissions"
    And I select "View gradebook" from the "jump" singleselect
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.lastinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"

  Scenario: Filter users on course participants page
    Given the following "activities" exist:
      | activity | course | idnumber | name           | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled |
      | assign   | C1     | assign1  | TestAssignment | Test assignment description | 0                                   | 0                             |
    And I am on the "C1" "Course" page logged in as "student1"
    And I log out
    And I am on the "C1" "Course" page logged in as "student2"
    And I log out
    And I am on the "C1" "Course" page logged in as "teacher"
    And I follow "Participants"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"
    And I click on "A" "link" in the ".initialbar.lastinitial .page-item.A" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "B" "link" in the ".initialbar.firstinitial .page-item.B" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I am on "Course 1" course homepage
    And I follow "Participants"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.lastinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"

  @javascript
  Scenario: Filter users on activity completion page
    Given the following "activities" exist:
      | activity | course | idnumber | name           | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled |
      | assign   | C1     | assign1  | TestAssignment | Test assignment description | 0                                   | 0                             |
    And I am on the "assign1" "assign Activity editing" page logged in as "admin"
    And I expand all fieldsets
    And I set the field "Completion tracking" to "1"
    And I click on "Save and return to course" "button"
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I click on "Assignment - TestAssignment" "checkbox"
    And I click on "Save changes" "button"
    And I log out
    And I am on the "C1" "Course" page logged in as "teacher"
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"
    And I click on "A" "link" in the ".initialbar.lastinitial .page-item.A" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "B" "link" in the ".initialbar.firstinitial .page-item.B" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should not see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should not see "Cstudent Cstudent"
    And I click on "All" "link" in the ".initialbar.lastinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And ".page-item.active.B" "css_element" should not exist in the ".initialbar.firstinitial" "css_element"
    And ".page-item.active.A" "css_element" should not exist in the ".initialbar.lastinitial" "css_element"
    And I should see "Astudent Astudent"
    And I should see "Bstudent Astudent"
    And I should see "Cstudent Cstudent"

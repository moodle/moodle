@core @core_course
Feature: Deleting a course
  In order to delete a course
  As a moodle admin
  I need to be able to delete a course in management interface

  @javascript
  Scenario: Deleting a course from course category management interface
    Given the following "courses" exists:
      | fullname | shortname | idnumber |
      | Course 1 | Course1 | C1 |
      | Course 2 | Course2 | C2 |
    And I log in as "admin"
    When I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Add/edit courses"
    And I follow "Miscellaneous"
    Then I should see "Course 2" in the "#movecourses" "css_element"
    And I should see "Course 1" in the "#movecourses" "css_element"
    And I click on "Delete" "link" in the "Course 1" "table_row"
    And I should see "Are you absolutely sure you want to completely delete this course and all the data it contains?"
    And I should see "Course 1 (Course1)"
    And I press "Continue"
    And I should see "Course1 has been completely deleted"
    And I press "Continue"
    And I should see "Course 2" in the "#movecourses" "css_element"
    And I should not see "Course 1" in the "#movecourses" "css_element"

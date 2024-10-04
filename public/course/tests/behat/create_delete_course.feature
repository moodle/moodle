@core @core_course
Feature: Test we can both create and delete a course.
  As a Moodle admin
  I need to test I can create a course
  I need to test I can delete a course

  Scenario: Create a course
    Given the following "categories" exist:
      | name | category 0| idnumber |
      | Cat 1 | 0 | CAT1 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "No courses in this category" in the "#course-listing" "css_element"
    And I click on "Create new course" "link" in the ".course-listing-actions" "css_element"
    And I set the following fields to these values:
      | Course full name | Test course: create a course |
      | Course short name | TCCAC |
      | Course ID number | TC3401 |
      | Course summary | This course has been created by automated tests. |
    And I press "Save and return"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Test course: create a course" in the "#course-listing" "css_element"

  @javascript
  Scenario: Delete a course via its management listing
    Given the following "categories" exist:
      | name | category 0| idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Test course: create a course | TCCAC | TC3401 |
      | CAT1 | Test course 2: create another course | TC2CAC | TC3402 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Test course: create a course" in the "#course-listing" "css_element"
    And I should see "Test course 2: create another course" in the "#course-listing" "css_element"
    And I click on "delete" action for "Test course: create a course" in management course listing
    # Redirect
    And I should see "Delete TCCAC"
    And I should see "Test course: create a course (TCCAC)"
    And I press "Delete"
    # Redirect
    And I should see "Deleting TCCAC"
    And I should see "TCCAC has been completely deleted"
    And I press "Continue"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Test course 2: create another course" in the "#course-listing" "css_element"

  @javascript
  Scenario: Delete a course via its management details page
    Given the following "categories" exist:
      | name | category 0| idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Test course: create a course | TCCAC | TC3401 |
      | CAT1 | Test course 2: create another course | TC2CAC | TC3402 |

    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    And I click on category "Cat 1" in the management interface
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Test course: create a course" in the "#course-listing" "css_element"
    And I should see "Test course 2: create another course" in the "#course-listing" "css_element"
    And I click on course "Test course: create a course" in the management interface
    # Redirect
    And I should see the "Course categories and courses" management page with a course selected
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Test course: create a course" in the "#course-listing" "css_element"
    And I should see "Test course 2: create another course" in the "#course-listing" "css_element"
    And I should see "Test course: create a course" in the "#course-detail" "css_element"
    And I click on "Delete" "link" in the ".course-detail-listing-actions" "css_element"
    # Redirect
    And I should see "Delete TCCAC"
    And I should see "Test course: create a course (TCCAC)"
    And I press "Delete"
    # Redirect
    And I should see "Deleting TCCAC"
    And I should see "TCCAC has been completely deleted"
    And I press "Continue"
    # Redirect
    And I should see the "Course categories and courses" management page
    And I should see "Cat 1" in the "#category-listing" "css_element"
    And I should see "Test course 2: create another course" in the "#course-listing" "css_element"

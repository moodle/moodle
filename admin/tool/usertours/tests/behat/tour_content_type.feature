@tool @tool_usertours
Feature: Apply content type to a tour
  In order to give more content to a tour
  As an administrator
  I need to change the content type of the user tour

  Background:
    Given I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour    |
      | Description        | My first tour |
      | Apply to URL match | /my/%         |
      | Tour is enabled    | 1             |
    And I add a new user tour with:
      | Name               | tour_activityinfo_activity_student_title,tool_usertours   |
      | Description        | tour_activityinfo_activity_student_content,tool_usertours |
      | Apply to URL match | /my/%                                                     |
      | Tour is enabled    | 0                                                         |

  @javascript
  Scenario: User can choose the the content type of the tour step
    Given I open the User tour settings page
    And I click on "View" "link" in the "My first tour" "table_row"
    When I click on "New step" "link"
    Then "Content type" "select" should exist
    And the "Content type" select box should contain "Language string ID"
    And the "Content type" select box should contain "Manual"
    And I select "Language string ID" from the "Content type" singleselect
    And I should see " Language string ID"
    And "#fgroup_id_contenthtmlgrp" "css_element" should not be visible
    And I select "Manual" from the "Content type" singleselect
    And "#fgroup_id_contenthtmlgrp" "css_element" should be visible
    And I should not see "Language string ID" in the "#fitem_id_contentlangstring" "css_element"

  @javascript
  Scenario: Create a new step with Moodle Language content type
    Given I open the User tour settings page
    And I click on "View" "link" in the "My first tour" "table_row"
    And I click on "New step" "link"
    And I set the field "Title" to "tour_activityinfo_course_teacher_title,tool_usertours"
    And I select "Language string ID" from the "Content type" singleselect
    And I set the field "Language string ID" to "tour_activityinfo_course_teacher_content_abc,tool_usertours"
    When I press "Save changes"
    Then I should see "Invalid language string ID"
    And I set the field "Language string ID" to "tour_activityinfo_course_teacher_content,tool_usertours"
    And I press "Save changes"
    And I should see "New: Activity information"
    And I should see "New course settings 'Show completion conditions' and 'Show activity dates' enable you to choose whether activity completion conditions (if set) and/or dates are displayed for students on the course page."
    And I click on "Edit" "link" in the "New: Activity information" "table_row"
    And I should see "Editing \"New: Activity information\""
    And the field "Title" matches value "tour_activityinfo_course_teacher_title,tool_usertours"
    And the field "Language string ID" matches value "tour_activityinfo_course_teacher_content,tool_usertours"

  @javascript
  Scenario: Create a new step with manual content type
    Given I open the User tour settings page
    And I click on "View" "link" in the "My first tour" "table_row"
    And I click on "New step" "link"
    And I set the field "Title" to "tour_activityinfo_course_teacher_title,tool_usertours"
    And I select "Manual" from the "Content type" singleselect
    And I set the field "id_content" to "<b>Test content</b>"
    And I press "Save changes"
    And I should see "New: Activity information"
    And I should see "Test content"
    And I click on "Edit" "link" in the "New: Activity information" "table_row"
    And I should see "Editing \"New: Activity information\""
    And I should not see "Language string ID" in the "#fitem_id_contentlangstring" "css_element"
    And the field "Title" matches value "tour_activityinfo_course_teacher_title,tool_usertours"
    And the field "id_content" matches value "<b>Test content</b>"

  @javascript
  Scenario: Tour name and description can be translatable
    Given I open the User tour settings page
    And I should see "New: Activity information"
    And I should see "Activity dates plus what to do to complete the activity are shown on the activity page."
    When I click on "View" "link" in the "New: Activity information" "table_row"
    Then I should see "New: Activity information"
    And I should see "This is the 'New: Activity information' tour. It applies to the path '/my/%'."
    And I click on "edit the tour defaults" "link"
    And I should see "New: Activity information"
    And the field "Name" matches value "tour_activityinfo_activity_student_title,tool_usertours"
    And the field "Description" matches value "tour_activityinfo_activity_student_content,tool_usertours"

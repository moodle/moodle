@core @core_course
Feature: Summary text format should be preserved on edit and set by preferred editor format on creation
  In order to edit the course summary
  As a course creator
  The format specified for the summary must be honored

  Scenario: Preferred editor format should be used for summary field on course creation
    Given the following "user preferences" exist:
      | user  | preference  | value     |
      | admin | htmleditor  | textarea  |
    And I log in as "admin"
    And I go to the courses management page
    When I click on "Create new course" "link"
    Then the field "Course summary format" matches value "0"

  Scenario: Summary format must be preserved on course edit
    Given the following "user preferences" exist:
      | user  | preference  | value     |
      | admin | htmleditor  | textarea  |
    And I log in as "admin"
    And I go to the courses management page
    And I click on "Create new course" "link"
    And I set the following fields to these values:
      | Course full name   | C1                          |
      | Course short name  | C1                          |
      | Course summary     | Course description          |
    # 4 is assumed to be Markdown format.
    And I set the field with xpath "//select[@name='summary_editor[format]']" to "4"
    And I press "Save and display"
    When I click on "Settings" "link"
    Then the field "Course summary format" matches value "4"

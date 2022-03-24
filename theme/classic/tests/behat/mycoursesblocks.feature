@javascript @theme_classic
Feature: My courses page block layout in Classic theme
  In order to have a clear and consistent view on the my courses page
  As a student
  I need to see the blocks in the expected placement

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student1 | Student   | 1        | student@example.com |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the following fields to these values:
      | Page contexts    | Display throughout the entire site |
      | Text block title | Text on all pages                  |
      | Content          | This is visible on all pages       |
      | Default region   | Right                              |
    And I press "Save changes"

  Scenario: Student can see relevant blocks with correct placement on my courses page
    When I log in as "student1"
    And I am on the "My courses" page
    Then "Course overview" "text" should exist in the "region-main" "region"
    And "This is visible on all pages" "text" should exist in the ".columnright" "css_element"

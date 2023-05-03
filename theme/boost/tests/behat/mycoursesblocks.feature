@javascript @theme_boost
Feature: My courses page block layout in Boost theme
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
    And I add the "Text" block to the default region with:
      | Text block title | Text on all pages                  |
      | Content          | This is visible on all pages       |
    And I configure the "Text on all pages" block
    And I set the following fields to these values:
      | Page contexts    | Display throughout the entire site |
      | Default region   | Right                              |
    And I click on "Save changes" "button" in the "Configure Text on all pages block" "dialogue"

  Scenario: Student can see relevant blocks with correct placement on my courses page
    When I log in as "student1"
    And I am on the "My courses" page
    Then "Course overview" "text" should exist in the "region-main" "region"
    And I should see "This is visible on all pages"
    And I press "Close block drawer"
    And "Course overview" "text" should exist in the "region-main" "region"
    And I should not see "This is visible on all pages"

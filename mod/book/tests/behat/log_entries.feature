@mod @mod_book
Feature: In a book, verify log entries
  In order to create log entries
  As an admin
  I need to perform various actions in a book.

  @javascript @_switch_window
  Scenario: perform various book actions and verify log entries.
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "activity" exists:
      | course   | C1        |
      | activity | book      |
      | name     | Test book |
    And I am on the "Course 1" course page logged in as admin
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test book" "book activity" page
    And I set the following fields to these values:
      | Chapter title | First chapter |
      | Content | First chapter |
    And I press "Save changes"
    And I click on "Add new chapter" "link" in the "Table of contents" "block"
    And I set the following fields to these values:
      | Chapter title | Second chapter |
      | Content | Second chapter |
    And I press "Save changes"
    And I click on "Edit" "link" in the "Table of contents" "block"
    And I set the following fields to these values:
      | Chapter title | First chapter edited |
      | Content | First chapter edited |
    And I press "Save changes"
    And I click on "Next" "link"
    And I click on "Previous" "link"
    And I navigate to "Print book" in current page administration
    And I am on the "Test book" "book activity" page
    And I navigate to "Download IMS CP" in current page administration
    And I navigate to "Reports > Logs" in site administration
    And I set the field "menuid" to "Course 1"
    And I press "Get these logs"
    Then I should see "Book exported"
    And I should see "Book printed"
    And I should see "Chapter viewed" in the "#report_log_r4_c5" "css_element"
    And I should see "Chapter viewed" in the "#report_log_r5_c5" "css_element"
    And I should see "Chapter viewed" in the "#report_log_r6_c5" "css_element"
    And I should see "Chapter updated" in the "#report_log_r7_c5" "css_element"
    And I should see "Chapter viewed" in the "#report_log_r8_c5" "css_element"
    And I should see "Chapter created" in the "#report_log_r9_c5" "css_element"
    And I click on "Chapter viewed" "link" in the "#report_log_r4_c5" "css_element"
    And I switch to "action" window
    And I change window size to "large"
    And I should see "1. First chapter edited" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter viewed" "link" in the "#report_log_r5_c5" "css_element"
    And I switch to "action" window
    And I should see "2. Second chapter" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter updated" "link" in the "#report_log_r7_c5" "css_element"
    And I switch to "action" window
    And I should see "1. First chapter edited" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter created" "link" in the "#report_log_r9_c5" "css_element"
    And I switch to "action" window
    And I should see "2. Second chapter" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter created" "link" in the "#report_log_r11_c5" "css_element"
    And I switch to "action" window
    And I should see "1. First chapter edited" in the ".book_content" "css_element"
    And I switch to the main window

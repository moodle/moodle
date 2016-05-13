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
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "Book" to section "1" and I fill the form with:
      | Name | Test book |
      | Description | A book about dreams! |
    And I follow "Test book"
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
    And I click on "Print book" "link" in the "Administration" "block"
    And I click on "Generate IMS CP" "link" in the "Administration" "block"
    And I click on "Logs" "link" in the "Administration" "block"
    Then I should see "Book exported"
    And I should see "Book printed"
    And I should see "Chapter viewed" in the "#report_log_r2_c5" "css_element"
    And I should see "Chapter viewed" in the "#report_log_r3_c5" "css_element"
    And I should see "Chapter viewed" in the "#report_log_r4_c5" "css_element"
    And I should see "Chapter updated" in the "#report_log_r5_c5" "css_element"
    And I should see "Chapter viewed" in the "#report_log_r6_c5" "css_element"
    And I should see "Chapter created" in the "#report_log_r7_c5" "css_element"
    And I should see "Chapter created" in the "#report_log_r9_c5" "css_element"
    And I click on "Chapter viewed" "link" in the "#report_log_r2_c5" "css_element"
    And I switch to "action" window
    And I should see "1 First chapter edited" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter viewed" "link" in the "#report_log_r3_c5" "css_element"
    And I switch to "action" window
    And I should see "2 Second chapter" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter updated" "link" in the "#report_log_r5_c5" "css_element"
    And I switch to "action" window
    And I should see "1 First chapter edited" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter created" "link" in the "#report_log_r7_c5" "css_element"
    And I switch to "action" window
    And I should see "2 Second chapter" in the ".book_content" "css_element"
    And I switch to the main window
    And I click on "Chapter created" "link" in the "#report_log_r9_c5" "css_element"
    And I switch to "action" window
    And I should see "1 First chapter edited" in the ".book_content" "css_element"
    And I switch to the main window

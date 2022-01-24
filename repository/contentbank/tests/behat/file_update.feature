@repository @repository_contentbank @javascript @core_h5p
Feature: Updating a file in the content bank after using in a course
  In order to use file alias
  As a user
  Updated files must update references when is an alias

  Background:
    Given the following "categories" exist:
      | name      | category | idnumber |
      | Category1 | 0        | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course1  | C1        | CAT1     |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname | filepath                                  |
      | Course       | C1        | contenttype_h5p | admin | package.h5p | /h5p/tests/fixtures/guess-the-answer.h5p  |
    And the following "activities" exist:
      | activity | name       | intro      | introformat | course | content  | contentformat | idnumber |
      | page     | PageName1  | PageDesc1  | 1           | C1     | H5Ptest  | 1             | 1        |

  Scenario: Referenced files is the default option and updates alias as well
    Given I am on the PageName1 "Page activity editing" page logged in as admin
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "package.h5p" "file" in repository content area
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Press here to reveal answer"
    And I switch to the main frame
    # Now edit the content in the content bank.
    When I am on "Course1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "package.h5p" "link"
    And I click on "Edit" "link"
    And I wait until the page is ready
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Title" to "Required title"
    And I set the field "Descriptive solution label" to "This is a new text"
    And I switch to the main frame
    And I click on "Save" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "This is a new text"
    And I switch to the main frame
    # Check the course page is updated.
    When I am on the PageName1 "Page activity" page
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "This is a new text"
    And I switch to the main frame

  Scenario: Copied files should not be updated if the original is edited
    Given I am on the PageName1 "Page activity editing" page logged in as admin
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "package.h5p" "file" in repository content area
    And I click on "Make a copy of the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Press here to reveal answer"
    And I switch to the main frame
    # Now edit the content in the content bank.
    When I am on "Course1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "package.h5p" "link"
    And I click on "Edit" "link"
    And I wait until the page is ready
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Title" to "Required title"
    And I set the field "Descriptive solution label" to "This is a new text"
    And I switch to the main frame
    And I click on "Save" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "This is a new text"
    And I switch to the main frame
    # Check the course page is not updated.
    When I am on the PageName1 "Page activity" page
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Press here to reveal answer"
    And I switch to the main frame

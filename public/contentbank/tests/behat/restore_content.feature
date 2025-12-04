@core @core_contentbank @core_h5p @contentbank_h5p
Feature: Content bank contents are retained when course is restored
  In order to restore content bank contents
  As a manager
  I need to be able to restore course containing the content bank

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | admin | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 site "Content bank" link not accessible without navigation block.
    And I add the "Navigation" block if not present

  @javascript
  Scenario: Deleted courses with content banks can be restored
    Given I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "delete" action for "Course 1" in management course listing
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    And I press "Continue"
    And I navigate to "Recycle bin" in current page administration
    And I click on "Restore" "link" in the "Course 1" "table_row"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    When I click on "Content bank" "link"
    And I click on "filltheblanks.h5p" "link"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries are Berlin, Washington, Beijing, Canberra and Brasilia the capitals?"

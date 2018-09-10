@mod @mod_checklist @checklist
Feature: A teacher can attach a link to an external URL to a checklist item

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Checklist" to section "1" and I fill the form with:
      | Checklist    | Test checklist      |
      | Introduction | This is a checklist |
      | Updates by   | Student only        |

  Scenario: A teacher links to an external website and then follows that link
    Given I follow "Test checklist"
    And "linkcourseid" "select" should not exist
    When I set the following fields to these values:
      | displaytext | Item with link   |
      | linkurl     | www.google.co.uk |
    And I press "Add"
    And I follow "Edit this item"
    Then the following fields match these values:
      | displaytext | Item with link          |
      | linkurl     | http://www.google.co.uk |

    When I set the following fields to these values:
      | displaytext | Item with link (edited) |
      | linkurl     | moodle.org              |
    And I press "Update"
    And I follow "Preview"
    Then I should see "Item with link (edited)"
    And I click on "Link associated with this item" "link" in the "Item with link (edited)" "list_item"
    And I should see "The Moodle Project"

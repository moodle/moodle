@mod @mod_glossary
Feature: Glossary can be set to various display formats
  In order to display different glossary formats
  As a teacher
  I can set the glossary activity display format

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given the following "activities" exist:
      | activity | course | name       |
      | glossary | C1     | Glossary 1 |
    And the following "mod_glossary > entries" exist:
      | glossary   | concept | definition         |
      | Glossary 1 | Entry 1 | Entry 1 definition |
      | Glossary 1 | Entry 2 | Entry 2 definition |

  Scenario: Glossary display format is entry list style
    Given I am on the "Glossary 1" "glossary activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | displayformat | entrylist |
    When I press "Save and display"
    # Confirm that glossary display format is entry list.
    # In this format, the concept definitions are not displayed.
    Then I should not see "by Admin User"
    And I should not see "Entry 1 definition"
    And I should not see "Entry 2 definition"
    And ".entrylist" "css_element" should exist

  Scenario: Glossary display format is FAQ-style
    Given I am on the "Glossary 1" "glossary activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | displayformat | faq |
    When I press "Save and display"
    # Confirm that glossary format is FAQ.
    # In this format, the words Question and Answer are displayed.
    Then I should see "Question:"
    And I should see "Answer:"
    And ".faq" "css_element" should exist

  @_file_upload @javascript
  Scenario: Glossary display format is full without author style
    Given I am on the "Glossary 1" "glossary activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | displayformat | fullwithoutauthor |
    And I press "Save and display"
    And I press "Add entry"
    # Add an entry with an attachment.
    And I set the following fields to these values:
      | Concept    | Entry 3                        |
      | Definition | Entry 3 definition             |
      | Attachment | lib/tests/fixtures/gd-logo.png |
    When I press "Save changes"
    # Confirm that glossary format is full without author style.
    # In this format, the image link should exist and author's name should not be visible.
    Then "gd-logo.png" "link" should exist
    And I should not see "by Admin User"
    And ".fullwithoutauthor" "css_element" should exist

  @_file_upload @javascript
  Scenario: Glossary display format is encyclopedia style
    Given I am on the "Glossary 1" "glossary activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | displayformat | encyclopedia |
    And I press "Save and display"
    And I press "Add entry"
    # Add an entry with an attachment.
    And I set the following fields to these values:
      | Concept    | Entry 3                        |
      | Definition | Entry 3 definition             |
      | Attachment | lib/tests/fixtures/gd-logo.png |
    When I press "Save changes"
    # Confirm that glossary format is encyclopedia.
    # In this format, the image element should be displayed.
    Then "//img[contains(@src, 'gd-logo.png')]" "xpath_element" should exist
    And ".encyclopedia" "css_element" should exist

  Scenario Outline: Glossary display format can be set to dictionary, continuous and full with author
    Given I am on the "Glossary 1" "glossary activity editing" page logged in as teacher1
    # Assign the corresponding display format to glossary activity.
    And I set the following fields to these values:
      | displayformat | <display_format> |
    When I press "Save and display"
    # Confirm that glossary format is the display format set in the previous step.
    Then I should <visibility> "by Admin User"
    And ".<display_format>" "css_element" should exist

    Examples:
      | display_format | visibility |
      | dictionary     | not see    |
      | continuous     | not see    |
      | fullwithauthor | see        |

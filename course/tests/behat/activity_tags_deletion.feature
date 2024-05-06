@core @core_course @core_tag
Feature: Delete activity tags during course reset
  As an admin,
  I should be able to delete activity tags by performing course reset

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activities" exist:
      | activity | name          | course | idnumber  |
      | book     | Test Book     | C1     | book1     |
      | forum    | Test Forum    | C1     | forum1    |
      | glossary | Test Glossary | C1     | glossary1 |

  @javascript
  Scenario: Delete book chapter tags using course reset
    # Added multiple tags to confirm that all tags are deleted on course reset.
    Given the following "mod_book > chapters" exist:
      | book      | title     | content           | tags                  |
      | Test Book | Chapter 1 | Chapter 1 content | SampleTag, ChapterTag |
    # Perform course reset without checking anything.
    And I log in as "admin"
    And I am on the "Course 1" "reset" page
    And I press "Reset"
    And I press "Continue"
    # Confirm that book chapter tags are not deleted.
    When I am on the "Test Book" "book activity" page
    Then I should see "SampleTag"
    And I should see "ChapterTag"
    # Delete book chapter tags using course reset.
    And I am on the "Course 1" "reset" page
    And I expand all fieldsets
    And I click on "Remove all book tags" "checkbox"
    And I press "Reset"
    # Confirm that book chapter tags are deleted.
    And I should see "Book tags have been deleted" in the "Books" "table_row"
    And I press "Continue"
    And I am on the "Test Book" "book activity" page
    And I should not see "SampleTag"
    And I should not see "ChapterTag"

  @javascript
  Scenario Outline: Delete forum discussion tags using course reset
    Given the following "mod_forum > discussions" exist:
      | user  | forum  | name         | message              | tags                     |
      | admin | forum1 | Discussion 1 | Discussion 1 message | SampleTag, DiscussionTag |
    # Perform course reset without checking anything.
    And I am on the "Course 1" "reset" page logged in as admin
    And I press "Reset"
    And I press "Continue"
    # Confirm that forum discussion tags are not deleted.
    When I am on the "Test Forum" "forum activity" page
    And I follow "Discussion 1"
    Then I should see "SampleTag"
    And I should see "DiscussionTag"
    And I am on the "Course 1" "reset" page
    And I expand all fieldsets
    # Depending on <resetcheck> value, either delete all discussion posts or remove all forum discussion tags only.
    And I click on "<resetcheck>" "checkbox"
    # Confirm `Remove all forum tags` is disabled when `Delete all posts` on previous step is checked.
    And the "Remove all forum tags" "checkbox" should be <canbechecked>
    And I press "Reset"
    And I should see "<resetmessage>" in the "Forums" "table_row"
    And I press "Continue"
    And I am on the "Test Forum" "forum activity" page
    # Confirm discussion is deleted when `Delete all posts` was checked.
    And I <forumview> see "There are no discussion topics yet in this forum"
    # Confirm all discussion tags are deleted.
    And I should not see "SampleTag"
    And I should not see "DiscussionTag"

    Examples:
      | resetcheck            | resetmessage                 | canbechecked | forumview  |
      | Delete all posts      | Delete all posts             | disabled     | should     |
      | Remove all forum tags | Forum tags have been deleted | enabled      | should not |

  @javascript
  Scenario Outline: Delete glossary entry tags using course reuse
    Given the following "mod_glossary > entries" exist:
      | glossary      | concept   | definition      | user  | tags                   |
      | Test Glossary | Aubergine | Also eggpgplant | admin | SampleTag, GlossaryTag |
    # Perform course reset without checking anything.
    And I am on the "Course 1" "reset" page logged in as admin
    And I press "Reset"
    And I press "Continue"
    # Confirm that glossary entry tags are not deleted.
    When I am on the "Test Glossary" "glossary activity" page
    Then I should see "SampleTag"
    And I should see "GlossaryTag"
    And I am on the "Course 1" "reset" page
    And I expand all fieldsets
    # Depending on <resetcheck> value, either delete all glossary entries or remove all glossary entry tags only.
    And I click on "<resetcheck>" "checkbox"
    # Confirm `Remove all forum tags` is disabled when `Delete entries from all glossaries` on previous step is checked.
    And the "Remove all glossary tags" "checkbox" should be <canbechecked>
    And I press "Reset"
    And I should see "<resetmessage>" in the "Glossaries" "table_row"
    And I press "Continue"
    And I am on the "Test Glossary" "glossary activity" page
    # Confirm glossary entries are deleted when `Delete entries from all glossaries` is checked.
    And I <glossaryview> see "No entries found in this section"
    # Confirm that glossary entry tags are deleted.
    And I should not see "SampleTag"
    And I should not see "GlossaryTag"

    Examples:
      | resetcheck                         | resetmessage                       | canbechecked | glossaryview |
      | Delete entries from all glossaries | Delete entries from all glossaries | disabled     | should       |
      | Remove all glossary tags           | Glossary tags have been deleted    | enabled      | should not   |

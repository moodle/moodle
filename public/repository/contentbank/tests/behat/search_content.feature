@repository @repository_contentbank @javascript
Feature: Search content bank files using the content bank files repository
  In order to find the content I need to select in the file picker
  As a user
  I need to be able to search in the content bank files repository by content name

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student  | Student   | 1        | student@example.com  |
      | teacher  | Teacher   | 1        | teacher1@example.com |
    And the following "categories" exist:
      | name      | category | idnumber |
      | Category1 | 0        | CAT1     |
      | Category2 | 0        | CAT2     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course1  | C1        | CAT1     |
      | Course2  | C2        | CAT2     |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname          | filepath                                    |
      | Course       | C1        | contenttype_h5p | admin | coursecontent1.h5p   | /h5p/tests/fixtures/filltheblanks.h5p       |
      | Course       | C2        | contenttype_h5p | admin | coursecontent2.h5p   | /h5p/tests/fixtures/find-the-words.h5p      |
      | Category     | CAT1      | contenttype_h5p | admin | categorycontent1.h5p | /h5p/tests/fixtures/ipsums.h5p              |
      | Category     | CAT2      | contenttype_h5p | admin | categorycontent2.h5p | /h5p/tests/fixtures/multiple-choice-2-6.h5p |
      | System       |           | contenttype_h5p | admin | systemcontent.h5p    | /h5p/tests/fixtures/greeting-card.h5p   |
    And the following "activities" exist:
      | activity | name       | intro      | introformat | course | idnumber |
      | folder   | Folder     | FolderDesc | 1           | C1     | folder   |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |

  Scenario: User can see a search field and reset search button in the content bank files repository
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    When I select "Content bank" repository in file picker
    Then "Search repository" "field" should be visible
    And "Refresh" "link" should be visible

  Scenario: User can see search results when there is content that matches the search criteria
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I set the field "Search repository" to "content"
    When I press enter
    Then I should see "5" elements in repository content area
    And I should see "systemcontent.h5p" "file" in repository content area
    And I should see "categorycontent1.h5p" "file" in repository content area
    And I should see "categorycontent2.h5p" "file" in repository content area
    And I should see "coursecontent1.h5p" "file" in repository content area
    And I should see "coursecontent2.h5p" "file" in repository content area

  Scenario: User can see search results when there is content that matches the search criteria ignoring case sensitivity
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I set the field "Search repository" to "COURSE"
    When I press enter
    Then I should see "2" elements in repository content area
    And I should see "coursecontent1.h5p" "file" in repository content area
    And I should see "coursecontent2.h5p" "file" in repository content area

  Scenario: User can not see any search results when there is not a content that matches the search criteria
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I set the field "Search repository" to "somecontent"
    When I press enter
    Then I should see "0" elements in repository content area
    And I should see "No files available" in the ".filepicker .fp-content" "css_element"

  Scenario: User can reset search criteria and see all content displayed prior the search action
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I should see "1" elements in repository content area
    And I should see "coursecontent1.h5p" "file" in repository content area
    And I set the field "Search repository" to "category"
    And I press enter
    And I should see "2" elements in repository content area
    And I should see "categorycontent1.h5p" "file" in repository content area
    And I should see "categorycontent2.h5p" "file" in repository content area
    When I click on "Refresh" "link"
    Then I should see "1" elements in repository content area
    And I should see "coursecontent1.h5p" "file" in repository content area

  Scenario: Editing teacher can see search results when the content is available to him and matches the search criteria
    Given I am on the Folder "Folder activity" page logged in as teacher
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I set the field "Search repository" to "content"
    When I press enter
    Then I should see "3" elements in repository content area
    And I should see "coursecontent1.h5p" "file" in repository content area
    And I should see "categorycontent1.h5p" "file" in repository content area
    And I should see "systemcontent.h5p" "file" in repository content area

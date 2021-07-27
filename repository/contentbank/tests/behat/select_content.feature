@repository @repository_contentbank @javascript
Feature: Select content bank files using the content bank files repository
  In order to re-use content bank files
  As a user
  I need to be able to view and select content bank files using the content bank repository

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student  | Student   | 1        | student@example.com  |
      | teacher1 | Teacher 1 | 1        | teacher1@example.com |
      | teacher2 | Teacher 2 | 1        | teacher2@example.com |
    And the following "categories" exist:
      | name         | category | idnumber |
      | Category1    | 0        | CAT1     |
      | SubCategory1 | CAT1     | SUBCAT1  |
    And the following "courses" exist:
      | fullname             | shortname | category |
      | MiscellaneousCourse1 | mscC1     | 0        |
      | MiscellaneousCourse2 | mscC2     | 0        |
      | Category1Course1     | cat1C1    | CAT1     |
      | SubCategory1Course1  | subcat1C1 | SUBCAT1  |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname             | filepath                                    |
      | Course       | mscC1     | contenttype_h5p | admin | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       |
      | Course       | mscC2     | contenttype_h5p | admin | find-the-words.h5p      | /h5p/tests/fixtures/find-the-words.h5p      |
      | Course       | subcat1C1 | contenttype_h5p | admin | greeting-card-887.h5p   | /h5p/tests/fixtures/greeting-card-887.h5p   |
      | Category     | CAT1      | contenttype_h5p | admin | ipsums.h5p              | /h5p/tests/fixtures/ipsums.h5p              |
      | Category     | SUBCAT1   | contenttype_h5p | admin | multiple-choice-2-6.h5p | /h5p/tests/fixtures/multiple-choice-2-6.h5p |
      | System       |           | contenttype_h5p | admin | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       |
    And the following "activities" exist:
      | activity | name       | intro      | introformat | course | idnumber |
      | forum    | Forum      | ForumDesc  | 1           | mscC1  | forum1   |
      | folder   | Folder     | FolderDesc | 1           | mscC1  | folder1  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | mscC1  | editingteacher |
      | teacher2 | mscC1  | teacher        |
      | student  | mscC1  | student        |

  Scenario: Admin can navigate and see all existing content bank files using the content bank repository
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    When I select "Content bank" repository in file picker
    Then I should see "System > Miscellaneous > MiscellaneousCourse1" breadcrumb navigation in repository
    And I should see "1" elements in repository content area
    And I should see "filltheblanks.h5p" "file" in repository content area
    And I click on "Miscellaneous" "link" in the ".file-picker .fp-pathbar" "css_element"
    And I should see "System > Miscellaneous" breadcrumb navigation in repository
    And I should see "2" elements in repository content area
    And I should see "MiscellaneousCourse1" "folder" in repository content area
    And I should see "MiscellaneousCourse2" "folder" in repository content area
    And I click on "MiscellaneousCourse2" "folder" in repository content area
    And I should see "System > Miscellaneous > MiscellaneousCourse2" breadcrumb navigation in repository
    And I should see "1" elements in repository content area
    And I should see "find-the-words.h5p" "file" in repository content area
    And I click on "System" "link" in the ".file-picker .fp-pathbar" "css_element"
    And I should see "System" breadcrumb navigation in repository
    And I should see "3" elements in repository content area
    And I should see "filltheblanks.h5p" "file" in repository content area
    And I should see "Miscellaneous" "folder" in repository content area
    And I should see "Category1" "folder" in repository content area
    And I click on "Category1" "folder" in repository content area
    And I should see "System > Category1" breadcrumb navigation in repository
    And I should see "3" elements in repository content area
    And I should see "SubCategory1" "folder" in repository content area
    And I should see "Category1Course1" "folder" in repository content area
    And I should see "ipsums.h5p" "file" in repository content area
    And I click on "SubCategory1" "folder" in repository content area
    And I should see "System > Category1 > SubCategory1" breadcrumb navigation in repository
    And I should see "2" elements in repository content area
    And I should see "SubCategory1Course1" "folder" in repository content area
    And I should see "multiple-choice-2-6.h5p" "file" in repository content area
    And I click on "SubCategory1Course1" "folder" in repository content area
    And I should see "System > Category1 > SubCategory1 > SubCategory1Course1" breadcrumb navigation in repository
    And I should see "1" elements in repository content area
    And I should see "greeting-card-887.h5p" "file" in repository content area

  Scenario: Admin can select and re-use content bank files using the content bank repository
    Given I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I should see "System > Miscellaneous > MiscellaneousCourse1" breadcrumb navigation in repository
    And I click on "System" "link" in the ".file-picker .fp-pathbar" "css_element"
    And I click on "Category1" "folder" in repository content area
    And I should see "ipsums.h5p" "file" in repository content area
    And I click on "ipsums.h5p" "file" in repository content area
    And I should see "Select ipsums.h5p"
    When I click on "Select this file" "button"
    Then I should see "1" elements in "Files" filemanager
    And I should see "ipsums.h5p" in the ".fp-content .fp-file" "css_element"

  Scenario: Editing teacher can navigate and see content bank files available to him using the content bank repository
    Given I am on the Folder "Folder activity" page logged in as teacher1
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    When I select "Content bank" repository in file picker
    Then I should see "System > Miscellaneous > MiscellaneousCourse1" breadcrumb navigation in repository
    And I should see "1" elements in repository content area
    And I should see "filltheblanks.h5p" "file" in repository content area
    And I click on "Miscellaneous" "link" in the ".file-picker .fp-pathbar" "css_element"
    And I should see "System > Miscellaneous" breadcrumb navigation in repository
    And I should see "1" elements in repository content area
    And I should see "MiscellaneousCourse1" "folder" in repository content area
    And I click on "System" "link" in the ".file-picker .fp-pathbar" "css_element"
    And I should see "System" breadcrumb navigation in repository
    And I should see "2" elements in repository content area
    And I should see "filltheblanks.h5p" "file" in repository content area
    And I should see "Miscellaneous" "folder" in repository content area

  Scenario: Editing teacher can select and re-use content bank files available to him using the content bank repository
    Given I am on the Folder "Folder activity" page logged in as teacher1
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    And I select "Content bank" repository in file picker
    And I should see "System > Miscellaneous > MiscellaneousCourse1" breadcrumb navigation in repository
    And I click on "System" "link" in the ".file-picker .fp-pathbar" "css_element"
    And I should see "filltheblanks.h5p" "file" in repository content area
    And I click on "filltheblanks.h5p" "file" in repository content area
    And I should see "Select filltheblanks.h5p"
    When I click on "Select this file" "button"
    Then I should see "1" elements in "Files" filemanager
    And I should see "filltheblanks.h5p" in the ".fp-content .fp-file" "css_element"

  Scenario: Non-editing teacher can not see the content bank repository
    Given I am on the Forum "Forum activity" page logged in as teacher2
    And I click on "Add a new discussion topic" "link"
    And I click on "Link" "button"
    When I click on "Browse repositories..." "button"
    Then I should not see "Content bank" in the ".fp-repo-area" "css_element"

  Scenario: Student can not see the content bank repository
    Given I am on the Forum "Forum activity" page logged in as student
    And I click on "Add a new discussion topic" "link"
    And I click on "Link" "button"
    When I click on "Browse repositories..." "button"
    Then I should not see "Content bank" in the ".fp-repo-area" "css_element"

  Scenario: Both content name and file name are shown when a content is selected
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname | filepath                       |
      | Course       | mscC1     | contenttype_h5p | admin | My content  | /h5p/tests/fixtures/ipsums.h5p |
    And I am on the Folder "Folder activity" page logged in as admin
    And I click on "Edit" "button"
    And I click on "Add..." "button"
    And I should see "Content bank" in the ".fp-repo-area" "css_element"
    When I select "Content bank" repository in file picker
    Then I should see "My content"
    And I click on "My content" "link"
    And I should see "Select My content"
    And the field "Save as" matches value "ipsums.h5p"

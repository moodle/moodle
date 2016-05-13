@core @core_cohort @_file_upload
Feature: A privileged user can create cohorts using a CSV file
  In order to create cohorts using a CSV file
  As an admin
  I need to be able to upload a CSV file and navigate through the upload process

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
      | Cat 3 | CAT1     | CAT3     |

  @javascript
  Scenario: Upload cohorts with default System context as admin
    When I log in as "admin"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    And I follow "Upload cohorts"
    And I upload "cohort/tests/fixtures/uploadcohorts1.csv" file to "File" filemanager
    And I click on "Preview" "button"
    Then the following should exist in the "previewuploadedcohorts" table:
      | name          | idnumber  | description       | Context       | visible | Status |
      | cohort name 1 | cohortid1 | first description | System        | 1       |        |
      | cohort name 2 | cohortid2 |                   | System        | 1       |        |
      | cohort name 3 | cohortid3 |                   | Miscellaneous | 0       |        |
      | cohort name 4 | cohortid4 |                   | Cat 1         | 1       |        |
      | cohort name 5 | cohortid5 |                   | Cat 2         | 0       |        |
      | cohort name 6 | cohortid6 |                   | Cat 3         | 1       |        |
    And I press "Upload cohorts"
    And I should see "Uploaded 6 cohorts"
    And I press "Continue"
    And the following should exist in the "cohorts" table:
      | Name          | Cohort ID | Description | Cohort size | Source           |
      | cohort name 1 | cohortid1 | first description | 0           | Created manually |
      | cohort name 2 | cohortid2 |             | 0           | Created manually |
    And I follow "All cohorts"
    And the following should exist in the "cohorts" table:
      | Category      | Name          | Cohort ID | Description       | Cohort size | Source           |
      | System        | cohort name 1 | cohortid1 | first description | 0           | Created manually |
      | System        | cohort name 2 | cohortid2 |                   | 0           | Created manually |
      | Miscellaneous | cohort name 3 | cohortid3 |                   | 0           | Created manually |
      | Cat 1         | cohort name 4 | cohortid4 |                   | 0           | Created manually |
      | Cat 2         | cohort name 5 | cohortid5 |                   | 0           | Created manually |
      | Cat 3         | cohort name 6 | cohortid6 |                   | 0           | Created manually |
    And ".dimmed_text" "css_element" should not exist in the "cohort name 1" "table_row"
    And ".dimmed_text" "css_element" should not exist in the "cohort name 2" "table_row"
    And ".dimmed_text" "css_element" should exist in the "cohort name 3" "table_row"
    And the "class" attribute of "cohort name 3" "table_row" should contain "dimmed_text"
    And ".dimmed_text" "css_element" should not exist in the "cohort name 4" "table_row"
    And the "class" attribute of "cohort name 5" "table_row" should contain "dimmed_text"
    And ".dimmed_text" "css_element" should not exist in the "cohort name 6" "table_row"

  @javascript
  Scenario: Upload cohorts with default category context as admin
    When I log in as "admin"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    And I follow "Upload cohorts"
    And I upload "cohort/tests/fixtures/uploadcohorts1.csv" file to "File" filemanager
    And I set the field "Default context" to "Cat 1 / Cat 3"
    And I click on "Preview" "button"
    Then the following should exist in the "previewuploadedcohorts" table:
      | name          | idnumber  | description       | Context                 | Status |
      | cohort name 1 | cohortid1 | first description | Cat 3         |        |
      | cohort name 2 | cohortid2 |                   | Cat 3         |        |
      | cohort name 3 | cohortid3 |                   | Miscellaneous |        |
      | cohort name 4 | cohortid4 |                   | Cat 1         |        |
      | cohort name 5 | cohortid5 |                   | Cat 2         |        |
      | cohort name 6 | cohortid6 |                   | Cat 3         |        |
    And I press "Upload cohorts"
    And I should see "Uploaded 6 cohorts"
    And I press "Continue"
    And I should see "Category: Cat 3: available cohorts (3)"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    And I follow "All cohorts"
    And the following should exist in the "cohorts" table:
      | Category      | Name          | Cohort ID | Description       | Cohort size | Source           |
      | Cat 3         | cohort name 1 | cohortid1 | first description | 0           | Created manually |
      | Cat 3         | cohort name 2 | cohortid2 |                   | 0           | Created manually |
      | Miscellaneous | cohort name 3 | cohortid3 |                   | 0           | Created manually |
      | Cat 1         | cohort name 4 | cohortid4 |                   | 0           | Created manually |
      | Cat 2         | cohort name 5 | cohortid5 |                   | 0           | Created manually |
      | Cat 3         | cohort name 6 | cohortid6 |                   | 0           | Created manually |

  @javascript
  Scenario: Upload cohorts with default category context as manager
    Given the following "users" exist:
      | username | firstname | lastname | email                  |
      | user1    | User      | 1        | user1@example.com |
    And the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | Category     | CAT1      |
    When I log in as "user1"
    And I follow "Courses"
    And I follow "Cat 1"
    And I navigate to "Cohorts" node in "Category: Cat 1"
    And I follow "Upload cohorts"
    And I upload "cohort/tests/fixtures/uploadcohorts1.csv" file to "File" filemanager
    And I click on "Preview" "button"
    Then the following should exist in the "previewuploadedcohorts" table:
      | name          | idnumber  | description       | Context | Status |
      | cohort name 1 | cohortid1 | first description | Cat 1   |        |
      | cohort name 2 | cohortid2 |                   | Cat 1   |        |
      | cohort name 3 | cohortid3 |                   | Cat 1   | Category Miscellaneous not found or you don't have permission to create a cohort there. The default context will be used. |
      | cohort name 4 | cohortid4 |                   | Cat 1   |        |
      | cohort name 5 | cohortid5 |                   | Cat 1   | Category CAT2 not found or you don't have permission to create a cohort there. The default context will be used. |
      | cohort name 6 | cohortid6 |                   | Cat 3   |        |
    And I press "Upload cohorts"
    And I should see "Uploaded 6 cohorts"

  @javascript
  Scenario: Upload cohorts with conflicting id number
    Given the following "cohorts" exist:
      | name   | idnumber  |
      | Cohort | cohortid2 |
    When I log in as "admin"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    And I follow "Upload cohorts"
    And I upload "cohort/tests/fixtures/uploadcohorts1.csv" file to "File" filemanager
    And I click on "Preview" "button"
    Then I should see "Errors were found in CSV data. See details below."
    Then the following should exist in the "previewuploadedcohorts" table:
      | name | idnumber | description | Context | Status |
      | cohort name 1 | cohortid1 | first description | System |  |
      | cohort name 2 | cohortid2 |  | System | Cohort with the same ID number already exists |
      | cohort name 3 | cohortid3 |  | Miscellaneous |  |
      | cohort name 4 | cohortid4 |  | Cat 1 |  |
      | cohort name 5 | cohortid5 |  | Cat 2 |  |
      | cohort name 6 | cohortid6 |  | Cat 3 |  |
    And "Upload cohorts" "button" should not exist

  @javascript
  Scenario: Upload cohorts with different ways of specifying context
    When I log in as "admin"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    And I follow "Upload cohorts"
    And I upload "cohort/tests/fixtures/uploadcohorts2.csv" file to "File" filemanager
    And I click on "Preview" "button"
    Then the following should exist in the "previewuploadedcohorts" table:
      | name                         | idnumber  | description | Context       | Status |
      | Specify category as name     | cohortid1 |             | Miscellaneous |        |
      | Specify category as idnumber | cohortid2 |             | Cat 1         |        |
      | Specify category as id       | cohortid3 |             | Miscellaneous |        |
      | Specify category as path     | cohortid4 |             | Cat 3         |        |
      | Specify category_id          | cohortid5 |             | Miscellaneous |        |
      | Specify category_idnumber    | cohortid6 |             | Cat 1         |        |
      | Specify category_path        | cohortid7 |             | Cat 3         |        |
    And I should not see "not found or you"
    And I press "Upload cohorts"
    And I should see "Uploaded 7 cohorts"
    And I press "Continue"
    And I follow "Upload cohorts"
    And I upload "cohort/tests/fixtures/uploadcohorts3.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And the following should exist in the "previewuploadedcohorts" table:
      | name                                         | idnumber   | description | Context | Status |
      | Specify context as id (system)               | cohortid8  |             | System  |        |
      | Specify context as name (system)             | cohortid9  |             | System  |        |
      | Specify context as category name only        | cohortid10 |             | Cat 1   |        |
      | Specify context as category path             | cohortid12 |             | Cat 3   |        |
      | Specify context as category idnumber         | cohortid13 |             | Cat 2   |        |
    And I should not see "not found or you"
    And I press "Upload cohorts"
    And I should see "Uploaded 5 cohorts"

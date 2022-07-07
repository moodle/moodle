@tool @tool_dataprivacy @javascript
Feature: Manage data storage purposes
  As the privacy officer
  In order to manage the data registry
  I need to be able to manage the data storage purposes for the data registry

  Background:
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I open the action menu in "region-main" "region"
    And I choose "Purposes" in the open action menu
    And I press "Add purpose"
    And I set the following fields to these values:
      | Name                                       | Purpose 1                                                      |
      | Description                                | Purpose 1 description                                          |
      | Lawful bases                               | Contract (GDPR Art. 6.1(b)),Legal obligation (GDPR Art 6.1(c)) |
      | Sensitive personal data processing reasons | Explicit consent (GDPR Art. 9.2(a))                            |
      | retentionperiodnumber                      | 2                                                              |
    When I press "Save"
    Then I should see "Purpose 1" in the "List of data purposes" "table"
    And I should see "Contract (GDPR Art. 6.1(b))" in the "Purpose 1" "table_row"
    And I should see "Legal obligation (GDPR Art 6.1(c))" in the "Purpose 1" "table_row"
    And I should see "Explicit consent (GDPR Art. 9.2(a))" in the "Purpose 1" "table_row"
    And I should see "2 years" in the "Purpose 1" "table_row"
    And "Purpose 1 Purpose 1 description" row "5" column of "List of data purposes" table should contain "No"

  Scenario: Update a data storage purpose
    Given I open the action menu in "Purpose 1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the following fields to these values:
      | Name                                       | Purpose 1 edited                                                |
      | Description                                | Purpose 1 description edited                                    |
      | Lawful bases                               | Contract (GDPR Art. 6.1(b)), Vital interests (GDPR Art. 6.1(d)) |
      | Sensitive personal data processing reasons | Explicit consent (GDPR Art. 9.2(a))                             |
      | retentionperiodnumber                      | 3                                                               |
      | protected                                  | 1                                                               |
    When I press "Save changes"
    Then I should see "Purpose 1 edited" in the "List of data purposes" "table"
    And I should see "Purpose 1 description edited" in the "Purpose 1 edited" "table_row"
    And I should see "Vital interests (GDPR Art. 6.1(d))" in the "Purpose 1 edited" "table_row"
    And I should see "3 years" in the "Purpose 1 edited" "table_row"
    But I should not see "Legal obligation (GDPR Art 6.1(c))" in the "Purpose 1 edited" "table_row"
    And "Purpose 1 edited Purpose 1 description edited" row "5" column of "List of data purposes" table should not contain "No"

  Scenario: Delete a data storage purpose
    Given I open the action menu in "Purpose 1" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "Delete purpose"
    And I should see "Are you sure you want to delete the purpose 'Purpose 1'?"
    When I click on "Delete" "button" in the "Delete purpose" "dialogue"
    Then I should not see "Purpose 1" in the "List of data purposes" "table"

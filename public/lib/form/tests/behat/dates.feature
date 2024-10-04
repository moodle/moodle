@core @core_form
Feature: Setting and validating date fields
  Behat steps to set and check the date fields

  Background:
    Given I log in as "admin"

  Scenario: Setting moodleform date fields by field label
    Given I am on fixture page "/lib/form/tests/behat/fixtures/dates_form.php"
    And I set the following fields to these values:
      | Simple only date                            | ## 2023-07-31 ##       |
      | simpleoptionaldateonly[enabled]             | 1                      |
      | Simple optional only date                   | ## 2023-08-31 ##       |
      | Simple date and time                        | ## 2023-07-31 11:15 ## |
      | simpleoptionaldatetime[enabled]             | 1                      |
      | Simple optional date and time               | ## 2023-08-31 14:45 ## |
      | Group1 only date                            | ## 2023-07-31 ##       |
      | group1optionaldateonly[enabled]             | 1                      |
      | Group1 optional only date                   | ## 2023-08-31 ##       |
      | Group1 date and time                        | ## 2023-07-31 11:15 ## |
      | group1optionaldatetime[enabled]             | 1                      |
      | Group1 optional date and time               | ## 2023-08-31 14:45 ## |
      | Group2 only date                            | ## 2023-07-31 ##       |
      | dategroup2[group2optionaldateonly][enabled] | 1                      |
      | Group2 optional only date                   | ## 2023-08-31 ##       |
      | Group2 date and time                        | ## 2023-07-31 11:15 ## |
      | dategroup2[group2optionaldatetime][enabled] | 1                      |
      | Group2 optional date and time               | ## 2023-08-31 14:45 ## |
    When I press "Send form"
    Then I should see "simpledateonly: 1690732800"
    And I should see "simpleoptionaldateonly: 1693411200"
    And I should see "simpledatetime: 1690773300"
    And I should see "simpleoptionaldatetime: 1693464300"
    And I should see "group1dateonly: 1690732800"
    And I should see "group1optionaldateonly: 1693411200"
    And I should see "group1datetime: 1690773300"
    And I should see "group1optionaldatetime: 1693464300"
    And I should see "dategroup2[group2dateonly]: 1690732800"
    And I should see "dategroup2[group2optionaldateonly]: 1693411200"
    And I should see "dategroup2[group2datetime]: 1690773300"
    And I should see "dategroup2[group2optionaldatetime]: 1693464300"

  Scenario: Setting moodleform date fields by field name
    Given I am on fixture page "/lib/form/tests/behat/fixtures/dates_form.php"
    And I set the following fields to these values:
      | simpledateonly                              | ## 2023-07-31 ##       |
      | simpleoptionaldateonly[enabled]             | 1                      |
      | simpleoptionaldateonly                      | ## 2023-08-31 ##       |
      | simpledatetime                              | ## 2023-07-31 11:15 ## |
      | simpleoptionaldatetime[enabled]             | 1                      |
      | simpleoptionaldatetime                      | ## 2023-08-31 14:45 ## |
      | group1dateonly                              | ## 2023-07-31 ##       |
      | group1optionaldateonly[enabled]             | 1                      |
      | group1optionaldateonly                      | ## 2023-08-31 ##       |
      | group1datetime                              | ## 2023-07-31 11:15 ## |
      | group1optionaldatetime[enabled]             | 1                      |
      | group1optionaldatetime                      | ## 2023-08-31 14:45 ## |
      | dategroup2[group2dateonly]                  | ## 2023-07-31 ##       |
      | dategroup2[group2optionaldateonly][enabled] | 1                      |
      | dategroup2[group2optionaldateonly]          | ## 2023-08-31 ##       |
      | dategroup2[group2datetime]                  | ## 2023-07-31 11:15 ## |
      | dategroup2[group2optionaldatetime][enabled] | 1                      |
      | dategroup2[group2optionaldatetime]          | ## 2023-08-31 14:45 ## |
    When I press "Send form"
    Then I should see "simpledateonly: 1690732800"
    And I should see "simpleoptionaldateonly: 1693411200"
    And I should see "simpledatetime: 1690773300"
    And I should see "simpleoptionaldatetime: 1693464300"
    And I should see "group1dateonly: 1690732800"
    And I should see "group1optionaldateonly: 1693411200"
    And I should see "group1datetime: 1690773300"
    And I should see "group1optionaldatetime: 1693464300"
    And I should see "dategroup2[group2dateonly]: 1690732800"
    And I should see "dategroup2[group2optionaldateonly]: 1693411200"
    And I should see "dategroup2[group2datetime]: 1690773300"
    And I should see "dategroup2[group2optionaldatetime]: 1693464300"

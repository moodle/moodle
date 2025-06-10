@ou @ou_vle @report @report_editdates
Feature: Edit course plugin dates
  When a user view edit dates report
  They can change the plugin date settings

  Background: Setup course and sample plugins
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
      | student2 | Student   | 2        | student2@asd.com |
      | student3 | Student   | 3        | student3@asd.com |
      | student4 | Student   | 4        | student4@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name        | Test quiz name 1       |
      | Description | Test forum description |
    And I add a "Quiz" to section "2" and I fill the form with:
      | Name        | Test quiz name 2       |
      | Description | Test forum description |
    And I add a "Quiz" to section "3" and I fill the form with:
      | Name        | Test quiz name 3       |
      | Description | Test forum description |
    Given I log out

  @javascript @_switch_iframe
  Scenario: Test edit dates report can be used to change plugin instance dates
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Dates" in current page administration
    And I follow "Dates"
    Then I should see "Course 1"
    And I should see "Activity view filter "
    And I follow "Expand all"
    Then I should see "Course start date"
    And I should see "Test quiz name 1"
    And I should see "Test quiz name 2"
    And I should see "Test quiz name 3"
    # test to see whether we can pick up xpath element
    And I set the field with xpath "//fieldset[@id='id_section2header']/div[@class='fcontainer clearfix']/div[2]/div[2]/fieldset[contains(@id, 'timeopen')]/div[1]/label/input" to "1"
    And I set the field with xpath "//fieldset[@id='id_section2header']/div[@class='fcontainer clearfix']/div[3]/div[2]/fieldset[contains(@id, 'timeclose')]/div[1]/label/input" to "1"
    And I set the field with xpath "//fieldset[@id='id_section3header']/div[@class='fcontainer clearfix']/div[2]/div[2]/fieldset[contains(@id, 'timeopen')]/div[1]/label/input" to "1"
    And I set the field with xpath "//fieldset[@id='id_section3header']/div[@class='fcontainer clearfix']/div[3]/div[2]/fieldset[contains(@id, 'timeclose')]/div[1]/label/input" to "1"
    And I press "Save changes"
    Then I should see "Course 1"
    And I should see "Activity view filter "
    And I follow "Expand all"
    Then I should see "Course start date"
    And I should see "Test quiz name 1"
    And I should see "Test quiz name 2"
    And I should see "Test quiz name 3"
    And the "value" attribute of "//fieldset[@id='id_section2header']/div[@class='fcontainer clearfix']/div[2]/div[2]/fieldset[contains(@id, 'timeopen')]/div[1]/label/input" "xpath_element" should contain "1"
    And the "value" attribute of "//fieldset[@id='id_section2header']/div[@class='fcontainer clearfix']/div[3]/div[2]/fieldset[contains(@id, 'close')]/div[1]/label/input" "xpath_element" should contain "1"
    And the "value" attribute of "//fieldset[@id='id_section3header']/div[@class='fcontainer clearfix']/div[2]/div[2]/fieldset[contains(@id, 'timeopen')]/div[1]/label/input" "xpath_element" should contain "1"
    And the "value" attribute of "//fieldset[@id='id_section3header']/div[@class='fcontainer clearfix']/div[3]/div[2]/fieldset[contains(@id, 'close')]/div[1]/label/input" "xpath_element" should contain "1"

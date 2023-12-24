@core @core_grades @javascript
Feature: Changing the aggregation of an item affects its weight and extra credit definition
  In order to switch to another aggregation method
  As an teacher
  I need to be able to edit the grade category settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "grade categories" exist:
      | fullname      | course | aggregation |
      | Cat mean      | C1     | 0           |
      | Cat median    | C1     | 2           |
      | Cat min       | C1     | 4           |
      | Cat max       | C1     | 6           |
      | Cat mode      | C1     | 8           |
      | Cat weighted  | C1     | 10          |
      | Cat weighted2 | C1     | 10          |
      | Cat simple    | C1     | 11          |
      | Cat ec        | C1     | 12          |
      | Cat natural & | C1     | 13          |
    And the following "grade items" exist:
      | itemname  | course | category      | aggregationcoef | aggregationcoef2 | weightoverride |
      | Item a1   | C1     | ?             | 0               | 0                | 0              |
      | Item a2   | C1     | ?             | 0               | 0.40             | 1              |
      | Item a3   | C1     | ?             | 1               | 0.10             | 1              |
      | Item a4   | C1     | ?             | 1               | 0                | 0              |
      | Item b1   | C1     | Cat natural & | 0               | 0                | 0              |
      | Item b2   | C1     | Cat natural & | 0               | 0.40             | 1              |
      | Item b3   | C1     | Cat natural & | 1               | 0.10             | 1              |
      | Item b4   | C1     | Cat natural & | 1               | 0                | 0              |
    And I log in as "admin"
    And I change window size to "large"
    And I set the following administration settings values:
      | grade_aggregations_visible | Mean of grades,Weighted mean of grades,Simple weighted mean of grades,Mean of grades (with extra credits),Median of grades,Lowest grade,Highest grade,Mode of grades,Natural |
    And I am on the "Course 1" "grades > Grader report > View" page
    And I turn editing mode on
    And I click on grade item menu "Cat mean" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I set the following fields to these values:
      | Weight adjusted     | 1  |
      | Weight              | 20 |
      | Extra credit        | 0  |
    And I press "Save changes"
    And I click on grade item menu "Cat median" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I set the following fields to these values:
      | Weight adjusted     | 1  |
      | Weight              | 5  |
      | Extra credit        | 0  |
    And I press "Save changes"
    And I click on grade item menu "Cat min" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I set the following fields to these values:
      | Weight adjusted     | 0  |
      | Weight              | 0  |
      | Extra credit        | 1  |
    And I press "Save changes"
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "1"
    And the field "Weight" matches value "40.0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "1"
    And the field "Weight" matches value "10.0"
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "1"
    And the field "Weight" matches value "40.0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "1"
    And the field "Weight" matches value "10.0"
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"

  Scenario: Switching a category from Natural aggregation to Mean of grades and back
    Given I click on grade item menu "Course 1" of type "course" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Mean of grades"
    When I click on "Save" "button" in the "Edit category" "dialogue"
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    Then I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Cat mean" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And I should not see "Weight adjusted"
    And I should not see "Weight" in the "#id_headerparent" "css_element"
    And I should not see "Extra credit"
    And I press "Cancel"
    And I click on grade item menu "Cat median" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And I should not see "Weight adjusted"
    And I should not see "Weight" in the "#id_headerparent" "css_element"
    And I should not see "Extra credit"
    And I press "Cancel"
    And I click on grade item menu "Cat min" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And I should not see "Weight adjusted"
    And I should not see "Weight" in the "#id_headerparent" "css_element"
    And I should not see "Extra credit"
    And I press "Cancel"
    And I click on grade item menu "Cat natural &" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Mean of grades"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item b1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Weight"
    And I should not see "Extra credit"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    # Switching back.
    And I click on grade item menu "Course 1" of type "course" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Natural"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Cat mean" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I press "Cancel"
    And I click on grade item menu "Cat median" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I press "Cancel"
    And I click on grade item menu "Cat min" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I press "Cancel"
    And I click on grade item menu "Cat natural &" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Natural"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item b1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"

  Scenario: Switching a category from Natural aggregation to Weighted mean of grades and back
    Given I click on grade item menu "Course 1" of type "course" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    Then I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Cat mean" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I press "Cancel"
    And I click on grade item menu "Cat median" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I press "Cancel"
    And I click on grade item menu "Cat min" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I press "Cancel"
    And I click on grade item menu "Cat natural &" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item b1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I should not see "Weight adjusted"
    And I should not see "Extra credit"
    And the field "Item weight" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    # Switching back.
    And I click on grade item menu "Course 1" of type "course" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Natural"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Cat mean" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I press "Cancel"
    And I click on grade item menu "Cat median" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I press "Cancel"
    And I click on grade item menu "Cat min" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    And I expand all fieldsets
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I press "Cancel"
    And I click on grade item menu "Cat natural &" of type "category" on "grader" page
    And I choose "Edit category" in the open action menu
    And I set the field "Aggregation" to "Natural"
    And I click on "Save" "button" in the "Edit category" "dialogue"
    And I wait until the page is ready
    And I click on grade item menu "Item b1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item b4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"

  Scenario: Switching grade items between categories
    # Move to same aggregation (Natural).
    Given I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Select Item a1" to "1"
    And I set the field "Select Item a2" to "1"
    And I set the field "Select Item a3" to "1"
    And I set the field "Select Item a4" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Cat natural &" "list_item" in the "Move items" "dialogue"
    When I click on "Move" "button" in the "Move items" "dialogue"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    Then the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "1"
    And the field "Weight" matches value "40.0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "1"
    And the field "Weight" matches value "10.0"
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    # Move to Mean of grades (with extra credit).
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Select Item a1" to "1"
    And I set the field "Select Item a2" to "1"
    And I set the field "Select Item a3" to "1"
    And I set the field "Select Item a4" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Cat ec" "list_item" in the "Move items" "dialogue"
    And I click on "Move" "button" in the "Move items" "dialogue"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    # Move to Simple weight mean of grades.
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Select Item a1" to "1"
    And I set the field "Select Item a2" to "1"
    And I set the field "Select Item a3" to "1"
    And I set the field "Select Item a4" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Cat simple" "list_item" in the "Move items" "dialogue"
    And I click on "Move" "button" in the "Move items" "dialogue"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Extra credit" matches value "1"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    # Move to Weighted mean of grades.
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Select Item a1" to "1"
    And I set the field "Select Item a2" to "1"
    And I set the field "Select Item a3" to "1"
    And I set the field "Select Item a4" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Cat weighted" "list_item" in the "Move items" "dialogue"
    And I click on "Move" "button" in the "Move items" "dialogue"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "1"
    And I set the field "Item weight" to "2"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "1"
    And I set the field "Item weight" to "5"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "1"
    And I set the field "Item weight" to "8"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "1"
    And I set the field "Item weight" to "11"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    # Move to same (Weighted mean of grades).
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Select Item a1" to "1"
    And I set the field "Select Item a2" to "1"
    And I set the field "Select Item a3" to "1"
    And I set the field "Select Item a4" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Cat weighted2" "list_item" in the "Move items" "dialogue"
    And I click on "Move" "button" in the "Move items" "dialogue"
    And I wait "2" seconds
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "2"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "5"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "8"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item weight" matches value "11"
    And I click on "Save" "button" in the "Edit grade item" "dialogue"
    # Move back to Natural.
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the field "Select Item a1" to "1"
    And I set the field "Select Item a2" to "1"
    And I set the field "Select Item a3" to "1"
    And I set the field "Select Item a4" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Cat natural &" "list_item" in the "Move items" "dialogue"
    And I click on "Move" "button" in the "Move items" "dialogue"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Item a1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a2" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a3" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Item a4" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Weight adjusted" matches value "0"
    And the field "Extra credit" matches value "0"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"

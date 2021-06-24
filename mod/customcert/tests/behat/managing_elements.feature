@mod @mod_customcert
Feature: Being able to manage elements in a certificate template
  In order to ensure managing elements in a certificate template works as expected
  As a teacher
  I need to manage elements in a certificate template

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    |
      | assign     | Assignment 1         | Assignment 1 intro         | C1     | assign1     |
      | assign     | Assignment 2         | Assignment 2 intro         | C1     | assign2     |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I navigate to "Edit certificate" in current page administration

  Scenario: Add and edit elements in a certificate template
    # Background image.
    And I add the element "Background image" to page "1" of the "Custom certificate 1" certificate template
    And I press "Save changes"
    And I should see "Background image" in the "elementstable" "table"
    # Border.
    And I add the element "Border" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Width  | 2 |
      | Colour | #045ECD |
    And I press "Save changes"
    And I should see "Border" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Border" "table_row"
    And the following fields match these values:
      | Width  | 2 |
      | Colour | #045ECD |
    And I press "Save changes"
    # Category name.
    And I add the element "Category name" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Category name" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Category name" "table_row"
    And the following fields match these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # Code.
    And I add the element "Code" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Code" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Code" "table_row"
    And the following fields match these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # Course field.
    And I add the element "Course field" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Course field" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Course field" "table_row"
    And the following fields match these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # Course name.
    And I add the element "Course name" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Course name" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Course name" "table_row"
    And the following fields match these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # Date.
    And I add the element "Date" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Date item                | Course start date |
      | Date format              | 2                 |
      | Font                     | Helvetica         |
      | Size                     | 20                |
      | Colour                   | #045ECD           |
      | Width                    | 20                |
      | Reference point location | Top left          |
    And I press "Save changes"
    And I should see "Date" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Date" "table_row"
    And the following fields match these values:
      | Date item                | Course start date |
      | Date format              | 2                 |
      | Font                     | Helvetica         |
      | Size                     | 20                |
      | Colour                   | #045ECD           |
      | Width                    | 20                |
      | Reference point location | Top left          |
    And I press "Save changes"
    # Date range.
    And I add the element "Date range" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Date item                | Course start date    |
      | Font                     | Helvetica            |
      | Size                     | 20                   |
      | Colour                   | #045ECD              |
      | Width                    | 20                   |
      | Reference point location | Top left             |
      | Fallback string          | {{range_first_year}} |
      | id_startdate_0_day       | 24                   |
      | id_startdate_0_month     | October              |
      | id_startdate_0_year      | 2015                 |
      | id_enddate_0_day         | 21                   |
      | id_enddate_0_month       | March                |
      | id_enddate_0_year        | 2016                 |
      | String                   | Oct to March         |
    And I press "Save changes"
    And I should see "Date range" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Date range" "table_row"
    And the following fields match these values:
      | Date item                | Course start date    |
      | Font                     | Helvetica            |
      | Size                     | 20                   |
      | Colour                   | #045ECD              |
      | Width                    | 20                   |
      | Reference point location | Top left             |
      | Fallback string          | {{range_first_year}} |
      | id_startdate_0_day       | 24                   |
      | id_startdate_0_month     | October              |
      | id_startdate_0_year      | 2015                 |
      | id_enddate_0_day         | 21                   |
      | id_enddate_0_month       | March                |
      | id_enddate_0_year        | 2016                 |
      | String                   | Oct to March         |
    And I press "Save changes"
    # Digital signature.
    And I add the element "Digital signature" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Signature name         | This is the signature name |
      | Signature password     | Some awesome password      |
      | Signature location     | Mordor                     |
      | Signature reason       | Meh, felt like it.         |
      | Signature contact info | Sauron                     |
      | Width                  | 25                         |
      | Height                 | 15                         |
    And I press "Save changes"
    And I should see "Digital signature" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Digital signature" "table_row"
    And the following fields match these values:
      | Signature name         | This is the signature name |
      | Signature password     | Some awesome password      |
      | Signature location     | Mordor                     |
      | Signature reason       | Meh, felt like it.         |
      | Signature contact info | Sauron                     |
      | Width                  | 25                         |
      | Height                 | 15                         |
    And I press "Save changes"
    # Grade.
    And I add the element "Grade" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Grade item               | Activity : Assignment 1 |
      | Grade format             | Percentage              |
      | Font                     | Helvetica               |
      | Size                     | 20                      |
      | Colour                   | #045ECD                 |
      | Width                    | 20                      |
      | Reference point location | Top left                |
    And I press "Save changes"
    And I should see "Grade" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Grade" "table_row"
    And the following fields match these values:
      | Grade item               | Activity : Assignment 1 |
      | Grade format             | Percentage              |
      | Font                     | Helvetica               |
      | Size                     | 20                      |
      | Colour                   | #045ECD                 |
      | Width                    | 20                      |
      | Reference point location | Top left                |
    And I press "Save changes"
    # Grade item name.
    And I add the element "Grade item name" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Grade item               | Activity : Assignment 2 |
      | Font                     | Helvetica               |
      | Size                     | 20                      |
      | Colour                   | #045ECD                 |
      | Width                    | 20                      |
      | Reference point location | Top left                |
    And I press "Save changes"
    And I should see "Grade item name" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Grade item name" "table_row"
    And the following fields match these values:
      | Grade item               | Activity : Assignment 2 |
      | Font                     | Helvetica               |
      | Size                     | 20                      |
      | Colour                   | #045ECD                 |
      | Width                    | 20                      |
      | Reference point location | Top left                |
    And I press "Save changes"
    # Image.
    And I add the element "Image" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Width         | 25  |
      | Height        | 15  |
      | Alpha channel | 0.7 |
    And I press "Save changes"
    And I should see "Image" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Image" "table_row"
    And the following fields match these values:
      | Width         | 25  |
      | Height        | 15  |
      | Alpha channel | 0.7 |
    And I press "Save changes"
    # Student name.
    And I add the element "Student name" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Student name" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Student name" "table_row"
    And the following fields match these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # Teacher name.
    And I add the element "Teacher name" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Teacher                  | Teacher 2 |
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Teacher name" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Teacher name" "table_row"
    And the following fields match these values:
      | Teacher                  | Teacher 2 |
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # Text.
    And I add the element "Text" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Text                     | Test this |
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "Text" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "Text" "table_row"
    And the following fields match these values:
      | Text                     | Test this |
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # User field.
    And I add the element "User field" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | User field               | Country   |
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I should see "User field" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "User field" "table_row"
    And the following fields match these values:
      | User field               | Country   |
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    # User picture.
    And I add the element "User picture" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Width  | 10 |
      | Height | 10 |
    And I press "Save changes"
    And I should see "User picture" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "User picture" "table_row"
    And the following fields match these values:
      | Width  | 10 |
      | Height | 10 |
    And I press "Save changes"
    # QR Code.
    And I add the element "QR code" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Width         | 25  |
      | Height        | 15  |
    And I press "Save changes"
    And I should see "QR code" in the "elementstable" "table"
    And I click on ".edit-icon" "css_element" in the "QR code" "table_row"
    And the following fields match these values:
      | Width         | 25  |
      | Height        | 15  |
    And I press "Save changes"
    # Just to test there are no exceptions being thrown.
    And I follow "Reposition elements"
    And I press "Save and close"
    And I press "Save changes and preview"

  Scenario: Delete an element from a certificate template
    And I add the element "Background image" to page "1" of the "Custom certificate 1" certificate template
    And I press "Save changes"
    And I should see "Background image" in the "elementstable" "table"
    And I add the element "Student name" to page "1" of the "Custom certificate 1" certificate template
    And I press "Save changes"
    And I should see "Student name" in the "elementstable" "table"
    And I click on ".delete-icon" "css_element" in the "Student name" "table_row"
    And I press "Cancel"
    And I should see "Background image" in the "elementstable" "table"
    And I should see "Student name" in the "elementstable" "table"
    And I click on ".delete-icon" "css_element" in the "Student name" "table_row"
    And I press "Continue"
    And I should see "Background image" in the "elementstable" "table"
    And I should not see "Student name" in the "elementstable" "table"

@mod @mod_dataform @dataformactivity
Feature: Dataform access

    @javascript
    Scenario: Access not-ready dataform
        Given I start afresh with dataform "Dataform not ready test"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform not ready test"
        Then I see "This dataform appears to be new or with incomplete setup"
        And I log out

        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Dataform not ready test"
        Then I see "This activity is not ready for viewing"

@mod @mod_dataform @dataformfield
Feature: Field management

    @javascript
    Scenario: Field management
        Given I run dataform scenario "field management" with:
            | fieldtype  |
            | checkbox   |
            | commentmdl   |
            | duration   |
            | entrystate   |
            | file   |
            | number   |
            | picture   |
            | radiobutton   |
            | ratingmdl   |
            | select   |
            | selectmulti   |
            | text   |
            | textarea   |
            | time   |
            | url   |

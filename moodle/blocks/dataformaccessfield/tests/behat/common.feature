@block @block_dataformaccessfield @mod_dataform @dataformrule
Feature: Block dataform access field

    @javascript
    Scenario: Manage access rule
        Given I run dataform scenario "access rule management" with:
            | ruletype | field |

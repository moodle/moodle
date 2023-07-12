SimpleSAMLphp Translation Portal
================================================================

<!-- 
    This file is written in Markdown syntax. 
    For more information about how to use the Markdown syntax, read here:
    http://daringfireball.net/projects/markdown/syntax
-->

<!-- {{TOC}} -->

## How translated terms are referred from a template

Here is an example of how two terms are included in a template from dictionary files:

    <h2><?php echo $this->t('{core:frontpage:about_header}'); ?></h2>
    <p><?php echo $this->t('{core:frontpage:about_text}'); ?></p>

In this example, two translated terms are included: `about_header` and `about_text`. Both these terms are found in a dictionary file named `frontpage`, inside the module named `core`.

**Note:** An important use-case here is that you can create your own module, that includes a new theme that overrides some of the default templates. You may in this template refer to both terms from the existing dictionary files, but you can also add new dictionary files in your new module that may introduce new alternative terms.

## The definition file

When the template library is about to lookup the translation of a term, it will lookup 

  * the definition file, for the English translation, and 
  * the translation file, for translation to other languages.

SimpleSAMLphp will always fallback to the English translation using the definition file, both:

  * when the term is not translated into the *current selected language*, and
  * when the translation file is not available at all.

The name of the definition file is `BASENAME.definition.json`, where the term is referred to like this: `{MODULENAME:BASENAME:TERM}`. The file MUST be placed in the followng location: `modules/MODULENAME/dictionaries/BASENAME.definition.json`.

The content of the defintion file is a *JSON encoded array* of `term => definition`, where definition is an array with an required `en` index for the english translation, and the value is the English text.

Here is an example of a definition file with three terms:

    {
        "header": {
            "en": "Missing cookie"
        },
        "description": {
            "en": "You appear to have disabled cookies in your browser. Please check the settings in your browser, and try again."
        },
        "retry": {
            "en": "Retry"
        }
    }

Note: you may not include other languages in the definition files, the `en` index is used in order to at a later point in time introduce more meta information for each term, like in example:

    "header": {
        "en": "Missing cookie",
        "_note": "This text shows up on the error page when the browser do not support cookies."
    },

To summarize the pattern of the definition file is as follows:

    {
        "TERM1": {
            "en": "English text 1"
        },
        "TERM2": {
            "en": "English text 2"
        }
    }

## The translation file

The translation file is similar to the definition file, but including translation to languages others than English.

The structure of the file is identical to the definition files, except from the language index, which now is not `en`, but the actual langauge that is translated:


    {
        "TERM1": {
            "no": "Norsk tekst 1",
            "da": "Dansk tekst 1"
        },
        "TERM2": {
            "no": "Norsk tekst 2",
            "da": "Dansk tekst 2"
        }
    }

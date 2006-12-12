$Id$

To Install it:
    - Enable if from "Administration/Filters".
  
To Use it:
    - Create your contents in multiple languages.
    - Enclose every language content between:
        <span lang="XX" class="multilang">your_content_here</span><span lang="YY" class="multilang">your_content_other_lang</span>
    - Test it (by changing your language).

How it works:
    - look for "lang blocks" in the code.
    - for each "lang block":
        - if there are texts in the currently active language, print them.
        - else, if there exists texts in the current parent language, print them.
        - else, print the first language found in the text.
    - text out of "lang blocks" will be showed always.

Definition of "lang block":
    Is a collection of lang tags separated only by whitespace chars (space,
    tab, linefeed or return chars).

One example in action:
    - This text:
        <span lang="en" class="multilang">Hello!</span><span lang="es" class="multilang">Hola!</span>
        This text is common for every language because it's out from any lang block.
        <span lang="en" class="multilang">Bye!</span><span lang="it" class="multilang">Ciao!</span>

    - Will print, if current language is English:
        Hello!
        This text is common for every language because it's out from any lang block.
        Bye!

    - And, in Spanish, it will print:
        Hola!
        This text is common for every language because it's out from any lang block.
        Bye!
    

Ciao, Eloy :-)
stronk7@moodle.org
2005-11-16

Syntax was changed in 1.8, the conversion of existing text is done from admin/multilangupgrade.php
Ciao, skodak :-)
2006-12-11
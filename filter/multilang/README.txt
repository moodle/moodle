$Id$

To Install it:
    - Enable if from "Administration/Filters".
  
To Use it:
    - Create your contents in multiple languages.
    - Enclose every language content between:
        <span lang="XX">your_content_here</span>
    - Test it (by changing your language).

How it works:
    - look for "lang blocks" in the code.
    - for each "lang block":
        - if there are texts in the currently active language, print them.
        - else, if there exists texts in the current parent language, print them.
        - else, if there are English texts, print them
        - else, print the first language found in the text.
    - text out of "lang blocks" will be showed always.

Definition of "lang block":
    Is a collection of lang tags separated only by whitespace chars (space,
    tab, linefeed or return chars).

One example in action:
    - This text:
        <span lang="en">Hello!</span><span lang="es">Hola!</span>
        This text is common for every language because it's out from any lang block.
        <span lang="en">Bye!</span><span lang="it">Ciao!</span>

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

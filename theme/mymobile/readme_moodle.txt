MyMobile Theme
==============

jQuery Mobile listview hack
---------------------------

The jQuery Mobile library (jquery.mobile-[...].js) includes a custom hack to
allow lisview elements to have a their <a> not as direct child of <li>. This
is used for activities which encapsulate the <a> in several divs.

Run the following command to view the hack:
    git show 3b84abce6ab7ff3862cd92c7d74ce0e8578004b3

Remember to place this hack in the library when you update it. Also, we often
forget that activities can be listed on the front page too, please test the
theme in both a course and the front page.

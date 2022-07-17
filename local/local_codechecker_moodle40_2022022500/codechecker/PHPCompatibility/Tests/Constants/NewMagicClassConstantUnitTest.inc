<?php

namespace foo {
    class bar {}

    echo bar::class; // foo\bar
}

namespace MyNameSpace {
	class xyz {}

	remove_filter('theme_filter', [\namespace\xyz::class, 'methodName'], 30);
}

/*
 * False positives check.
 */
echo bar::classProp; // Not the keyword.
new class {} // Anonymous class, not the keyword.

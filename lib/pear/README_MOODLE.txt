MOODLE-SPECIFIC PEAR MODIFICATIONS
==================================

Auth/RADIUS
===========

1/ Changed static call to correct alternative (MDL-38373):
    - From: PEAR::loadExtension('radius'); (in global scope)
    - To: $this->loadExtension('radius'); (in constructor)

XML/Parser
=================
1/ changed ereg_ to preg_
* http://cvs.moodle.org/moodle/lib/pear/XML/Parser.php.diff?r1=1.1&r2=1.2


Quickforms
==========
Full of our custom hacks, no way to upgrade to latest upstream.
Most probably we will stop using this library in the future.

MDL-20876 - replaced split() with explode() or preg_split() where appropriate
MDL-40267 - Moodle core_text strlen functions used for range rule rule to be utf8 safe.
MDL-46467 - $mform->hardfreeze causes labels to loose their for HTML attribute

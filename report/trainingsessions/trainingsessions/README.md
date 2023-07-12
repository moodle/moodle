moodle-report_trainingsessions
==============================

A structured report of use time using use_stats bloc time compliatons.

Provides : 

Student individual details report :
* Use time detailed in course structure
* Use time in course space (outside activities)
* Working sessions reports 

Dependencies:
===============
Block moodle-block_use_stats

Optional (PRO version):

For PDF generation, you will need using the VFLibs additional libraries you can get at 
http://github.com/vfremaux/moodle-local_vflibs

This will add adapted version of some core libraries. 

In our case, we need a better control of the page length in TCPDF for handling automatic
page breaks for long reports. This is not handled by the standard TCPDF library

Versions:
=========
Available in Moodle 2.x (master) and Moodle 1.9 (MOODLE_19_STABLE)

2016042900 : Adds a specific capability for batchs

2017021600 - New distribution policy
====================================

Due to the huge amount of demands for increasing reliability and accuracy of the time tracking tools in Moodle, and
the huge amount of work hours that it needs, we have decided to split our publication policy keeping the main essential functions
in the community version of the plugin, and deferring the most demanding, test and validation consuming, or advanced features into
a "pro" version. We hope usual users of such "dual release" plugins will understand our need to stay being able to maintain and pursue
provide support, innovation and code quality for Moodle, but being also supported ourself to do so. Plugin documentation provides
information about feature sets and how to get "pro" versions. In our scope is also to provide simpler plugins for the usual non advanced use,
and keep very fine options to people that really needs them.

Policy change will not affect the moodle version path of the plugin, data model remains unchanged, such as core bound definitions.

Pro version objectives:

* providing essential "pro" webservices to get full external control
* file formats exhaustivity, import, exports and document format output
* enhanced flexibility, rare or specific options
* payed customer integrations
* mass performance
* enhanced productivity

Community standard concerns:

* core features and service
* moodle standard compliance
* moodle core plugin API contract
* security implementation
* base documentation

2017080100 - X.X.0006
=============================
* adding grading setup control by capability
* adding defaults to grading mode selectors

2017080100 - X.X.0007
=============================

Allow suspended students to be removed from reports with a setting.

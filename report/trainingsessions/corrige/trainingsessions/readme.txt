Use Time Based Course Reports
####################################

Author : Valery Fremaux (valery.fremaux@club-internet.fr)

Dependancies : works with the blocks/use_stats log analyser module.

####################################

The Course Training Session report provides a structured reporting of elapsed time
by the usrs within a Moodle course, and presenting those detailed result conformely
to the course most probable pedagogic layout. 

The pedagogic organisation is compiled from the internal course setup information and
will be aware of most course formats in Moodle. At least are handled : 

- Topic course format
- Weekly course format
- Page course format
- flexSection format

#####################################

Install : Unzip the report in the /report directory of your Moodle installation.

You will need having installed the blocks/use_stats custom block

For PDF generation, you will need using the VFLibs additional libraries you can get at 
http://github.com/vfremaux/moodle-local_vflibs

This will add adapted version of some core libraries. 

In our case, we need a better control of the page length in TCPDF for handling automatic
page breaks for long reports. This is not handled by the standard TCPDF library

####################################

Features : 

* Per student reports : reports the entire pedagogic track with individual and summarized 
presence time.

* Per group (or training session bundled in groups) : Reports a summarized presence time 
for an entire group.

* Per group summary report

* Excel exports of individual timesheet

* Excel export of a group timesheet as an Excel multiple individual sheet.

* Batch generation and Raw report as one single Excel table for the course

* "All course" summary capitalisers.

Automated generation of group reports
======================================

the training session provides a batch management that allows storing tasks for generating single or multiple
document sets.

Setting capabilities for student access to his own report
=========================================================

As a default, student access is not given to reports. In case the own training
report needs to be displayed for students, change following configurations in Moodle :

1. Add the moodle/site:viewreports to the student role. 
2. Check the coursereport/trainingsessions:view is set for student role. 
3. Check the coursereport/trainingsessions:viewother is NOT set for student role. 

If you do not want students have access to this report everywhere in all courses they are enrolled in, 
but you want to control course by course the access to their report, use role override on student role
at course level and add an averride on the moodle/site:viewreports on that course. 

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

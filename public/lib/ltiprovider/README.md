This set of PHP classes encapsulates the code required by a Learning Tools Interoperability<sup>®</sup> (LTI<sup>®</sup>) compliant tool provider to communicate with an LTI tool consumer.
It includes support for LTI 1.1 and the unofficial extensions to LTI 1.0, as well as the registration process and services of LTI 2.0.
These classes are an extension of the LTI Tool Provider class library created by the ceLTIc project (http://www.spvsoftwareproducts.com/php/lti_tool_provider/).

Whilst supporting LTI is relatively simple, the benefits to using a class library like this one are:
* the abstraction layer provided by the classes keeps the LTI communications separate from the application code;
* the code can be re-used between multiple tool providers;
* LTI data is transformed into useful objects and missing data automatically replaced with sensible defaults;
* the outcomes service function uses LTI 1.1 or the unofficial outcomes extension according to whichever is supported by the tool consumer;
* the unofficial extensions for memberships and setting services are supported;
* additional functionality is included to:
    * enable/disable a consumer key;
    * set start and end times for enabling access for each consumer key;
    * set up arrangements such that users from different resource links can all collaborate together within a single tool provider link;
* tool providers can take advantage of LTI updates with minimal impact on their application code.

The wiki area of this repository contains [documentation](https://github.com/1EdTech/LTI-Tool-Provider-Library-PHP/wiki) for this library.  The [rating LTI application](https://github.com/1EdTech/LTI-Sample-Tool-Provider-PHP) is based on this library to further illustrate how it can be used.

&copy; 2016 IMS Global Learning Consortium Inc. All Rights Reserved. Trademark Policy - (www.imsglobal.org/trademarks)

<sup><sub>Learning Tools Interoperability and LTI are registered trademarks of IMS Global Learning Consortium Inc.</sub></sup>

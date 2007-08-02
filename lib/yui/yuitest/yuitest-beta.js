/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/
YAHOO.namespace("tool");

//-----------------------------------------------------------------------------
// TestLogger object
//-----------------------------------------------------------------------------

/**
 * Displays test execution progress and results, providing filters based on
 * different key events.
 * @namespace YAHOO.tool
 * @class TestLogger
 * @constructor
 * @param {HTMLElement} element (Optional) The element to create the logger in.
 * @param {Object} config (Optional) Configuration options for the logger.
 */
YAHOO.tool.TestLogger = function (element, config) {
    YAHOO.tool.TestLogger.superclass.constructor.call(this, element, config);
    this.init();
};

YAHOO.lang.extend(YAHOO.tool.TestLogger, YAHOO.widget.LogReader, {

    footerEnabled : true,
    newestOnTop : false,

    /**
     * Formats message string to HTML for output to console.
     * @private
     * @method formatMsg
     * @param oLogMsg {Object} Log message object.
     * @return {String} HTML-formatted message for output to console.
     */
    formatMsg : function(message /*:Object*/) {
    
        var category /*:String*/ = message.category;        
        var text /*:String*/ = this.html2Text(message.msg);
        
        return "<pre><p><span class=\"" + category + "\">" + category.toUpperCase() + "</span> " + text + "</p></pre>";
    
    },
    
    //-------------------------------------------------------------------------
    // Private Methods
    //-------------------------------------------------------------------------
    
    /*
     * Initializes the logger.
     * @private
     */
    init : function () {
    
        //attach to any available TestRunner
        if (YAHOO.tool.TestRunner){
            this.setTestRunner(YAHOO.tool.TestRunner);
        }
        
        //hide useless sources
        this.hideSource("global");
        this.hideSource("LogReader");
        
        //hide useless message categories
        this.hideCategory("warn");
        this.hideCategory("window");
        this.hideCategory("time");
        
        //reset the logger
        this.clearConsole();
    },
    
    /**
     * Clears the reference to the TestRunner from previous operations. This 
     * unsubscribes all events and removes the object reference.
     * @return {Void}
     * @static
     */
    clearTestRunner : function () /*:Void*/ {
        if (this._runner){
            this._runner.unsubscribeAll();
            this._runner = null;
        }
    },
    
    /**
     * Sets the source test runner that the logger should monitor.
     * @param {YAHOO.tool.TestRunner} testRunner The TestRunner to observe.
     * @return {Void}
     * @static
     */
    setTestRunner : function (testRunner /*:YAHOO.tool.TestRunner*/) /*:Void*/ {
    
        if (this._runner){
            this.clearTestRunner();
        }
        
        this._runner = testRunner;
        
        //setup event _handlers
        testRunner.subscribe(testRunner.TEST_PASS_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.TEST_FAIL_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.TEST_IGNORE_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.BEGIN_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.COMPLETE_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.TEST_SUITE_BEGIN_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.TEST_SUITE_COMPLETE_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.TEST_CASE_BEGIN_EVENT, this._handleTestRunnerEvent, this, true);
        testRunner.subscribe(testRunner.TEST_CASE_COMPLETE_EVENT, this._handleTestRunnerEvent, this, true);    
    },
    
    //-------------------------------------------------------------------------
    // Event Handlers
    //-------------------------------------------------------------------------
    
    /**
     * Handles all TestRunner events, outputting appropriate data into the console.
     * @param {Object} data The event data object.
     * @return {Void}
     * @private
     */
    _handleTestRunnerEvent : function (data /*:Object*/) /*:Void*/ {
    
        //shortcut variables
        var TestRunner /*:Object*/ = YAHOO.tool.TestRunner;
    
        //data variables
        var message /*:String*/ = "";
        var messageType /*:String*/ = "";
        
        switch(data.type){
            case TestRunner.BEGIN_EVENT:
                message = "Testing began at " + (new Date()).toString() + ".";
                messageType = "info";
                break;
                
            case TestRunner.COMPLETE_EVENT:
                message = "Testing completed at " + (new Date()).toString() + ".\nPassed:" 
                    + data.results.passed + " Failed:" + data.results.failed + " Total:" + data.results.total;
                messageType = "info";
                break;
                
            case TestRunner.TEST_FAIL_EVENT:
                message = data.testName + ": " + data.error.getMessage();
                messageType = "fail";
                break;
                
            case TestRunner.TEST_IGNORE_EVENT:
                message = data.testName + ": ignored.";
                messageType = "ignore";
                break;
                
            case TestRunner.TEST_PASS_EVENT:
                message = data.testName + ": passed.";
                messageType = "pass";
                break;
                
            case TestRunner.TEST_SUITE_BEGIN_EVENT:
                message = "Test suite \"" + data.testSuite.name + "\" started.";
                messageType = "info";
                break;
                
            case TestRunner.TEST_SUITE_COMPLETE_EVENT:
                message = "Test suite \"" + data.testSuite.name + "\" completed.\nPassed:" 
                    + data.results.passed + " Failed:" + data.results.failed + " Total:" + data.results.total;
                messageType = "info";
                break;
                
            case TestRunner.TEST_CASE_BEGIN_EVENT:
                message = "Test case \"" + data.testCase.name + "\" started.";
                messageType = "info";
                break;
                
            case TestRunner.TEST_CASE_COMPLETE_EVENT:
                message = "Test case \"" + data.testCase.name + "\" completed.\nPassed:" 
                    + data.results.passed + " Failed:" + data.results.failed + " Total:" + data.results.total;
                messageType = "info";
                break;
            default:
                message = "Unexpected event " + data.type;
                message = "info";
        }
    
        YAHOO.log(message, messageType, "TestRunner");    
    }
    
});
YAHOO.namespace("tool");

/**
 * The YUI test tool
 * @module yuitest
 * @namespace YAHOO.tool
 * @requires yahoo,dom,event,logger
 */


//-----------------------------------------------------------------------------
// TestRunner object
//-----------------------------------------------------------------------------

/**
 * Runs test suites and test cases, providing events to allowing for the
 * interpretation of test results.
 * @namespace YAHOO.tool
 * @class TestRunner
 * @static
 */
YAHOO.tool.TestRunner = (function(){

    function TestRunner(){
    
        //inherit from EventProvider
        TestRunner.superclass.constructor.apply(this,arguments);
        
        /**
         * The test objects to run.
         * @type Array
         * @private
         */
        this.items /*:Array*/ = [];
        
        //create events
        var events /*:Array*/ = [
            this.TEST_CASE_BEGIN_EVENT,
            this.TEST_CASE_COMPLETE_EVENT,
            this.TEST_SUITE_BEGIN_EVENT,
            this.TEST_SUITE_COMPLETE_EVENT,
            this.TEST_PASS_EVENT,
            this.TEST_FAIL_EVENT,
            this.TEST_IGNORE_EVENT,
            this.COMPLETE_EVENT,
            this.BEGIN_EVENT
        ];
        for (var i=0; i < events.length; i++){
            this.createEvent(events[i], { scope: this });
        }
       
   
    }
    
    YAHOO.lang.extend(TestRunner, YAHOO.util.EventProvider, {
    
        //-------------------------------------------------------------------------
        // Constants
        //-------------------------------------------------------------------------
         
        /**
         * Fires when a test case is opened but before the first 
         * test is executed.
         * @event testcasebegin
         */         
        TEST_CASE_BEGIN_EVENT /*:String*/ : "testcasebegin",
        
        /**
         * Fires when all tests in a test case have been executed.
         * @event testcasecomplete
         */        
        TEST_CASE_COMPLETE_EVENT /*:String*/ : "testcasecomplete",
        
        /**
         * Fires when a test suite is opened but before the first 
         * test is executed.
         * @event testsuitebegin
         */        
        TEST_SUITE_BEGIN_EVENT /*:String*/ : "testsuitebegin",
        
        /**
         * Fires when all test cases in a test suite have been
         * completed.
         * @event testsuitecomplete
         */        
        TEST_SUITE_COMPLETE_EVENT /*:String*/ : "testsuitecomplete",
        
        /**
         * Fires when a test has passed.
         * @event pass
         */        
        TEST_PASS_EVENT /*:String*/ : "pass",
        
        /**
         * Fires when a test has failed.
         * @event fail
         */        
        TEST_FAIL_EVENT /*:String*/ : "fail",
        
        /**
         * Fires when a test has been ignored.
         * @event ignore
         */        
        TEST_IGNORE_EVENT /*:String*/ : "ignore",
        
        /**
         * Fires when all test suites and test cases have been completed.
         * @event complete
         */        
        COMPLETE_EVENT /*:String*/ : "complete",
        
        /**
         * Fires when the run() method is called.
         * @event begin
         */        
        BEGIN_EVENT /*:String*/ : "begin",    
    
        //-------------------------------------------------------------------------
        // Private Methods
        //-------------------------------------------------------------------------
         
        /**
         * Runs a given test case.
         * @param {YAHOO.tool.TestCase} testCase The test case to run.
         * @return {Object} Results of the execution with properties passed, failed, and total.
         * @method _runTestCase
         * @private
         * @static
         */
        _runTestCase : function (testCase /*YAHOO.tool.TestCase*/) /*:Void*/{
        
            //object to store results
            var results /*:Object*/ = {};
        
            //test case begins
            this.fireEvent(this.TEST_CASE_BEGIN_EVENT, { testCase: testCase });
        
            //gather the test functions
            var tests /*:Array*/ = [];
            for (var prop in testCase){
                if (prop.indexOf("test") === 0 && typeof testCase[prop] == "function") {
                    tests.push(prop);
                }
            }
            
            //get the "should" test cases
            var shouldFail /*:Object*/ = testCase._should.fail || {};
            var shouldError /*:Object*/ = testCase._should.error || {};
            var shouldIgnore /*:Object*/ = testCase._should.ignore || {};
            
            //test counts
            var failCount /*:int*/ = 0;
            var passCount /*:int*/ = 0;
            var runCount /*:int*/ = 0;
            
            //run each test
            for (var i=0; i < tests.length; i++){
            
                //figure out if the test should be ignored or not
                if (shouldIgnore[tests[i]]){
                    this.fireEvent(this.TEST_IGNORE_EVENT, { testCase: testCase, testName: tests[i] });
                    continue;
                }
            
                //variable to hold whether or not the test failed
                var failed /*:Boolean*/ = false;
                var error /*:Error*/ = null;
            
                //run the setup
                testCase.setUp();
                
                //try the test
                try {
                
                    //run the test
                    testCase[tests[i]]();
                    
                    //if it should fail, and it got here, then it's a fail because it didn't
                    if (shouldFail[tests[i]]){
                        error = new YAHOO.util.ShouldFail();
                        failed = true;
                    } else if (shouldError[tests[i]]){
                        error = new YAHOO.util.ShouldError();
                        failed = true;
                    }
                               
                } catch (thrown /*:Error*/){
                    if (thrown instanceof YAHOO.util.AssertionError) {
                        if (!shouldFail[tests[i]]){
                            error = thrown;
                            failed = true;
                        }
                    } else {
                        //first check to see if it should error
                        if (!shouldError[tests[i]]) {                        
                            error = new YAHOO.util.UnexpectedError(thrown);
                            failed = true;
                        } else {
                            //check to see what type of data we have
                            if (YAHOO.lang.isString(shouldError[tests[i]])){
                                
                                //if it's a string, check the error message
                                if (thrown.message != shouldError[tests[i]]){
                                    error = new YAHOO.util.UnexpectedError(thrown);
                                    failed = true;                                    
                                }
                            } else if (YAHOO.lang.isObject(shouldError[tests[i]])){
                            
                                //if it's an object, check the instance and message
                                if (!(thrown instanceof shouldError[tests[i]].constructor) || 
                                        thrown.message != shouldError[tests[i]].message){
                                    error = new YAHOO.util.UnexpectedError(thrown);
                                    failed = true;                                    
                                }
                            
                            }
                        
                        }
                    }
                    
                } finally {
                
                    //fireEvent appropriate event
                    if (failed) {
                        this.fireEvent(this.TEST_FAIL_EVENT, { testCase: testCase, testName: tests[i], error: error });
                    } else {
                        this.fireEvent(this.TEST_PASS_EVENT, { testCase: testCase, testName: tests[i] });
                    }            
                }
                
                //run the tear down
                testCase.tearDown();
                
                //update results
                results[tests[i]] = { 
                    result: failed ? "fail" : "pass",
                    message : error ? error.getMessage() : "Test passed"
                };
                
                //update counts
                runCount++;
                failCount += (failed ? 1 : 0);
                passCount += (failed ? 0 : 1);
            }
            
            //add test counts to results
            results.total = runCount;
            results.failed = failCount;
            results.passed = passCount;
            
            //test case is done
            this.fireEvent(this.TEST_CASE_COMPLETE_EVENT, { testCase: testCase, results: results });
            
            //return results
            return results;
        
        },
        
        /**
         * Runs all the tests in a test suite.
         * @param {YAHOO.tool.TestSuite} testSuite The test suite to run.
         * @return {Object} Results of the execution with properties passed, failed, and total.
         * @method _runTestSuite
         * @private
         * @static
         */
        _runTestSuite : function (testSuite /*:YAHOO.tool.TestSuite*/) {
        
            //object to store results
            var results /*:Object*/ = {
                passed: 0,
                failed: 0,
                total: 0
            };
        
            //fireEvent event for beginning of test suite run
            this.fireEvent(this.TEST_SUITE_BEGIN_EVENT, { testSuite: testSuite });
        
            //iterate over the test suite items
            for (var i=0; i < testSuite.items.length; i++){
                var result = null;
                if (testSuite.items[i] instanceof YAHOO.tool.TestSuite) {
                    result = this._runTestSuite(testSuite.items[i]);
                } else if (testSuite.items[i] instanceof YAHOO.tool.TestCase) {
                    result = this._runTestCase(testSuite.items[i]);
                }
                
                if (result !== null){
                    results.total += result.total;
                    results.passed += result.passed;
                    results.failed += result.failed;
                    results[testSuite.items[i].name] = result;
                }
            }
    
            //fireEvent event for completion of test suite run
            this.fireEvent(this.TEST_SUITE_COMPLETE_EVENT, { testSuite: testSuite, results: results });
            
            //return the results
            return results;
        
        },
        
        /**
         * Runs a test case or test suite, returning the results.
         * @param {YAHOO.tool.TestCase|YAHOO.tool.TestSuite} testObject The test case or test suite to run.
         * @return {Object} Results of the execution with properties passed, failed, and total.
         * @private
         * @method _run
         * @static
         */
        _run : function (testObject /*:YAHOO.tool.TestCase|YAHOO.tool.TestSuite*/) /*:Void*/ {
            if (YAHOO.lang.isObject(testObject)){
                if (testObject instanceof YAHOO.tool.TestSuite) {
                    return this._runTestSuite(testObject);
                } else if (testObject instanceof YAHOO.tool.TestCase) {
                    return this._runTestCase(testObject);
                } else {
                    throw new TypeError("_run(): Expected either YAHOO.tool.TestCase or YAHOO.tool.TestSuite.");
                }    
            }        
        },
        
        //-------------------------------------------------------------------------
        // Protected Methods
        //-------------------------------------------------------------------------   
    
        /*
         * Fires events for the TestRunner. This overrides the default fireEvent()
         * method from EventProvider to add the type property to the data that is
         * passed through on each event call.
         * @param {String} type The type of event to fire.
         * @param {Object} data (Optional) Data for the event.
         * @method fireEvent
         * @static
         * @protected
         */
        fireEvent : function (type /*:String*/, data /*:Object*/) /*:Void*/ {
            data = data || {};
            data.type = type;
            TestRunner.superclass.fireEvent.call(this, type, data);
        },
        
        //-------------------------------------------------------------------------
        // Public Methods
        //-------------------------------------------------------------------------   
    
        /**
         * Adds a test suite or test case to the list of test objects to run.
         * @param testObject Either a TestCase or a TestSuite that should be run.
         */
        add : function (testObject /*:Object*/) /*:Void*/ {
            this.items.push(testObject);
        },
        
        /**
         * Removes all test objects from the runner.
         */
        clear : function () /*:Void*/ {
            while(this.items.length){
                this.items.pop();
            }
        },
    
        /**
         * Runs the test suite.
         */
        run : function (testObject /*:Object*/) /*:Void*/ { 
            var results = null;
            
            this.fireEvent(this.BEGIN_EVENT);
       
            //an object passed in overrides everything else
            if (YAHOO.lang.isObject(testObject)){
                results = this._run(testObject);  
            } else {
                results = {
                    passed: 0,
                    failed: 0,
                    total: 0
                };
                for (var i=0; i < this.items.length; i++){
                    var result = this._run(this.items[i]);
                    results.passed += result.passed;
                    results.failed += result.failed;
                    results.total += result.total;
                    results[this.items[i].name] = result;
                }            
            }
            
            this.fireEvent(this.COMPLETE_EVENT, { results: results });
        }    
    });
    
    return new TestRunner();
    
})();
YAHOO.namespace("tool");


//-----------------------------------------------------------------------------
// TestSuite object
//-----------------------------------------------------------------------------

/**
 * A test suite that can contain a collection of TestCase and TestSuite objects.
 * @param {String} name The name of the test fixture.
 * @namespace YAHOO.tool
 * @class TestSuite
 * @constructor
 */
YAHOO.tool.TestSuite = function (name /*:String*/) {

    /**
     * The name of the test suite.
     */
    this.name /*:String*/ = name || YAHOO.util.Dom.generateId(null, "testSuite");

    /**
     * Array of test suites and
     * @private
     */
    this.items /*:Array*/ = [];

};

YAHOO.tool.TestSuite.prototype = {
    
    /**
     * Adds a test suite or test case to the test suite.
     * @param {YAHOO.tool.TestSuite||YAHOO.tool.TestCase} testObject The test suite or test case to add.
     */
    add : function (testObject /*:YAHOO.tool.TestSuite*/) /*:Void*/ {
        if (testObject instanceof YAHOO.tool.TestSuite || testObject instanceof YAHOO.tool.TestCase) {
            this.items.push(testObject);
        }
    }
    
};
YAHOO.namespace("tool");

//-----------------------------------------------------------------------------
// TestCase object
//-----------------------------------------------------------------------------

/**
 * Test case containing various tests to run.
 * @param template An object containing any number of test methods, other methods,
 *                 an optional name, and anything else the test case needs.
 * @class TestCase
 * @namespace YAHOO.tool
 * @constructor
 */
YAHOO.tool.TestCase = function (template /*:Object*/) {
    
    /**
     * Special rules for the test case. Possible subobjects
     * are fail, for tests that should fail, and error, for
     * tests that should throw an error.
     */
    this._should /*:Object*/ = {};
    
    //copy over all properties from the template to this object
    for (var prop in template) {
        this[prop] = template[prop];
    }    
    
    //check for a valid name
    if (!YAHOO.lang.isString(this.name)){
        /**
         * Name for the test case.
         */
        this.name /*:String*/ = YAHOO.util.Dom.generateId(null, "testCase");
    }

};

YAHOO.tool.TestCase.prototype = {  

    //-------------------------------------------------------------------------
    // Test Methods
    //-------------------------------------------------------------------------

    /**
     * Function to run before each test is executed.
     */
    setUp : function () /*:Void*/ {
    },
    
    /**
     * Function to run after each test is executed.
     */
    tearDown: function () /*:Void*/ {    
    }
};
YAHOO.namespace("util");

//-----------------------------------------------------------------------------
// Assert object
//-----------------------------------------------------------------------------

/**
 * The Assert object provides functions to test JavaScript values against
 * known and expected results. Whenever a comparison (assertion) fails,
 * an error is thrown.
 *
 * @namespace YAHOO.util
 * @class Assert
 * @static
 */
YAHOO.util.Assert = {

    //-------------------------------------------------------------------------
    // Generic Assertion Methods
    //-------------------------------------------------------------------------
    
    /** 
     * Forces an assertion error to occur.
     * @param {String} message (Optional) The message to display with the failure.
     * @method fail
     * @static
     */
    fail : function (message /*:String*/) /*:Void*/ {
        throw new YAHOO.util.AssertionError(message || "Test force-failed.");
    },       
    
    //-------------------------------------------------------------------------
    // Equality Assertion Methods
    //-------------------------------------------------------------------------    
    
    /**
     * Asserts that a value is equal to another. This uses the double equals sign
     * so type cohersion may occur.
     * @param {Object} expected The expected value.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method areEqual
     * @static
     */
    areEqual : function (expected /*:Object*/, actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (expected != actual) {
            throw new YAHOO.util.ComparisonFailure(message || "Values should be equal.", expected, actual);
        }
    },
    
    /**
     * Asserts that a value is not equal to another. This uses the double equals sign
     * so type cohersion may occur.
     * @param {Object} unexpected The unexpected value.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method areNotEqual
     * @static
     */
    areNotEqual : function (unexpected /*:Object*/, actual /*:Object*/, 
                         message /*:String*/) /*:Void*/ {
        if (unexpected == actual) {
            throw new YAHOO.util.UnexpectedValue(message || "Values should not be equal.", unexpected);
        }
    },
    
    /**
     * Asserts that a value is not the same as another. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} unexpected The unexpected value.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method areNotSame
     * @static
     */
    areNotSame : function (unexpected /*:Object*/, actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (unexpected === actual) {
            throw new YAHOO.util.UnexpectedValue(message || "Values should not be the same.", unexpected);
        }
    },

    /**
     * Asserts that a value is the same as another. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} expected The expected value.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method areSame
     * @static
     */
    areSame : function (expected /*:Object*/, actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (expected !== actual) {
            throw new YAHOO.util.ComparisonFailure(message || "Values should be the same.", expected, actual);
        }
    },    
    
    //-------------------------------------------------------------------------
    // Boolean Assertion Methods
    //-------------------------------------------------------------------------    
    
    /**
     * Asserts that a value is false. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isFalse
     * @static
     */
    isFalse : function (actual /*:Boolean*/, message /*:String*/) {
        if (false !== actual) {
            throw new YAHOO.util.ComparisonFailure(message || "Value should be false.", false, actual);
        }
    },
    
    /**
     * Asserts that a value is true. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isTrue
     * @static
     */
    isTrue : function (actual /*:Boolean*/, message /*:String*/) /*:Void*/ {
        if (true !== actual) {
            throw new YAHOO.util.ComparisonFailure(message || "Value should be true.", true, actual);
        }

    },
    
    //-------------------------------------------------------------------------
    // Special Value Assertion Methods
    //-------------------------------------------------------------------------    
    
    /**
     * Asserts that a value is not a number.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNaN
     * @static
     */
    isNaN : function (actual /*:Object*/, message /*:String*/) /*:Void*/{
        if (!isNaN(actual)){
            throw new YAHOO.util.ComparisonFailure(message || "Value should be NaN.", NaN, actual);
        }    
    },
    
    /**
     * Asserts that a value is not the special NaN value.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNotNaN
     * @static
     */
    isNotNaN : function (actual /*:Object*/, message /*:String*/) /*:Void*/{
        if (isNaN(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Values should not be NaN.", NaN);
        }    
    },
    
    /**
     * Asserts that a value is not null. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNotNull
     * @static
     */
    isNotNull : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (YAHOO.lang.isNull(actual)) {
            throw new YAHOO.util.UnexpectedValue(message || "Values should not be null.", null);
        }
    },

    /**
     * Asserts that a value is not undefined. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNotUndefined
     * @static
     */
    isNotUndefined : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (YAHOO.lang.isUndefined(actual)) {
            throw new YAHOO.util.UnexpectedValue(message || "Value should not be undefined.", undefined);
        }
    },

    /**
     * Asserts that a value is null. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNull
     * @static
     */
    isNull : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isNull(actual)) {
            throw new YAHOO.util.ComparisonFailure(message || "Value should be null.", null, actual);
        }
    },
        
    /**
     * Asserts that a value is undefined. This uses the triple equals sign
     * so no type cohersion may occur.
     * @param {Object} expected The expected value.
     * @param {Object} actual The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isUndefined
     * @static
     */
    isUndefined : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isUndefined(actual)) {
            throw new YAHOO.util.ComparisonFailure(message || "Value should be undefined.", undefined, actual);
        }
    },    
    
    //--------------------------------------------------------------------------
    // Instance Assertion Methods
    //--------------------------------------------------------------------------    
   
    /**
     * Asserts that a value is an array.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isArray
     * @static
     */
    isArray : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isArray(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Value should be an array.", actual);
        }    
    },
   
    /**
     * Asserts that a value is a Boolean.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isBoolean
     * @static
     */
    isBoolean : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isBoolean(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Value should be a Boolean.", actual);
        }    
    },
   
    /**
     * Asserts that a value is a function.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isFunction
     * @static
     */
    isFunction : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isFunction(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Value should be a function.", actual);
        }    
    },
   
    /**
     * Asserts that a value is an instance of a particular object. This may return
     * incorrect results when comparing objects from one frame to constructors in
     * another frame. For best results, don't use in a cross-frame manner.
     * @param {Function} expected The function that the object should be an instance of.
     * @param {Object} actual The object to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isInstanceOf
     * @static
     */
    isInstanceOf : function (expected /*:Function*/, actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!(actual instanceof expected)){
            throw new YAHOO.util.ComparisonFailure(message || "Value isn't an instance of expected type.", expected, actual);
        }
    },
    
    /**
     * Asserts that a value is a number.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNumber
     * @static
     */
    isNumber : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isNumber(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Value should be a number.", actual);
        }    
    },    
    
    /**
     * Asserts that a value is an object.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isObject
     * @static
     */
    isObject : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isObject(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Value should be an object.", actual);
        }
    },
    
    /**
     * Asserts that a value is a string.
     * @param {Object} actual The value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isString
     * @static
     */
    isString : function (actual /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.isString(actual)){
            throw new YAHOO.util.UnexpectedValue(message || "Value should be a string.", actual);
        }
    },
    
    /**
     * Asserts that a value is of a particular type. 
     * @param {String} expectedType The expected type of the variable.
     * @param {Object} actualValue The actual value to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isTypeOf
     * @static
     */
    isTypeOf : function (expectedType /*:String*/, actualValue /*:Object*/, message /*:String*/) /*:Void*/{
        if (typeof actualValue != expectedType){
            throw new YAHOO.util.ComparisonFailure(message || "Value should be of type " + expected + ".", expected, typeof actual);
        }
    }
};

//-----------------------------------------------------------------------------
// Assertion errors
//-----------------------------------------------------------------------------

/**
 * AssertionError is thrown whenever an assertion fails. It provides methods
 * to more easily get at error information and also provides a base class
 * from which more specific assertion errors can be derived.
 *
 * @param {String} message The message to display when the error occurs.
 * @namespace YAHOO.util
 * @class AssertionError
 * @extends Error
 * @constructor
 */ 
YAHOO.util.AssertionError = function (message /*:String*/){

    //call superclass
    arguments.callee.superclass.constructor.call(this, message);
    
    /*
     * Error message. Must be duplicated to ensure browser receives it.
     * @type String
     * @property message
     */
    this.message /*:String*/ = message;
    
    /**
     * The name of the error that occurred.
     * @type String
     * @property name
     */
    this.name /*:String*/ = "AssertionError";
};

//inherit methods
YAHOO.lang.extend(YAHOO.util.AssertionError, Error, {

    /**
     * Returns a fully formatted error for an assertion failure. This should
     * be overridden by all subclasses to provide specific information.
     * @method getMessage
     * @return {String} A string describing the error.
     */
    getMessage : function () /*:String*/ {
        return this.message;
    },
    
    /**
     * Returns a string representation of the error.
     * @method toString
     * @return {String} A string representation of the error.
     */
    toString : function () /*:String*/ {
        return this.name + ": " + this.getMessage();
    },
    
    /**
     * Returns a primitive value version of the error. Same as toString().
     * @method valueOf
     * @return {String} A primitive value version of the error.
     */
    valueOf : function () /*:String*/ {
        return this.toString();
    }

});

/**
 * ComparisonFailure is subclass of AssertionError that is thrown whenever
 * a comparison between two values fails. It provides mechanisms to retrieve
 * both the expected and actual value.
 *
 * @param {String} message The message to display when the error occurs.
 * @param {Object} expected The expected value.
 * @param {Object} actual The actual value that caused the assertion to fail.
 * @namespace YAHOO.util
 * @extends YAHOO.util.AssertionError
 * @class ComparisonFailure
 * @constructor
 */ 
YAHOO.util.ComparisonFailure = function (message /*:String*/, expected /*:Object*/, actual /*:Object*/){

    //call superclass
    arguments.callee.superclass.constructor.call(this, message);
    
    /**
     * The expected value.
     * @type Object
     * @property expected
     */
    this.expected /*:Object*/ = expected;
    
    /**
     * The actual value.
     * @type Object
     * @property actual
     */
    this.actual /*:Object*/ = actual;
    
    /**
     * The name of the error that occurred.
     * @type String
     * @property name
     */
    this.name /*:String*/ = "ComparisonFailure";
    
};

//inherit methods
YAHOO.lang.extend(YAHOO.util.ComparisonFailure, YAHOO.util.AssertionError, {

    /**
     * Returns a fully formatted error for an assertion failure. This message
     * provides information about the expected and actual values.
     * @method toString
     * @return {String} A string describing the error.
     */
    getMessage : function () /*:String*/ {
        return this.message + "\nExpected: " + this.expected + " (" + (typeof this.expected) + ")"  +
            "\nActual:" + this.actual + " (" + (typeof this.actual) + ")";
    }

});

/**
 * UnexpectedValue is subclass of AssertionError that is thrown whenever
 * a value was unexpected in its scope. This typically means that a test
 * was performed to determine that a value was *not* equal to a certain
 * value.
 *
 * @param {String} message The message to display when the error occurs.
 * @param {Object} unexpected The unexpected value.
 * @namespace YAHOO.util
 * @extends YAHOO.util.AssertionError
 * @class UnexpectedValue
 * @constructor
 */ 
YAHOO.util.UnexpectedValue = function (message /*:String*/, unexpected /*:Object*/){

    //call superclass
    arguments.callee.superclass.constructor.call(this, message);
    
    /**
     * The unexpected value.
     * @type Object
     * @property unexpected
     */
    this.unexpected /*:Object*/ = unexpected;
    
    /**
     * The name of the error that occurred.
     * @type String
     * @property name
     */
    this.name /*:String*/ = "UnexpectedValue";
    
};

//inherit methods
YAHOO.lang.extend(YAHOO.util.UnexpectedValue, YAHOO.util.AssertionError, {

    /**
     * Returns a fully formatted error for an assertion failure. The message
     * contains information about the unexpected value that was encountered.
     * @method getMessage
     * @return {String} A string describing the error.
     */
    getMessage : function () /*:String*/ {
        return this.message + "\nUnexpected: " + this.unexpected + " (" + (typeof this.unexpected) + ") ";
    }

});

/**
 * ShouldFail is subclass of AssertionError that is thrown whenever
 * a test was expected to fail but did not.
 *
 * @param {String} message The message to display when the error occurs.
 * @namespace YAHOO.util
 * @extends YAHOO.util.AssertionError
 * @class ShouldFail
 * @constructor
 */  
YAHOO.util.ShouldFail = function (message /*:String*/){

    //call superclass
    arguments.callee.superclass.constructor.call(this, message || "This test should fail but didn't.");
    
    /**
     * The name of the error that occurred.
     * @type String
     * @property name
     */
    this.name /*:String*/ = "ShouldFail";
    
};

//inherit methods
YAHOO.lang.extend(YAHOO.util.ShouldFail, YAHOO.util.AssertionError);

/**
 * ShouldError is subclass of AssertionError that is thrown whenever
 * a test is expected to throw an error but doesn't.
 *
 * @param {String} message The message to display when the error occurs.
 * @namespace YAHOO.util
 * @extends YAHOO.util.AssertionError
 * @class ShouldError
 * @constructor
 */  
YAHOO.util.ShouldError = function (message /*:String*/){

    //call superclass
    arguments.callee.superclass.constructor.call(this, message || "This test should have thrown an error but didn't.");
    
    /**
     * The name of the error that occurred.
     * @type String
     * @property name
     */
    this.name /*:String*/ = "ShouldError";
    
};

//inherit methods
YAHOO.lang.extend(YAHOO.util.ShouldError, YAHOO.util.AssertionError);

/**
 * UnexpectedError is subclass of AssertionError that is thrown whenever
 * an error occurs within the course of a test and the test was not expected
 * to throw an error.
 *
 * @param {Error} cause The unexpected error that caused this error to be 
 *                      thrown.
 * @namespace YAHOO.util
 * @extends YAHOO.util.AssertionError
 * @class UnexpectedError
 * @constructor
 */  
YAHOO.util.UnexpectedError = function (cause /*:Object*/){

    //call superclass
    arguments.callee.superclass.constructor.call(this, "Unexpected error: " + cause.message);
    
    /**
     * The unexpected error that occurred.
     * @type Error
     * @property cause
     */
    this.cause /*:Error*/ = cause;
    
    /**
     * The name of the error that occurred.
     * @type String
     * @property name
     */
    this.name /*:String*/ = "UnexpectedError";
    
};

//inherit methods
YAHOO.lang.extend(YAHOO.util.UnexpectedError, YAHOO.util.AssertionError);
//-----------------------------------------------------------------------------
// ArrayAssert object
//-----------------------------------------------------------------------------

/**
 * The ArrayAssert object provides functions to test JavaScript array objects
 * for a variety of cases.
 *
 * @namespace YAHOO.util
 * @class ArrayAssert
 * @static
 */
 
YAHOO.util.ArrayAssert = {

    /**
     * Asserts that a value is present in an array. This uses the triple equals 
     * sign so no type cohersion may occur.
     * @param {Object} needle The value that is expected in the array.
     * @param {Array} haystack An array of values.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method contains
     * @static
     */
    contains : function (needle /*:Object*/, haystack /*:Array*/, 
                           message /*:String*/) /*:Void*/ {
        
        var found /*:Boolean*/ = false;
        
        //begin checking values
        for (var i=0; i < haystack.length && !found; i++){
            if (haystack[i] === needle) {
                found = true;
            }
        }
        
        if (!found){
            YAHOO.util.Assert.fail(message || "Value (" + needle + ") not found in array.");
        }
    },

    /**
     * Asserts that a set of values are present in an array. This uses the triple equals 
     * sign so no type cohersion may occur. For this assertion to pass, all values must
     * be found.
     * @param {Object[]} needles An array of values that are expected in the array.
     * @param {Array} haystack An array of values to check.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method containsItems
     * @static
     */
    containsItems : function (needles /*:Object[]*/, haystack /*:Array*/, 
                           message /*:String*/) /*:Void*/ {

        //begin checking values
        for (var i=0; i < needles.length; i++){
            this.contains(needles[i], haystack, message);
        }
        
        if (!found){
            YAHOO.util.Assert.fail(message || "Value not found in array.");
        }
    },

    /**
     * Asserts that a value matching some condition is present in an array. This uses
     * a function to determine a match.
     * @param {Function} matcher A function that returns true if the items matches or false if not.
     * @param {Array} haystack An array of values.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method containsMatch
     * @static
     */
    containsMatch : function (matcher /*:Function*/, haystack /*:Array*/, 
                           message /*:String*/) /*:Void*/ {
        
        //check for valid matcher
        if (typeof matcher != "function"){
            throw new TypeError("ArrayAssert.containsMatch(): First argument must be a function.");
        }
        
        var found /*:Boolean*/ = false;
        
        //begin checking values
        for (var i=0; i < haystack.length && !found; i++){
            if (matcher(haystack[i])) {
                found = true;
            }
        }
        
        if (!found){
            YAHOO.util.Assert.fail(message || "No match found in array.");
        }
    },

    /**
     * Asserts that a value is not present in an array. This uses the triple equals 
     * sign so no type cohersion may occur.
     * @param {Object} needle The value that is expected in the array.
     * @param {Array} haystack An array of values.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method doesNotContain
     * @static
     */
    doesNotContain : function (needle /*:Object*/, haystack /*:Array*/, 
                           message /*:String*/) /*:Void*/ {
        
        var found /*:Boolean*/ = false;
        
        //begin checking values
        for (var i=0; i < haystack.length && !found; i++){
            if (haystack[i] === needle) {
                found = true;
            }
        }
        
        if (found){
            YAHOO.util.Assert.fail(message || "Value found in array.");
        }
    },

    /**
     * Asserts that a set of values are not present in an array. This uses the triple equals 
     * sign so no type cohersion may occur. For this assertion to pass, all values must
     * not be found.
     * @param {Object[]} needles An array of values that are not expected in the array.
     * @param {Array} haystack An array of values to check.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method doesNotContainItems
     * @static
     */
    doesNotContainItems : function (needles /*:Object[]*/, haystack /*:Array*/, 
                           message /*:String*/) /*:Void*/ {

        for (var i=0; i < needles.length; i++){
            this.doesNotContain(needles[i], haystack, message);
        }

    },
        
    /**
     * Asserts that no values matching a condition are present in an array. This uses
     * a function to determine a match.
     * @param {Function} matcher A function that returns true if the items matches or false if not.
     * @param {Array} haystack An array of values.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method doesNotContainMatch
     * @static
     */
    doesNotContainMatch : function (matcher /*:Function*/, haystack /*:Array*/, 
                           message /*:String*/) /*:Void*/ {
        
        //check for valid matcher
        if (typeof matcher != "function"){
            throw new TypeError("ArrayAssert.doesNotContainMatch(): First argument must be a function.");
        }

        var found /*:Boolean*/ = false;
        
        //begin checking values
        for (var i=0; i < haystack.length && !found; i++){
            if (matcher(haystack[i])) {
                found = true;
            }
        }
        
        if (found){
            YAHOO.util.Assert.fail(message || "Value found in array.");
        }
    },
        
    /**
     * Asserts that the given value is contained in an array at the specified index.
     * This uses the triple equals sign so no type cohersion will occur.
     * @param {Object} needle The value to look for.
     * @param {Array} haystack The array to search in.
     * @param {int} index The index at which the value should exist.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method indexOf
     * @static
     */
    indexOf : function (needle /*:Object*/, haystack /*:Array*/, index /*:int*/, message /*:String*/) /*:Void*/ {
    
        //try to find the value in the array
        for (var i=0; i < haystack.length; i++){
            if (haystack[i] === needle){
                YAHOO.util.Assert.areEqual(index, i, message || "Value exists at index " + i + " but should be at index " + index + ".");
                return;
            }
        }
        
        //if it makes it here, it wasn't found at all
        YAHOO.util.Assert.fail(message || "Value doesn't exist in array.");        
    },
        
    /**
     * Asserts that the values in an array are equal, and in the same position,
     * as values in another array. This uses the double equals sign
     * so type cohersion may occur. Note that the array objects themselves
     * need not be the same for this test to pass.
     * @param {Array} expected An array of the expected values.
     * @param {Array} actual Any array of the actual values.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method itemsAreEqual
     * @static
     */
    itemsAreEqual : function (expected /*:Array*/, actual /*:Array*/, 
                           message /*:String*/) /*:Void*/ {
        
        //one may be longer than the other, so get the maximum length
        var len /*:int*/ = Math.max(expected.length, actual.length);
        
        //begin checking values
        for (var i=0; i < len; i++){
            YAHOO.util.Assert.areEqual(expected[i], actual[i], message || 
                    "Values in position " + i + " are not equal.");
        }
    },
    
    /**
     * Asserts that the values in an array are equivalent, and in the same position,
     * as values in another array. This uses a function to determine if the values
     * are equivalent. Note that the array objects themselves
     * need not be the same for this test to pass.
     * @param {Array} expected An array of the expected values.
     * @param {Array} actual Any array of the actual values.
     * @param {Function} comparator A function that returns true if the values are equivalent
     *      or false if not.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @return {Void}
     * @method itemsAreEquivalent
     * @static
     */
    itemsAreEquivalent : function (expected /*:Array*/, actual /*:Array*/, 
                           comparator /*:Function*/, message /*:String*/) /*:Void*/ {
        
        //make sure the comparator is valid
        if (typeof comparator != "function"){
            throw new TypeError("ArrayAssert.itemsAreEquivalent(): Third argument must be a function.");
        }
        
        //one may be longer than the other, so get the maximum length
        var len /*:int*/ = Math.max(expected.length, actual.length);
        
        //begin checking values
        for (var i=0; i < len; i++){
            if (!comparator(expected[i], actual[i])){
                throw new YAHOO.util.ComparisonFailure(message || "Values in position " + i + " are not equivalent.", expected[i], actual[i]);
            }
        }
    },
    
    /**
     * Asserts that an array is empty.
     * @param {Array} actual The array to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isEmpty
     * @static
     */
    isEmpty : function (actual /*:Array*/, message /*:String*/) /*:Void*/ {        
        if (actual.length > 0){
            YAHOO.util.Assert.fail(message || "Array should be empty.");
        }
    },    
    
    /**
     * Asserts that an array is not empty.
     * @param {Array} actual The array to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method isNotEmpty
     * @static
     */
    isNotEmpty : function (actual /*:Array*/, message /*:String*/) /*:Void*/ {        
        if (actual.length === 0){
            YAHOO.util.Assert.fail(message || "Array should not be empty.");
        }
    },    
    
    /**
     * Asserts that the values in an array are the same, and in the same position,
     * as values in another array. This uses the triple equals sign
     * so no type cohersion will occur. Note that the array objects themselves
     * need not be the same for this test to pass.
     * @param {Array} expected An array of the expected values.
     * @param {Array} actual Any array of the actual values.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method itemsAreSame
     * @static
     */
    itemsAreSame : function (expected /*:Array*/, actual /*:Array*/, 
                          message /*:String*/) /*:Void*/ {
        
        //one may be longer than the other, so get the maximum length
        var len /*:int*/ = Math.max(expected.length, actual.length);
        
        //begin checking values
        for (var i=0; i < len; i++){
            YAHOO.util.Assert.areSame(expected[i], actual[i], 
                message || "Values in position " + i + " are not the same.");
        }
    },
    
    /**
     * Asserts that the given value is contained in an array at the specified index,
     * starting from the back of the array.
     * This uses the triple equals sign so no type cohersion will occur.
     * @param {Object} needle The value to look for.
     * @param {Array} haystack The array to search in.
     * @param {int} index The index at which the value should exist.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method lastIndexOf
     * @static
     */
    lastIndexOf : function (needle /*:Object*/, haystack /*:Array*/, index /*:int*/, message /*:String*/) /*:Void*/ {
    
        //try to find the value in the array
        for (var i=haystack.length; i >= 0; i--){
            if (haystack[i] === needle){
                YAHOO.util.Assert.areEqual(index, i, message || "Value exists at index " + i + " but should be at index " + index + ".");
                return;
            }
        }
        
        //if it makes it here, it wasn't found at all
        YAHOO.util.Assert.fail(message || "Value doesn't exist in array.");        
    }
    
};
YAHOO.namespace("util");


//-----------------------------------------------------------------------------
// ObjectAssert object
//-----------------------------------------------------------------------------

/**
 * The ObjectAssert object provides functions to test JavaScript objects
 * for a variety of cases.
 *
 * @namespace YAHOO.util
 * @class ObjectAssert
 * @static
 */
YAHOO.util.ObjectAssert = {
        
    /**
     * Asserts that all properties in the object exist in another object.
     * @param {Object} expected An object with the expected properties.
     * @param {Object} actual An object with the actual properties.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method propertiesAreEqual
     * @static
     */
    propertiesAreEqual : function (expected /*:Object*/, actual /*:Object*/, 
                           message /*:String*/) /*:Void*/ {
        
        //get all properties in the object
        var properties /*:Array*/ = [];        
        for (var property in expected){
            properties.push(property);
        }
        
        //see if the properties are in the expected object
        for (var i=0; i < properties.length; i++){
            YAHOO.util.Assert.isNotUndefined(actual[properties[i]], message || 
                    "Property'" + properties[i] + "' expected.");
        }

    },
    
    /**
     * Asserts that an object has a property with the given name.
     * @param {String} propertyName The name of the property to test.
     * @param {Object} object The object to search.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method hasProperty
     * @static
     */    
    hasProperty : function (propertyName /*:String*/, object /*:Object*/, message /*:String*/) /*:Void*/ {
        if (YAHOO.lang.isUndefined(object[propertyName])){
            YAHOO.util.Assert.fail(message || 
                    "Property " + propertyName + " not found on object.");
        }    
    },
    
    /**
     * Asserts that a property with the given name exists on an object instance (not on its prototype).
     * @param {String} propertyName The name of the property to test.
     * @param {Object} object The object to search.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method hasProperty
     * @static
     */    
    hasOwnProperty : function (propertyName /*:String*/, object /*:Object*/, message /*:String*/) /*:Void*/ {
        if (!YAHOO.lang.hasOwnProperty(object, propertyName)){
            YAHOO.util.Assert.fail(message || 
                    "Property " + propertyName + " not found on object instance.");
        }     
    }
};
//-----------------------------------------------------------------------------
// DateAssert object
//-----------------------------------------------------------------------------

/**
 * The DateAssert object provides functions to test JavaScript Date objects
 * for a variety of cases.
 *
 * @namespace YAHOO.util
 * @class DateAssert
 * @static
 */
 
YAHOO.util.DateAssert = {

    /**
     * Asserts that a date's month, day, and year are equal to another date's.
     * @param {Date} expected The expected date.
     * @param {Date} actual The actual date to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method areEqual
     * @static
     */
    datesAreEqual : function (expected /*:Date*/, actual /*:Date*/, message /*:String*/){
        if (expected instanceof Date && actual instanceof Date){
            YAHOO.util.Assert.areEqual(expected.getFullYear(), actual.getFullYear(), message || "Years should be equal.");
            YAHOO.util.Assert.areEqual(expected.getMonth(), actual.getMonth(), message || "Months should be equal.");
            YAHOO.util.Assert.areEqual(expected.getDate(), actual.getDate(), message || "Day of month should be equal.");
        } else {
            throw new TypeError("DateAssert.datesAreEqual(): Expected and actual values must be Date objects.");
        }
    },

    /**
     * Asserts that a date's hour, minutes, and seconds are equal to another date's.
     * @param {Date} expected The expected date.
     * @param {Date} actual The actual date to test.
     * @param {String} message (Optional) The message to display if the assertion fails.
     * @method areEqual
     * @static
     */
    timesAreEqual : function (expected /*:Date*/, actual /*:Date*/, message /*:String*/){
        if (expected instanceof Date && actual instanceof Date){
            YAHOO.util.Assert.areEqual(expected.getHours(), actual.getHours(), message || "Hours should be equal.");
            YAHOO.util.Assert.areEqual(expected.getMinutes(), actual.getMinutes(), message || "Minutes should be equal.");
            YAHOO.util.Assert.areEqual(expected.getSeconds(), actual.getSeconds(), message || "Seconds should be equal.");
        } else {
            throw new TypeError("DateAssert.timesAreEqual(): Expected and actual values must be Date objects.");
        }
    }
    
};
YAHOO.namespace("util");

/**
 * The UserAction object provides functions that simulate events occurring in
 * the browser. Since these are simulated events, they do not behave exactly
 * as regular, user-initiated events do, but can be used to test simple
 * user interactions safely.
 *
 * @namespace YAHOO.util
 * @class UserAction
 * @static
 */
YAHOO.util.UserAction = {

    //--------------------------------------------------------------------------
    // Generic event methods
    //--------------------------------------------------------------------------

    /**
     * Simulates a key event using the given event information to populate
     * the generated event object. This method does browser-equalizing
     * calculations to account for differences in the DOM and IE event models
     * as well as different browser quirks. Note: keydown causes Safari 2.x to
     * crash.
     * @method simulateKeyEvent
     * @private
     * @static
     * @param {HTMLElement} target The target of the given event.
     * @param {String} type The type of event to fire. This can be any one of
     *      the following: keyup, keydown, and keypress.
     * @param {Boolean} bubbles (Optional) Indicates if the event can be
     *      bubbled up. DOM Level 3 specifies that all key events bubble by
     *      default. The default is true.
     * @param {Boolean} cancelable (Optional) Indicates if the event can be
     *      canceled using preventDefault(). DOM Level 3 specifies that all
     *      key events can be cancelled. The default 
     *      is true.
     * @param {Window} view (Optional) The view containing the target. This is
     *      typically the window object. The default is window.
     * @param {Boolean} ctrlKey (Optional) Indicates if one of the CTRL keys
     *      is pressed while the event is firing. The default is false.
     * @param {Boolean} altKey (Optional) Indicates if one of the ALT keys
     *      is pressed while the event is firing. The default is false.
     * @param {Boolean} shiftKey (Optional) Indicates if one of the SHIFT keys
     *      is pressed while the event is firing. The default is false.
     * @param {Boolean} metaKey (Optional) Indicates if one of the META keys
     *      is pressed while the event is firing. The default is false.
     * @param {int} keyCode (Optional) The code for the key that is in use. 
     *      The default is 0.
     * @param {int} charCode (Optional) The Unicode code for the character
     *      associated with the key being used. The default is 0.
     */
    simulateKeyEvent : function (target /*:HTMLElement*/, type /*:String*/, 
                                 bubbles /*:Boolean*/,  cancelable /*:Boolean*/,    
                                 view /*:Window*/,
                                 ctrlKey /*:Boolean*/,    altKey /*:Boolean*/, 
                                 shiftKey /*:Boolean*/,   metaKey /*:Boolean*/, 
                                 keyCode /*:int*/,        charCode /*:int*/) /*:Void*/                             
    {
        //check target
        target = YAHOO.util.Dom.get(target);        
        if (!target){
            throw new Error("simulateKeyEvent(): Invalid target.");
        }
        
        //check event type
        if (YAHOO.lang.isString(type)){
            type = type.toLowerCase();
            switch(type){
                case "keyup":
                case "keydown":
                case "keypress":
                    break;
                case "textevent": //DOM Level 3
                    type = "keypress";
                    break;
                    // @TODO was the fallthrough intentional, if so throw error
                default:
                    throw new Error("simulateKeyEvent(): Event type '" + type + "' not supported.");
            }
        } else {
            throw new Error("simulateKeyEvent(): Event type must be a string.");
        }
        
        //setup default values
        if (!YAHOO.lang.isBoolean(bubbles)){
            bubbles = true; //all key events bubble
        }
        if (!YAHOO.lang.isBoolean(cancelable)){
            cancelable = true; //all key events can be cancelled
        }
        if (!YAHOO.lang.isObject(view)){
            view = window; //view is typically window
        }
        if (!YAHOO.lang.isBoolean(ctrlKey)){
            ctrlKey = false;
        }
        if (!YAHOO.lang.isBoolean(altKey)){
            altKey = false;
        }
        if (!YAHOO.lang.isBoolean(shiftKey)){
            shiftKey = false;
        }
        if (!YAHOO.lang.isBoolean(metaKey)){
            metaKey = false;
        }
        if (!YAHOO.lang.isNumber(keyCode)){
            keyCode = 0;
        }
        if (!YAHOO.lang.isNumber(charCode)){
            charCode = 0; 
        }
        
        //check for DOM-compliant browsers first
        if (YAHOO.lang.isFunction(document.createEvent)){
        
            //try to create a mouse event
            var event /*:MouseEvent*/ = null;
            
            try {
                
                //try to create key event
                event = document.createEvent("KeyEvents");
                
                /*
                 * Interesting problem: Firefox implemented a non-standard
                 * version of initKeyEvent() based on DOM Level 2 specs.
                 * Key event was removed from DOM Level 2 and re-introduced
                 * in DOM Level 3 with a different interface. Firefox is the
                 * only browser with any implementation of Key Events, so for
                 * now, assume it's Firefox if the above line doesn't error.
                 */
                //TODO: Decipher between Firefox's implementation and a correct one.
                event.initKeyEvent(type, bubbles, cancelable, view, ctrlKey,
                    altKey, shiftKey, metaKey, keyCode, charCode);       
                
            } catch (ex /*:Error*/){

                /*
                 * If it got here, that means key events aren't officially supported. 
                 * Safari/WebKit is a real problem now. WebKit 522 won't let you
                 * set keyCode, charCode, or other properties if you use a
                 * UIEvent, so we first must try to create a generic event. The
                 * fun part is that this will throw an error on Safari 2.x. The
                 * end result is that we need another try...catch statement just to
                 * deal with this mess.
                 */
                try {

                    //try to create generic event - will fail in Safari 2.x
                    event = document.createEvent("Events");

                } catch (uierror /*:Error*/){

                    //the above failed, so create a UIEvent for Safari 2.x
                    event = document.createEvent("UIEvents");

                } finally {

                    event.initEvent(type, bubbles, cancelable);
    
                    //initialize
                    event.view = view;
                    event.altKey = altKey;
                    event.ctrlKey = ctrlKey;
                    event.shiftKey = shiftKey;
                    event.metaKey = metaKey;
                    event.keyCode = keyCode;
                    event.charCode = charCode;
          
                }          
             
            }
            
            //fire the event
            target.dispatchEvent(event);

        } else if (YAHOO.lang.isObject(document.createEventObject)){ //IE
        
            //create an IE event object
            event = document.createEventObject();
            
            //assign available properties
            event.bubbles = bubbles;
            event.cancelable = cancelable;
            event.view = view;
            event.ctrlKey = ctrlKey;
            event.altKey = altKey;
            event.shiftKey = shiftKey;
            event.metaKey = metaKey;
            
            /*
             * IE doesn't support charCode explicitly. CharCode should
             * take precedence over any keyCode value for accurate
             * representation.
             */
            event.keyCode = (charCode > 0) ? charCode : keyCode;
            
            //fire the event
            target.fireEvent("on" + type, event);  
                    
        } else {
            throw new Error("simulateKeyEvent(): No event simulation framework present.");
        }
    },

    /**
     * Simulates a mouse event using the given event information to populate
     * the generated event object. This method does browser-equalizing
     * calculations to account for differences in the DOM and IE event models
     * as well as different browser quirks.
     * @method simulateMouseEvent
     * @private
     * @static
     * @param {HTMLElement} target The target of the given event.
     * @param {String} type The type of event to fire. This can be any one of
     *      the following: click, dblclick, mousedown, mouseup, mouseout,
     *      mouseover, and mousemove.
     * @param {Boolean} bubbles (Optional) Indicates if the event can be
     *      bubbled up. DOM Level 2 specifies that all mouse events bubble by
     *      default. The default is true.
     * @param {Boolean} cancelable (Optional) Indicates if the event can be
     *      canceled using preventDefault(). DOM Level 2 specifies that all
     *      mouse events except mousemove can be cancelled. The default 
     *      is true for all events except mousemove, for which the default 
     *      is false.
     * @param {Window} view (Optional) The view containing the target. This is
     *      typically the window object. The default is window.
     * @param {int} detail (Optional) The number of times the mouse button has
     *      been used. The default value is 1.
     * @param {int} screenX (Optional) The x-coordinate on the screen at which
     *      point the event occured. The default is 0.
     * @param {int} screenY (Optional) The y-coordinate on the screen at which
     *      point the event occured. The default is 0.
     * @param {int} clientX (Optional) The x-coordinate on the client at which
     *      point the event occured. The default is 0.
     * @param {int} clientY (Optional) The y-coordinate on the client at which
     *      point the event occured. The default is 0.
     * @param {Boolean} ctrlKey (Optional) Indicates if one of the CTRL keys
     *      is pressed while the event is firing. The default is false.
     * @param {Boolean} altKey (Optional) Indicates if one of the ALT keys
     *      is pressed while the event is firing. The default is false.
     * @param {Boolean} shiftKey (Optional) Indicates if one of the SHIFT keys
     *      is pressed while the event is firing. The default is false.
     * @param {Boolean} metaKey (Optional) Indicates if one of the META keys
     *      is pressed while the event is firing. The default is false.
     * @param {int} button (Optional) The button being pressed while the event
     *      is executing. The value should be 0 for the primary mouse button
     *      (typically the left button), 1 for the terciary mouse button
     *      (typically the middle button), and 2 for the secondary mouse button
     *      (typically the right button). The default is 0.
     * @param {HTMLElement} relatedTarget (Optional) For mouseout events,
     *      this is the element that the mouse has moved to. For mouseover
     *      events, this is the element that the mouse has moved from. This
     *      argument is ignored for all other events. The default is null.
     */
    simulateMouseEvent : function (target /*:HTMLElement*/, type /*:String*/, 
                                   bubbles /*:Boolean*/,  cancelable /*:Boolean*/,    
                                   view /*:Window*/,        detail /*:int*/, 
                                   screenX /*:int*/,        screenY /*:int*/, 
                                   clientX /*:int*/,        clientY /*:int*/,       
                                   ctrlKey /*:Boolean*/,    altKey /*:Boolean*/, 
                                   shiftKey /*:Boolean*/,   metaKey /*:Boolean*/, 
                                   button /*:int*/,         relatedTarget /*:HTMLElement*/) /*:Void*/
    {
        
        //check target
        target = YAHOO.util.Dom.get(target);        
        if (!target){
            throw new Error("simulateMouseEvent(): Invalid target.");
        }
        
        //check event type
        if (YAHOO.lang.isString(type)){
            type = type.toLowerCase();
            switch(type){
                case "mouseover":
                case "mouseout":
                case "mousedown":
                case "mouseup":
                case "click":
                case "dblclick":
                case "mousemove":
                    break;
                default:
                    throw new Error("simulateMouseEvent(): Event type '" + type + "' not supported.");
            }
        } else {
            throw new Error("simulateMouseEvent(): Event type must be a string.");
        }
        
        //setup default values
        if (!YAHOO.lang.isBoolean(bubbles)){
            bubbles = true; //all mouse events bubble
        }
        if (!YAHOO.lang.isBoolean(cancelable)){
            cancelable = (type != "mousemove"); //mousemove is the only one that can't be cancelled
        }
        if (!YAHOO.lang.isObject(view)){
            view = window; //view is typically window
        }
        if (!YAHOO.lang.isNumber(detail)){
            detail = 1;  //number of mouse clicks must be at least one
        }
        if (!YAHOO.lang.isNumber(screenX)){
            screenX = 0; 
        }
        if (!YAHOO.lang.isNumber(screenY)){
            screenY = 0; 
        }
        if (!YAHOO.lang.isNumber(clientX)){
            clientX = 0; 
        }
        if (!YAHOO.lang.isNumber(clientY)){
            clientY = 0; 
        }
        if (!YAHOO.lang.isBoolean(ctrlKey)){
            ctrlKey = false;
        }
        if (!YAHOO.lang.isBoolean(altKey)){
            altKey = false;
        }
        if (!YAHOO.lang.isBoolean(shiftKey)){
            shiftKey = false;
        }
        if (!YAHOO.lang.isBoolean(metaKey)){
            metaKey = false;
        }
        if (!YAHOO.lang.isNumber(button)){
            button = 0; 
        }
        
        //check for DOM-compliant browsers first
        if (YAHOO.lang.isFunction(document.createEvent)){
        
            //try to create a mouse event
            var event /*:MouseEvent*/ = document.createEvent("MouseEvents");
            
            //Safari 2.x (WebKit 418) still doesn't implement initMouseEvent()
            if (event.initMouseEvent){
                event.initMouseEvent(type, bubbles, cancelable, view, detail,
                                     screenX, screenY, clientX, clientY, 
                                     ctrlKey, altKey, shiftKey, metaKey, 
                                     button, relatedTarget);
            } else { //Safari
            
                //the closest thing available in Safari 2.x is UIEvents
                event = document.createEvent("UIEvents");
                event.initEvent(type, bubbles, cancelable);
                event.view = view;
                event.detail = detail;
                event.screenX = screenX;
                event.screenY = screenY;
                event.clientX = clientX;
                event.clientY = clientY;
                event.ctrlKey = ctrlKey;
                event.altKey = altKey;
                event.metaKey = metaKey;
                event.shiftKey = shiftKey;
                event.button = button;
                event.relatedTarget = relatedTarget;
            }
            
            /*
             * Check to see if relatedTarget has been assigned. Firefox
             * versions less than 2.0 don't allow it to be assigned via
             * initMouseEvent() and the property is readonly after event
             * creation, so in order to keep YAHOO.util.getRelatedTarget()
             * working, assign to the IE proprietary toElement property
             * for mouseout event and fromElement property for mouseover
             * event.
             */
            if (relatedTarget && !event.relatedTarget){
                if (type == "mouseout"){
                    event.toElement = relatedTarget;
                } else if (type == "mouseover"){
                    event.fromElement = relatedTarget;
                }
            }
            
            //fire the event
            target.dispatchEvent(event);

        } else if (YAHOO.lang.isObject(document.createEventObject)){ //IE
        
            //create an IE event object
            event = document.createEventObject();
            
            //assign available properties
            event.bubbles = bubbles;
            event.cancelable = cancelable;
            event.view = view;
            event.detail = detail;
            event.screenX = screenX;
            event.screenY = screenY;
            event.clientX = clientX;
            event.clientY = clientY;
            event.ctrlKey = ctrlKey;
            event.altKey = altKey;
            event.metaKey = metaKey;
            event.shiftKey = shiftKey;

            //fix button property for IE's wacky implementation
            switch(button){
                case 0:
                    event.button = 1;
                    break;
                case 1:
                    event.button = 4;
                    break;
                case 2:
                    //leave as is
                    break;
                default:
                    event.button = 0;                    
            }    

            /*
             * Have to use relatedTarget because IE won't allow assignment
             * to toElement or fromElement on generic events. This keeps
             * YAHOO.util.Event.getRelatedTarget() functional.
             */
            event.relatedTarget = relatedTarget;
            
            //fire the event
            target.fireEvent("on" + type, event);
                    
        } else {
            throw new Error("simulateMouseEvent(): No event simulation framework present.");
        }
    },
   
    //--------------------------------------------------------------------------
    // Mouse events
    //--------------------------------------------------------------------------

    /**
     * Simulates a mouse event on a particular element.
     * @param {HTMLElement} target The element to click on.
     * @param {String} type The type of event to fire. This can be any one of
     *      the following: click, dblclick, mousedown, mouseup, mouseout,
     *      mouseover, and mousemove.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method mouseEvent
     * @static
     */
    fireMouseEvent : function (target /*:HTMLElement*/, type /*:String*/, 
                           options /*:Object*/) /*:Void*/
    {
        options = options || {};
        this.simulateMouseEvent(target, type, options.bubbles,
            options.cancelable, options.view, options.detail, options.screenX,        
            options.screenY, options.clientX, options.clientY, options.ctrlKey,
            options.altKey, options.shiftKey, options.metaKey, options.button,         
            options.relatedTarget);        
    },

    /**
     * Simulates a click on a particular element.
     * @param {HTMLElement} target The element to click on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method click
     * @static     
     */
    click : function (target /*:HTMLElement*/, options /*:Object*/) /*:Void*/ {
        this.fireMouseEvent(target, "click", options);
    },
    
    /**
     * Simulates a double click on a particular element.
     * @param {HTMLElement} target The element to double click on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method dblclick
     * @static
     */
    dblclick : function (target /*:HTMLElement*/, options /*:Object*/) /*:Void*/ {
        this.fireMouseEvent( target, "dblclick", options);
    },
    
    /**
     * Simulates a mousedown on a particular element.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method mousedown
     * @static
     */
    mousedown : function (target /*:HTMLElement*/, options /*Object*/) /*:Void*/ {
        this.fireMouseEvent(target, "mousedown", options);
    },
    
    /**
     * Simulates a mousemove on a particular element.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method mousemove
     * @static
     */
    mousemove : function (target /*:HTMLElement*/, options /*Object*/) /*:Void*/ {
        this.fireMouseEvent(target, "mousemove", options);
    },
    
    /**
     * Simulates a mouseout event on a particular element. Use "relatedTarget"
     * on the options object to specify where the mouse moved to.
     * Quirks: Firefox less than 2.0 doesn't set relatedTarget properly, so
     * toElement is assigned in its place. IE doesn't allow toElement to be
     * be assigned, so relatedTarget is assigned in its place. Both of these
     * concessions allow YAHOO.util.Event.getRelatedTarget() to work correctly
     * in both browsers.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method mouseout
     * @static
     */
    mouseout : function (target /*:HTMLElement*/, options /*Object*/) /*:Void*/ {
        this.fireMouseEvent(target, "mouseout", options);
    },
    
    /**
     * Simulates a mouseover event on a particular element. Use "relatedTarget"
     * on the options object to specify where the mouse moved from.
     * Quirks: Firefox less than 2.0 doesn't set relatedTarget properly, so
     * fromElement is assigned in its place. IE doesn't allow fromElement to be
     * be assigned, so relatedTarget is assigned in its place. Both of these
     * concessions allow YAHOO.util.Event.getRelatedTarget() to work correctly
     * in both browsers.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method mouseover
     * @static
     */
    mouseover : function (target /*:HTMLElement*/, options /*Object*/) /*:Void*/ {
        this.fireMouseEvent(target, "mouseover", options);
    },
    
    /**
     * Simulates a mouseup on a particular element.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method mouseup
     * @static
     */
    mouseup : function (target /*:HTMLElement*/, options /*Object*/) /*:Void*/ {
        this.fireMouseEvent(target, "mouseup", options);
    },
    
    //--------------------------------------------------------------------------
    // Key events
    //--------------------------------------------------------------------------

    /**
     * Fires an event that normally would be fired by the keyboard (keyup,
     * keydown, keypress). Make sure to specify either keyCode or charCode as
     * an option.
     * @private
     * @param {String} type The type of event ("keyup", "keydown" or "keypress").
     * @param {HTMLElement} target The target of the event.
     * @param {Object} options Options for the event. Either keyCode or charCode
     *                         are required.
     * @method fireKeyEvent
     * @static
     */     
    fireKeyEvent : function (type /*:String*/, target /*:HTMLElement*/,
                             options /*:Object*/) /*:Void*/ 
    {
        options = options || {};
        this.simulateKeyEvent(target, type, options.bubbles,
            options.cancelable, options.view, options.ctrlKey,
            options.altKey, options.shiftKey, options.metaKey, 
            options.keyCode, options.charCode);    
    },
    
    /**
     * Simulates a keydown event on a particular element.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method keydown
     * @static
     */
    keydown : function (target /*:HTMLElement*/, options /*:Object*/) /*:Void*/ {
        this.fireKeyEvent("keydown", target, options);
    },
    
    /**
     * Simulates a keypress on a particular element.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method keypress
     * @static
     */
    keypress : function (target /*:HTMLElement*/, options /*:Object*/) /*:Void*/ {
        this.fireKeyEvent("keypress", target, options);
    },
    
    /**
     * Simulates a keyup event on a particular element.
     * @param {HTMLElement} target The element to act on.
     * @param {Object} options Additional event options (use DOM standard names).
     * @method keyup
     * @static
     */
    keyup : function (target /*:HTMLElement*/, options /*Object*/) /*:Void*/ {
        this.fireKeyEvent("keyup", target, options);
    }
    

};
YAHOO.namespace("tool");

//-----------------------------------------------------------------------------
// TestManager object
//-----------------------------------------------------------------------------

/**
 * Runs pages containing test suite definitions.
 * @namespace YAHOO.tool
 * @class TestManager
 * @static
 */
YAHOO.tool.TestManager = {

    /**
     * Constant for the testpagebegin custom event
     * @property TEST_PAGE_BEGIN_EVENT
     * @static
     * @type string
     * @final
     */
    TEST_PAGE_BEGIN_EVENT /*:String*/ : "testpagebegin",

    /**
     * Constant for the testpagecomplete custom event
     * @property TEST_PAGE_COMPLETE_EVENT
     * @static
     * @type string
     * @final
     */
    TEST_PAGE_COMPLETE_EVENT /*:String*/ : "testpagecomplete",

    /**
     * Constant for the testmanagerbegin custom event
     * @property TEST_MANAGER_BEGIN_EVENT
     * @static
     * @type string
     * @final
     */
    TEST_MANAGER_BEGIN_EVENT /*:String*/ : "testmanagerbegin",

    /**
     * Constant for the testmanagercomplete custom event
     * @property TEST_MANAGER_COMPLETE_EVENT
     * @static
     * @type string
     * @final
     */
    TEST_MANAGER_COMPLETE_EVENT /*:String*/ : "testmanagercomplete",

    //-------------------------------------------------------------------------
    // Private Properties
    //-------------------------------------------------------------------------
    
    
    /**
     * The URL of the page currently being executed.
     * @type String
     * @private
     * @property _curPage
     * @static
     */
    _curPage /*:String*/ : null,
    
    /**
     * The frame used to load and run tests.
     * @type Window
     * @private
     * @property _frame
     * @static
     */
    _frame /*:Window*/ : null,
    
    /**
     * The logger used to output results from the various tests.
     * @type YAHOO.tool.TestLogger
     * @private
     * @property _logger
     * @static
     */
    _logger : null,
    
    /**
     * The timeout ID for the next iteration through the tests.
     * @type int
     * @private
     * @property _timeoutId
     * @static
     */
    _timeoutId /*:int*/ : 0,
    
    /**
     * Array of pages to load.
     * @type String[]
     * @private
     * @property _pages
     * @static
     */
    _pages /*:String[]*/ : [],
    
    /**
     * Aggregated results
     * @type Object
     * @private
     * @property _results
     * @static
     */
    _results: null,
    
    //-------------------------------------------------------------------------
    // Private Methods
    //-------------------------------------------------------------------------
    
    /**
     * Handles TestRunner.COMPLETE_EVENT, storing the results and beginning
     * the loop again.
     * @param {Object} data Data about the event.
     * @return {Void}
     * @private
     * @static
     */
    _handleTestRunnerComplete : function (data /*:Object*/) /*:Void*/ {

        this.fireEvent(this.TEST_PAGE_COMPLETE_EVENT, {
                page: this._curPage,
                results: data.results
            });
    
        //save results
        //this._results[this.curPage] = data.results;
        
        //process 'em
        this._processResults(this._curPage, data.results);
        
        this._logger.clearTestRunner();
    
        //if there's more to do, set a timeout to begin again
        if (this._pages.length){
            this._timeoutId = setTimeout(function(){
                YAHOO.tool.TestManager._run();
            }, 1000);
        }
    },
    
    /**
     * Processes the results of a test page run, outputting log messages
     * for failed tests.
     * @return {Void}
     * @private
     * @static
     */
    _processResults : function (page /*:String*/, results /*:Object*/) /*:Void*/ {

        var r = this._results;

        r.page_results[page] = results;

        if (results.passed) {
            r.pages_passed++;
            r.tests_passed += results.passed;
        }

        if (results.failed) {
            r.pages_failed++;
            r.tests_failed += results.failed;
            r.failed.push(page);
        } else {
            r.passed.push(page);
        }

        if (!this._pages.length) {
            this.fireEvent(this.TEST_MANAGER_COMPLETE_EVENT, this._results);
        }

    },
    
    /**
     * Loads the next test page into the iframe.
     * @return {Void}
     * @static
     * @private
     */
    _run : function () /*:Void*/ {
    
        //set the current page
        this._curPage = this._pages.shift();

        this.fireEvent(this.TEST_PAGE_BEGIN_EVENT, this._curPage);
        
        //load the frame - destroy history in case there are other iframes that
        //need testing
        this._frame.location.replace(this._curPage);
    
    },
        
    //-------------------------------------------------------------------------
    // Public Methods
    //-------------------------------------------------------------------------
    
    /**
     * Signals that a test page has been loaded. This should be called from
     * within the test page itself to notify the TestManager that it is ready.
     * @return {Void}
     * @static
     */
    load : function () /*:Void*/ {
        if (parent.YAHOO.tool.TestManager !== this){
            parent.YAHOO.tool.TestManager.load();
        } else {
            
            if (this._frame) {
                //assign event handling
                var TestRunner = this._frame.YAHOO.tool.TestRunner;

                this._logger.setTestRunner(TestRunner);
                TestRunner.subscribe(TestRunner.COMPLETE_EVENT, this._handleTestRunnerComplete, this, true);
                
                //run it
                TestRunner.run();
            }
        }
    },
    
    /**
     * Sets the pages to be loaded.
     * @param {String[]} pages An array of URLs to load.
     * @return {Void}
     * @static
     */
    setPages : function (pages /*:String[]*/) /*:Void*/ {
        this._pages = pages;
    },
    
    /**
     * Begins the process of running the tests.
     * @return {Void}
     * @static
     */
    start : function () /*:Void*/ {

        if (!this._initialized) {

            /**
             * Fires when loading a test page
             * @event testpagebegin
             * @param curPage {string} the page being loaded
             * @static
             */
            this.createEvent(this.TEST_PAGE_BEGIN_EVENT);

            /**
             * Fires when a test page is complete
             * @event testpagecomplete
             * @param obj {page: string, results: object} the name of the
             * page that was loaded, and the test suite results
             * @static
             */
            this.createEvent(this.TEST_PAGE_COMPLETE_EVENT);

            /**
             * Fires when the test manager starts running all test pages
             * @event testmanagerbegin
             * @static
             */
            this.createEvent(this.TEST_MANAGER_BEGIN_EVENT);

            /**
             * Fires when the test manager finishes running all test pages.  External
             * test runners should subscribe to this event in order to get the
             * aggregated test results.
             * @event testmanagercomplete
             * @param obj { pages_passed: int, pages_failed: int, tests_passed: int
             *              tests_failed: int, passed: string[], failed: string[],
             *              page_results: {} }
             * @static
             */
            this.createEvent(this.TEST_MANAGER_COMPLETE_EVENT);

            //create iframe if not already available
            if (!this._frame){
                var frame /*:HTMLElement*/ = document.createElement("iframe");
                frame.style.visibility = "hidden";
                frame.style.position = "absolute";
                document.body.appendChild(frame);
                this._frame = frame.contentWindow || frame.contentDocument.ownerWindow;
            }
            
            //create test logger if not already available
            if (!this._logger){
                this._logger = new YAHOO.tool.TestLogger();
            }

            this._initialized = true;
        }


        // reset the results cache
        this._results = {
            // number of pages that pass
            pages_passed: 0,
            // number of pages that fail
            pages_failed: 0,
            // total number of tests passed
            tests_passed: 0,
            // total number of tests failed
            tests_failed: 0,
            // array of pages that passed
            passed: [],
            // array of pages that failed
            failed: [],
            // map of full results for each page
            page_results: {}
        };

        this.fireEvent(this.TEST_MANAGER_BEGIN_EVENT, null);
        this._run();
    
    },

    /**
     * Stops the execution of tests.
     * @return {Void}
     * @static
     */
    stop : function () /*:Void*/ {
        clearTimeout(this._timeoutId);
    }

};

YAHOO.lang.augmentObject(YAHOO.tool.TestManager, YAHOO.util.EventProvider.prototype);

YAHOO.register("yuitest", YAHOO.tool.TestRunner, {version: "2.3.0", build: "442"});

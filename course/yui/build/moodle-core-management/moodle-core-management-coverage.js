if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/moodle-core-management/moodle-core-management.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-core-management/moodle-core-management.js",
    code: []
};
_yuitest_coverage["build/moodle-core-management/moodle-core-management.js"].code=["YUI.add('moodle-core-management', function (Y, NAME) {","","/**"," *"," * @class ManagementConsole"," * @constructor"," * @param {Object} config"," */","function ManagementConsole(config) {","    ManagementConsole.superclass.constructor.apply(this, [config]);","}","ManagementConsole.NAME = 'moodle-course-management';","ManagementConsole.CSS_PREFIX = 'management';","ManagementConsole.ATTRS = {","    element : {","        setter : function(node) {","            if (typeof(node) === 'string') {","                node = Y.one('#'+node);","            }","            return node;","        },","        sesskey : function() {},","        categorylisting : {},","        courselisting : {},","        coursedetails : {},","        activecategoryid : {},","        activecourseid : {},","        categories : {","            setter : function(item, name) {","                if (Y.Lang.isArray(item)) {","                    return item;","                }","                var items = this.get(name);","                items[item.get('categoryid')] = item;","                return items;","            },","            value : []","        },","        course : {","            setter : function(item, name) {","                if (Y.Lang.isArray(item)) {","                    return item;","                }","                var items = this.get(name);","                items[item.get('courseid')] = item;","                return items;","            },","            value : []","        }","    }","};","ManagementConsole.prototype = {","    initializer : function() {","        var element;","        this.set('element', 'coursecat-management');","        element = this.get('element');","        this.set('sesskey', element.one('input[name=sesskey]').getAttribute('value'));","        this.set('categorylisting', element.one('#category-listing'));","        this.set('courselisting', element.one('#course-listing'));","        this.set('coursedetails', element.one('#course-detail'));","        this.set('activecategoryid', this.get('categorylisting').one('.listitem[data-selected=\"1\"]').getData('id'));","        this.set('activecourseid', this.get('courselisting').one('.listitem[data-selected=\"1\"]').getData('id'));","        this.initialise_categories();","        this.initialise_courses();","    },","    initialise_categories : function() {","        this.get('categorylisting').all('.listitem[data-id]').each(function(node){","            this.set('categories', new ManagedCategory({node : node, managementconsole : this}));","        }, this);","    },","    initialise_courses : function() {","        var category = this.get('categories')[this.get('activecategoryid')];","        this.get('categorylisting').all('.listitem[data-id]').each(function(node){","            this.set('courses', new ManagedCourse({node : node, managementconsole : this, category : category}));","        }, this);","    },","    debug : function() {","        var categories = this.get('categories'),","            courses = this.get('courses'),","            c = Y.Node.create,","            out = c('<div></div>'),","            categoryid, courseid, category, course;","        out.append(c('<div></div>').append('Categories: ' + categories.length()));","        out.append(c('<div></div>').append('Courses: ' + courses.length()));","        for (categoryid in categories) {","            category = categories[categoryid];","            out.append(c('<div></div>').append('Category: '+category.get('node').one(' > div > a').get('innerHTML')));","        }","        for (courseid in courses) {","            course = courses[courseid];","            out.append(c('<div></div>').append('Category: '+course.get('node').one(' > div > a').get('innerHTML')));","        }","        this.get('element').append(out);","    }","};","Y.extend(ManagementConsole, Y.Base, ManagementConsole.prototype);","","","/**"," * @class ManagementCategory"," * @constructor"," * @param {Object} config"," */","function ManagedCategory(config) {","    ManagedCategory.superclass.constructor.apply(this, config);","}","ManagedCategory.NAME = 'moodle-course-management-managedcategory';","ManagedCategory.CSS_PREFIX = 'management-managedcategory';","ManagedCategory.ATTRS = {","    categoryid : {},","    selected : {","        value : false","    },","    node : {","        ","    },","    /**","     *","     * @type ManagementConsole","     */","    managementconsole : {}","};","ManagedCategory.prototype = {","    initializer : function() {","        var node = this.get('node');","        this.set('categoryid', node.getData('id'));","        this.set('selected', (node.getData('selected') === '1'));","    }","};","Y.extend(ManagedCategory, Y.Base, ManagedCategory.prototype);","","","","","/**"," * @class ManagementCourse"," * @constructor"," * @param {Object} config"," */","function ManagedCourse(config) {","    ManagedCourse.superclass.constructor.apply(this, config);","}","ManagedCourse.NAME = 'moodle-course-management-managedcategory';","ManagedCourse.CSS_PREFIX = 'management-managedcategory';","ManagedCourse.ATTRS = {","    courseid : {},","    selected : {","        value : false","    },","    node : {","","    },","    /**","     *","     * @type ManagementConsole","     */","    managementconsole : {},","    /**","     *","     * @type ManagedCategory","     */","    category : {}","};","ManagedCourse.prototype = {","    initializer : function() {","        var node = this.get('node');","        this.set('courseid', node.getData('id'));","    }","};","Y.extend(ManagedCourse, Y.Base, ManagedCourse.prototype);","","M.course = M.course || {};","M.course.init_management = function(config) {","    var m = new ManagementConsole(config);","    m.debug();","};","","}, '@VERSION@');"];
_yuitest_coverage["build/moodle-core-management/moodle-core-management.js"].lines = {"1":0,"9":0,"10":0,"12":0,"13":0,"14":0,"17":0,"18":0,"20":0,"30":0,"31":0,"33":0,"34":0,"35":0,"41":0,"42":0,"44":0,"45":0,"46":0,"52":0,"54":0,"55":0,"56":0,"57":0,"58":0,"59":0,"60":0,"61":0,"62":0,"63":0,"64":0,"67":0,"68":0,"72":0,"73":0,"74":0,"78":0,"83":0,"84":0,"85":0,"86":0,"87":0,"89":0,"90":0,"91":0,"93":0,"96":0,"104":0,"105":0,"107":0,"108":0,"109":0,"123":0,"125":0,"126":0,"127":0,"130":0,"140":0,"141":0,"143":0,"144":0,"145":0,"164":0,"166":0,"167":0,"170":0,"172":0,"173":0,"174":0,"175":0};
_yuitest_coverage["build/moodle-core-management/moodle-core-management.js"].functions = {"ManagementConsole:9":0,"setter:16":0,"setter:29":0,"setter:40":0,"initializer:53":0,"(anonymous 2):67":0,"initialise_categories:66":0,"(anonymous 3):73":0,"initialise_courses:71":0,"debug:77":0,"ManagedCategory:104":0,"initializer:124":0,"ManagedCourse:140":0,"initializer:165":0,"init_management:173":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-core-management/moodle-core-management.js"].coveredLines = 70;
_yuitest_coverage["build/moodle-core-management/moodle-core-management.js"].coveredFunctions = 16;
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 1);
YUI.add('moodle-core-management', function (Y, NAME) {

/**
 *
 * @class ManagementConsole
 * @constructor
 * @param {Object} config
 */
_yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 9);
function ManagementConsole(config) {
    _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "ManagementConsole", 9);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 10);
ManagementConsole.superclass.constructor.apply(this, [config]);
}
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 12);
ManagementConsole.NAME = 'moodle-course-management';
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 13);
ManagementConsole.CSS_PREFIX = 'management';
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 14);
ManagementConsole.ATTRS = {
    element : {
        setter : function(node) {
            _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "setter", 16);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 17);
if (typeof(node) === 'string') {
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 18);
node = Y.one('#'+node);
            }
            _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 20);
return node;
        },
        sesskey : function() {},
        categorylisting : {},
        courselisting : {},
        coursedetails : {},
        activecategoryid : {},
        activecourseid : {},
        categories : {
            setter : function(item, name) {
                _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "setter", 29);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 30);
if (Y.Lang.isArray(item)) {
                    _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 31);
return item;
                }
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 33);
var items = this.get(name);
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 34);
items[item.get('categoryid')] = item;
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 35);
return items;
            },
            value : []
        },
        course : {
            setter : function(item, name) {
                _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "setter", 40);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 41);
if (Y.Lang.isArray(item)) {
                    _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 42);
return item;
                }
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 44);
var items = this.get(name);
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 45);
items[item.get('courseid')] = item;
                _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 46);
return items;
            },
            value : []
        }
    }
};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 52);
ManagementConsole.prototype = {
    initializer : function() {
        _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "initializer", 53);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 54);
var element;
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 55);
this.set('element', 'coursecat-management');
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 56);
element = this.get('element');
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 57);
this.set('sesskey', element.one('input[name=sesskey]').getAttribute('value'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 58);
this.set('categorylisting', element.one('#category-listing'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 59);
this.set('courselisting', element.one('#course-listing'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 60);
this.set('coursedetails', element.one('#course-detail'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 61);
this.set('activecategoryid', this.get('categorylisting').one('.listitem[data-selected="1"]').getData('id'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 62);
this.set('activecourseid', this.get('courselisting').one('.listitem[data-selected="1"]').getData('id'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 63);
this.initialise_categories();
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 64);
this.initialise_courses();
    },
    initialise_categories : function() {
        _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "initialise_categories", 66);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 67);
this.get('categorylisting').all('.listitem[data-id]').each(function(node){
            _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "(anonymous 2)", 67);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 68);
this.set('categories', new ManagedCategory({node : node, managementconsole : this}));
        }, this);
    },
    initialise_courses : function() {
        _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "initialise_courses", 71);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 72);
var category = this.get('categories')[this.get('activecategoryid')];
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 73);
this.get('categorylisting').all('.listitem[data-id]').each(function(node){
            _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "(anonymous 3)", 73);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 74);
this.set('courses', new ManagedCourse({node : node, managementconsole : this, category : category}));
        }, this);
    },
    debug : function() {
        _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "debug", 77);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 78);
var categories = this.get('categories'),
            courses = this.get('courses'),
            c = Y.Node.create,
            out = c('<div></div>'),
            categoryid, courseid, category, course;
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 83);
out.append(c('<div></div>').append('Categories: ' + categories.length()));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 84);
out.append(c('<div></div>').append('Courses: ' + courses.length()));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 85);
for (categoryid in categories) {
            _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 86);
category = categories[categoryid];
            _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 87);
out.append(c('<div></div>').append('Category: '+category.get('node').one(' > div > a').get('innerHTML')));
        }
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 89);
for (courseid in courses) {
            _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 90);
course = courses[courseid];
            _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 91);
out.append(c('<div></div>').append('Category: '+course.get('node').one(' > div > a').get('innerHTML')));
        }
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 93);
this.get('element').append(out);
    }
};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 96);
Y.extend(ManagementConsole, Y.Base, ManagementConsole.prototype);


/**
 * @class ManagementCategory
 * @constructor
 * @param {Object} config
 */
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 104);
function ManagedCategory(config) {
    _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "ManagedCategory", 104);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 105);
ManagedCategory.superclass.constructor.apply(this, config);
}
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 107);
ManagedCategory.NAME = 'moodle-course-management-managedcategory';
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 108);
ManagedCategory.CSS_PREFIX = 'management-managedcategory';
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 109);
ManagedCategory.ATTRS = {
    categoryid : {},
    selected : {
        value : false
    },
    node : {
        
    },
    /**
     *
     * @type ManagementConsole
     */
    managementconsole : {}
};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 123);
ManagedCategory.prototype = {
    initializer : function() {
        _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "initializer", 124);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 125);
var node = this.get('node');
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 126);
this.set('categoryid', node.getData('id'));
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 127);
this.set('selected', (node.getData('selected') === '1'));
    }
};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 130);
Y.extend(ManagedCategory, Y.Base, ManagedCategory.prototype);




/**
 * @class ManagementCourse
 * @constructor
 * @param {Object} config
 */
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 140);
function ManagedCourse(config) {
    _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "ManagedCourse", 140);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 141);
ManagedCourse.superclass.constructor.apply(this, config);
}
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 143);
ManagedCourse.NAME = 'moodle-course-management-managedcategory';
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 144);
ManagedCourse.CSS_PREFIX = 'management-managedcategory';
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 145);
ManagedCourse.ATTRS = {
    courseid : {},
    selected : {
        value : false
    },
    node : {

    },
    /**
     *
     * @type ManagementConsole
     */
    managementconsole : {},
    /**
     *
     * @type ManagedCategory
     */
    category : {}
};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 164);
ManagedCourse.prototype = {
    initializer : function() {
        _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "initializer", 165);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 166);
var node = this.get('node');
        _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 167);
this.set('courseid', node.getData('id'));
    }
};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 170);
Y.extend(ManagedCourse, Y.Base, ManagedCourse.prototype);

_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 172);
M.course = M.course || {};
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 173);
M.course.init_management = function(config) {
    _yuitest_coverfunc("build/moodle-core-management/moodle-core-management.js", "init_management", 173);
_yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 174);
var m = new ManagementConsole(config);
    _yuitest_coverline("build/moodle-core-management/moodle-core-management.js", 175);
m.debug();
};

}, '@VERSION@');

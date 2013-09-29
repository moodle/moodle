YUI.add('moodle-core-management', function (Y, NAME) {

/**
 *
 * @class ManagementConsole
 * @constructor
 * @param {Object} config
 */
function ManagementConsole(config) {
    ManagementConsole.superclass.constructor.apply(this, [config]);
}
ManagementConsole.NAME = 'moodle-course-management';
ManagementConsole.CSS_PREFIX = 'management';
ManagementConsole.ATTRS = {
    element : {
        setter : function(node) {
            if (typeof(node) === 'string') {
                node = Y.one('#'+node);
            }
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
                if (Y.Lang.isArray(item)) {
                    return item;
                }
                var items = this.get(name);
                items[item.get('categoryid')] = item;
                return items;
            },
            value : []
        },
        course : {
            setter : function(item, name) {
                if (Y.Lang.isArray(item)) {
                    return item;
                }
                var items = this.get(name);
                items[item.get('courseid')] = item;
                return items;
            },
            value : []
        }
    }
};
ManagementConsole.prototype = {
    initializer : function() {
        var element;
        this.set('element', 'coursecat-management');
        element = this.get('element');
        this.set('sesskey', element.one('input[name=sesskey]').getAttribute('value'));
        this.set('categorylisting', element.one('#category-listing'));
        this.set('courselisting', element.one('#course-listing'));
        this.set('coursedetails', element.one('#course-detail'));
        this.set('activecategoryid', this.get('categorylisting').one('.listitem[data-selected="1"]').getData('id'));
        this.set('activecourseid', this.get('courselisting').one('.listitem[data-selected="1"]').getData('id'));
        this.initialise_categories();
        this.initialise_courses();
    },
    initialise_categories : function() {
        this.get('categorylisting').all('.listitem[data-id]').each(function(node){
            this.set('categories', new ManagedCategory({node : node, managementconsole : this}));
        }, this);
    },
    initialise_courses : function() {
        var category = this.get('categories')[this.get('activecategoryid')];
        this.get('categorylisting').all('.listitem[data-id]').each(function(node){
            this.set('courses', new ManagedCourse({node : node, managementconsole : this, category : category}));
        }, this);
    },
    debug : function() {
        var categories = this.get('categories'),
            courses = this.get('courses'),
            c = Y.Node.create,
            out = c('<div></div>'),
            categoryid, courseid, category, course;
        out.append(c('<div></div>').append('Categories: ' + categories.length()));
        out.append(c('<div></div>').append('Courses: ' + courses.length()));
        for (categoryid in categories) {
            category = categories[categoryid];
            out.append(c('<div></div>').append('Category: '+category.get('node').one(' > div > a').get('innerHTML')));
        }
        for (courseid in courses) {
            course = courses[courseid];
            out.append(c('<div></div>').append('Category: '+course.get('node').one(' > div > a').get('innerHTML')));
        }
        this.get('element').append(out);
    }
};
Y.extend(ManagementConsole, Y.Base, ManagementConsole.prototype);


/**
 * @class ManagementCategory
 * @constructor
 * @param {Object} config
 */
function ManagedCategory(config) {
    ManagedCategory.superclass.constructor.apply(this, config);
}
ManagedCategory.NAME = 'moodle-course-management-managedcategory';
ManagedCategory.CSS_PREFIX = 'management-managedcategory';
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
ManagedCategory.prototype = {
    initializer : function() {
        var node = this.get('node');
        this.set('categoryid', node.getData('id'));
        this.set('selected', (node.getData('selected') === '1'));
    }
};
Y.extend(ManagedCategory, Y.Base, ManagedCategory.prototype);




/**
 * @class ManagementCourse
 * @constructor
 * @param {Object} config
 */
function ManagedCourse(config) {
    ManagedCourse.superclass.constructor.apply(this, config);
}
ManagedCourse.NAME = 'moodle-course-management-managedcategory';
ManagedCourse.CSS_PREFIX = 'management-managedcategory';
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
ManagedCourse.prototype = {
    initializer : function() {
        var node = this.get('node');
        this.set('courseid', node.getData('id'));
    }
};
Y.extend(ManagedCourse, Y.Base, ManagedCourse.prototype);

M.course = M.course || {};
M.course.init_management = function(config) {
    var m = new ManagementConsole(config);
    m.debug();
};

}, '@VERSION@');

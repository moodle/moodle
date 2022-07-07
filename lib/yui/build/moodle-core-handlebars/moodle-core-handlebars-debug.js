YUI.add('moodle-core-handlebars', function (Y, NAME) {

// Handlebars only exists in the global namespace and we do not want to
// Clone the entire thing.

// It should be reasonably safe within Moodle to add some core
// functionality to Handlebars at this level.
Y.Handlebars.registerHelper('get_string', function() {
    var args = new Y.Array(arguments);

    // Pop off the Handlebars object at the end.
    args.pop();

    // Add the Handlebars hashtable to support get_string arguments.
    args.push(arguments[arguments.length - 1].hash);

    // Perform the get_string.
    return M.util.get_string.apply(this, args);
});

Y.Handlebars.registerHelper('image_url', function() {
    var args = new Y.Array(arguments);

    // Pop off the Handlebars object at the end.
    args.pop();

    // Perform the get_string.
    return M.util.image_url.apply(this, args);
});


}, '@VERSION@');

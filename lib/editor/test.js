// All our custom buttons will call this function when clicked.
// We use the buttonId parameter to determine what button
// triggered the call.
function clickHandler(editor, buttonId) {
  switch (buttonId) {
    case "my-toc":
      editor.insertHTML("<h1>Table Of Contents</h1>");
      break;
    case "my-date":
      editor.insertHTML((new Date()).toString());
      break;
  }
};

// Create a new configuration object
var config = new HTMLArea.Config();

// Register our custom buttons
config.registerButton("my-toc",  "Insert TOC", "images/em.icon.smile.gif", false, clickHandler);
config.registerButton("my-date", "Insert date/time", "icon_ins_char.gif", false, clickHandler);

// Append the buttons to the default toolbar
config.toolbar.push(["linebreak", "my-toc", "my-date"]);

// Replace an existing textarea with an HTMLArea object having the above config.
HTMLArea.replace("TA", config);
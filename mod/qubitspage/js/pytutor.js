var SVG_ARROW_POLYGON = "0,3 12,3 12,0 18,5 12,10 12,7 0,7";
var SVG_ARROW_HEIGHT = 10; // must match height of SVG_ARROW_POLYGON

var curVisualizerID = 1; // global to uniquely identify each ExecutionVisualizer instance

function ExecutionVisualizer(domRootID, dat, params) {
  this.curInputCode = dat.code.main_code.rtrim(); // kill trailing spaces
  this.curTrace = dat.trace;
  this.sourceFiles = dat.code;
  this.curFile = "";

  this.DEFAULT_EMBEDDED_CODE_DIV_WIDTH = 350;
  this.DEFAULT_EMBEDDED_CODE_DIV_HEIGHT = 400;

  // if the final entry is raw_input or mouse_input, then trim it from the trace and
  // set a flag to prompt for user input when execution advances to the
  // end of the trace
  if (this.curTrace.length > 0) {
    var lastEntry = this.curTrace[this.curTrace.length - 1];
    if (lastEntry.event == "raw_input") {
      this.promptForUserInput = true;
      this.userInputPromptStr = htmlspecialchars(lastEntry.prompt);
      this.curTrace.pop(); // kill last entry so that it doesn't get displayed
    } else if (lastEntry.event == "mouse_input") {
      this.promptForMouseInput = true;
      this.userInputPromptStr = htmlspecialchars(lastEntry.prompt);
      this.curTrace.pop(); // kill last entry so that it doesn't get displayed
    }
  }

  this.curInstr = 0;

  this.params = params;
  if (!this.params) {
    this.params = {}; // make it an empty object by default
  }

  var arrowLinesDef = this.params.arrowLines !== undefined;
  var highlightLinesDef = this.params.highlightLines !== undefined;

  if (!arrowLinesDef && !highlightLinesDef) {
    // neither is set
    this.params.highlightLines = false;
    this.params.arrowLines = true;
  } else if (arrowLinesDef && highlightLinesDef) {
    // both are set, so just use their set values
  } else if (arrowLinesDef) {
    // only arrowLines set
    this.params.highlightLines = !this.params.arrowLines;
  } else {
    // only highlightLines set
    this.params.arrowLines = !this.params.highlightLines;
  }

  this.compactFuncLabels = this.params.compactFuncLabels;

  // audible!
  if (this.params.pyCrazyMode) {
    this.params.arrowLines = this.params.highlightLines = false;
  }

  if (this.params.visualizerIdOverride) {
    this.visualizerID = this.params.visualizerIdOverride;
  } else {
    // needs to be unique!
    this.visualizerID = curVisualizerID;
    curVisualizerID++;
  }

  this.leftGutterSvgInitialized = false;
  this.arrowOffsetY = undefined;
  this.codeRowHeight = undefined;

  // avoid 'undefined' state
  this.disableHeapNesting = this.params.disableHeapNesting == true;
  this.drawParentPointers = this.params.drawParentPointers == true;
  this.textualMemoryLabels = this.params.textualMemoryLabels == true;
  this.showOnlyOutputs = this.params.showOnlyOutputs == true;
  this.tabularView = this.params.tabularView == true;
  this.showAllFrameLabels = this.params.showAllFrameLabels == true;

  this.executeCodeWithRawInputFunc = this.params.executeCodeWithRawInputFunc;

  // cool, we can create a separate jsPlumb instance for each visualization:
  this.jsPlumbInstance = jsPlumb.getInstance({
    Endpoint: ["Dot", { radius: 3 }],
    EndpointStyles: [
      { fillStyle: connectorBaseColor },
      { fillstyle: null } /* make right endpoint invisible */,
    ],
    Anchors: ["RightMiddle", "LeftMiddle"],
    PaintStyle: { lineWidth: 1, strokeStyle: connectorBaseColor },

    Connector: ["StateMachine"],
    Overlays: [
      ["Arrow", { length: 10, width: 7, foldback: 0.55, location: 1 }],
    ],
    EndpointHoverStyles: [
      { fillStyle: connectorHighlightColor },
      { fillstyle: null } /* make right endpoint invisible */,
    ],
    HoverPaintStyle: { lineWidth: 1, strokeStyle: connectorHighlightColor },
  });

  // true iff trace ended prematurely since maximum instruction limit has
  // been reached
  var instrLimitReached = false;

  // the root elements for jQuery and D3 selections, respectively.
  // ALWAYS use these and never use raw $(__) or d3.select(__)
  this.domRoot = $("#" + domRootID);
  this.domRoot.data("vis", this); // bnm store a reference to this as div data for use later.
  this.domRootD3 = d3.select("#" + domRootID);

  // stick a new div.ExecutionVisualizer within domRoot and make that
  // the new domRoot:
  this.domRoot.html('<div class="ExecutionVisualizer"></div>');

  this.domRoot = this.domRoot.find("div.ExecutionVisualizer");
  this.domRootD3 = this.domRootD3.select("div.ExecutionVisualizer");

  // initialize in renderPyCodeOutput()
  this.codeOutputLines = null;
  this.breakpoints = null; // set of execution points to set as breakpoints
  this.sortedBreakpointsList = []; // sorted and synced with breakpointLines

  this.classAttrsHidden = {}; // kludgy hack for 'show/hide attributes' for class objects

  // API for adding a hook, created by David Pritchard
  this.pytutor_hooks = {}; // keys, hook names; values, list of functions

  if (this.params.lang === "java") {
    this.activateJavaFrontend(); // ohhhh yeah!
  }

  // how many lines does curTrace print to stdout max?
  this.numStdoutLines = 0;
  // go backwards from the end ... sometimes the final entry doesn't
  // have an stdout
  var lastStdout;
  for (var i = this.curTrace.length - 1; i >= 0; i--) {
    lastStdout = this.curTrace[i].stdout;
    if (lastStdout) {
      break;
    }
  }

  if (lastStdout) {
    this.numStdoutLines = lastStdout.rtrim().split("\n").length;
  }

  this.try_hook("end_constructor", { myViz: this });

  this.hasRendered = false;

  this.render(); // go for it!
}

ExecutionVisualizer.prototype.add_pytutor_hook = function (hook_name, func) {
  if (this.pytutor_hooks[hook_name]) this.pytutor_hooks[hook_name].push(func);
  else this.pytutor_hooks[hook_name] = [func];
};

ExecutionVisualizer.prototype.try_hook = function (hook_name, args) {
  if (this.pytutor_hooks[hook_name]) {
    for (var i = 0; i < this.pytutor_hooks[hook_name].length; i++) {
      // apply w/o "this", and pack sole arg into array as required by apply
      var handled_and_result = this.pytutor_hooks[hook_name][i].apply(null, [
        args,
      ]);

      if (handled_and_result && handled_and_result[0])
        return handled_and_result;
    }
  }
  return [false];
};

// for managing state related to pesky jsPlumb connectors, need to reset
// before every call to renderDataStructures, or else all hell breaks
// loose. yeah, this is kludgy and stateful, but at least all of the
// relevant state gets shoved into one unified place
ExecutionVisualizer.prototype.resetJsPlumbManager = function () {
  this.jsPlumbManager = {
    heap_pointer_src_id: 1, // increment this to be unique for each heap_pointer_src_*

    // Key:   CSS ID of the div element representing the stack frame variable
    //        (for stack->heap connections) or heap object (for heap->heap connections)
    //        the format is: '<this.visualizerID>__heap_pointer_src_<src id>'
    // Value: CSS ID of the div element representing the value rendered in the heap
    //        (the format is given by generateHeapObjID())
    //
    // The reason we need to prepend this.visualizerID is because jsPlumb needs
    // GLOBALLY UNIQUE IDs for use as connector endpoints.
    //
    // TODO: jsPlumb might be able to directly take DOM elements rather
    // than IDs, which makes the above point moot. But let's just stick
    // with this for now until I want to majorly refactor :)

    // the only elements in these sets are NEW elements to be rendered in this
    // particular call to renderDataStructures.
    connectionEndpointIDs: d3.map(),
    heapConnectionEndpointIDs: d3.map(), // subset of connectionEndpointIDs for heap->heap connections
    // analogous to connectionEndpointIDs, except for environment parent pointers
    parentPointerConnectionEndpointIDs: d3.map(),

    renderedHeapObjectIDs: d3.map(), // format given by generateHeapObjID()
  };
};

// create a unique ID, which is often necessary so that jsPlumb doesn't get confused
// due to multiple ExecutionVisualizer instances being displayed simultaneously
ExecutionVisualizer.prototype.generateID = function (original_id) {
  // (it's safer to start names with a letter rather than a number)
  return "v" + this.visualizerID + "__" + original_id;
};

// create a unique CSS ID for a heap object, which should include both
// its ID and the current step number. this is necessary if we want to
// display the same heap object at multiple execution steps.
ExecutionVisualizer.prototype.generateHeapObjID = function (objID, stepNum) {
  return this.generateID("heap_object_" + objID + "_s" + stepNum);
};

ExecutionVisualizer.prototype.render = function () {
  if (this.hasRendered) {
    alert(
      "ERROR: You should only call render() ONCE on an ExecutionVisualizer object."
    );
    return;
  }

  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  var codeDisplayHTML =
    '<div id="codeDisplayDiv">\
        <div id="langDisplayDiv"></div>\
        <div id="pyCodeOutputDiv"/>\
        <div id="editCodeLinkDiv"><a id="editBtn">Edit code</a>\
        <span id="liveModeSpan" style="display: none;">| <a id="editLiveModeBtn" href="#">Live programming</a></a>\
        </div>\
        <div id="legendDiv"/>\
        <div id="executionSlider" class="executionSlider"/>\
        <div id="executionSliderFooter"/>\
        <div id="vcrControls">\
        <button id="jmpFirstInstr", type="button">&lt;&lt; First</button>\
        <button id="jmpStepBack", type="button">&lt; Back</button>\
        <button id="jmpStepFwd", type="button">Forward &gt;</button>\
        <button id="jmpLastInstr", type="button">Last &gt;&gt;</button>\
        <div><span id="curInstr">Step ? of ?</span></div>\
        </div>\
        <div id="rawUserInputDiv">\
        <span id="userInputPromptStr"/>\
        <input type="text" id="raw_input_textbox" size="30"/>\
        <button id="raw_input_submit_btn">Submit</button>\
        </div>\
        <div id="errorOutput"/>\
        <div id="stepAnnotationDiv">\
        <textarea class="annotationText" id="stepAnnotationEditor" cols="60" rows="3"></textarea>\
        <div class="annotationText" id="stepAnnotationViewer"></div>\
        </div>\
        <div id="annotateLinkDiv"><button id="annotateBtn" type="button">Annotate this step</button></div>\
        </div>';

  var outputsHTML =
    '<div id="htmlOutputDiv"></div>\
        <div id="progOutputs">\
        <div id="printOutputDocs">Print output (drag lower right corner to resize)</div>\n\
        <textarea id="pyStdout" cols="40" rows="5" wrap="off" readonly></textarea>\
        </div>';

  var codeVizHTML =
    '<div id="dataViz">\
        <table id="stackHeapTable">\
        <tr>\
        <td id="stack_td">\
        <div id="globals_area">\
        <div id="stackHeader">Frames</div>\
        </div>\
        <div id="stack"></div>\
        </td>\
        <td id="heap_td">\
        <div id="heap">\
        <div id="heapHeader">Objects</div>\
        </div>\
        </td>\
        </tr>\
        </table>\
        </div>';

  // override
  if (myViz.tabularView) {
    codeVizHTML = '<div id="optTabularView"></div>';
  }

  var vizHeaderHTML =
    '<div id="vizHeader">\
        <textarea class="vizTitleText" id="vizTitleEditor" cols="60" rows="1"></textarea>\
        <div class="vizTitleText" id="vizTitleViewer"></div>\
        <textarea class="vizDescriptionText" id="vizDescriptionEditor" cols="75" rows="2"></textarea>\
        <div class="vizDescriptionText" id="vizDescriptionViewer"></div>\
        </div>';

  if (this.params.verticalStack) {
    this.domRoot.html(
      vizHeaderHTML +
        '<table border="0" class="visualizer"><tr><td class="vizLayoutTd" id="vizLayoutTdFirst"">' +
        codeDisplayHTML +
        '</td></tr><tr><td class="vizLayoutTd" id="vizLayoutTdSecond">' +
        codeVizHTML +
        "</td></tr></table>"
    );
  } else {
    this.domRoot.html(
      vizHeaderHTML +
        '<table border="0" class="visualizer"><tr><td class="vizLayoutTd" id="vizLayoutTdFirst">' +
        codeDisplayHTML +
        '</td><td class="vizLayoutTd" id="vizLayoutTdSecond">' +
        codeVizHTML +
        "</td></tr></table>"
    );
  }

  if (this.showOnlyOutputs) {
    myViz.domRoot.find("#dataViz").hide();
    this.domRoot.find("#vizLayoutTdSecond").append(outputsHTML);

    if (this.params.verticalStack) {
      this.domRoot.find("#vizLayoutTdSecond").css("padding-top", "25px");
    } else {
      this.domRoot.find("#vizLayoutTdSecond").css("padding-left", "25px");
    }
  } else {
    var stdoutHeight = "75px";
    // heuristic for code with really small outputs
    if (this.numStdoutLines <= 3) {
      stdoutHeight = 18 * this.numStdoutLines + "px";
    }
    if (this.params.embeddedMode) {
      stdoutHeight = "45px";
    }

    // position this under the code:
    //this.domRoot.find('#vizLayoutTdFirst').append(outputsHTML);

    // position this above visualization (started trying this on 2016-06-01)
    this.domRoot.find("#vizLayoutTdSecond").prepend(outputsHTML);

    // do this only after adding to DOM
    this.domRoot
      .find("#pyStdout")
      .width("350px")
      .height(stdoutHeight)
      .resizable();
  }

  if (this.params.arrowLines) {
    this.domRoot
      .find("#legendDiv")
      .append('<svg id="prevLegendArrowSVG"/> line that has just executed')
      .append(
        '<p style="margin-top: 4px"><svg id="curLegendArrowSVG"/> next line to execute</p>'
      );

    myViz.domRootD3
      .select("svg#prevLegendArrowSVG")
      .append("polygon")
      .attr("points", SVG_ARROW_POLYGON)
      .attr("fill", lightArrowColor);

    myViz.domRootD3
      .select("svg#curLegendArrowSVG")
      .append("polygon")
      .attr("points", SVG_ARROW_POLYGON)
      .attr("fill", darkArrowColor);
  } else if (this.params.highlightLines) {
    myViz.domRoot
      .find("#legendDiv")
      .append(
        '<span class="highlight-legend highlight-prev">line that has just executed</span> '
      )
      .append(
        '<span class="highlight-legend highlight-cur">next line to execute</span>'
      );
  } else if (this.params.pyCrazyMode) {
    myViz.domRoot
      .find("#legendDiv")
      .append(
        '<a href="https://github.com/pgbovine/Py2crazy">Py2crazy</a> mode!'
      )
      .append(
        " Stepping through (roughly) each executed expression. Color codes:<p/>"
      )
      .append(
        '<span class="pycrazy-highlight-prev">expression that just executed</span><br/>'
      )
      .append(
        '<span class="pycrazy-highlight-cur">next expression to execute</span>'
      );
  }

  if (this.params.editCodeBaseURL) {
    // kinda kludgy
    var pyVer = "2"; // default
    if (this.params.lang === "js") {
      pyVer = "js";
    } else if (this.params.lang === "ts") {
      pyVer = "ts";
    } else if (this.params.lang === "java") {
      pyVer = "java";
    } else if (this.params.lang === "py3") {
      pyVer = "3";
    } else if (this.params.lang === "c") {
      pyVer = "c";
    } else if (this.params.lang === "cpp") {
      pyVer = "cpp";
    }

    var urlStr = $.param.fragment(
      this.params.editCodeBaseURL,
      { code: this.curInputCode, py: pyVer },
      2
    );
    this.domRoot.find("#editBtn").attr("href", urlStr);
  } else {
    this.domRoot.find("#editCodeLinkDiv").hide(); // just hide for simplicity!
    this.domRoot.find("#editBtn").attr("href", "#");
    this.domRoot.find("#editBtn").click(function () {
      return false;
    }); // DISABLE the link!
  }

  if (this.params.lang !== undefined) {
    if (this.params.lang === "js") {
      this.domRoot.find("#langDisplayDiv").html("JavaScript");
    } else if (this.params.lang === "ts") {
      this.domRoot.find("#langDisplayDiv").html("TypeScript");
    } else if (this.params.lang === "ruby") {
      this.domRoot.find("#langDisplayDiv").html("Ruby");
    } else if (this.params.lang === "java") {
      this.domRoot.find("#langDisplayDiv").html("Java");
    } else if (this.params.lang === "py2") {
      this.domRoot.find("#langDisplayDiv").html("Python 2.7");
    } else if (this.params.lang === "py3") {
      this.domRoot.find("#langDisplayDiv").html("Python 3");
    } else if (this.params.lang === "c") {
      this.domRoot
        .find("#langDisplayDiv")
        .html(
          'C (gcc 4.8, C11) <font color="#e93f34">EXPERIMENTAL!</font><br/>see <a href="https://github.com/pgbovine/opt-cpp-backend/issues" target="_blank">known bugs</a> and report to philip@pgbovine.net'
        );
    } else if (this.params.lang === "cpp") {
      this.domRoot
        .find("#langDisplayDiv")
        .html(
          'C++ (gcc 4.8, C++11) <font color="#e93f34">EXPERIMENTAL!</font><br/>see <a href="https://github.com/pgbovine/opt-cpp-backend/issues" target="_blank">known bugs</a> and report to philip@pgbovine.net'
        );
    } else {
      this.domRoot.find("#langDisplayDiv").hide();
    }
  }

  if (this.params.allowEditAnnotations !== undefined) {
    this.allowEditAnnotations = this.params.allowEditAnnotations;
  } else {
    this.allowEditAnnotations = false;
  }

  if (this.params.pyCrazyMode !== undefined) {
    this.pyCrazyMode = this.params.pyCrazyMode;
  } else {
    this.pyCrazyMode = false;
  }

  this.domRoot.find("#stepAnnotationEditor").hide();

  if (this.params.embeddedMode) {
    this.embeddedMode = true;
    // nix this for now ...
    //this.params.hideOutput = true; // put this before hideOutput handler

    // don't override if they've already been set!
    if (this.params.codeDivWidth === undefined) {
      this.params.codeDivWidth = this.DEFAULT_EMBEDDED_CODE_DIV_WIDTH;
    }

    if (this.params.codeDivHeight === undefined) {
      this.params.codeDivHeight = this.DEFAULT_EMBEDDED_CODE_DIV_HEIGHT;
    }

    this.allowEditAnnotations = false;

    // add an extra label to link back to the main site, so that viewers
    // on the embedded page know that they're seeing an OPT visualization
    this.domRoot
      .find("#codeDisplayDiv")
      .append(
        '<div style="font-size: 8pt; margin-bottom: 20px;">Visualized using <a href="http://pythontutor.com" target="_blank" style="color: #3D58A2;">Online Python Tutor</a> by <a href="http://www.pgbovine.net/" target="_blank" style="color: #3D58A2;">Philip Guo</a></div>'
      );

    myViz.domRoot.find("#executionSliderDocs").hide(); // cut out extraneous docs
  }

  myViz.editAnnotationMode = false;

  if (this.allowEditAnnotations) {
    var ab = this.domRoot.find("#annotateBtn");

    ab.click(function () {
      if (myViz.editAnnotationMode) {
        myViz.enterViewAnnotationsMode();

        myViz.domRoot
          .find(
            "#jmpFirstInstr,#jmpLastInstr,#jmpStepBack,#jmpStepFwd,#executionSlider,#editCodeLinkDiv,#stepAnnotationViewer"
          )
          .show();
        myViz.domRoot.find("#stepAnnotationEditor").hide();
        ab.html("Annotate this step");
      } else {
        myViz.enterEditAnnotationsMode();

        myViz.domRoot
          .find(
            "#jmpFirstInstr,#jmpLastInstr,#jmpStepBack,#jmpStepFwd,#executionSlider,#editCodeLinkDiv,#stepAnnotationViewer"
          )
          .hide();
        myViz.domRoot.find("#stepAnnotationEditor").show();
        ab.html("Done annotating");
      }
    });
  } else {
    this.domRoot.find("#annotateBtn").hide();
  }

  // not enough room for these extra buttons ...
  if (this.params.codeDivWidth && this.params.codeDivWidth < 470) {
    this.domRoot.find("#jmpFirstInstr").hide();
    this.domRoot.find("#jmpLastInstr").hide();
  }

  if (this.params.codeDivWidth) {
    // set width once
    this.domRoot.find("#codeDisplayDiv").width(this.params.codeDivWidth);
    // it will propagate to the slider

    //this.domRoot.find("#pyStdout").css("width", this.params.codeDivWidth - 20 /* wee tweaks */);
  }

  // enable left-right draggable pane resizer (originally from David Pritchard)
  this.domRoot.find("#codeDisplayDiv").resizable({
    handles: "e",
    minWidth: 250, //otherwise looks really goofy
    resize: function (event, ui) {
      // old name: syncStdoutWidth, now not appropriate
      // resize stdout box in unison
      //myViz.domRoot.find("#pyStdout").css("width", $(this).width() - 20 /* wee tweaks */);

      myViz.domRoot.find("#codeDisplayDiv").css("height", "auto"); // redetermine height if necessary
      myViz.renderSliderBreakpoints(); // update breakpoint display accordingly on resize
      if (myViz.params.updateOutputCallback)
        // report size change
        myViz.params.updateOutputCallback(this);
    },
  });

  if (this.params.codeDivHeight) {
    this.domRoot
      .find("#pyCodeOutputDiv")
      .css("max-height", this.params.codeDivHeight + "px");
  }

  // create a persistent globals frame
  // (note that we need to keep #globals_area separate from #stack for d3 to work its magic)
  this.domRoot
    .find("#globals_area")
    .append(
      '<div class="stackFrame" id="' +
        myViz.generateID("globals") +
        '"><div id="' +
        myViz.generateID("globals_header") +
        '" class="stackFrameHeader">' +
        this.getRealLabel("Global frame") +
        '</div><table class="stackFrameVarTable" id="' +
        myViz.generateID("global_table") +
        '"></table></div>'
    );

  if (this.params.hideOutput) {
    this.domRoot.find("#progOutputs").hide();
  }

  this.domRoot.find("#jmpFirstInstr").click(function () {
    myViz.renderStep(0);
  });

  this.domRoot.find("#jmpLastInstr").click(function () {
    myViz.renderStep(myViz.curTrace.length - 1);
  });

  this.domRoot.find("#jmpStepBack").click(function () {
    myViz.stepBack();
  });

  this.domRoot.find("#jmpStepFwd").click(function () {
    myViz.stepForward();
  });

  // disable controls initially ...
  this.domRoot.find("#vcrControls #jmpFirstInstr").attr("disabled", true);
  this.domRoot.find("#vcrControls #jmpStepBack").attr("disabled", true);
  this.domRoot.find("#vcrControls #jmpStepFwd").attr("disabled", true);
  this.domRoot.find("#vcrControls #jmpLastInstr").attr("disabled", true);

  // must postprocess curTrace prior to running precomputeCurTraceLayouts() ...
  var lastEntry = this.curTrace[this.curTrace.length - 1];

  this.instrLimitReached = lastEntry.event == "instruction_limit_reached";

  if (this.instrLimitReached) {
    this.curTrace.pop(); // kill last entry
    var warningMsg = lastEntry.exception_msg;
    this.instrLimitReachedWarningMsg = warningMsg;
    myViz.domRoot.find("#errorOutput").html(htmlspecialchars(warningMsg));
    myViz.domRoot.find("#errorOutput").show();
  }

  // set up slider after postprocessing curTrace

  var sliderDiv = this.domRoot.find("#executionSlider");
  sliderDiv.slider({ min: 0, max: this.curTrace.length - 1, step: 1 });
  //disable keyboard actions on the slider itself (to prevent double-firing of events)
  sliderDiv.find(".ui-slider-handle").unbind("keydown");
  // make skinnier and taller
  sliderDiv.find(".ui-slider-handle").css("width", "0.8em");
  sliderDiv.find(".ui-slider-handle").css("height", "1.4em");
  this.domRoot.find(".ui-widget-content").css("font-size", "0.9em");

  this.domRoot.find("#executionSlider").bind("slide", function (evt, ui) {
    // this is SUPER subtle. if this value was changed programmatically,
    // then evt.originalEvent will be undefined. however, if this value
    // was changed by a user-initiated event, then this code should be
    // executed ...
    if (evt.originalEvent) {
      myViz.renderStep(ui.value);
    }
  });

  if (this.params.startingInstruction) {
    this.params.jumpToEnd = false; // override! make sure to handle FIRST

    // weird special case for something like:
    // e=raw_input(raw_input("Enter something:"))
    if (this.params.startingInstruction == this.curTrace.length) {
      this.params.startingInstruction--;
    }

    // fail-soft with out-of-bounds startingInstruction values:
    if (this.params.startingInstruction < 0) {
      this.params.startingInstruction = 0;
    }
    if (this.params.startingInstruction >= this.curTrace.length) {
      this.params.startingInstruction = this.curTrace.length - 1;
    }

    assert(
      0 <= this.params.startingInstruction &&
        this.params.startingInstruction < this.curTrace.length
    );
    this.curInstr = this.params.startingInstruction;
  }

  if (this.params.jumpToEnd) {
    var firstErrorStep = -1;
    for (var i = 0; i < this.curTrace.length; i++) {
      var e = this.curTrace[i];
      if (e.event == "exception" || e.event == "uncaught_exception") {
        firstErrorStep = i;
        break;
      }
    }

    // set to first error step if relevant since that's more informative
    // than simply jumping to the very end
    if (firstErrorStep >= 0) {
      this.curInstr = firstErrorStep;
    } else {
      this.curInstr = this.curTrace.length - 1;
    }
  }

  if (this.params.hideCode) {
    this.domRoot.find("#vizLayoutTdFirst").hide(); // gigantic hack!
  }

  this.try_hook("end_render", { myViz: this });

  this.precomputeCurTraceLayouts();

  if (!this.params.hideCode) {
    this.renderPyCodeOutput();
  }

  // EXPERIMENTAL!
  if (this.tabularView) {
    this.renderTabularView();

    // scroll vizLayoutTdFirst down to always align with the vertical
    // scrolling ...
    $(window).scroll(function () {
      var codePane = myViz.domRoot.find("#vizLayoutTdFirst");
      var docScrollTop = $(document).scrollTop();
      var offset = codePane.offset().top;
      var delta = docScrollTop - offset;
      if (delta < 0) {
        delta = 0;
      }

      // don't scroll past the bottom of the optTabularView table:
      var optTable = myViz.domRoot.find("#optTabularView");
      var optTableBottom = optTable.height() + optTable.offset().top;
      var codeDisplayHeight = myViz.domRoot.find("#codeDisplayDiv").height();
      if (delta - offset < optTableBottom - codeDisplayHeight) {
        codePane.css("padding-top", delta);
      }
    });
  }

  var ruiDiv = myViz.domRoot.find("#rawUserInputDiv");
  ruiDiv.find("#userInputPromptStr").html(myViz.userInputPromptStr);
  ruiDiv.find("#raw_input_submit_btn").click(function () {
    var userInput = ruiDiv.find("#raw_input_textbox").val();
    // advance instruction count by 1 to get to the NEXT instruction
    myViz.executeCodeWithRawInputFunc(userInput, myViz.curInstr + 1);
  });

  this.updateOutput();

  this.hasRendered = true;
};

ExecutionVisualizer.prototype.showVizHeaderViewMode = function () {
  var titleVal = this.domRoot.find("#vizTitleEditor").val().trim();
  var descVal = this.domRoot.find("#vizDescriptionEditor").val().trim();

  this.domRoot.find("#vizTitleEditor,#vizDescriptionEditor").hide();

  if (!titleVal && !descVal) {
    this.domRoot.find("#vizHeader").hide();
  } else {
    this.domRoot
      .find("#vizHeader,#vizTitleViewer,#vizDescriptionViewer")
      .show();
    if (titleVal) {
      this.domRoot.find("#vizTitleViewer").html(htmlsanitize(titleVal)); // help prevent HTML/JS injection attacks
    }

    if (descVal) {
      this.domRoot.find("#vizDescriptionViewer").html(htmlsanitize(descVal)); // help prevent HTML/JS injection attacks
    }
  }
};

ExecutionVisualizer.prototype.showVizHeaderEditMode = function () {
  this.domRoot.find("#vizHeader").show();

  this.domRoot.find("#vizTitleViewer,#vizDescriptionViewer").hide();
  this.domRoot.find("#vizTitleEditor,#vizDescriptionEditor").show();
};

ExecutionVisualizer.prototype.destroyAllAnnotationBubbles = function () {
  var myViz = this;

  // hopefully destroys all old bubbles and reclaims their memory
  if (myViz.allAnnotationBubbles) {
    $.each(myViz.allAnnotationBubbles, function (i, e) {
      e.destroyQTip();
    });
  }

  // remove this handler as well!
  this.domRoot.find("#pyCodeOutputDiv").unbind("scroll");

  myViz.allAnnotationBubbles = null;
};

ExecutionVisualizer.prototype.initStepAnnotation = function () {
  var curEntry = this.curTrace[this.curInstr];
  if (curEntry.stepAnnotation) {
    this.domRoot
      .find("#stepAnnotationViewer")
      .html(htmlsanitize(curEntry.stepAnnotation)); // help prevent HTML/JS injection attacks
    this.domRoot.find("#stepAnnotationEditor").val(curEntry.stepAnnotation);
  } else {
    this.domRoot.find("#stepAnnotationViewer").html("");
    this.domRoot.find("#stepAnnotationEditor").val("");
  }
};

ExecutionVisualizer.prototype.initAllAnnotationBubbles = function () {
  var myViz = this;

  // TODO: check for memory leaks
  //console.log('initAllAnnotationBubbles');

  myViz.destroyAllAnnotationBubbles();

  var codelineIDs = [];
  $.each(this.domRoot.find("#pyCodeOutput .cod"), function (i, e) {
    codelineIDs.push($(e).attr("id"));
  });

  var heapObjectIDs = [];
  $.each(this.domRoot.find(".heapObject"), function (i, e) {
    heapObjectIDs.push($(e).attr("id"));
  });

  var variableIDs = [];
  $.each(this.domRoot.find(".variableTr"), function (i, e) {
    variableIDs.push($(e).attr("id"));
  });

  var frameIDs = [];
  $.each(this.domRoot.find(".stackFrame"), function (i, e) {
    frameIDs.push($(e).attr("id"));
  });

  myViz.allAnnotationBubbles = [];

  $.each(codelineIDs, function (i, e) {
    myViz.allAnnotationBubbles.push(new AnnotationBubble(myViz, "codeline", e));
  });
  $.each(heapObjectIDs, function (i, e) {
    myViz.allAnnotationBubbles.push(new AnnotationBubble(myViz, "object", e));
  });
  $.each(variableIDs, function (i, e) {
    myViz.allAnnotationBubbles.push(new AnnotationBubble(myViz, "variable", e));
  });
  $.each(frameIDs, function (i, e) {
    myViz.allAnnotationBubbles.push(new AnnotationBubble(myViz, "frame", e));
  });

  this.domRoot.find("#pyCodeOutputDiv").scroll(function () {
    $.each(myViz.allAnnotationBubbles, function (i, e) {
      if (e.type == "codeline") {
        e.redrawCodelineBubble();
      }
    });
  });

  //console.log('initAllAnnotationBubbles', myViz.allAnnotationBubbles.length);
};

ExecutionVisualizer.prototype.enterViewAnnotationsMode = function () {
  this.editAnnotationMode = false;
  var curEntry = this.curTrace[this.curInstr];

  // TODO: check for memory leaks!!!
  var myViz = this;

  if (!myViz.allAnnotationBubbles) {
    if (curEntry.bubbleAnnotations) {
      // If there is an existing annotations object, then initiate all annotations bubbles
      // and display them in 'View' mode
      myViz.initAllAnnotationBubbles();

      $.each(myViz.allAnnotationBubbles, function (i, e) {
        var txt = curEntry.bubbleAnnotations[e.domID];
        if (txt) {
          e.preseedText(txt);
        }
      });
    }
  }

  if (myViz.allAnnotationBubbles) {
    var curAnnotations = {};

    $.each(myViz.allAnnotationBubbles, function (i, e) {
      e.enterViewMode();

      if (e.text) {
        curAnnotations[e.domID] = e.text;
      }
    });

    // Save annotations directly into the current trace entry as an 'annotations' object
    // directly mapping domID -> text.
    //
    // NB: This scheme can break if the functions for generating domIDs are altered.
    curEntry.bubbleAnnotations = curAnnotations;
  }

  var stepAnnotationEditorVal = myViz.domRoot
    .find("#stepAnnotationEditor")
    .val()
    .trim();
  if (stepAnnotationEditorVal) {
    curEntry.stepAnnotation = stepAnnotationEditorVal;
  } else {
    delete curEntry.stepAnnotation; // go as far as to DELETE this field entirely
  }

  myViz.initStepAnnotation();

  myViz.showVizHeaderViewMode();

  // redraw all connectors and bubbles in new vertical position ..
  myViz.redrawConnectors();
  myViz.redrawAllAnnotationBubbles();
};

ExecutionVisualizer.prototype.enterEditAnnotationsMode = function () {
  this.editAnnotationMode = true;

  // TODO: check for memory leaks!!!
  var myViz = this;

  var curEntry = this.curTrace[this.curInstr];

  if (!myViz.allAnnotationBubbles) {
    myViz.initAllAnnotationBubbles();
  }

  $.each(myViz.allAnnotationBubbles, function (i, e) {
    e.enterEditMode();
  });

  if (curEntry.stepAnnotation) {
    myViz.domRoot.find("#stepAnnotationEditor").val(curEntry.stepAnnotation);
  } else {
    myViz.domRoot.find("#stepAnnotationEditor").val("");
  }

  myViz.showVizHeaderEditMode();

  // redraw all connectors and bubbles in new vertical position ..
  myViz.redrawConnectors();
  myViz.redrawAllAnnotationBubbles();
};

ExecutionVisualizer.prototype.redrawAllAnnotationBubbles = function () {
  if (this.allAnnotationBubbles) {
    $.each(this.allAnnotationBubbles, function (i, e) {
      e.redrawBubble();
    });
  }
};

// find the previous/next breakpoint to c or return -1 if it doesn't exist
ExecutionVisualizer.prototype.findPrevBreakpoint = function () {
  var myViz = this;
  var c = myViz.curInstr;

  if (myViz.sortedBreakpointsList.length == 0) {
    return -1;
  } else {
    for (var i = 1; i < myViz.sortedBreakpointsList.length; i++) {
      var prev = myViz.sortedBreakpointsList[i - 1];
      var cur = myViz.sortedBreakpointsList[i];
      if (c <= prev) return -1;
      if (cur >= c) return prev;
    }

    // final edge case:
    var lastElt =
      myViz.sortedBreakpointsList[myViz.sortedBreakpointsList.length - 1];
    return lastElt < c ? lastElt : -1;
  }
};

ExecutionVisualizer.prototype.findNextBreakpoint = function () {
  var myViz = this;
  var c = myViz.curInstr;

  if (myViz.sortedBreakpointsList.length == 0) {
    return -1;
  }
  // usability hack: if you're currently on a breakpoint, then
  // single-step forward to the next execution point, NOT the next
  // breakpoint. it's often useful to see what happens when the line
  // at a breakpoint executes.
  else if ($.inArray(c, myViz.sortedBreakpointsList) >= 0) {
    return c + 1;
  } else {
    for (var i = 0; i < myViz.sortedBreakpointsList.length - 1; i++) {
      var cur = myViz.sortedBreakpointsList[i];
      var next = myViz.sortedBreakpointsList[i + 1];
      if (c < cur) return cur;
      if (cur <= c && c < next)
        // subtle
        return next;
    }

    // final edge case:
    var lastElt =
      myViz.sortedBreakpointsList[myViz.sortedBreakpointsList.length - 1];
    return lastElt > c ? lastElt : -1;
  }
};

// Render the correct files in the code output.
ExecutionVisualizer.prototype.renderCorrectFile = function (prevStep) {
  var myViz = this;
  var curStep = myViz.curTrace[myViz.curInstr];
  // console.log(curStep);
  if (curStep && curStep.custom_module_name) {
    this.curInputCode = this.sourceFiles[curStep.custom_module_name];
  } else if (curStep) {
    this.curInputCode = this.sourceFiles.main_code;
  }
  if (prevStep && prevStep > 0) {
    pastStep = myViz.curTrace[prevStep];
  } else if (!prevStep && myViz.curInstr > 0) {
    // If not prevStep passed in - calculate it;
    pastStep = myViz.curTrace[myViz.curInstr - 1];
  }

  // Update Current File Indicator
  var newFileName = "Python 3";
  if (curStep.custom_module_name) {
    newFileName = curStep.custom_module_name + ".py";
  }
  this.domRoot.find("#langDisplayDiv").html(newFileName);

  if (pastStep && pastStep.custom_module_name != curStep.custom_module_name) {
    this.justSwitchedFiles = true;
  } else {
    this.justSwitchedFiles = false;
    // this controls whether we display last line highlighting.
  }

  var lines = this.curInputCode.split("\n");
  this.codeOutputLines = [];
  for (var i = 0; i < lines.length; i++) {
    var cod = lines[i];
    var n = {
      text: cod,
      lineNumber: i || 1,
      executionPoints: [],
      breakpointHere: false,
    };

    $.each(this.curTrace, function (j, elt) {
      if (elt.line === n.lineNumber) {
        n.executionPoints.push(j);
      }
    });

    // if there is a comment containing 'breakpoint' and this line was actually executed,
    // then set a breakpoint on this line
    var breakpointInComment = false;
    var toks = cod.split("#");
    for (var j = 1 /* start at index 1, not 0 */; j < toks.length; j++) {
      if (toks[j].indexOf("breakpoint") != -1) {
        breakpointInComment = true;
      }
    }

    if (breakpointInComment && n.executionPoints.length > 0) {
      n.breakpointHere = true;
      this.addToBreakpoints(n.executionPoints);
    }

    this.codeOutputLines.push(n);
  }

  this.renderPyCodeOutput(true);
  this.updateOutput(true);
};

// returns true if action successfully taken
ExecutionVisualizer.prototype.stepForward = function () {
  var myViz = this;

  if (myViz.editAnnotationMode) {
    return;
  }

  if (myViz.curInstr < myViz.curTrace.length - 1) {
    // if there is a next breakpoint, then jump to it ...
    if (myViz.sortedBreakpointsList.length > 0) {
      var nextBreakpoint = myViz.findNextBreakpoint();
      if (nextBreakpoint != -1) myViz.curInstr = nextBreakpoint;
      else myViz.curInstr += 1; // prevent "getting stuck" on a solitary breakpoint
    } else {
      myViz.curInstr += 1;
    }

    this.renderCorrectFile();
    myViz.updateOutput(true);
    return true;
  }

  return false;
};

// returns true if action successfully taken
ExecutionVisualizer.prototype.stepBack = function () {
  var myViz = this;

  if (myViz.editAnnotationMode) {
    return;
  }

  if (myViz.curInstr > 0) {
    // if there is a prev breakpoint, then jump to it ...
    if (myViz.sortedBreakpointsList.length > 0) {
      var prevBreakpoint = myViz.findPrevBreakpoint();
      if (prevBreakpoint != -1) myViz.curInstr = prevBreakpoint;
      else myViz.curInstr -= 1; // prevent "getting stuck" on a solitary breakpoint
    } else {
      myViz.curInstr -= 1;
    }
    myViz.renderCorrectFile();
    myViz.updateOutput();
    return true;
  }

  return false;
};

ExecutionVisualizer.prototype.renderSliderBreakpoints = function () {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  myViz.domRoot.find("#executionSliderFooter").empty();
  var w = myViz.domRoot.find("#executionSlider").width();

  // I originally didn't want to delete and re-create this overlay every time,
  // but if I don't do so, there are weird flickering artifacts with clearing
  // the SVG container; so it's best to just delete and re-create the container each time
  var sliderOverlay = myViz.domRootD3
    .select("#executionSliderFooter")
    .append("svg")
    .attr("id", "sliderOverlay")
    .attr("width", w)
    .attr("height", 12);

  var xrange = d3.scale
    .linear()
    .domain([0, myViz.curTrace.length - 1])
    .range([0, w]);

  sliderOverlay
    .selectAll("rect")
    .data(myViz.sortedBreakpointsList)
    .enter()
    .append("rect")
    .attr("x", function (d, i) {
      // make edge case of 0 look decent:
      return d === 0 ? 0 : xrange(d) - 1;
    })
    .attr("y", 0)
    .attr("width", 2)
    .attr("height", 12)
    .style("fill", function (d) {
      return breakpointColor;
    });
};

ExecutionVisualizer.prototype.renderPyCodeOutput = function () {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  // initialize!
  this.breakpoints = d3.map();
  this.sortedBreakpointsList = [];

  // an array of objects with the following fields:
  //   'text' - the text of the line of code
  //   'lineNumber' - one-indexed (always the array index + 1)
  //   'executionPoints' - an ordered array of zero-indexed execution points where this line was executed
  //   'breakpointHere' - has a breakpoint been set here?
  this.codeOutputLines = [];

  function _getSortedBreakpointsList() {
    var ret = [];
    myViz.breakpoints.forEach(function (k, v) {
      ret.push(Number(k)); // these should be NUMBERS, not strings
    });
    ret.sort(function (x, y) {
      return x - y;
    }); // WTF, javascript sort is lexicographic by default!
    return ret;
  }

  function addToBreakpoints(executionPoints) {
    $.each(executionPoints, function (i, ep) {
      myViz.breakpoints.set(ep, 1);
    });
    myViz.sortedBreakpointsList = _getSortedBreakpointsList();
  }

  function removeFromBreakpoints(executionPoints) {
    $.each(executionPoints, function (i, ep) {
      myViz.breakpoints.remove(ep);
    });
    myViz.sortedBreakpointsList = _getSortedBreakpointsList();
  }

  function setBreakpoint(t, d) {
    addToBreakpoints(d.executionPoints);
    d3.select(t.parentNode).select("td.lineNo").style("color", breakpointColor);
    d3.select(t.parentNode).select("td.lineNo").style("font-weight", "bold");
    d3.select(t.parentNode).select("td.cod").style("color", breakpointColor);
    myViz.renderSliderBreakpoints();
  }

  function unsetBreakpoint(t, d) {
    removeFromBreakpoints(d.executionPoints);
    d3.select(t.parentNode).select("td.lineNo").style("color", "");
    d3.select(t.parentNode).select("td.lineNo").style("font-weight", "");
    d3.select(t.parentNode).select("td.cod").style("color", "");
    myViz.renderSliderBreakpoints();
  }

  var lines = this.curInputCode.split("\n");

  for (var i = 0; i < lines.length; i++) {
    var cod = lines[i];

    var n = {};
    n.text = cod;
    n.lineNumber = i + 1;
    n.executionPoints = [];
    n.breakpointHere = false;

    $.each(this.curTrace, function (j, elt) {
      if (elt.line == n.lineNumber) {
        n.executionPoints.push(j);
      }
    });

    // if there is a comment containing 'breakpoint' and this line was actually executed,
    // then set a breakpoint on this line
    var breakpointInComment = false;
    var toks = cod.split("#");
    for (var j = 1 /* start at index 1, not 0 */; j < toks.length; j++) {
      if (toks[j].indexOf("breakpoint") != -1) {
        breakpointInComment = true;
      }
    }

    if (breakpointInComment && n.executionPoints.length > 0) {
      n.breakpointHere = true;
      addToBreakpoints(n.executionPoints);
    }

    this.codeOutputLines.push(n);
  }

  myViz.domRoot.find("#pyCodeOutputDiv").empty();

  // maps this.codeOutputLines to both table columns
  var codeOutputD3 = this.domRootD3
    .select("#pyCodeOutputDiv")
    .append("table")
    .attr("id", "pyCodeOutput")
    .selectAll("tr")
    .data(this.codeOutputLines)
    .enter()
    .append("tr")
    .selectAll("td")
    .data(function (d, i) {
      return [d, d] /* map full data item down both columns */;
    })
    .enter()
    .append("td")
    .attr("class", function (d, i) {
      if (i == 0) {
        return "lineNo";
      } else {
        return "cod";
      }
    })
    .attr("id", function (d, i) {
      if (i == 0) {
        return "lineNo" + d.lineNumber;
      } else {
        return myViz.generateID("cod" + d.lineNumber); // make globally unique (within the page)
      }
    })
    .html(function (d, i) {
      if (i == 0) {
        return d.lineNumber;
      } else {
        return htmlspecialchars(d.text);
      }
    });

  // create a left-most gutter td that spans ALL rows ...
  // (NB: valign="top" is CRUCIAL for this to work in IE)
  if (myViz.params.arrowLines) {
    myViz.domRoot
      .find("#pyCodeOutput tr:first")
      .prepend(
        '<td id="gutterTD" valign="top" rowspan="' +
          this.codeOutputLines.length +
          '"><svg id="leftCodeGutterSVG"/></td>'
      );

    // create prevLineArrow and curLineArrow
    myViz.domRootD3
      .select("svg#leftCodeGutterSVG")
      .append("polygon")
      .attr("id", "prevLineArrow")
      .attr("points", SVG_ARROW_POLYGON)
      .attr("fill", lightArrowColor);

    myViz.domRootD3
      .select("svg#leftCodeGutterSVG")
      .append("polygon")
      .attr("id", "curLineArrow")
      .attr("points", SVG_ARROW_POLYGON)
      .attr("fill", darkArrowColor);
  }

  // 2012-09-05: Disable breakpoints for now to simplify UX
  // 2016-05-01: Revive breakpoint functionality
  codeOutputD3
    .style("cursor", function (d, i) {
      // don't do anything if exePts empty (i.e., this line was never executed)
      var exePts = d.executionPoints;
      if (!exePts || exePts.length == 0) {
        return;
      } else {
        return "pointer";
      }
    })
    .on("click", function (d, i) {
      // don't do anything if exePts empty (i.e., this line was never executed)
      var exePts = d.executionPoints;
      if (!exePts || exePts.length == 0) {
        return;
      }

      d.breakpointHere = !d.breakpointHere; // toggle
      if (d.breakpointHere) {
        setBreakpoint(this, d);
      } else {
        unsetBreakpoint(this, d);
      }
    });
};

// takes a string inputStr and returns an HTML version with
// the characters from [highlightIndex, highlightIndex+extent) highlighted with
// a span of class highlightCssClass
function htmlWithHighlight(inputStr, highlightInd, extent, highlightCssClass) {
  var prefix = "";
  if (highlightInd > 0) {
    prefix = inputStr.slice(0, highlightInd);
  }

  var highlightedChars = inputStr.slice(highlightInd, highlightInd + extent);

  var suffix = "";
  if (highlightInd + extent < inputStr.length) {
    suffix = inputStr.slice(highlightInd + extent, inputStr.length);
  }

  // ... then set the current line to lineHTML
  var lineHTML =
    htmlspecialchars(prefix) +
    '<span class="' +
    highlightCssClass +
    '">' +
    htmlspecialchars(highlightedChars) +
    "</span>" +
    htmlspecialchars(suffix);
  return lineHTML;
}

ExecutionVisualizer.prototype.renderStdout = function () {
  var curEntry = this.curTrace[this.curInstr];

  // if there isn't anything in curEntry.stdout, don't even bother
  // displaying the pane (but this may cause jumpiness later)
  if (!this.params.hideOutput && this.numStdoutLines > 0) {
    this.domRoot.find("#progOutputs").show();

    var pyStdout = this.domRoot.find("#pyStdout");

    // keep original horizontal scroll level:
    var oldLeft = pyStdout.scrollLeft();
    pyStdout.val(curEntry.stdout.rtrim() /* trim trailing spaces */);
    pyStdout.scrollLeft(oldLeft);
    pyStdout.scrollTop(pyStdout[0].scrollHeight); // scroll to bottom, though
  } else {
    this.domRoot.find("#progOutputs").hide();
  }
};

// update some fields corresponding to the current and previously
// executed lines in the trace so that they can be highlighted
// copied and pasted from highlightCodeLine, which is hacky :/
ExecutionVisualizer.prototype.updateCurPrevLines = function () {
  var myViz = this;
  var totalInstrs = myViz.curTrace.length;
  var isLastInstr = myViz.curInstr === totalInstrs - 1;

  /* if instrLimitReached, then treat like a normal non-terminating line */
  var isTerminated = !myViz.instrLimitReached && isLastInstr;

  var curLineNumber = null;
  var prevLineNumber = null;

  var curEntry = myViz.curTrace[myViz.curInstr];
  var hasError = false;

  var curIsReturn = curEntry.event == "return";
  var prevIsReturn = false;

  if (myViz.curInstr > 0) {
    prevLineNumber = myViz.curTrace[myViz.curInstr - 1].line;
    prevIsReturn = myViz.curTrace[myViz.curInstr - 1].event == "return";

    if (prevIsReturn) {
      var idx = myViz.curInstr - 1;
      var retStack = myViz.curTrace[idx].stack_to_render;
      assert(retStack.length > 0);
      var retFrameId = retStack[retStack.length - 1].frame_id;

      // now go backwards until we find a 'call' to this frame
      while (idx >= 0) {
        var entry = myViz.curTrace[idx];
        if (entry.event == "call" && entry.stack_to_render) {
          var topFrame =
            entry.stack_to_render[entry.stack_to_render.length - 1];
          if (topFrame.frame_id == retFrameId) {
            break; // DONE, we found the call that corresponds to this return
          }
        }
        idx--;
      }

      // now idx is the index of the 'call' entry. we need to find the
      // entry before that, which is the instruction before the call.
      // THAT's the line of the call site.
      if (idx > 0) {
        var callingEntry = myViz.curTrace[idx - 1];
        prevLineNumber = callingEntry.line; // WOOHOO!!!
        prevIsReturn = false; // this is now a call site, not a return
      }
    }
  }

  if (curEntry.event == "exception" || curEntry.event == "uncaught_exception") {
    assert(curEntry.exception_msg);
    hasError = true;
    myViz.curLineExceptionMsg = curEntry.exception_msg;
  }

  curLineNumber = curEntry.line;

  // edge case for the final instruction :0
  if (isTerminated && !hasError) {
    // don't show redundant arrows on the same line when terminated ...
    if (prevLineNumber == curLineNumber) {
      curLineNumber = null;
    }
  }

  // add these fields to myViz, which is the point of this function!
  myViz.curLineNumber = curLineNumber;
  myViz.prevLineNumber = prevLineNumber;
  myViz.curLineIsReturn = curIsReturn;
  myViz.prevLineIsReturn = prevIsReturn;
};

// This function is called every time the display needs to be updated
// smoothTransition is OPTIONAL!
ExecutionVisualizer.prototype.updateOutput = function (smoothTransition) {
  if (this.params.hideCode) {
    this.updateOutputMini();
  } else {
    this.updateOutputFull(smoothTransition);
  }
  this.renderStdout();
  this.try_hook("end_updateOutput", { myViz: this });
};

ExecutionVisualizer.prototype.updateOutputFull = function (smoothTransition) {
  assert(this.curTrace);
  assert(!this.params.hideCode);

  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  // there's no point in re-rendering if this pane isn't even visible in the first place!
  if (!myViz.domRoot.is(":visible")) {
    return;
  }

  // reset
  myViz.curLineNumber = undefined;
  myViz.prevLineNumber = undefined;
  myViz.curLineIsReturn = undefined;
  myViz.prevLineIsReturn = undefined;
  myViz.curLineExceptionMsg = undefined;

  // really nitpicky!!! gets the difference in width between the code display
  // and the maximum width of its enclosing div
  myViz.codeHorizontalOverflow =
    myViz.domRoot.find("#pyCodeOutput").width() -
    myViz.domRoot.find("#pyCodeOutputDiv").width();
  // should always be positive
  if (myViz.codeHorizontalOverflow < 0) {
    myViz.codeHorizontalOverflow = 0;
  }

  // TODO: don't do this since it screws with other stuff, and we're not
  // activating annotation bubbles right now ...
  // crucial resets for annotations (TODO: kludgy)
  //myViz.destroyAllAnnotationBubbles();
  //myViz.initStepAnnotation();

  var prevDataVizHeight = myViz.domRoot.find("#dataViz").height();

  var gutterSVG = myViz.domRoot.find("svg#leftCodeGutterSVG");

  // one-time initialization of the left gutter
  // (we often can't do this earlier since the entire pane
  //  might be invisible and hence returns a height of zero or NaN
  //  -- the exact format depends on browser)
  if (!myViz.leftGutterSvgInitialized && myViz.params.arrowLines) {
    // set the gutter's height to match that of its parent
    gutterSVG.height(gutterSVG.parent().height());

    var firstRowOffsetY = myViz.domRoot
      .find("table#pyCodeOutput tr:first")
      .offset().top;

    // first take care of edge case when there's only one line ...
    myViz.codeRowHeight = myViz.domRoot
      .find("table#pyCodeOutput td.cod:first")
      .height();

    // ... then handle the (much more common) multi-line case ...
    // this weird contortion is necessary to get the accurate row height on Internet Explorer
    // (simpler methods work on all other major browsers, erghhhhhh!!!)
    if (this.codeOutputLines && this.codeOutputLines.length > 1) {
      var secondRowOffsetY = myViz.domRoot
        .find("table#pyCodeOutput tr:nth-child(2)")
        .offset().top;
      myViz.codeRowHeight = secondRowOffsetY - firstRowOffsetY;
    }

    assert(myViz.codeRowHeight > 0);

    var gutterOffsetY = gutterSVG.offset().top;
    var teenyAdjustment = gutterOffsetY - firstRowOffsetY;

    // super-picky detail to adjust the vertical alignment of arrows so that they line up
    // well with the pointed-to code text ...
    // (if you want to manually adjust tableTop, then ~5 is a reasonable number)
    myViz.arrowOffsetY =
      Math.floor(myViz.codeRowHeight / 2 - SVG_ARROW_HEIGHT / 2) -
      teenyAdjustment;

    myViz.leftGutterSvgInitialized = true;
  }

  if (myViz.params.arrowLines) {
    assert(myViz.arrowOffsetY !== undefined);
    assert(myViz.codeRowHeight !== undefined);
    assert(
      0 <= myViz.arrowOffsetY && myViz.arrowOffsetY <= myViz.codeRowHeight
    );
  }

  // call the callback if necessary (BEFORE rendering)
  if (this.params.updateOutputCallback) {
    this.params.updateOutputCallback(this);
  }

  var curEntry = this.curTrace[this.curInstr];
  var hasError = false;
  // bnm  Render a question
  if (curEntry.question) {
    //alert(curEntry.question.text);

    $("#" + curEntry.question.div).modal({ position: ["25%", "50%"] });
  }

  if (myViz.params.debugMode) {
    console.log("updateOutputFull", curEntry);
    myViz.debugMode = true;
  }

  // render VCR controls:
  var totalInstrs = this.curTrace.length;

  var isLastInstr = this.curInstr == totalInstrs - 1;

  var vcrControls = myViz.domRoot.find("#vcrControls");

  if (isLastInstr) {
    if (this.promptForUserInput || this.promptForMouseInput) {
      vcrControls
        .find("#curInstr")
        .html(
          '<b><font color="' +
            brightRed +
            '">Enter user input below:</font></b>'
        );
    } else if (this.instrLimitReached) {
      vcrControls.find("#curInstr").html("Instruction limit reached");
    } else {
      vcrControls.find("#curInstr").html("Program terminated");
    }
  } else {
    vcrControls
      .find("#curInstr")
      .html(
        "Step " + String(this.curInstr + 1) + " of " + String(totalInstrs - 1)
      );
  }

  vcrControls.find("#jmpFirstInstr").attr("disabled", false);
  vcrControls.find("#jmpStepBack").attr("disabled", false);
  vcrControls.find("#jmpStepFwd").attr("disabled", false);
  vcrControls.find("#jmpLastInstr").attr("disabled", false);

  if (this.curInstr == 0) {
    vcrControls.find("#jmpFirstInstr").attr("disabled", true);
    vcrControls.find("#jmpStepBack").attr("disabled", true);
  }
  if (isLastInstr) {
    vcrControls.find("#jmpLastInstr").attr("disabled", true);
    vcrControls.find("#jmpStepFwd").attr("disabled", true);
  }

  // PROGRAMMATICALLY change the value, so evt.originalEvent should be undefined
  myViz.domRoot.find("#executionSlider").slider("value", this.curInstr);

  // render error (if applicable):
  if (curEntry.event == "exception" || curEntry.event == "uncaught_exception") {
    assert(curEntry.exception_msg);

    if (curEntry.exception_msg == "Unknown error") {
      myViz.domRoot
        .find("#errorOutput")
        .html(
          "Unknown error: Please email a bug report to philip@pgbovine.net"
        );
    } else {
      myViz.domRoot
        .find("#errorOutput")
        .html(htmlspecialchars(curEntry.exception_msg));
    }

    myViz.domRoot.find("#errorOutput").show();

    hasError = true;
    myViz.curLineExceptionMsg = curEntry.exception_msg;
  } else {
    if (!this.instrLimitReached) {
      // ugly, I know :/
      myViz.domRoot.find("#errorOutput").hide();
    }
  }

  function highlightCodeLine() {
    /* if instrLimitReached, then treat like a normal non-terminating line */
    var isTerminated = !myViz.instrLimitReached && isLastInstr;

    var pcod = myViz.domRoot.find("#pyCodeOutputDiv");
    var curLineNumber = null;
    var prevLineNumber = null;

    // only relevant if in myViz.pyCrazyMode
    var prevColumn = undefined;
    var prevExprStartCol = undefined;
    var prevExprWidth = undefined;

    var curIsReturn = curEntry.event == "return";
    var prevIsReturn = false;

    if (myViz.curInstr > 0) {
      prevLineNumber = myViz.curTrace[myViz.curInstr - 1].line;
      prevIsReturn = myViz.curTrace[myViz.curInstr - 1].event == "return";

      if (prevIsReturn) {
        var idx = myViz.curInstr - 1;
        var retStack = myViz.curTrace[idx].stack_to_render;
        assert(retStack.length > 0);
        var retFrameId = retStack[retStack.length - 1].frame_id;

        // now go backwards until we find a 'call' to this frame
        while (idx >= 0) {
          var entry = myViz.curTrace[idx];
          if (entry.event == "call" && entry.stack_to_render) {
            var topFrame =
              entry.stack_to_render[entry.stack_to_render.length - 1];
            if (topFrame.frame_id == retFrameId) {
              break; // DONE, we found the call that corresponds to this return
            }
          }
          idx--;
        }

        // now idx is the index of the 'call' entry. we need to find the
        // entry before that, which is the instruction before the call.
        // THAT's the line of the call site.
        if (idx > 0) {
          var callingEntry = myViz.curTrace[idx - 1];
          prevLineNumber = callingEntry.line; // WOOHOO!!!
          prevIsReturn = false; // this is now a call site, not a return
          smoothTransition = false;
        }
      }

      if (myViz.pyCrazyMode) {
        var p = myViz.curTrace[myViz.curInstr - 1];
        prevColumn = p.column;
        // if these don't exist, set reasonable defaults
        prevExprStartCol =
          p.expr_start_col !== undefined ? p.expr_start_col : p.column;
        prevExprWidth = p.expr_width !== undefined ? p.expr_width : 1;
      }
    }

    curLineNumber = curEntry.line;

    if (myViz.pyCrazyMode) {
      var curColumn = curEntry.column;

      // if these don't exist, set reasonable defaults
      var curExprStartCol =
        curEntry.expr_start_col !== undefined
          ? curEntry.expr_start_col
          : curColumn;
      var curExprWidth =
        curEntry.expr_width !== undefined ? curEntry.expr_width : 1;

      var curLineInfo = myViz.codeOutputLines[curLineNumber - 1];
      assert(curLineInfo.lineNumber == curLineNumber);
      var codeAtLine = curLineInfo.text;

      // shotgun approach: reset ALL lines to their natural (unbolded) state
      $.each(myViz.codeOutputLines, function (i, e) {
        var d = myViz.generateID("cod" + e.lineNumber);
        myViz.domRoot.find("#" + d).html(htmlspecialchars(e.text));
      });

      // Three possible cases:
      // 1. previous and current trace entries are on the SAME LINE
      // 2. previous and current trace entries are on different lines
      // 3. there is no previous trace entry

      if (prevLineNumber == curLineNumber) {
        var curLineHTML = "";

        // tricky tricky!
        // generate a combined line with both previous and current
        // columns highlighted

        for (var i = 0; i < codeAtLine.length; i++) {
          var isCur =
            curExprStartCol <= i && i < curExprStartCol + curExprWidth;
          var isPrev =
            prevExprStartCol <= i && i < prevExprStartCol + prevExprWidth;

          var htmlEscapedChar = htmlspecialchars(codeAtLine[i]);

          if (isCur && isPrev) {
            curLineHTML +=
              '<span class="pycrazy-highlight-prev-and-cur">' +
              htmlEscapedChar +
              "</span>";
          } else if (isPrev) {
            curLineHTML +=
              '<span class="pycrazy-highlight-prev">' +
              htmlEscapedChar +
              "</span>";
          } else if (isCur) {
            curLineHTML +=
              '<span class="pycrazy-highlight-cur">' +
              htmlEscapedChar +
              "</span>";
          } else {
            curLineHTML += htmlEscapedChar;
          }
        }

        assert(curLineHTML);
        myViz.domRoot
          .find("#" + myViz.generateID("cod" + curLineNumber))
          .html(curLineHTML);
      } else {
        if (prevLineNumber) {
          var prevLineInfo = myViz.codeOutputLines[prevLineNumber - 1];
          var prevLineHTML = htmlWithHighlight(
            prevLineInfo.text,
            prevExprStartCol,
            prevExprWidth,
            "pycrazy-highlight-prev"
          );
          myViz.domRoot
            .find("#" + myViz.generateID("cod" + prevLineNumber))
            .html(prevLineHTML);
        }
        var curLineHTML = htmlWithHighlight(
          codeAtLine,
          curExprStartCol,
          curExprWidth,
          "pycrazy-highlight-cur"
        );
        myViz.domRoot
          .find("#" + myViz.generateID("cod" + curLineNumber))
          .html(curLineHTML);
      }
    }

    // on 'return' events, give a bit more of a vertical nudge to show that
    // the arrow is aligned with the 'bottom' of the line ...
    var prevVerticalNudge = prevIsReturn
      ? Math.floor(myViz.codeRowHeight / 3)
      : 0;
    var curVerticalNudge = curIsReturn
      ? Math.floor(myViz.codeRowHeight / 3)
      : 0;

    // edge case for the final instruction :0
    if (isTerminated && !hasError) {
      // don't show redundant arrows on the same line when terminated ...
      if (prevLineNumber == curLineNumber) {
        curLineNumber = null;
      }
      // otherwise have a smaller vertical nudge (to fit at bottom of display table)
      else {
        curVerticalNudge = curVerticalNudge - 2;
      }
    }

    if (myViz.params.arrowLines) {
      // console.log("Checking for arrowLines")
      if (prevLineNumber && !myViz.justSwitchedFiles) {
        var pla = myViz.domRootD3.select("#prevLineArrow");
        var translatePrevCmd =
          "translate(0, " +
          ((prevLineNumber - 1) * myViz.codeRowHeight +
            myViz.arrowOffsetY +
            prevVerticalNudge) +
          ")";

        if (smoothTransition) {
          pla
            .transition()
            .duration(200)
            .attr("fill", "white")
            .each("end", function () {
              pla
                .attr("transform", translatePrevCmd)
                .attr("fill", lightArrowColor);

              gutterSVG.find("#prevLineArrow").show(); // show at the end to avoid flickering
            });
        } else {
          pla.attr("transform", translatePrevCmd);
          gutterSVG.find("#prevLineArrow").show();
        }
      } else {
        gutterSVG.find("#prevLineArrow").hide();
      }

      if (curLineNumber) {
        var cla = myViz.domRootD3.select("#curLineArrow");
        var translateCurCmd =
          "translate(0, " +
          ((curLineNumber - 1) * myViz.codeRowHeight +
            myViz.arrowOffsetY +
            curVerticalNudge) +
          ")";

        if (smoothTransition) {
          cla
            .transition()
            .delay(200)
            .duration(250)
            .attr("transform", translateCurCmd);
        } else {
          cla.attr("transform", translateCurCmd);
        }

        gutterSVG.find("#curLineArrow").show();
      } else {
        gutterSVG.find("#curLineArrow").hide();
      }
    }

    myViz.domRootD3
      .selectAll("#pyCodeOutputDiv td.cod")
      .style("border-top", function (d) {
        if (hasError && d.lineNumber == curEntry.line) {
          return "1px solid " + errorColor;
        } else {
          return "";
        }
      })
      .style("border-bottom", function (d) {
        // COPY AND PASTE ALERT!
        if (hasError && d.lineNumber == curEntry.line) {
          return "1px solid " + errorColor;
        } else {
          return "";
        }
      });

    // returns True iff lineNo is visible in pyCodeOutputDiv
    function isOutputLineVisible(lineNo) {
      var lineNoTd = myViz.domRoot.find("#lineNo" + lineNo);
      var LO = lineNoTd.offset().top;

      var PO = pcod.offset().top;
      var ST = pcod.scrollTop();
      var H = pcod.height();

      // add a few pixels of fudge factor on the bottom end due to bottom scrollbar
      return PO <= LO && LO < PO + H - 30;
    }

    // smoothly scroll pyCodeOutputDiv so that the given line is at the center
    function scrollCodeOutputToLine(lineNo) {
      var lineNoTd = myViz.domRoot.find("#lineNo" + lineNo);
      var LO = lineNoTd.offset().top;

      var PO = pcod.offset().top;
      var ST = pcod.scrollTop();
      var H = pcod.height();

      pcod.stop(); // first stop all previously-queued animations
      pcod.animate({ scrollTop: ST + (LO - PO - Math.round(H / 2)) }, 300);
    }

    if (myViz.params.highlightLines) {
      myViz.domRoot
        .find("#pyCodeOutputDiv td.cod")
        .removeClass("highlight-prev");
      myViz.domRoot
        .find("#pyCodeOutputDiv td.cod")
        .removeClass("highlight-cur");
      if (curLineNumber)
        myViz.domRoot
          .find("#" + myViz.generateID("cod" + curLineNumber))
          .addClass("highlight-cur");

      if (prevLineNumber && !myViz.justSwitchedFiles) {
        myViz.domRoot
          .find("#" + myViz.generateID("cod" + prevLineNumber))
          .addClass("highlight-prev");
      }
    }

    // smoothly scroll code display
    // console.log("Is the code visible?", isOutputLineVisible(curEntry.line))
    if (!isOutputLineVisible(curEntry.line) || myViz.justSwitchedFiles) {
      scrollCodeOutputToLine(curEntry.line);
    }

    // add these fields to myViz
    myViz.curLineNumber = curLineNumber;
    myViz.prevLineNumber = prevLineNumber;
    myViz.curLineIsReturn = curIsReturn;
    myViz.prevLineIsReturn = prevIsReturn;
  } // end of highlightCodeLine

  // render code output:
  if (curEntry.line) {
    highlightCodeLine();
  }

  // inject user-specified HTML/CSS/JS output:
  // YIKES -- HUGE CODE INJECTION VULNERABILITIES :O
  myViz.domRoot.find("#htmlOutputDiv").empty();
  if (curEntry.html_output) {
    if (curEntry.css_output) {
      myViz.domRoot
        .find("#htmlOutputDiv")
        .append('<style type="text/css">' + curEntry.css_output + "</style>");
    }
    myViz.domRoot.find("#htmlOutputDiv").append(curEntry.html_output);

    // inject and run JS *after* injecting HTML and CSS
    if (curEntry.js_output) {
      // NB: when jQuery injects JS, it executes the code immediately
      // and then removes the entire <script> block from the DOM
      myViz.domRoot
        .find("#htmlOutputDiv")
        .append(
          '<script type="text/javascript">' + curEntry.js_output + "</script>"
        );
    }
  }

  // finally, render all of the data structures
  var curEntry = this.curTrace[this.curInstr];
  var curToplevelLayout = this.curTraceLayouts[this.curInstr];
  this.renderDataStructures(curEntry, curToplevelLayout);

  this.enterViewAnnotationsMode(); // ... and render optional annotations (if any exist)

  // call the callback if necessary (BEFORE rendering)
  if (myViz.domRoot.find("#dataViz").height() != prevDataVizHeight) {
    if (this.params.heightChangeCallback) {
      this.params.heightChangeCallback(this);
    }
  }

  // handle raw user input
  var ruiDiv = myViz.domRoot.find("#rawUserInputDiv");
  ruiDiv.hide(); // hide by default

  if (isLastInstr && myViz.executeCodeWithRawInputFunc) {
    if (myViz.promptForUserInput) {
      ruiDiv.show();
    }
  }
}; // end of updateOutputFull

ExecutionVisualizer.prototype.updateOutputMini = function () {
  assert(this.params.hideCode);
  var curEntry = this.curTrace[this.curInstr];
  var curToplevelLayout = this.curTraceLayouts[this.curInstr];
  this.renderDataStructures(curEntry, curToplevelLayout);

  this.enterViewAnnotationsMode(); // ... and render optional annotations (if any exist)
};

ExecutionVisualizer.prototype.renderStep = function (step) {
  assert(0 <= step);
  assert(step < this.curTrace.length);

  // ignore redundant calls
  if (this.curInstr == step) {
    return;
  }
  var pastStep = this.curInstr;
  this.curInstr = step;
  this.renderCorrectFile(pastStep);
  this.updateOutput();
};

ExecutionVisualizer.prototype.isCppMode = function () {
  return this.params.lang === "c" || this.params.lang === "cpp";
};

ExecutionVisualizer.prototype.precomputeCurTraceLayouts = function () {
  this.curTraceLayouts = [];
  this.curTraceLayouts.push([]); // pre-seed with an empty sentinel to simplify the code

  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  assert(this.curTrace.length > 0);
  $.each(this.curTrace, function (i, curEntry) {
    var prevLayout = myViz.curTraceLayouts[myViz.curTraceLayouts.length - 1];

    // make a DEEP COPY of prevLayout to use as the basis for curLine
    var curLayout = $.extend(true /* deep copy */, [], prevLayout);

    // initialize with all IDs from curLayout
    var idsToRemove = d3.map();
    $.each(curLayout, function (i, row) {
      for (var j = 1 /* ignore row ID tag */; j < row.length; j++) {
        idsToRemove.set(row[j], 1);
      }
    });

    var idsAlreadyLaidOut = d3.map(); // to prevent infinite recursion

    function curLayoutIndexOf(id) {
      for (var i = 0; i < curLayout.length; i++) {
        var row = curLayout[i];
        var index = row.indexOf(id);
        if (index > 0) {
          // index of 0 is impossible since it's the row ID tag
          return { row: row, index: index };
        }
      }
      return null;
    }

    function isLinearObj(heapObj) {
      var hook_result = myViz.try_hook("isLinearObj", { heapObj: heapObj });
      if (hook_result[0]) return hook_result[1];

      return (
        heapObj[0] == "LIST" || heapObj[0] == "TUPLE" || heapObj[0] == "SET"
      );
    }

    function recurseIntoObject(id, curRow, newRow) {
      // heuristic for laying out 1-D linked data structures: check for enclosing elements that are
      // structurally identical and then lay them out as siblings in the same "row"
      var heapObj = curEntry.heap[id];

      if (myViz.isCppMode()) {
        // soften this assumption since C-style pointers might not point
        // to the heap; they can point to any piece of data!
        if (!heapObj) {
          return;
        }
      } else {
        assert(heapObj);
      }

      if (isLinearObj(heapObj)) {
        $.each(heapObj, function (ind, child) {
          if (ind < 1) return; // skip type tag

          if (!myViz.isPrimitiveType(child)) {
            var childID = getRefID(child);

            if (myViz.disableHeapNesting) {
              updateCurLayout(childID, [], []);
            } else {
              updateCurLayout(childID, curRow, newRow);
            }
          }
        });
      } else if (heapObj[0] == "DICT") {
        $.each(heapObj, function (ind, child) {
          if (ind < 1) return; // skip type tag

          if (myViz.disableHeapNesting) {
            var dictKey = child[0];
            if (!myViz.isPrimitiveType(dictKey)) {
              var keyChildID = getRefID(dictKey);
              updateCurLayout(keyChildID, [], []);
            }
          }

          var dictVal = child[1];
          if (!myViz.isPrimitiveType(dictVal)) {
            var childID = getRefID(dictVal);
            if (myViz.structurallyEquivalent(heapObj, curEntry.heap[childID])) {
              updateCurLayout(childID, curRow, newRow);
            } else if (myViz.disableHeapNesting) {
              updateCurLayout(childID, [], []);
            }
          }
        });
      } else if (heapObj[0] == "INSTANCE" || heapObj[0] == "CLASS") {
        jQuery.each(heapObj, function (ind, child) {
          var headerLength = heapObj[0] == "INSTANCE" ? 2 : 3;
          if (ind < headerLength) return;

          if (myViz.disableHeapNesting) {
            var instKey = child[0];
            if (!myViz.isPrimitiveType(instKey)) {
              var keyChildID = getRefID(instKey);
              updateCurLayout(keyChildID, [], []);
            }
          }

          var instVal = child[1];
          if (!myViz.isPrimitiveType(instVal)) {
            var childID = getRefID(instVal);
            if (myViz.structurallyEquivalent(heapObj, curEntry.heap[childID])) {
              updateCurLayout(childID, curRow, newRow);
            } else if (myViz.disableHeapNesting) {
              updateCurLayout(childID, [], []);
            }
          }
        });
      } else if (heapObj[0] == "C_ARRAY" || heapObj[0] == "C_STRUCT") {
        updateCurLayoutAndRecurse(heapObj);
      }
    }

    function updateCurLayout(id, curRow, newRow) {
      if (idsAlreadyLaidOut.has(id)) {
        return; // PUNT!
      }

      var curLayoutLoc = curLayoutIndexOf(id);

      var alreadyLaidOut = idsAlreadyLaidOut.has(id);
      idsAlreadyLaidOut.set(id, 1); // unconditionally set now

      // if id is already in curLayout ...
      if (curLayoutLoc) {
        var foundRow = curLayoutLoc.row;
        var foundIndex = curLayoutLoc.index;

        idsToRemove.remove(id); // this id is already accounted for!

        // very subtle ... if id hasn't already been handled in
        // this iteration, then splice newRow into foundRow. otherwise
        // (later) append newRow onto curLayout as a truly new row
        if (!alreadyLaidOut) {
          if (newRow.length > 1) {
            var args = [foundIndex, 0];
            for (var i = 1; i < newRow.length; i++) {
              // ignore row ID tag
              args.push(newRow[i]);
              idsToRemove.remove(newRow[i]);
            }
            foundRow.splice.apply(foundRow, args);

            // remove ALL elements from newRow since they've all been accounted for
            // (but don't reassign it away to an empty list, since the
            // CALLER checks its value. TODO: how to get rid of this gross hack?!?)
            newRow.splice(0, newRow.length);
          }
        }

        // recurse to find more top-level linked entries to append onto foundRow
        recurseIntoObject(id, foundRow, []);
      } else {
        // push id into newRow ...
        if (newRow.length == 0) {
          newRow.push("row" + id); // unique row ID (since IDs are unique)
        }
        newRow.push(id);

        // recurse to find more top-level linked entries ...
        recurseIntoObject(id, curRow, newRow);

        // if newRow hasn't been spliced into an existing row yet during
        // a child recursive call ...
        if (newRow.length > 0) {
          if (curRow && curRow.length > 0) {
            // append onto the END of curRow if it exists
            for (var i = 1; i < newRow.length; i++) {
              // ignore row ID tag
              curRow.push(newRow[i]);
            }
          } else {
            curLayout.push($.extend(true /* make a deep copy */, [], newRow));
          }

          // regardless, newRow is now accounted for, so clear it
          for (var i = 1; i < newRow.length; i++) {
            // ignore row ID tag
            idsToRemove.remove(newRow[i]);
          }
          newRow.splice(0, newRow.length); // kill it!
        }
      }
    }

    function updateCurLayoutAndRecurse(elt) {
      if (!elt) return; // early bail out

      if (isHeapRef(elt, curEntry.heap)) {
        var id = getRefID(elt);
        updateCurLayout(id, null, []);
      }
      recurseIntoCStructArray(elt);
    }

    function recurseIntoCStructArray(val) {
      if (val[0] === "C_ARRAY") {
        $.each(val, function (ind, elt) {
          if (ind < 2) return;
          updateCurLayoutAndRecurse(elt);
        });
      } else if (val[0] === "C_STRUCT") {
        $.each(val, function (ind, kvPair) {
          if (ind < 3) return;
          updateCurLayoutAndRecurse(kvPair[1]);
        });
      }
    }

    // iterate through all globals and ordered stack frames and call updateCurLayout
    $.each(curEntry.ordered_globals, function (i, varname) {
      var val = curEntry.globals[varname];
      if (val !== undefined) {
        // might not be defined at this line, which is OKAY!
        // TODO: try to unify this behavior between C/C++ and other languages:
        if (myViz.isCppMode()) {
          updateCurLayoutAndRecurse(val);
        } else {
          if (!myViz.isPrimitiveType(val)) {
            var id = getRefID(val);
            updateCurLayout(id, null, []);
          }
        }
      }
    });

    $.each(curEntry.stack_to_render, function (i, frame) {
      $.each(frame.ordered_varnames, function (xxx, varname) {
        var val = frame.encoded_locals[varname];
        // TODO: try to unify this behavior between C/C++ and other languages:
        if (myViz.isCppMode()) {
          updateCurLayoutAndRecurse(val);
        } else {
          if (!myViz.isPrimitiveType(val)) {
            var id = getRefID(val);
            updateCurLayout(id, null, []);
          }
        }
      });
    });

    // iterate through remaining elements of idsToRemove and REMOVE them from curLayout
    idsToRemove.forEach(function (id, xxx) {
      id = Number(id); // keys are stored as strings, so convert!!!
      $.each(curLayout, function (rownum, row) {
        var ind = row.indexOf(id);
        if (ind > 0) {
          // remember that index 0 of the row is the row ID tag
          row.splice(ind, 1);
        }
      });
    });

    // now remove empty rows (i.e., those with only a row ID tag) from curLayout
    curLayout = curLayout.filter(function (row) {
      return row.length > 1;
    });

    myViz.curTraceLayouts.push(curLayout);
  });

  this.curTraceLayouts.splice(0, 1); // remove seeded empty sentinel element
  assert(this.curTrace.length == this.curTraceLayouts.length);
};

var heapPtrSrcRE = /__heap_pointer_src_/;

var rightwardNudgeHack = true; // suggested by John DeNero, toggle with global

ExecutionVisualizer.prototype.renderDataStructures = function (
  curEntry,
  curToplevelLayout
) {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  if (myViz.tabularView) {
    return; // return EARLY!!!
  }

  myViz.resetJsPlumbManager(); // very important!!!

  var existingConnectionEndpointIDs = d3.map();
  myViz.jsPlumbInstance.select({ scope: "varValuePointer" }).each(function (c) {
    if (!c.sourceId.match(heapPtrSrcRE)) {
      existingConnectionEndpointIDs.set(c.sourceId, c.targetId);
    }
  });

  var existingParentPointerConnectionEndpointIDs = d3.map();
  myViz.jsPlumbInstance
    .select({ scope: "frameParentPointer" })
    .each(function (c) {
      existingParentPointerConnectionEndpointIDs.set(c.sourceId, c.targetId);
    });

  $.each(curToplevelLayout, function (xxx, row) {
    for (var i = 0; i < row.length; i++) {
      var objID = row[i];
      var heapObjID = myViz.generateHeapObjID(objID, myViz.curInstr);
      myViz.jsPlumbManager.renderedHeapObjectIDs.set(heapObjID, 1);
    }
  });

  myViz.domRoot
    .find("#heap")
    .empty()
    .html('<div id="heapHeader">Objects</div>');

  var heapRows = myViz.domRootD3
    .select("#heap")
    .selectAll("table.heapRow")
    .attr("id", function (d, i) {
      return "heapRow" + i;
    }) // add unique ID
    .data(curToplevelLayout, function (objLst) {
      return objLst[0]; // return first element, which is the row ID tag
    });

  // insert new heap rows
  heapRows
    .enter()
    .append("table")
    //.each(function(objLst, i) {console.log('NEW ROW:', objLst, i);})
    .attr("id", function (d, i) {
      return "heapRow" + i;
    }) // add unique ID
    .attr("class", "heapRow");

  // delete a heap row
  var hrExit = heapRows.exit();
  hrExit
    .each(function (d, idx) {
      //console.log('DEL ROW:', d, idx);
      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  // update an existing heap row
  var toplevelHeapObjects = heapRows
    //.each(function(objLst, i) { console.log('UPDATE ROW:', objLst, i); })
    .selectAll("td.toplevelHeapObject")
    .data(
      function (d, i) {
        return d.slice(1, d.length);
      } /* map over each row, skipping row ID tag */,
      function (objID) {
        return objID;
      } /* each object ID is unique for constancy */
    );

  // insert a new toplevelHeapObject
  var tlhEnter = toplevelHeapObjects
    .enter()
    .append("td")
    .attr("class", "toplevelHeapObject")
    .attr("id", function (d, i) {
      return "toplevel_heap_object_" + d;
    });

  // remember that the enter selection is added to the update
  // selection so that we can process it later ...

  // update a toplevelHeapObject
  toplevelHeapObjects
    .order() // VERY IMPORTANT to put in the order corresponding to data elements
    .each(function (objID, i) {
      //console.log('NEW/UPDATE ELT', objID);

      // TODO: add a smoother transition in the future
      // Right now, just delete the old element and render a new one in its place
      $(this).empty();

      if (myViz.isCppMode()) {
        // TODO: why might this be undefined?!? because the object
        // disappeared from the heap all of a sudden?!?
        if (curEntry.heap[objID] !== undefined) {
          myViz.renderCompoundObject(objID, myViz.curInstr, $(this), true);
        }
      } else {
        myViz.renderCompoundObject(objID, myViz.curInstr, $(this), true);
      }
    });

  // delete a toplevelHeapObject
  var tlhExit = toplevelHeapObjects.exit();
  tlhExit
    .each(function (d, idx) {
      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  // Render globals and then stack frames using d3:

  // TODO: this sometimes seems buggy on Safari, so nix it for now:
  function highlightAliasedConnectors(d, i) {
    // if this row contains a stack pointer, then highlight its arrow and
    // ALL aliases that also point to the same heap object
    var stackPtrId = $(this).find("div.stack_pointer").attr("id");
    if (stackPtrId) {
      var foundTargetId = null;
      myViz.jsPlumbInstance.select({ source: stackPtrId }).each(function (c) {
        foundTargetId = c.targetId;
      });

      // use foundTargetId to highlight ALL ALIASES
      myViz.jsPlumbInstance.select().each(function (c) {
        if (c.targetId == foundTargetId) {
          c.setHover(true);
          $(c.canvas).css("z-index", 2000); // ... and move it to the VERY FRONT
        } else {
          c.setHover(false);
        }
      });
    }
  }

  function unhighlightAllConnectors(d, i) {
    myViz.jsPlumbInstance.select().each(function (c) {
      c.setHover(false);
    });
  }

  var realGlobalsLst = [];
  $.each(curEntry.ordered_globals, function (i, varname) {
    var val = curEntry.globals[varname];

    // (use '!==' to do an EXACT match against undefined)
    if (val !== undefined) {
      // might not be defined at this line, which is OKAY!
      realGlobalsLst.push(varname);
    }
  });

  var globalsID = myViz.generateID("globals");
  var globalTblID = myViz.generateID("global_table");

  var globalVarTable = myViz.domRootD3
    .select("#" + globalTblID)
    .selectAll("tr")
    .data(
      realGlobalsLst,
      function (d) {
        return d;
      } // use variable name as key
    );

  globalVarTable
    .enter()
    .append("tr")
    .attr("class", "variableTr")
    .attr("id", function (d, i) {
      return myViz.generateID(varnameToCssID("global__" + d + "_tr")); // make globally unique (within the page)
    });

  var globalVarTableCells = globalVarTable
    .selectAll("td.stackFrameVar,td.stackFrameValue")
    .data(function (d, i) {
      return [d, d];
    }); /* map varname down both columns */

  globalVarTableCells
    .enter()
    .append("td")
    .attr("class", function (d, i) {
      return i == 0 ? "stackFrameVar" : "stackFrameValue";
    });

  // remember that the enter selection is added to the update
  // selection so that we can process it later ...

  // UPDATE
  globalVarTableCells
    .order() // VERY IMPORTANT to put in the order corresponding to data elements
    .each(function (varname, i) {
      if (i == 0) {
        $(this).html(varname);
      } else {
        // always delete and re-render the global var ...
        // NB: trying to cache and compare the old value using,
        // say -- $(this).attr('data-curvalue', valStringRepr) -- leads to
        // a mysterious and killer memory leak that I can't figure out yet
        $(this).empty();

        // make sure varname doesn't contain any weird
        // characters that are illegal for CSS ID's ...
        var varDivID = myViz.generateID("global__" + varnameToCssID(varname));

        // need to get rid of the old connector in preparation for rendering a new one:
        existingConnectionEndpointIDs.remove(varDivID);

        var val = curEntry.globals[varname];
        if (myViz.isPrimitiveType(val)) {
          myViz.renderPrimitiveObject(val, $(this));
        } else if (val[0] === "C_STRUCT" || val[0] === "C_ARRAY") {
          // C structs and arrays can be inlined in frames
          myViz.renderCStructArray(val, myViz.curInstr, $(this));
        } else {
          var heapObjID = myViz.generateHeapObjID(
            getRefID(val),
            myViz.curInstr
          );

          if (myViz.textualMemoryLabels) {
            var labelID = varDivID + "_text_label";
            $(this).append(
              '<div class="objectIdLabel" id="' +
                labelID +
                '">id' +
                getRefID(val) +
                "</div>"
            );
            $(this)
              .find("div#" + labelID)
              .hover(
                function () {
                  myViz.jsPlumbInstance.connect({
                    source: labelID,
                    target: heapObjID,
                    scope: "varValuePointer",
                  });
                },
                function () {
                  myViz.jsPlumbInstance.select({ source: labelID }).detach();
                }
              );
          } else {
            $(this).append(
              '<div class="stack_pointer" id="' + varDivID + '">&nbsp;</div>'
            );

            assert(!myViz.jsPlumbManager.connectionEndpointIDs.has(varDivID));
            myViz.jsPlumbManager.connectionEndpointIDs.set(varDivID, heapObjID);
            //console.log('STACK->HEAP', varDivID, heapObjID);
          }
        }
      }
    });

  globalVarTableCells
    .exit()
    .each(function (d, idx) {
      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  globalVarTable
    .exit()
    .each(function (d, i) {
      // detach all stack_pointer connectors for divs that are being removed
      $(this)
        .find(".stack_pointer")
        .each(function (i, sp) {
          existingConnectionEndpointIDs.remove($(sp).attr("id"));
        });

      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  // for aesthetics, hide globals if there aren't any globals to display
  if (curEntry.ordered_globals.length == 0) {
    this.domRoot.find("#" + globalsID).hide();
  } else {
    this.domRoot.find("#" + globalsID).show();
  }

  // holy cow, the d3 code for stack rendering is ABSOLUTELY NUTS!

  var stackDiv = myViz.domRootD3.select("#stack");

  // VERY IMPORTANT for selectAll selector to be SUPER specific here!
  var stackFrameDiv = stackDiv
    .selectAll("div.stackFrame,div.zombieStackFrame")
    .data(curEntry.stack_to_render, function (frame) {
      // VERY VERY VERY IMPORTANT for properly handling closures and nested functions
      // (see the backend code for more details)
      return frame.unique_hash;
    });

  var sfdEnter = stackFrameDiv
    .enter()
    .append("div")
    .attr("class", function (d, i) {
      return d.is_zombie ? "zombieStackFrame" : "stackFrame";
    })
    .attr("id", function (d, i) {
      return d.is_zombie
        ? myViz.generateID("zombie_stack" + i)
        : myViz.generateID("stack" + i);
    })
    // HTML5 custom data attributes
    .attr("data-frame_id", function (frame, i) {
      return frame.frame_id;
    })
    .attr("data-parent_frame_id", function (frame, i) {
      return frame.parent_frame_id_list.length > 0
        ? frame.parent_frame_id_list[0]
        : null;
    })
    .each(function (frame, i) {
      if (!myViz.drawParentPointers) {
        return;
      }
      // only run if myViz.drawParentPointers is true ...

      var my_CSS_id = $(this).attr("id");

      if (frame.parent_frame_id_list.length > 0) {
        var parent_frame_id = frame.parent_frame_id_list[0];

        myViz.domRoot
          .find("div#stack [data-frame_id=" + parent_frame_id + "]")
          .each(function (i, e) {
            var parent_CSS_id = $(this).attr("id");
            //console.log('connect', my_CSS_id, parent_CSS_id);
            myViz.jsPlumbManager.parentPointerConnectionEndpointIDs.set(
              my_CSS_id,
              parent_CSS_id
            );
          });
      } else {
        if (curEntry.ordered_globals.length > 0) {
          myViz.jsPlumbManager.parentPointerConnectionEndpointIDs.set(
            my_CSS_id,
            globalsID
          );
        }
      }

      var my_frame_id = frame.frame_id;
      myViz.domRoot
        .find("div#stack [data-parent_frame_id=" + my_frame_id + "]")
        .each(function (i, e) {
          var child_CSS_id = $(this).attr("id");
          //console.log('connect', child_CSS_id, my_CSS_id);
          myViz.jsPlumbManager.parentPointerConnectionEndpointIDs.set(
            child_CSS_id,
            my_CSS_id
          );
        });
    });

  sfdEnter
    .append("div")
    .attr("class", "stackFrameHeader")
    .html(function (frame, i) {
      var funcName = htmlspecialchars(frame.func_name)
        .replace("&lt;lambda&gt;", "\u03bb")
        .replace("\n", "<br/>");

      var headerLabel = funcName;

      if (frame.is_parent || myViz.showAllFrameLabels) {
        headerLabel = "f" + frame.frame_id + ": " + headerLabel;
      }

      // optional (btw, this isn't a CSS id)
      if (frame.parent_frame_id_list.length > 0) {
        var parentFrameID = frame.parent_frame_id_list[0];
        headerLabel = headerLabel + " [parent=f" + parentFrameID + "]";
      } else if (myViz.showAllFrameLabels) {
        headerLabel = headerLabel + " [parent=Global]";
      }

      return headerLabel;
    });

  sfdEnter.append("table").attr("class", "stackFrameVarTable");

  var stackVarTable = stackFrameDiv
    .order() // VERY IMPORTANT to put in the order corresponding to data elements
    .select("table")
    .selectAll("tr")
    .data(
      function (frame) {
        return frame.ordered_varnames.map(function (varname) {
          return { varname: varname, frame: frame };
        });
      },
      function (d) {
        // TODO: why would d ever be null?!? weird
        if (d) {
          return d.varname; // use variable name as key
        }
      }
    );

  stackVarTable
    .enter()
    .append("tr")
    .attr("class", "variableTr")
    .attr("id", function (d, i) {
      return myViz.generateID(
        varnameToCssID(d.frame.unique_hash + "__" + d.varname + "_tr")
      ); // make globally unique (within the page)
    });

  var stackVarTableCells = stackVarTable
    .selectAll("td.stackFrameVar,td.stackFrameValue")
    .data(function (d, i) {
      return [d, d] /* map identical data down both columns */;
    });

  stackVarTableCells
    .enter()
    .append("td")
    .attr("class", function (d, i) {
      return i == 0 ? "stackFrameVar" : "stackFrameValue";
    });

  stackVarTableCells
    .order() // VERY IMPORTANT to put in the order corresponding to data elements
    .each(function (d, i) {
      var varname = d.varname;
      var frame = d.frame;

      if (i == 0) {
        if (varname == "__return__")
          $(this).html('<span class="retval">Return<br/>value</span>');
        else $(this).html(varname);
      } else {
        $(this).empty();

        // make sure varname and frame.unique_hash don't contain any weird
        // characters that are illegal for CSS ID's ...
        var varDivID = myViz.generateID(
          varnameToCssID(frame.unique_hash + "__" + varname)
        );

        // need to get rid of the old connector in preparation for rendering a new one:
        existingConnectionEndpointIDs.remove(varDivID);

        var val = frame.encoded_locals[varname];
        if (myViz.isPrimitiveType(val)) {
          myViz.renderPrimitiveObject(val, $(this));
        } else if (val[0] === "C_STRUCT" || val[0] === "C_ARRAY") {
          // C structs and arrays can be inlined in frames
          myViz.renderCStructArray(val, myViz.curInstr, $(this));
        } else {
          var heapObjID = myViz.generateHeapObjID(
            getRefID(val),
            myViz.curInstr
          );
          if (myViz.textualMemoryLabels) {
            var labelID = varDivID + "_text_label";
            $(this).append(
              '<div class="objectIdLabel" id="' +
                labelID +
                '">id' +
                getRefID(val) +
                "</div>"
            );
            $(this)
              .find("div#" + labelID)
              .hover(
                function () {
                  myViz.jsPlumbInstance.connect({
                    source: labelID,
                    target: heapObjID,
                    scope: "varValuePointer",
                  });
                },
                function () {
                  myViz.jsPlumbInstance.select({ source: labelID }).detach();
                }
              );
          } else {
            // add a stub so that we can connect it with a connector later.
            // IE needs this div to be NON-EMPTY in order to properly
            // render jsPlumb endpoints, so that's why we add an "&nbsp;"!
            $(this).append(
              '<div class="stack_pointer" id="' + varDivID + '">&nbsp;</div>'
            );

            assert(!myViz.jsPlumbManager.connectionEndpointIDs.has(varDivID));
            myViz.jsPlumbManager.connectionEndpointIDs.set(varDivID, heapObjID);
            //console.log('STACK->HEAP', varDivID, heapObjID);
          }
        }
      }
    });

  stackVarTableCells
    .exit()
    .each(function (d, idx) {
      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  stackVarTable
    .exit()
    .each(function (d, i) {
      $(this)
        .find(".stack_pointer")
        .each(function (i, sp) {
          // detach all stack_pointer connectors for divs that are being removed
          existingConnectionEndpointIDs.remove($(sp).attr("id"));
        });

      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  stackFrameDiv
    .exit()
    .each(function (frame, i) {
      $(this)
        .find(".stack_pointer")
        .each(function (i, sp) {
          existingConnectionEndpointIDs.remove($(sp).attr("id"));
        });

      var my_CSS_id = $(this).attr("id");

      existingParentPointerConnectionEndpointIDs.forEach(function (k, v) {
        if (k == my_CSS_id || v == my_CSS_id) {
          existingParentPointerConnectionEndpointIDs.remove(k);
        }
      });

      $(this).empty(); // crucial for garbage collecting jsPlumb connectors!
    })
    .remove();

  if (rightwardNudgeHack) {
    var nudger_to_nudged_rows = {};

    var srcHeapConnectorIDs =
      myViz.jsPlumbManager.heapConnectionEndpointIDs.keys();
    srcHeapConnectorIDs.sort();

    $.each(srcHeapConnectorIDs, function (i, srcID) {
      var dstID = myViz.jsPlumbManager.heapConnectionEndpointIDs.get(srcID);

      var srcAnchorObject = myViz.domRoot.find("#" + srcID);
      var srcHeapObject = srcAnchorObject.closest(".heapObject");
      var dstHeapObject = myViz.domRoot.find("#" + dstID);
      assert(dstHeapObject.attr("class") == "heapObject");

      var srcHeapRow = srcHeapObject.closest(".heapRow");
      var dstHeapRow = dstHeapObject.closest(".heapRow");

      var srcRowID = srcHeapRow.attr("id");
      var dstRowID = dstHeapRow.attr("id");

      // only consider nudging if srcID and dstID are on different rows
      if (srcRowID != dstRowID) {
        var srcAnchorLeft = srcAnchorObject.offset().left;
        var srcHeapObjectLeft = srcHeapObject.offset().left;
        var dstHeapObjectLeft = dstHeapObject.offset().left;

        // if srcAnchorObject is to the RIGHT of dstHeapObject, then nudge
        // dstHeapObject to the right
        if (srcAnchorLeft > dstHeapObjectLeft) {
          // an extra nudge of 32px matches up pretty well with the
          // current CSS padding around .toplevelHeapObject
          var delta = srcAnchorLeft - dstHeapObjectLeft + 32;

          // set margin rather than padding so that arrows tips still end
          // at the left edge of the element.
          // whoa, set relative CSS using +=, nice!
          dstHeapObject.css("margin-left", "+=" + delta);

          //console.log(srcRowID, 'nudged', dstRowID, 'by', delta);

          var cur_nudgee_set = nudger_to_nudged_rows[srcRowID];
          if (cur_nudgee_set === undefined) {
            cur_nudgee_set = d3.map();
            nudger_to_nudged_rows[srcRowID] = cur_nudgee_set;
          }
          cur_nudgee_set.set(dstRowID, 1 /* useless value */);

          // now if dstRowID itself nudged some other nodes, then nudge
          // all of its nudgees by delta as well
          var dst_nudgee_set = nudger_to_nudged_rows[dstRowID];
          if (dst_nudgee_set) {
            dst_nudgee_set.forEach(function (k, v) {
              // don't nudge if it's yourself, to make cycles look
              // somewhat reasonable (although still not ideal). e.g.,:
              //   x = [1,2]
              //   y = [3,x]
              //   x[1] = y
              if (k != srcRowID) {
                // nudge this entire ROW by delta as well
                myViz.domRoot.find("#" + k).css("margin-left", "+=" + delta);

                // then transitively add to entry for srcRowID
                cur_nudgee_set.set(k, 1 /* useless value */);
              }
            });
          }
        }
      }
    });
  }

  myViz.jsPlumbInstance.reset();

  // use jsPlumb scopes to keep the different kinds of pointers separated
  function renderVarValueConnector(varID, valueID) {
    // special-case handling for C/C++ pointers, just to keep from rocking
    // the boat on my existing (battle-tested) code
    if (myViz.isCppMode()) {
      if (myViz.domRoot.find("#" + valueID).length) {
        myViz.jsPlumbInstance.connect({
          source: varID,
          target: valueID,
          scope: "varValuePointer",
        });
      } else {
        // pointer isn't pointing to anything valid; put a poo emoji here
        myViz.domRoot
          .find("#" + varID)
          .html("\uD83D\uDCA9" /* pile of poo emoji */);
      }
    } else {
      myViz.jsPlumbInstance.connect({
        source: varID,
        target: valueID,
        scope: "varValuePointer",
      });
    }
  }

  var totalParentPointersRendered = 0;

  function renderParentPointerConnector(srcID, dstID) {
    // SUPER-DUPER-ugly hack since I can't figure out a cleaner solution for now:
    // if either srcID or dstID no longer exists, then SKIP rendering ...
    if (
      myViz.domRoot.find("#" + srcID).length == 0 ||
      myViz.domRoot.find("#" + dstID).length == 0
    ) {
      return;
    }

    //console.log('renderParentPointerConnector:', srcID, dstID);

    myViz.jsPlumbInstance.connect({
      source: srcID,
      target: dstID,
      anchors: ["LeftMiddle", "LeftMiddle"],

      // 'horizontally offset' the parent pointers up so that they don't look as ugly ...
      //connector: ["Flowchart", { stub: 9 + (6 * (totalParentPointersRendered + 1)) }],

      // actually let's try a bezier curve ...
      connector: ["Bezier", { curviness: 45 }],

      endpoint: ["Dot", { radius: 4 }],
      //hoverPaintStyle: {lineWidth: 1, strokeStyle: connectorInactiveColor}, // no hover colors
      scope: "frameParentPointer",
    });
    totalParentPointersRendered++;
  }

  if (!myViz.textualMemoryLabels) {
    if (!myViz.isCppMode()) {
      existingConnectionEndpointIDs.forEach(renderVarValueConnector);
    }
    // add all the NEW connectors that have arisen in this call to renderDataStructures
    myViz.jsPlumbManager.connectionEndpointIDs.forEach(renderVarValueConnector);
  }
  // do the same for environment parent pointers
  if (myViz.drawParentPointers) {
    existingParentPointerConnectionEndpointIDs.forEach(
      renderParentPointerConnector
    );
    myViz.jsPlumbManager.parentPointerConnectionEndpointIDs.forEach(
      renderParentPointerConnector
    );
  }

  function highlight_frame(frameID) {
    myViz.jsPlumbInstance.select().each(function (c) {
      // find the enclosing .stackFrame ...
      var stackFrameDiv = c.source.closest(".stackFrame");

      // if this connector starts in the selected stack frame ...
      if (stackFrameDiv.attr("id") == frameID) {
        // then HIGHLIGHT IT!
        c.setPaintStyle({ lineWidth: 1, strokeStyle: connectorBaseColor });
        c.endpoints[0].setPaintStyle({ fillStyle: connectorBaseColor });
        //c.endpoints[1].setVisible(false, true, true); // JUST set right endpoint to be invisible

        $(c.canvas).css("z-index", 1000); // ... and move it to the VERY FRONT
      }
      // for heap->heap connectors
      else if (
        myViz.jsPlumbManager.heapConnectionEndpointIDs.has(
          c.endpoints[0].elementId
        )
      ) {
        // NOP since it's already the color and style we set by default
      }
      // TODO: maybe this needs special consideration for C/C++ code? dunno
      else if (stackFrameDiv.length > 0) {
        // else unhighlight it
        // (only if c.source actually belongs to a stackFrameDiv (i.e.,
        //  it originated from the stack). for instance, in C there are
        //  heap pointers, but we doen't use heapConnectionEndpointIDs)
        c.setPaintStyle({ lineWidth: 1, strokeStyle: connectorInactiveColor });
        c.endpoints[0].setPaintStyle({ fillStyle: connectorInactiveColor });
        //c.endpoints[1].setVisible(false, true, true); // JUST set right endpoint to be invisible

        $(c.canvas).css("z-index", 0);
      }
    });

    // clear everything, then just activate this one ...
    myViz.domRoot.find(".stackFrame").removeClass("highlightedStackFrame");
    myViz.domRoot.find("#" + frameID).addClass("highlightedStackFrame");
  }

  // highlight the top-most non-zombie stack frame or, if not available, globals
  var frame_already_highlighted = false;
  $.each(curEntry.stack_to_render, function (i, e) {
    if (e.is_highlighted) {
      highlight_frame(myViz.generateID("stack" + i));
      frame_already_highlighted = true;
    }
  });

  if (!frame_already_highlighted) {
    highlight_frame(myViz.generateID("globals"));
  }

  myViz.try_hook("end_renderDataStructures", { myViz: myViz });
};

// render a tabular visualization where each row is an execution step,
// and each column is a variable
ExecutionVisualizer.prototype.renderTabularView = function () {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  myViz.resetJsPlumbManager(); // very important!!!

  var allGlobalVars = [];
  // Key: function name
  // Value: list of ordered variables for function
  var funcNameToOrderedVars = {};
  var orderedFuncNames = []; // function names in order of appearance

  // iterate through the entire trace and find all global variables, and
  // all local variables in all functions, in order of appearance in the trace
  $.each(myViz.curTrace, function (i, elt) {
    $.each(elt.ordered_globals, function (i, g) {
      // don't add duplicates into this list,
      // but need to use a list to maintain ORDERING
      if ($.inArray(g, allGlobalVars) === -1) {
        allGlobalVars.push(g);
      }
    });

    $.each(elt.stack_to_render, function (i, sf) {
      var funcVarsList = funcNameToOrderedVars[sf.func_name];
      if (funcVarsList === undefined) {
        funcVarsList = [];
        funcNameToOrderedVars[sf.func_name] = funcVarsList;
        orderedFuncNames.push(sf.func_name);
      }
      $.each(sf.ordered_varnames, function (i, v) {
        // don't add duplicates into this list,
        // but need to use a list to maintain ORDERING
        // (ignore the special __return__ value)
        if ($.inArray(v, funcVarsList) === -1 && v !== "__return__") {
          funcVarsList.push(v);
        }
      });
    });
  });

  var allVarNames = ["Step"];

  $.each(allGlobalVars, function (i, e) {
    allVarNames.push(e);
  });
  $.each(orderedFuncNames, function (i, funcName) {
    $.each(funcNameToOrderedVars[funcName], function (i, v) {
      allVarNames.push(funcName + ":" + v);
    });
  });

  // get the values of all objects in trace entry e
  function getAllOrderedValues(curEntry) {
    // HACK! start with a blank sentinel since the first column of the
    // table is the step number
    var allVarValues = [""];

    $.each(allGlobalVars, function (i, e) {
      allVarValues.push(curEntry.globals[e]);
    });

    // for local variables, grab only the values in the highlighted
    // frame (if any)
    var highlightedFrame = null;
    $.each(curEntry.stack_to_render, function (i, sf) {
      if (sf.is_highlighted) {
        highlightedFrame = sf;
      }
    });

    $.each(orderedFuncNames, function (i, funcName) {
      $.each(funcNameToOrderedVars[funcName], function (i, v) {
        var found = false;
        if (highlightedFrame && funcName == highlightedFrame.func_name) {
          var obj = highlightedFrame.encoded_locals[v];
          if (obj) {
            allVarValues.push(obj);
            found = true;
          }
        }

        // push an empty value if not found since we want everything to
        // align with allVarNames
        if (!found) {
          allVarValues.push(undefined);
        }
      });
    });

    // TODO: also walk up parent pointers to also display variable
    // values in enclosing frames

    return allVarValues;
  }

  var tblRoot = myViz.domRootD3.select("#optTabularView");
  var tbl = tblRoot.append("table");
  tbl.attr("id", "optTable");

  var tHead = tbl.append("thead").attr("class", "stepTableThead").append("tr");
  tHead.attr("class", "stepTableTr");
  tHead
    .selectAll("thead td")
    .data(allVarNames)
    .enter()
    .append("td")
    .attr("class", "stepTableTd")
    .html(function (d) {
      return d;
    });

  var tBody = tbl.append("tbody");
  tBody.attr("class", "stepTableTbody");

  var stepsAndTraceEntries = [];
  $.each(myViz.curTrace, function (i, e) {
    stepsAndTraceEntries.push([i, e]);
  });

  var tr = tBody
    .selectAll("tbody tr")
    .data(stepsAndTraceEntries)
    .enter()
    .append("tr")
    .attr("step", function (d, i) {
      return i;
    })
    .attr("class", "stepTableTr");

  var td = tr
    .selectAll("td")
    .data(function (e) {
      return getAllOrderedValues(e[1]);
    })
    .enter()
    .append("td")
    .attr("class", "stepTableTd")
    .each(function (obj, i) {
      $(this).empty();

      // TODO: fixme -- this is super kludgy; must be a better way
      var step = parseInt($(this).closest("tr").attr("step"));

      if (i == 0) {
        $(this).html(step + 1); // one-indexed for readability
      } else {
        if (obj === undefined) {
          // keep this table cell EMPTY
        } else {
          myViz.renderNestedObject(obj, step, $(this), "tabular");
        }
      }
    });

  myViz.jsPlumbInstance.reset();

  function renderTableVarValueConnector(varID, valueID) {
    myViz.jsPlumbInstance.connect({
      source: varID,
      target: valueID,
      scope: "varValuePointer",
      // make it look more aesthetically pleasing:
      anchors: ["TopCenter", "LeftMiddle"],
    });
  }

  myViz.jsPlumbManager.connectionEndpointIDs.forEach(
    renderTableVarValueConnector
  );
};

// rendering functions, which all take a d3 dom element to anchor the
// new element to render
ExecutionVisualizer.prototype.renderPrimitiveObject = function (
  obj,
  d3DomElement
) {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  if (
    this.try_hook("renderPrimitiveObject", {
      obj: obj,
      d3DomElement: d3DomElement,
    })[0]
  )
    return;

  var typ = typeof obj;

  if (obj == null) {
    d3DomElement.append(
      '<span class="nullObj">' + this.getRealLabel("None") + "</span>"
    );
  } else if (typ == "number") {
    d3DomElement.append('<span class="numberObj">' + obj + "</span>");
  } else if (typ == "boolean") {
    if (obj) {
      d3DomElement.append(
        '<span class="boolObj">' + this.getRealLabel("True") + "</span>"
      );
    } else {
      d3DomElement.append(
        '<span class="boolObj">' + this.getRealLabel("False") + "</span>"
      );
    }
  } else if (typ == "string") {
    // escape using htmlspecialchars to prevent HTML/script injection
    var literalStr = htmlspecialchars(obj);

    // print as a double-quoted string literal
    // with explicit newlines as <br/>
    literalStr = literalStr.replace(new RegExp("\n", "g"), "<br/>"); // replace ALL
    literalStr = literalStr.replace(new RegExp('"', "g"), '\\"'); // replace ALL
    literalStr = '"' + literalStr + '"';

    d3DomElement.append('<span class="stringObj">' + literalStr + "</span>");
  } else if (typ == "object") {
    if (obj[0] == "C_DATA") {
      var typeName = obj[2];
      // special cases for brevity:
      if (typeName === "short int") {
        typeName = "short";
      } else if (typeName === "short unsigned int") {
        typeName = "unsigned short";
      } else if (typeName === "long int") {
        typeName = "long";
      } else if (typeName === "long long int") {
        typeName = "long long";
      } else if (typeName === "long unsigned int") {
        typeName = "unsigned long";
      } else if (typeName === "long long unsigned int") {
        typeName = "unsigned long long int";
      }

      var isValidPtr =
        typeName === "pointer" &&
        obj[3] !== "<UNINITIALIZED>" &&
        obj[3] !== "<UNALLOCATED>";

      var addr = obj[1];
      var leader = "";
      if (myViz.debugMode) {
        leader = addr + "<br/>"; // prepend address
      }

      // prefix with 'cdata_' so that we can distinguish this from a
      // top-level heap ID generated by generateHeapObjID
      var cdataId = myViz.generateHeapObjID("cdata_" + addr, myViz.curInstr);

      if (isValidPtr) {
        // for pointers, put cdataId in the header
        d3DomElement.append(
          '<div id="' +
            cdataId +
            '" class="cdataHeader">' +
            leader +
            typeName +
            "</div>"
        );

        var ptrVal = obj[3];

        // add a stub so that we can connect it with a connector later.
        // IE needs this div to be NON-EMPTY in order to properly
        // render jsPlumb endpoints, so that's why we add an "&nbsp;"!
        var ptrSrcId = myViz.generateHeapObjID(
          "ptrSrc_" + addr,
          myViz.curInstr
        );
        var ptrTargetId = myViz.generateHeapObjID(
          "cdata_" + ptrVal,
          myViz.curInstr
        ); // don't forget cdata_ prefix!

        var debugInfo = "";
        if (myViz.debugMode) {
          debugInfo = ptrTargetId;
        }

        // make it really narrow so that the div doesn't STRETCH too wide
        d3DomElement.append(
          '<div style="width: 10px;" id="' +
            ptrSrcId +
            '" class="cdataElt">&nbsp;' +
            debugInfo +
            "</div>"
        );

        assert(!myViz.jsPlumbManager.connectionEndpointIDs.has(ptrSrcId));
        myViz.jsPlumbManager.connectionEndpointIDs.set(ptrSrcId, ptrTargetId);
        //console.log(ptrSrcId, '->', ptrTargetId);
      } else {
        // for non-pointers, put cdataId on the element itself, so that
        // pointers can point directly at the element, not the header
        d3DomElement.append(
          '<div class="cdataHeader">' + leader + typeName + "</div>"
        );

        var rep = "";
        if (typeof obj[3] === "string") {
          var literalStr = obj[3];
          if (literalStr === "<UNINITIALIZED>") {
            rep = '<span class="cdataUninit">?</span>';
            //rep = '\uD83D\uDCA9'; // pile of poo emoji
          } else if (literalStr == "<UNALLOCATED>") {
            rep = "\uD83D\uDC80"; // skull emoji
          } else {
            // a regular string
            literalStr = literalStr.replace(new RegExp("\n", "g"), "\\n"); // replace ALL
            literalStr = literalStr.replace(new RegExp("\t", "g"), "\\t"); // replace ALL
            literalStr = literalStr.replace(new RegExp('"', "g"), '\\"'); // replace ALL

            // print as a SINGLE-quoted string literal (to emulate C-style chars)
            literalStr = "'" + literalStr + "'";
            rep = htmlspecialchars(literalStr);
          }
        } else {
          rep = htmlspecialchars(obj[3]);
        }

        d3DomElement.append(
          '<div id="' + cdataId + '" class="cdataElt">' + rep + "</div>"
        );
      }
    } else {
      assert(obj[0] == "SPECIAL_FLOAT" || obj[0] == "JS_SPECIAL_VAL");
      d3DomElement.append('<span class="numberObj">' + obj[1] + "</span>");
    }
  } else {
    assert(false);
  }
};

ExecutionVisualizer.prototype.renderNestedObject = function (
  obj,
  stepNum,
  d3DomElement
) {
  if (this.isPrimitiveType(obj)) {
    this.renderPrimitiveObject(obj, d3DomElement);
  } else {
    if (obj[0] === "REF") {
      // obj is a ["REF", <int>] so dereference the 'pointer' to render that object
      this.renderCompoundObject(getRefID(obj), stepNum, d3DomElement, false);
    } else {
      assert(obj[0] === "C_STRUCT" || obj[0] === "C_ARRAY");
      this.renderCStructArray(obj, stepNum, d3DomElement);
    }
  }
};

ExecutionVisualizer.prototype.renderCompoundObject = function (
  objID,
  stepNum,
  d3DomElement,
  isTopLevel
) {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  var heapObjID = myViz.generateHeapObjID(objID, stepNum);

  if (
    !isTopLevel &&
    myViz.jsPlumbManager.renderedHeapObjectIDs.has(heapObjID)
  ) {
    var srcDivID = myViz.generateID(
      "heap_pointer_src_" + myViz.jsPlumbManager.heap_pointer_src_id
    );
    myViz.jsPlumbManager.heap_pointer_src_id++; // just make sure each source has a UNIQUE ID

    var dstDivID = heapObjID;

    if (myViz.textualMemoryLabels) {
      var labelID = srcDivID + "_text_label";
      d3DomElement.append(
        '<div class="objectIdLabel" id="' + labelID + '">id' + objID + "</div>"
      );

      myViz.domRoot.find("div#" + labelID).hover(
        function () {
          myViz.jsPlumbInstance.connect({
            source: labelID,
            target: dstDivID,
            scope: "varValuePointer",
          });
        },
        function () {
          myViz.jsPlumbInstance.select({ source: labelID }).detach();
        }
      );
    } else {
      // render jsPlumb arrow source since this heap object has already been rendered
      // (or will be rendered soon)

      // add a stub so that we can connect it with a connector later.
      // IE needs this div to be NON-EMPTY in order to properly
      // render jsPlumb endpoints, so that's why we add an "&nbsp;"!
      d3DomElement.append('<div id="' + srcDivID + '">&nbsp;</div>');

      assert(!myViz.jsPlumbManager.connectionEndpointIDs.has(srcDivID));
      myViz.jsPlumbManager.connectionEndpointIDs.set(srcDivID, dstDivID);
      //console.log('HEAP->HEAP', srcDivID, dstDivID);

      assert(!myViz.jsPlumbManager.heapConnectionEndpointIDs.has(srcDivID));
      myViz.jsPlumbManager.heapConnectionEndpointIDs.set(srcDivID, dstDivID);
    }

    return; // early return!
  }

  // wrap ALL compound objects in a heapObject div so that jsPlumb
  // connectors can point to it:
  d3DomElement.append('<div class="heapObject" id="' + heapObjID + '"></div>');
  d3DomElement = myViz.domRoot.find("#" + heapObjID); // TODO: maybe inefficient

  myViz.jsPlumbManager.renderedHeapObjectIDs.set(heapObjID, 1);

  var curHeap = myViz.curTrace[stepNum].heap;
  var obj = curHeap[objID];
  assert($.isArray(obj));

  // prepend the type label with a memory address label
  var typeLabelPrefix = "";
  if (myViz.textualMemoryLabels) {
    typeLabelPrefix = "id" + objID + ":";
  }

  var hook_result = myViz.try_hook("renderCompoundObject", {
    objID: objID,
    d3DomElement: d3DomElement,
    isTopLevel: isTopLevel,
    obj: obj,
    typeLabelPrefix: typeLabelPrefix,
    stepNum: stepNum,
    myViz: myViz,
  });
  if (hook_result[0]) return;

  if (
    obj[0] == "LIST" ||
    obj[0] == "TUPLE" ||
    obj[0] == "SET" ||
    obj[0] == "DICT"
  ) {
    var label = obj[0].toLowerCase();

    assert(obj.length >= 1);
    if (obj.length == 1) {
      d3DomElement.append(
        '<div class="typeLabel">' +
          typeLabelPrefix +
          " empty " +
          myViz.getRealLabel(label) +
          "</div>"
      );
    } else {
      d3DomElement.append(
        '<div class="typeLabel">' +
          typeLabelPrefix +
          myViz.getRealLabel(label) +
          "</div>"
      );
      d3DomElement.append('<table class="' + label + 'Tbl"></table>');
      var tbl = d3DomElement.children("table");

      if (obj[0] == "LIST" || obj[0] == "TUPLE") {
        tbl.append("<tr></tr><tr></tr>");
        var headerTr = tbl.find("tr:first");
        var contentTr = tbl.find("tr:last");
        $.each(obj, function (ind, val) {
          if (ind < 1) return; // skip type tag and ID entry

          // add a new column and then pass in that newly-added column
          // as d3DomElement to the recursive call to child:
          headerTr.append('<td class="' + label + 'Header"></td>');
          headerTr.find("td:last").append(ind - 1);

          contentTr.append('<td class="' + label + 'Elt"></td>');
          myViz.renderNestedObject(val, stepNum, contentTr.find("td:last"));
        });
      } else if (obj[0] == "SET") {
        // create an R x C matrix:
        var numElts = obj.length - 1;

        // gives roughly a 3x5 rectangular ratio, square is too, err,
        // 'square' and boring
        var numRows = Math.round(Math.sqrt(numElts));
        if (numRows > 3) {
          numRows -= 1;
        }

        var numCols = Math.round(numElts / numRows);
        // round up if not a perfect multiple:
        if (numElts % numRows) {
          numCols += 1;
        }

        jQuery.each(obj, function (ind, val) {
          if (ind < 1) return; // skip 'SET' tag

          if ((ind - 1) % numCols == 0) {
            tbl.append("<tr></tr>");
          }

          var curTr = tbl.find("tr:last");
          curTr.append('<td class="setElt"></td>');
          myViz.renderNestedObject(val, stepNum, curTr.find("td:last"));
        });
      } else if (obj[0] == "DICT") {
        $.each(obj, function (ind, kvPair) {
          if (ind < 1) return; // skip 'DICT' tag

          tbl.append(
            '<tr class="dictEntry"><td class="dictKey"></td><td class="dictVal"></td></tr>'
          );
          var newRow = tbl.find("tr:last");
          var keyTd = newRow.find("td:first");
          var valTd = newRow.find("td:last");

          var key = kvPair[0];
          var val = kvPair[1];

          myViz.renderNestedObject(key, stepNum, keyTd);
          myViz.renderNestedObject(val, stepNum, valTd);
        });
      }
    }
  } else if (obj[0] == "INSTANCE" || obj[0] == "CLASS") {
    var isInstance = obj[0] == "INSTANCE";
    var headerLength = isInstance ? 2 : 3;

    assert(obj.length >= headerLength);

    if (isInstance) {
      d3DomElement.append(
        '<div class="typeLabel">' +
          typeLabelPrefix +
          obj[1] +
          " " +
          myViz.getRealLabel("instance") +
          "</div>"
      );
    } else {
      var superclassStr = "";
      if (obj[2].length > 0) {
        superclassStr += "[extends " + obj[2].join(", ") + "] ";
      }
      d3DomElement.append(
        '<div class="typeLabel">' +
          typeLabelPrefix +
          obj[1] +
          " class " +
          superclassStr +
          "<br/>" +
          '<a href="#" id="attrToggleLink">hide attributes</a>' +
          "</div>"
      );
    }

    // right now, let's NOT display class members, since that clutters
    // up the display too much. in the future, consider displaying
    // class members in a pop-up pane on mouseover or mouseclick
    // actually nix what i just said above ...
    //if (!isInstance) return;

    if (obj.length > headerLength) {
      var lab = isInstance ? "inst" : "class";
      d3DomElement.append('<table class="' + lab + 'Tbl"></table>');

      var tbl = d3DomElement.children("table");

      $.each(obj, function (ind, kvPair) {
        if (ind < headerLength) return; // skip header tags

        tbl.append(
          '<tr class="' +
            lab +
            'Entry"><td class="' +
            lab +
            'Key"></td><td class="' +
            lab +
            'Val"></td></tr>'
        );

        var newRow = tbl.find("tr:last");
        var keyTd = newRow.find("td:first");
        var valTd = newRow.find("td:last");

        // the keys should always be strings, so render them directly (and without quotes):
        // (actually this isn't the case when strings are rendered on the heap)
        if (typeof kvPair[0] == "string") {
          // common case ...
          var attrnameStr = htmlspecialchars(kvPair[0]);
          keyTd.append('<span class="keyObj">' + attrnameStr + "</span>");
        } else {
          // when strings are rendered as heap objects ...
          myViz.renderNestedObject(kvPair[0], stepNum, keyTd);
        }

        // values can be arbitrary objects, so recurse:
        myViz.renderNestedObject(kvPair[1], stepNum, valTd);
      });
    }

    if (!isInstance) {
      $(d3DomElement.selector + " .typeLabel #attrToggleLink").click(
        function () {
          var elt = $(d3DomElement.selector + " .classTbl");
          elt.toggle();
          $(this).html((elt.is(":visible") ? "hide" : "show") + " attributes");

          if (elt.is(":visible")) {
            myViz.classAttrsHidden[d3DomElement.selector] = false;
            $(this).html("hide attributes");
          } else {
            myViz.classAttrsHidden[d3DomElement.selector] = true;
            $(this).html("show attributes");
          }

          myViz.redrawConnectors(); // redraw all arrows!

          return false; // so that the <a href="#"> doesn't reload the page
        }
      );

      // "remember" whether this was hidden earlier during this
      // visualization session
      if (myViz.classAttrsHidden[d3DomElement.selector]) {
        $(d3DomElement.selector + " .classTbl").hide();
        $(d3DomElement.selector + " .typeLabel #attrToggleLink").html(
          "show attributes"
        );
      }
    }
  } else if (obj[0] == "INSTANCE_PPRINT") {
    d3DomElement.append(
      '<div class="typeLabel">' + typeLabelPrefix + obj[1] + " instance</div>"
    );

    strRepr = htmlspecialchars(obj[2]); // escape strings!
    d3DomElement.append(
      '<table class="customObjTbl"><tr><td class="customObjElt">' +
        strRepr +
        "</td></tr></table>"
    );
  } else if (obj[0] == "FUNCTION") {
    assert(obj.length == 3);

    // pretty-print lambdas and display other weird characters:
    var funcName = htmlspecialchars(obj[1]).replace("&lt;lambda&gt;", "\u03bb");
    var parentFrameID = obj[2]; // optional

    if (!myViz.compactFuncLabels) {
      d3DomElement.append(
        '<div class="typeLabel">' +
          typeLabelPrefix +
          myViz.getRealLabel("function") +
          "</div>"
      );
    }

    var funcPrefix = myViz.compactFuncLabels ? "func" : "";

    if (parentFrameID) {
      d3DomElement.append(
        '<div class="funcObj">' +
          funcPrefix +
          " " +
          funcName +
          " [parent=f" +
          parentFrameID +
          "]</div>"
      );
    } else if (myViz.showAllFrameLabels) {
      d3DomElement.append(
        '<div class="funcObj">' +
          funcPrefix +
          " " +
          funcName +
          " [parent=Global]</div>"
      );
    } else {
      d3DomElement.append(
        '<div class="funcObj">' + funcPrefix + " " + funcName + "</div>"
      );
    }
  } else if (obj[0] == "JS_FUNCTION") {
    /* TODO: refactor me */
    // JavaScript function
    assert(obj.length == 5);
    var funcName = htmlspecialchars(obj[1]);
    var funcCode = typeLabelPrefix + htmlspecialchars(obj[2]);
    var funcProperties = obj[3]; // either null or a non-empty list of key-value pairs
    var parentFrameID = obj[4];

    if (funcProperties || parentFrameID || myViz.showAllFrameLabels) {
      d3DomElement.append('<table class="classTbl"></table>');
      var tbl = d3DomElement.children("table");
      tbl.append(
        '<tr><td class="funcCod" colspan="2"><pre class="funcCode">' +
          funcCode +
          "</pre>" +
          "</td></tr>"
      );

      if (funcProperties) {
        assert(funcProperties.length > 0);
        $.each(funcProperties, function (ind, kvPair) {
          tbl.append(
            '<tr class="classEntry"><td class="classKey"></td><td class="classVal"></td></tr>'
          );
          var newRow = tbl.find("tr:last");
          var keyTd = newRow.find("td:first");
          var valTd = newRow.find("td:last");
          keyTd.append(
            '<span class="keyObj">' + htmlspecialchars(kvPair[0]) + "</span>"
          );
          myViz.renderNestedObject(kvPair[1], stepNum, valTd);
        });
      }

      if (parentFrameID) {
        tbl.append(
          '<tr class="classEntry"><td class="classKey">parent</td><td class="classVal">' +
            "f" +
            parentFrameID +
            "</td></tr>"
        );
      } else if (myViz.showAllFrameLabels) {
        tbl.append(
          '<tr class="classEntry"><td class="classKey">parent</td><td class="classVal">' +
            "global" +
            "</td></tr>"
        );
      }
    } else {
      // compact form:
      d3DomElement.append('<pre class="funcCode">' + funcCode + "</pre>");
    }
  } else if (obj[0] == "HEAP_PRIMITIVE") {
    assert(obj.length == 3);

    var typeName = obj[1];
    var primitiveVal = obj[2];

    // add a bit of padding to heap primitives, for aesthetics
    d3DomElement.append('<div class="heapPrimitive"></div>');
    d3DomElement
      .find("div.heapPrimitive")
      .append(
        '<div class="typeLabel">' + typeLabelPrefix + typeName + "</div>"
      );
    myViz.renderPrimitiveObject(
      primitiveVal,
      d3DomElement.find("div.heapPrimitive")
    );
  } else if (obj[0] == "C_STRUCT" || obj[0] == "C_ARRAY") {
    myViz.renderCStructArray(obj, stepNum, d3DomElement);
  } else {
    // render custom data type
    assert(obj.length == 2);

    var typeName = obj[0];
    var strRepr = obj[1];

    strRepr = htmlspecialchars(strRepr); // escape strings!

    d3DomElement.append(
      '<div class="typeLabel">' + typeLabelPrefix + typeName + "</div>"
    );
    d3DomElement.append(
      '<table class="customObjTbl"><tr><td class="customObjElt">' +
        strRepr +
        "</td></tr></table>"
    );
  }
};

ExecutionVisualizer.prototype.renderCStructArray = function (
  obj,
  stepNum,
  d3DomElement
) {
  var myViz = this; // to prevent confusion of 'this' inside of nested functions

  if (obj[0] == "C_STRUCT") {
    assert(obj.length >= 3);
    var addr = obj[1];
    var typename = obj[2];

    var leader = "";
    if (myViz.debugMode) {
      leader = addr + "<br/>";
    }
    if (myViz.params.lang === "cpp") {
      // call it 'object' instead of 'struct'
      d3DomElement.append(
        '<div class="typeLabel">' + leader + "object " + typename + "</div>"
      );
    } else {
      d3DomElement.append(
        '<div class="typeLabel">' + leader + "struct " + typename + "</div>"
      );
    }

    if (obj.length > 3) {
      d3DomElement.append('<table class="instTbl"></table>');

      var tbl = d3DomElement.children("table");

      $.each(obj, function (ind, kvPair) {
        if (ind < 3) return; // skip header tags

        tbl.append(
          '<tr class="instEntry"><td class="instKey"></td><td class="instVal"></td></tr>'
        );

        var newRow = tbl.find("tr:last");
        var keyTd = newRow.find("td:first");
        var valTd = newRow.find("td:last");

        // the keys should always be strings, so render them directly (and without quotes):
        // (actually this isn't the case when strings are rendered on the heap)
        assert(typeof kvPair[0] == "string");
        // common case ...
        var attrnameStr = htmlspecialchars(kvPair[0]);
        keyTd.append('<span class="keyObj">' + attrnameStr + "</span>");

        // values can be arbitrary objects, so recurse:
        myViz.renderNestedObject(kvPair[1], stepNum, valTd);
      });
    }
  } else {
    assert(obj[0] == "C_ARRAY");
    assert(obj.length >= 2);
    var addr = obj[1];

    var leader = "";
    if (myViz.debugMode) {
      leader = addr + "<br/>";
    }
    d3DomElement.append('<div class="typeLabel">' + leader + "array</div>");
    d3DomElement.append('<table class="cArrayTbl"></table>');
    var tbl = d3DomElement.children("table");

    tbl.append("<tr></tr><tr></tr>");
    var headerTr = tbl.find("tr:first");
    var contentTr = tbl.find("tr:last");
    $.each(obj, function (ind, val) {
      if (ind < 2) return; // skip 'C_ARRAY' and addr

      // add a new column and then pass in that newly-added column
      // as d3DomElement to the recursive call to child:
      headerTr.append('<td class="cArrayHeader"></td>');
      headerTr.find("td:last").append(ind - 2 /* adjust */);

      contentTr.append('<td class="cArrayElt"></td>');
      myViz.renderNestedObject(val, stepNum, contentTr.find("td:last"));
    });
  }
};

ExecutionVisualizer.prototype.redrawConnectors = function () {
  this.jsPlumbInstance.repaintEverything();
};

ExecutionVisualizer.prototype.getRealLabel = function (label) {
  if (
    this.params.lang === "js" ||
    this.params.lang === "ts" ||
    this.params.lang === "ruby"
  ) {
    if (label === "list") {
      return "array";
    } else if (label === "instance") {
      return "object";
    } else if (label === "True") {
      return "true";
    } else if (label === "False") {
      return "false";
    }
  }

  if (this.params.lang === "js") {
    if (label === "dict") {
      return "Map";
    } else if (label === "set") {
      return "Set";
    }
  }

  if (this.params.lang === "ruby") {
    if (label === "dict") {
      return "hash";
    } else if (label === "set") {
      return "Set"; // the Ruby Set class is capitalized
    } else if (label === "function") {
      return "method";
    } else if (label === "None") {
      return "nil";
    } else if (label === "Global frame") {
      return "Global Object";
    }
  }

  // default fallthrough case
  return label;
};

// Utilities

/* colors - see pytutor.css for more colors */

var highlightedLineColor = "#e4faeb";
var highlightedLineBorderColor = "#005583";

var highlightedLineLighterColor = "#e8fff0";

var funcCallLineColor = "#a2eebd";

var brightRed = "#e93f34";

var connectorBaseColor = "#005583";
var connectorHighlightColor = brightRed;
var connectorInactiveColor = "#cccccc";

var errorColor = brightRed;

var breakpointColor = brightRed;

// Unicode arrow types: '\u21d2', '\u21f0', '\u2907'
var darkArrowColor = brightRed;
var lightArrowColor = "#c9e6ca";

function assert(cond) {
  if (!cond) {
    alert("Assertion Failure (see console log for backtrace)");
    throw "Assertion Failure";
  }
}

function htmlspecialchars(str) {
  if (typeof str == "string") {
    str = str.replace(/&/g, "&amp;"); /* must do &amp; first */

    // ignore these for now ...
    //str = str.replace(/"/g, "&quot;");
    //str = str.replace(/'/g, "&#039;");

    str = str.replace(/</g, "&lt;");
    str = str.replace(/>/g, "&gt;");

    // replace spaces:
    str = str.replace(/ /g, "&nbsp;");

    // replace tab as four spaces:
    str = str.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
  }
  return str;
}

// same as htmlspecialchars except don't worry about expanding spaces or
// tabs since we want proper word wrapping in divs.
function htmlsanitize(str) {
  if (typeof str == "string") {
    str = str.replace(/&/g, "&amp;"); /* must do &amp; first */

    str = str.replace(/</g, "&lt;");
    str = str.replace(/>/g, "&gt;");
  }
  return str;
}

String.prototype.rtrim = function () {
  return this.replace(/\s*$/g, "");
};

// make sure varname doesn't contain any weird
// characters that are illegal for CSS ID's ...
//
// I know for a fact that iterator tmp variables named '_[1]'
// are NOT legal names for CSS ID's.
// I also threw in '{', '}', '(', ')', '<', '>' as illegal characters.
//
// also some variable names are like '.0' (for generator expressions),
// and '.' seems to be illegal.
//
// also '=', '!', and '?' are common in Ruby names, so escape those as well
//
// also spaces are illegal, so convert to '_'
// TODO: what other characters are illegal???
var lbRE = new RegExp("\\[|{|\\(|<", "g");
var rbRE = new RegExp("\\]|}|\\)|>", "g");
function varnameToCssID(varname) {
  // make sure to REPLACE ALL (using the 'g' option)
  // rather than just replacing the first entry
  return varname
    .replace(lbRE, "LeftB_")
    .replace(rbRE, "_RightB")
    .replace(/[!]/g, "_BANG_")
    .replace(/[?]/g, "_QUES_")
    .replace(/[:]/g, "_COLON_")
    .replace(/[=]/g, "_EQ_")
    .replace(/[.]/g, "_DOT_")
    .replace(/ /g, "_");
}

// compare two JSON-encoded compound objects for structural equivalence:
ExecutionVisualizer.prototype.structurallyEquivalent = function (obj1, obj2) {
  // punt if either isn't a compound type
  if (this.isPrimitiveType(obj1) || this.isPrimitiveType(obj2)) {
    return false;
  }

  // must be the same compound type
  if (obj1[0] != obj2[0]) {
    return false;
  }

  // must have the same number of elements or fields
  if (obj1.length != obj2.length) {
    return false;
  }

  // for a list or tuple, same size (e.g., a cons cell is a list/tuple of size 2)
  if (obj1[0] == "LIST" || obj1[0] == "TUPLE") {
    return true;
  } else {
    var startingInd = -1;

    if (obj1[0] == "DICT") {
      startingInd = 2;
    } else if (obj1[0] == "INSTANCE") {
      startingInd = 3;
    } else {
      return false; // punt on all other types
    }

    var obj1fields = d3.map();

    // for a dict or object instance, same names of fields (ordering doesn't matter)
    for (var i = startingInd; i < obj1.length; i++) {
      obj1fields.set(obj1[i][0], 1); // use as a set
    }

    for (var i = startingInd; i < obj2.length; i++) {
      if (!obj1fields.has(obj2[i][0])) {
        return false;
      }
    }

    return true;
  }
};

ExecutionVisualizer.prototype.isPrimitiveType = function (obj) {
  var hook_result = this.try_hook("isPrimitiveType", { obj: obj });
  if (hook_result[0]) return hook_result[1];

  // null is a primitive
  if (obj === null) {
    return true;
  }

  if (typeof obj == "object") {
    // kludge: only 'SPECIAL_FLOAT' objects count as primitives
    return (
      obj[0] == "SPECIAL_FLOAT" ||
      obj[0] == "JS_SPECIAL_VAL" ||
      obj[0] == "C_DATA" /* TODO: is this right?!? */
    );
  } else {
    // non-objects are primitives
    return true;
  }
};

function isHeapRef(obj, heap) {
  // ordinary REF
  if (obj[0] === "REF") {
    return heap[obj[1]] !== undefined;
  } else if (obj[0] === "C_DATA" && obj[2] === "pointer") {
    // C-style pointer that has a valid value
    if (obj[3] != "<UNINITIALIZED>" && obj[3] != "<UNALLOCATED>") {
      return heap[obj[3]] !== undefined;
    }
  }

  return false;
}

function getRefID(obj) {
  if (obj[0] == "REF") {
    return obj[1];
  } else {
    assert(obj[0] === "C_DATA" && obj[2] === "pointer");
    assert(obj[3] != "<UNINITIALIZED>" && obj[3] != "<UNALLOCATED>");
    return obj[3]; // pointed-to address
  }
}

// Annotation bubbles

var qtipShared = {
  show: {
    ready: true, // show on document.ready instead of on mouseenter
    delay: 0,
    event: null,
    effect: function () {
      $(this).show();
    }, // don't do any fancy fading because it screws up with scrolling
  },
  hide: {
    fixed: true,
    event: null,
    effect: function () {
      $(this).hide();
    }, // don't do any fancy fading because it screws up with scrolling
  },
  style: {
    classes: "ui-tooltip-pgbootstrap", // my own customized version of the bootstrap style
  },
};

// a speech bubble annotation to attach to:
//   'codeline' - a line of code
//   'frame'    - a stack frame
//   'variable' - a variable within a stack frame
//   'object'   - an object on the heap
// (as determined by the 'type' param)
//
// domID is the ID of the element to attach to (without the leading '#' sign)
function AnnotationBubble(parentViz, type, domID) {
  this.parentViz = parentViz;

  this.domID = domID;
  this.hashID = "#" + domID;

  this.type = type;

  if (type == "codeline") {
    this.my = "left center";
    this.at = "right center";
  } else if (type == "frame") {
    this.my = "right center";
    this.at = "left center";
  } else if (type == "variable") {
    this.my = "right center";
    this.at = "left center";
  } else if (type == "object") {
    this.my = "bottom left";
    this.at = "top center";
  } else {
    assert(false);
  }

  // possible states:
  //   'invisible'
  //   'edit'
  //   'view'
  //   'minimized'
  //   'stub'
  this.state = "invisible";

  this.text = ""; // the actual contents of the annotation bubble

  this.qtipHidden = false; // is there a qtip object present but hidden? (TODO: kinda confusing)
}

AnnotationBubble.prototype.showStub = function () {
  assert(this.state == "invisible" || this.state == "edit");
  assert(this.text == "");

  var myBubble = this; // to avoid name clashes with 'this' in inner scopes

  // destroy then create a new tip:
  this.destroyQTip();
  $(this.hashID).qtip(
    $.extend({}, qtipShared, {
      content: " ",
      id: this.domID,
      position: {
        my: this.my,
        at: this.at,
        adjust: {
          x: myBubble.type == "codeline" ? -6 : 0, // shift codeline tips over a bit for aesthetics
        },
        effect: null, // disable all cutesy animations
      },
      style: {
        classes: "ui-tooltip-pgbootstrap ui-tooltip-pgbootstrap-stub",
      },
    })
  );

  $(this.qTipID())
    .unbind("click") // unbind all old handlers
    .click(function () {
      myBubble.showEditor();
    });

  this.state = "stub";
};

AnnotationBubble.prototype.showEditor = function () {
  assert(
    this.state == "stub" || this.state == "view" || this.state == "minimized"
  );

  var myBubble = this; // to avoid name clashes with 'this' in inner scopes

  var ta = '<textarea class="bubbleInputText">' + this.text + "</textarea>";

  // destroy then create a new tip:
  this.destroyQTip();
  $(this.hashID).qtip(
    $.extend({}, qtipShared, {
      content: ta,
      id: this.domID,
      position: {
        my: this.my,
        at: this.at,
        adjust: {
          x: myBubble.type == "codeline" ? -6 : 0, // shift codeline tips over a bit for aesthetics
        },
        effect: null, // disable all cutesy animations
      },
    })
  );

  $(this.qTipContentID())
    .find("textarea.bubbleInputText")
    // set handler when the textarea loses focus
    .blur(function () {
      myBubble.text = $(this).val().trim(); // strip all leading and trailing spaces

      if (myBubble.text) {
        myBubble.showViewer();
      } else {
        myBubble.showStub();
      }
    })
    .focus(); // grab focus so that the user can start typing right away!

  this.state = "edit";
};

AnnotationBubble.prototype.bindViewerClickHandler = function () {
  var myBubble = this;

  $(this.qTipID())
    .unbind("click") // unbind all old handlers
    .click(function () {
      if (myBubble.parentViz.editAnnotationMode) {
        myBubble.showEditor();
      } else {
        myBubble.minimizeViewer();
      }
    });
};

AnnotationBubble.prototype.showViewer = function () {
  assert(this.state == "edit" || this.state == "invisible");
  assert(this.text); // must be non-empty!

  var myBubble = this;
  // destroy then create a new tip:
  this.destroyQTip();
  $(this.hashID).qtip(
    $.extend({}, qtipShared, {
      content: htmlsanitize(this.text), // help prevent HTML/JS injection attacks
      id: this.domID,
      position: {
        my: this.my,
        at: this.at,
        adjust: {
          x: myBubble.type == "codeline" ? -6 : 0, // shift codeline tips over a bit for aesthetics
        },
        effect: null, // disable all cutesy animations
      },
    })
  );

  this.bindViewerClickHandler();
  this.state = "view";
};

AnnotationBubble.prototype.minimizeViewer = function () {
  assert(this.state == "view");

  var myBubble = this;

  $(this.hashID).qtip("option", "content.text", " "); //hack to "minimize" its size

  $(this.qTipID())
    .unbind("click") // unbind all old handlers
    .click(function () {
      if (myBubble.parentViz.editAnnotationMode) {
        myBubble.showEditor();
      } else {
        myBubble.restoreViewer();
      }
    });

  this.state = "minimized";
};

AnnotationBubble.prototype.restoreViewer = function () {
  assert(this.state == "minimized");
  $(this.hashID).qtip("option", "content.text", htmlsanitize(this.text)); // help prevent HTML/JS injection attacks
  this.bindViewerClickHandler();
  this.state = "view";
};

// NB: actually DESTROYS the QTip object
AnnotationBubble.prototype.makeInvisible = function () {
  assert(this.state == "stub" || this.state == "edit");
  this.destroyQTip();
  this.state = "invisible";
};

AnnotationBubble.prototype.destroyQTip = function () {
  $(this.hashID).qtip("destroy");
};

AnnotationBubble.prototype.qTipContentID = function () {
  return "#ui-tooltip-" + this.domID + "-content";
};

AnnotationBubble.prototype.qTipID = function () {
  return "#ui-tooltip-" + this.domID;
};

AnnotationBubble.prototype.enterEditMode = function () {
  assert(this.parentViz.editAnnotationMode);
  if (this.state == "invisible") {
    this.showStub();

    if (this.type == "codeline") {
      this.redrawCodelineBubble();
    }
  }
};

AnnotationBubble.prototype.enterViewMode = function () {
  assert(!this.parentViz.editAnnotationMode);
  if (this.state == "stub") {
    this.makeInvisible();
  } else if (this.state == "edit") {
    this.text = $(this.qTipContentID())
      .find("textarea.bubbleInputText")
      .val()
      .trim(); // strip all leading and trailing spaces

    if (this.text) {
      this.showViewer();

      if (this.type == "codeline") {
        this.redrawCodelineBubble();
      }
    } else {
      this.makeInvisible();
    }
  } else if (this.state == "invisible") {
    // this happens when, say, you first enter View Mode
    if (this.text) {
      this.showViewer();

      if (this.type == "codeline") {
        this.redrawCodelineBubble();
      }
    }
  }
};

AnnotationBubble.prototype.preseedText = function (txt) {
  assert(this.state == "invisible");
  this.text = txt;
};

AnnotationBubble.prototype.redrawCodelineBubble = function () {
  assert(this.type == "codeline");

  if (isOutputLineVisibleForBubbles(this.domID)) {
    if (this.qtipHidden) {
      $(this.hashID).qtip("show");
    } else {
      $(this.hashID).qtip("reposition");
    }

    this.qtipHidden = false;
  } else {
    $(this.hashID).qtip("hide");
    this.qtipHidden = true;
  }
};

AnnotationBubble.prototype.redrawBubble = function () {
  $(this.hashID).qtip("reposition");
};

// NB: copy-and-paste from isOutputLineVisible with some minor tweaks
function isOutputLineVisibleForBubbles(lineDivID) {
  var pcod = $("#pyCodeOutputDiv");

  var lineNoTd = $("#" + lineDivID);
  var LO = lineNoTd.offset().top;

  var PO = pcod.offset().top;
  var ST = pcod.scrollTop();
  var H = pcod.height();

  // add a few pixels of fudge factor on the bottom end due to bottom scrollbar
  return PO <= LO && LO < PO + H - 25;
}

// popup question dialog code from Brad Miller

// inputId is the ID of the input element
// divId is the div that containsthe visualizer
// answer is a dotted form of an attribute that lives in the curEntry of the trace
// So if we want to ask for the value of a global variable we would say 'globals.a'
// this allows us do do curTrace[i].globals.a  But we do it in the loop below using the
// [] operator.
function traceQCheckMe(inputId, divId, answer) {
  var vis = $("#" + divId).data("vis");
  var i = vis.curInstr;
  var curEntry = vis.curTrace[i + 1];
  var ans = $("#" + inputId).val();
  var attrs = answer.split(".");
  var correctAns = curEntry;
  for (var j in attrs) {
    correctAns = correctAns[attrs[j]];
  }
  var feedbackElement = $("#" + divId + "_feedbacktext");
  if (ans.length > 0 && ans == correctAns) {
    feedbackElement.html("Correct");
  } else {
    feedbackElement.html(vis.curTrace[i].question.feedback);
  }
}

function closeModal(divId) {
  $.modal.close();
  $("#" + divId)
    .data("vis")
    .stepForward();
}

// All of the Java frontend code in this function was written by David
// Pritchard and Will Gwozdz, and integrated by Philip Guo
ExecutionVisualizer.prototype.activateJavaFrontend = function () {
  var prevLine = null;
  this.curTrace.forEach(function (e, i) {
    // ugh the Java backend doesn't attach line numbers to exception
    // events, so just take the previous line number as our best guess
    if (e.event === "exception" && !e.line) {
      e.line = prevLine;
    }

    // super hack by Philip that reverses the direction of the stack so
    // that it grows DOWN and renders the same way as the Python and JS
    // visualizer stacks
    if (e.stack_to_render !== undefined) {
      e.stack_to_render.reverse();
    }

    prevLine = e.line;
  });

  this.add_pytutor_hook("renderPrimitiveObject", function (args) {
    var obj = args.obj,
      d3DomElement = args.d3DomElement;
    var typ = typeof obj;
    if (obj == null) d3DomElement.append('<span class="nullObj">null</span>');
    else if (typ == "number")
      d3DomElement.append('<span class="numberObj">' + obj + "</span>");
    else if (typ == "boolean") {
      if (obj) d3DomElement.append('<span class="boolObj">true</span>');
      else d3DomElement.append('<span class="boolObj">false</span>');
    } else if (obj instanceof Array && obj[0] == "VOID") {
      d3DomElement.append('<span class="voidObj">void</span>');
    } else if (obj instanceof Array && obj[0] == "NUMBER-LITERAL") {
      // actually transmitted as a string
      d3DomElement.append('<span class="numberObj">' + obj[1] + "</span>");
    } else if (obj instanceof Array && obj[0] == "CHAR-LITERAL") {
      var asc = obj[1].charCodeAt(0);
      var ch = obj[1];

      // default
      var show = asc.toString(16);
      while (show.length < 4) show = "0" + show;
      show = "\\u" + show;

      if (ch == "\n") show = "\\n";
      else if (ch == "\r") show = "\\r";
      else if (ch == "\t") show = "\\t";
      else if (ch == "\b") show = "\\b";
      else if (ch == "\f") show = "\\f";
      else if (ch == "'") show = "\\'";
      else if (ch == '"') show = '\\"';
      else if (ch == "\\") show = "\\\\";
      else if (asc >= 32) show = ch;

      // stringObj to make monospace
      d3DomElement.append('<span class="stringObj">\'' + show + "'</span>");
    } else return [false]; // we didn't handle it
    return [true]; // we handled it
  });

  this.add_pytutor_hook("isPrimitiveType", function (args) {
    var obj = args.obj;
    if (
      (obj instanceof Array && obj[0] == "VOID") ||
      (obj instanceof Array && obj[0] == "NUMBER-LITERAL") ||
      (obj instanceof Array && obj[0] == "CHAR-LITERAL") ||
      (obj instanceof Array && obj[0] == "ELIDE")
    )
      return [true, true]; // we handled it, it's primitive
    return [false]; // didn't handle it
  });

  this.add_pytutor_hook("end_updateOutput", function (args) {
    var myViz = args.myViz;
    var curEntry = myViz.curTrace[myViz.curInstr];
    if (myViz.params.stdin && myViz.params.stdin != "") {
      var stdinPosition = curEntry.stdinPosition || 0;
      var stdinContent =
        '<span style="color:lightgray;text-decoration: line-through">' +
        escapeHtml(myViz.params.stdin.substr(0, stdinPosition)) +
        "</span>" +
        escapeHtml(myViz.params.stdin.substr(stdinPosition));
      myViz.domRoot.find("#stdinShow").html(stdinContent);
    }
    return [false];
  });

  this.add_pytutor_hook("end_constructor", function (args) {
    var myViz = args.myViz;
    if (
      myViz.curTrace.length > 0 &&
      myViz.curTrace[myViz.curTrace.length - 1] &&
      myViz.curTrace[myViz.curTrace.length - 1].stdout
    ) {
      myViz.hasStdout = true;
      myViz.stdoutLines =
        myViz.curTrace[myViz.curTrace.length - 1].stdout.split("\n").length;
    }
    // if last frame is a step limit
    else if (
      myViz.curTrace.length > 1 &&
      myViz.curTrace[myViz.curTrace.length - 2] &&
      myViz.curTrace[myViz.curTrace.length - 2].stdout
    ) {
      myViz.hasStdout = true;
      myViz.stdoutLines =
        myViz.curTrace[myViz.curTrace.length - 2].stdout.split("\n").length;
    } else {
      myViz.stdoutLines = -1;
    }
    if (myViz.hasStdout)
      for (var i = 0; i < myViz.curTrace.length; i++)
        if (!myViz.curTrace[i].stdout) myViz.curTrace[i].stdout = " "; // always show it, if it's ever used
  });

  this.add_pytutor_hook("end_render", function (args) {
    var myViz = args.myViz;

    // uhh, dunno what this was for ...
    //myViz.domRoot.find('#pyStdout').attr('cols', 1); // commented out by pgbovine
    //myViz.domRoot.find('#pyStdout').attr('rows', Math.min(10, myViz.stdoutLines));

    if (myViz.params.stdin && myViz.params.stdin != "") {
      var stdinHTML =
        '<div id="stdinWrap">stdin:<pre id="stdinShow" style="border:1px solid gray"></pre></div>';
      myViz.domRoot.find("#dataViz").append(stdinHTML);
    }

    myViz.domRoot
      .find("#" + myViz.generateID("globals_header"))
      .html("Static fields");
  });

  this.add_pytutor_hook("isLinearObject", function (args) {
    var heapObj = args.heapObj;
    if (heapObj[0] == "STACK" || heapObj[0] == "QUEUE") return ["true", "true"];
    return ["false"];
  });

  this.add_pytutor_hook("renderCompoundObject", function (args) {
    var objID = args.objID;
    var d3DomElement = args.d3DomElement;
    var obj = args.obj;
    var typeLabelPrefix = args.typeLabelPrefix;
    var myViz = args.myViz;
    var stepNum = args.stepNum;

    if (!(obj[0] == "LIST" || obj[0] == "QUEUE" || obj[0] == "STACK"))
      return [false]; // didn't handle

    var label = obj[0].toLowerCase();
    var visibleLabel = { list: "array", queue: "queue", stack: "stack" }[label];

    if (obj.length == 1) {
      d3DomElement.append(
        '<div class="typeLabel">' +
          typeLabelPrefix +
          "empty " +
          visibleLabel +
          "</div>"
      );
      return [true]; //handled
    }

    d3DomElement.append(
      '<div class="typeLabel">' + typeLabelPrefix + visibleLabel + "</div>"
    );
    d3DomElement.append('<table class="' + label + 'Tbl"></table>');
    var tbl = d3DomElement.children("table");

    if (obj[0] == "LIST") {
      tbl.append("<tr></tr><tr></tr>");
      var headerTr = tbl.find("tr:first");
      var contentTr = tbl.find("tr:last");

      // i: actual index in json object; ind: apparent index
      for (var i = 1, ind = 0; i < obj.length; i++) {
        var val = obj[i];
        var elide = val instanceof Array && val[0] == "ELIDE";

        // add a new column and then pass in that newly-added column
        // as d3DomElement to the recursive call to child:
        headerTr.append('<td class="' + label + 'Header"></td>');
        headerTr.find("td:last").append(elide ? "&hellip;" : ind);

        contentTr.append('<td class="' + label + 'Elt"></td>');
        if (!elide) {
          myViz.renderNestedObject(val, stepNum, contentTr.find("td:last"));
          ind++;
        } else {
          contentTr.find("td:last").append("&hellip;");
          ind += val[1]; // val[1] is the number of cells to skip
        }
      }
    } // end of LIST handling

    // Stack and Queue handling code by Will Gwozdz
    /* The table produced for stacks and queues is formed slightly differently than the others,
                                    missing the header row. Two rows made the dashed border not line up properly */
    if (obj[0] == "STACK") {
      tbl.append("<tr></tr><tr></tr>");
      var contentTr = tbl.find("tr:last");
      contentTr.append(
        '<td class="' +
          label +
          'FElt">' +
          '<span class="stringObj symbolic">&#8596;</span>' +
          "</td>"
      );
      $.each(obj, function (ind, val) {
        if (ind < 1) return; // skip type tag and ID entry
        contentTr.append('<td class="' + label + 'Elt"></td>');
        myViz.renderNestedObject(val, stepNum, contentTr.find("td:last"));
      });
      contentTr.append('<td class="' + label + 'LElt">' + "</td>");
    }

    if (obj[0] == "QUEUE") {
      tbl.append("<tr></tr><tr></tr>");
      var contentTr = tbl.find("tr:last");
      // Add arrows showing in/out direction
      contentTr.append(
        '<td class="' +
          label +
          'FElt">' +
          '<span class="stringObj symbolic">&#8592;</span></td>'
      );
      $.each(obj, function (ind, val) {
        if (ind < 1) return; // skip type tag and ID entry
        contentTr.append('<td class="' + label + 'Elt"></td>');
        myViz.renderNestedObject(val, stepNum, contentTr.find("td:last"));
      });
      contentTr.append(
        '<td class="' +
          label +
          'LElt">' +
          '<span class="stringObj symbolic">&#8592;</span></td>'
      );
    }

    return [true]; // did handle
  });

  this.add_pytutor_hook("end_renderDataStructures", function (args) {
    var myViz = args.myViz;
    myViz.domRoot.find("td.instKey:contains('___NO_LABEL!___')").hide();
    myViz.domRoot.find(".typeLabel:contains('dict')").each(function (i) {
      if ($(this).html() == "dict") $(this).html("symbol table");
      if ($(this).html() == "empty dict") $(this).html("empty symbol table");
    });
  });

  // java synthetics cause things which javascript doesn't like in an id

  // VERY important to bind(this) so that when it's called, 'this' is this current object
  var old_generateID = ExecutionVisualizer.prototype.generateID.bind(this);
  this.generateID = function (original_id) {
    var sanitized = original_id.replace(/[^0-9a-zA-Z_]/g, function (match) {
      return "-" + match.charCodeAt(0) + "-";
    });
    return old_generateID(sanitized);
  };

  // utility functions
  var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
    "/": "&#x2F;",
  };

  var escapeHtml = function (string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
      return entityMap[s];
    });
  };
};

var GUI =
(window["webpackJsonpGUI"] = window["webpackJsonpGUI"] || []).push([["blocksonly"],{

/***/ "./node_modules/css-loader/index.js?!./node_modules/postcss-loader/src/index.js?!./src/playground/blocks-only.css":
/*!***********************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--5-1!./node_modules/postcss-loader/src??postcss!./src/playground/blocks-only.css ***!
  \***********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".blocks-only_controls_1Fuds {\n    position: absolute;\n    z-index: 2;\n    top: 10px;\n    right: 15px;\n}\n", ""]);

// exports
exports.locals = {
	"controls": "blocks-only_controls_1Fuds"
};

/***/ }),

/***/ "./src/playground/blocks-only.css":
/*!****************************************!*\
  !*** ./src/playground/blocks-only.css ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../node_modules/css-loader??ref--5-1!../../node_modules/postcss-loader/src??postcss!./blocks-only.css */ "./node_modules/css-loader/index.js?!./node_modules/postcss-loader/src/index.js?!./src/playground/blocks-only.css");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./src/playground/blocks-only.jsx":
/*!****************************************!*\
  !*** ./src/playground/blocks-only.jsx ***!
  \****************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "./node_modules/react/index.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "./node_modules/react-dom/index.js");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _containers_controls_jsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../containers/controls.jsx */ "./src/containers/controls.jsx");
/* harmony import */ var _containers_blocks_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../containers/blocks.jsx */ "./src/containers/blocks.jsx");
/* harmony import */ var _containers_gui_jsx__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../containers/gui.jsx */ "./src/containers/gui.jsx");
/* harmony import */ var _lib_hash_parser_hoc_jsx__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../lib/hash-parser-hoc.jsx */ "./src/lib/hash-parser-hoc.jsx");
/* harmony import */ var _lib_app_state_hoc_jsx__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../lib/app-state-hoc.jsx */ "./src/lib/app-state-hoc.jsx");
/* harmony import */ var _blocks_only_css__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./blocks-only.css */ "./src/playground/blocks-only.css");
/* harmony import */ var _blocks_only_css__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_blocks_only_css__WEBPACK_IMPORTED_MODULE_8__);










var mapStateToProps = function mapStateToProps(state) {
  return {
    vm: state.scratchGui.vm
  };
};

var VMBlocks = Object(react_redux__WEBPACK_IMPORTED_MODULE_2__["connect"])(mapStateToProps)(_containers_blocks_jsx__WEBPACK_IMPORTED_MODULE_4__["default"]);
var VMControls = Object(react_redux__WEBPACK_IMPORTED_MODULE_2__["connect"])(mapStateToProps)(_containers_controls_jsx__WEBPACK_IMPORTED_MODULE_3__["default"]);

var BlocksOnly = function BlocksOnly(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_gui_jsx__WEBPACK_IMPORTED_MODULE_5__["default"], props, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(VMBlocks, {
    grow: 1,
    options: {
      media: "static/blocks-media/"
    }
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(VMControls, {
    className: _blocks_only_css__WEBPACK_IMPORTED_MODULE_8___default.a.controls
  }));
};

var App = Object(_lib_app_state_hoc_jsx__WEBPACK_IMPORTED_MODULE_7__["default"])(Object(_lib_hash_parser_hoc_jsx__WEBPACK_IMPORTED_MODULE_6__["default"])(BlocksOnly));
var appTarget = document.createElement('div');
document.body.appendChild(appTarget);
react_dom__WEBPACK_IMPORTED_MODULE_1___default.a.render( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(App, null), appTarget);

/***/ })

},[["./src/playground/blocks-only.jsx","lib.min"]]]);
//# sourceMappingURL=blocksonly.js.map
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/resources/preview-update.js":
/*!*****************************************!*\
  !*** ./src/resources/preview-update.js ***!
  \*****************************************/
/***/ (() => {

eval("(function (win) {\n  var ready = function ready() {\n    var Craft = win.Craft;\n    var Garnish = win.Garnish;\n    var doc = win.document;\n    var wrapper = doc.querySelector('.sp-preview-wrapper');\n\n    if (!wrapper) {\n      return;\n    }\n\n    var anchor = wrapper.querySelector('a');\n    var img = anchor.querySelector('img');\n    img.addEventListener('load', function () {\n      wrapper.style.opacity = 1;\n    }, false);\n    img.addEventListener('error', function () {\n      wrapper.style.opacity = 1;\n      anchor.setAttribute('href', '#');\n    });\n    Garnish.on(Craft.DraftEditor, 'update', function (event) {\n      var draftEditor = event.target;\n      var url = Craft.getCpUrl('social-previews/draft', {\n        id: draftEditor.settings.draftId,\n        ts: new Date().getTime()\n      });\n      anchor.setAttribute('href', url);\n      wrapper.style.opacity = 0.5;\n      img.setAttribute('src', url);\n    });\n  };\n\n  win.addEventListener('DOMContentLoaded', ready, false);\n})(window);\n\n//# sourceURL=webpack:///./src/resources/preview-update.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/resources/preview-update.js"]();
/******/ 	
/******/ })()
;
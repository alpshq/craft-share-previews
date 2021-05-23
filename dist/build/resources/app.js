/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/resources/app.js":
/*!******************************!*\
  !*** ./src/resources/app.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings */ \"./src/resources/settings.js\");\n/* harmony import */ var _template_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./template-editor */ \"./src/resources/template-editor.js\");\n\n\n\n(function (win) {\n  var ready = function ready() {\n    (0,_settings__WEBPACK_IMPORTED_MODULE_0__.default)(win);\n    (0,_template_editor__WEBPACK_IMPORTED_MODULE_1__.default)(win);\n  };\n\n  win.addEventListener('DOMContentLoaded', ready, false);\n})(window);\n\n//# sourceURL=webpack:///./src/resources/app.js?");

/***/ }),

/***/ "./src/resources/settings.js":
/*!***********************************!*\
  !*** ./src/resources/settings.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\nfunction _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }\n\nfunction _nonIterableRest() { throw new TypeError(\"Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\n\nfunction _iterableToArrayLimit(arr, i) { var _i = arr && (typeof Symbol !== \"undefined\" && arr[Symbol.iterator] || arr[\"@@iterator\"]); if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i[\"return\"] != null) _i[\"return\"](); } finally { if (_d) throw _e; } } return _arr; }\n\nfunction _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }\n\nfunction _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\n\nfunction _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === \"string\") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === \"Object\" && o.constructor) n = o.constructor.name; if (n === \"Map\" || n === \"Set\") return Array.from(o); if (n === \"Arguments\" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }\n\nfunction _iterableToArray(iter) { if (typeof Symbol !== \"undefined\" && iter[Symbol.iterator] != null || iter[\"@@iterator\"] != null) return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }\n\nfunction _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }\n\nvar getTemplateId = function getTemplateId(node) {\n  while (node.hasAttribute('data-template') === false) {\n    node = node.parentNode;\n  }\n\n  return Number(node.getAttribute('data-template'));\n};\n\nvar loadTemplates = function loadTemplates(doc) {\n  var selector = '.share-preview-settings div[data-template]';\n  return _toConsumableArray(doc.querySelectorAll(selector)).map(function (template) {\n    return {\n      node: template,\n      idx: Number(template.getAttribute('data-template')),\n      layers: _toConsumableArray(template.querySelectorAll('div[data-layer]')).map(function (layer) {\n        return {\n          node: layer,\n          idx: Number(layer.getAttribute('data-layer')),\n          inputs: _toConsumableArray(layer.querySelectorAll('select,input')).filter(function (inp) {\n            return (inp.getAttribute('name') || '').indexOf('[layers]') > -1;\n          })\n        };\n      })\n    };\n  });\n};\n\nvar exec = function exec(win) {\n  return;\n  var doc = win.document;\n  var templates = loadTemplates(doc);\n\n  var removeLayer = function removeLayer(templateIdx, layerIdx) {};\n\n  console.log(templates);\n\n  var getTemplateNode = function getTemplateNode(templateId) {\n    return doc.querySelector(\".share-preview-settings div[data-template=\\\"\".concat(templateId, \"\\\"\"));\n  }; // const reindexLayers = (templateId) => {\n  //   const template = getTemplateNode(templateId);\n  //   const layers = [...template.querySelectorAll('div[data-layer]')];\n  //   let layerIdx = layers.length;\n  //\n  //   layers.forEach(layer => {\n  //     layerIdx -= 1;\n  //\n  //     [...layer.querySelectorAll('input,select')]\n  //       .filter(inp => {\n  //         const name = inp.getAttribute('name') || '';\n  //         return name.indexOf('[layers]') > -1;\n  //       })\n  //       .forEach(inp => {\n  //         let name = inp.getAttribute('name');\n  //\n  //         const parts = name.split('[layers]');\n  //\n  //         name = parts[1].substr(name.indexOf(']'));\n  //\n  //         const newName = `${parts[0]}[layers][${layerIdx}]${name}`;\n  //\n  //\n  //         inp.setAttribute('name', newName);\n  //       });\n  //   });\n  // };\n\n\n  templates.forEach(function (_ref) {\n    var templateIdx = _ref.idx,\n        layers = _ref.layers;\n    layers.forEach(function (_ref2) {\n      var layerIdx = _ref2.idx,\n          node = _ref2.node;\n      node.querySelector('.btn.delete-layer').addEventListener('click', function (ev) {\n        removeLayer(templateIdx, layerIdx);\n      }, false);\n    });\n  });\n  var button = doc.querySelector('#settings-btn-preview');\n\n  var deleteLayerButtons = _toConsumableArray(doc.querySelectorAll('.btn.delete-layer'));\n\n  deleteLayerButtons.forEach(function (btn) {\n    btn.addEventListener('click', function (ev) {\n      ev.preventDefault();\n      var layer = ev.target.parentNode;\n\n      while (layer.hasAttribute('data-layer') === false) {\n        layer = layer.parentNode;\n      }\n\n      var templateId = getTemplateId(layer);\n      layer.parentNode.removeChild(layer);\n      reindexLayers(templateId);\n    }, false);\n  });\n\n  var generatePreview = function generatePreview(ev) {\n    ev.preventDefault();\n    var formEl = ev.target.parentNode;\n\n    while (formEl.nodeName.toLowerCase() !== 'form') {\n      formEl = formEl.parentNode;\n    } // let idx = 0;\n    // [...doc.querySelectorAll('div[data-layer]')].forEach(el => {\n    //   [...el.querySelectorAll('input,select')].forEach(el => {\n    //     const name = el.getAttribute('name');\n    //\n    //     if (!name || name.indexOf('layers') < 0) {\n    //       return;\n    //     }\n    //\n    //     const newName = name\n    //       .split('[layers][]')\n    //       .join(`[layers][${idx}]`);\n    //\n    //     el.setAttribute('name', newName);\n    //   });\n    //\n    //   idx++;\n    // });\n    // [...formEl.querySelectorAll('input,select')].forEach(el => {\n    //   if (el.indexOf('layers') < 0) {\n    //     return;\n    //   }\n    // })\n\n\n    var form = new FormData(formEl);\n\n    var values = _toConsumableArray(form.entries()).filter(function (_ref3) {\n      var _ref4 = _slicedToArray(_ref3, 1),\n          key = _ref4[0];\n\n      return key.indexOf('settings') > -1;\n    });\n\n    console.log(formEl, values);\n  };\n\n  button.addEventListener('click', generatePreview, false);\n  generatePreview({\n    target: button,\n    preventDefault: function preventDefault() {}\n  });\n};\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (exec);\n\n//# sourceURL=webpack:///./src/resources/settings.js?");

/***/ }),

/***/ "./src/resources/template-editor.js":
/*!******************************************!*\
  !*** ./src/resources/template-editor.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\nvar calculateValue = function calculateValue(ev) {\n  var inp = ev.target;\n  var value = inp.value;\n  console.log(value, 'return (' + value + ')');\n  var calculatedValue = Function('return (' + value + ')')();\n  console.log(calculatedValue);\n};\n\nvar exec = function exec(win) {\n  var doc = win.document;\n  var editor = doc.querySelector('.template-editor');\n\n  if (!editor) {\n    return;\n  }\n\n  editor.addEventListener('change', function () {\n    console.log('change');\n  }, false); // const calcInputs = [...editor.querySelectorAll('[data-features=\"calc\"]')];\n  //\n  // calcInputs.forEach(inp => {\n  //   inp.addEventListener('keyup', calculateValue, false);\n  //   inp.addEventListener('change', calculateValue, false);\n  // });\n};\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (exec);\n\n//# sourceURL=webpack:///./src/resources/template-editor.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./src/resources/app.js");
/******/ 	
/******/ })()
;
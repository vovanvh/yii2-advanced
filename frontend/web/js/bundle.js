/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./frontend/src/open.weather.ts":
/*!**************************************!*\
  !*** ./frontend/src/open.weather.ts ***!
  \**************************************/
/***/ (function(__unused_webpack_module, exports) {


var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.renderWeatherInElements = renderWeatherInElements;
const fetchedCache = new Map();
function fetchWeather(lat, lon) {
    return __awaiter(this, void 0, void 0, function* () {
        var _a;
        const coordinatesKey = `${lat},${lon}`;
        if (fetchedCache.has(coordinatesKey)) {
            return fetchedCache.get(coordinatesKey);
        }
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;
        try {
            const res = yield fetch(url);
            if (!res.ok)
                throw new Error(`API error: ${res.statusText}`);
            const data = yield res.json();
            const temperature = (_a = data === null || data === void 0 ? void 0 : data.current_weather) === null || _a === void 0 ? void 0 : _a.temperature;
            if (typeof temperature === 'number') {
                fetchedCache.set(coordinatesKey, temperature);
                return temperature;
            }
            return null;
        }
        catch (err) {
            console.error('Fetch failed:', err);
            return null;
        }
    });
}
function renderWeatherInElements(className) {
    return __awaiter(this, void 0, void 0, function* () {
        const elements = document.querySelectorAll(`.${className}`);
        for (const el of elements) {
            const lat = parseFloat(el.dataset.lat || '');
            const lon = parseFloat(el.dataset.lon || '');
            if (isNaN(lat) || isNaN(lon)) {
                console.warn('Invalid coordinates for element:', el);
                continue;
            }
            const temp = yield fetchWeather(lat, lon);
            if (temp !== null) {
                el.innerText = `🌡️ ${temp}°C`;
                el.style.display = 'block'; // Or remove a 'hidden' class if used
            }
            else {
                el.innerText = 'Weather data unavailable';
            }
        }
    });
}


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
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
var exports = __webpack_exports__;
/*!*****************************!*\
  !*** ./frontend/src/app.ts ***!
  \*****************************/

Object.defineProperty(exports, "__esModule", ({ value: true }));
const open_weather_1 = __webpack_require__(/*! ./open.weather */ "./frontend/src/open.weather.ts");
(0, open_weather_1.renderWeatherInElements)('weather-box').then(r => {
    console.log('Done!');
});

})();

/******/ })()
;
//# sourceMappingURL=bundle.js.map
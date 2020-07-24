/*! @vimeo/player v2.6.3 | (c) 2018 Vimeo | MIT License | https://github.com/vimeo/player.js */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e.Vimeo=e.Vimeo||{},e.Vimeo.Player=t())}(this,function(){"use strict";function o(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}var e="undefined"!=typeof global&&"[object global]"==={}.toString.call(global);function i(e,t){return 0===e.indexOf(t.toLowerCase())?e:"".concat(t.toLowerCase()).concat(e.substr(0,1).toUpperCase()).concat(e.substr(1))}function c(e){return/^(https?:)?\/\/((player|www).)?vimeo.com(?=$|\/)/.test(e)}function u(){var e,t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{},n=t.id,o=t.url,r=n||o;if(!r)throw new Error("An id or url must be passed, either in an options object or as a data-vimeo-id or data-vimeo-url attribute.");if(e=r,!isNaN(parseFloat(e))&&isFinite(e)&&Math.floor(e)==e)return"https://vimeo.com/".concat(r);if(c(r))return r.replace("http:","https:");if(n)throw new TypeError("â€œ".concat(n,"â€ is not a valid video id."));throw new TypeError("â€œ".concat(r,"â€ is not a vimeo.com url."))}var t=void 0!==Array.prototype.indexOf,n="undefined"!=typeof window&&void 0!==window.postMessage;if(!(e||t&&n))throw new Error("Sorry, the Vimeo Player API is not available in this browser.");var r="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:{};!function(e){if(!e.WeakMap){var n=Object.prototype.hasOwnProperty,r=function(e,t,n){Object.defineProperty?Object.defineProperty(e,t,{configurable:!0,writable:!0,value:n}):e[t]=n};e.WeakMap=function(){function e(){if(void 0===this)throw new TypeError("Constructor WeakMap requires 'new'");if(r(this,"_id","_WeakMap"+"_"+t()+"."+t()),0<arguments.length)throw new TypeError("WeakMap iterable is not supported")}function o(e,t){if(!i(e)||!n.call(e,"_id"))throw new TypeError(t+" method called on incompatible receiver "+typeof e)}function t(){return Math.random().toString().substring(2)}return r(e.prototype,"delete",function(e){if(o(this,"delete"),!i(e))return!1;var t=e[this._id];return!(!t||t[0]!==e)&&(delete e[this._id],!0)}),r(e.prototype,"get",function(e){if(o(this,"get"),i(e)){var t=e[this._id];return t&&t[0]===e?t[1]:void 0}}),r(e.prototype,"has",function(e){if(o(this,"has"),!i(e))return!1;var t=e[this._id];return!(!t||t[0]!==e)}),r(e.prototype,"set",function(e,t){if(o(this,"set"),!i(e))throw new TypeError("Invalid value used as weak map key");var n=e[this._id];return n&&n[0]===e?n[1]=t:r(e,this._id,[e,t]),this}),r(e,"_polyfill",!0),e}()}function i(e){return Object(e)===e}}("undefined"!=typeof self?self:"undefined"!=typeof window?window:r);var a,s=(function(e){var t,n,o;o=function(){var t,a,n,e=Object.prototype.toString,o="undefined"!=typeof setImmediate?function(e){return setImmediate(e)}:setTimeout;try{Object.defineProperty({},"x",{}),t=function(e,t,n,o){return Object.defineProperty(e,t,{value:n,writable:!0,configurable:!1!==o})}}catch(e){t=function(e,t,n){return e[t]=n,e}}function i(e,t){n.add(e,t),a||(a=o(n.drain))}function u(e){var t,n=typeof e;return null==e||"object"!=n&&"function"!=n||(t=e.then),"function"==typeof t&&t}function c(){for(var e=0;e<this.chain.length;e++)r(this,1===this.state?this.chain[e].success:this.chain[e].failure,this.chain[e]);this.chain.length=0}function r(e,t,n){var o,r;try{!1===t?n.reject(e.msg):(o=!0===t?e.msg:t.call(void 0,e.msg))===n.promise?n.reject(TypeError("Promise-chain cycle")):(r=u(o))?r.call(o,n.resolve,n.reject):n.resolve(o)}catch(e){n.reject(e)}}function s(e){var t=this;t.triggered||(t.triggered=!0,t.def&&(t=t.def),t.msg=e,t.state=2,0<t.chain.length&&i(c,t))}function l(e,n,o,r){for(var t=0;t<n.length;t++)!function(t){e.resolve(n[t]).then(function(e){o(t,e)},r)}(t)}function f(e){this.def=e,this.triggered=!1}function d(e){this.promise=e,this.state=0,this.triggered=!1,this.chain=[],this.msg=void 0}function h(e){if("function"!=typeof e)throw TypeError("Not a function");if(0!==this.__NPO__)throw TypeError("Not a promise");this.__NPO__=1;var o=new d(this);this.then=function(e,t){var n={success:"function"!=typeof e||e,failure:"function"==typeof t&&t};return n.promise=new this.constructor(function(e,t){if("function"!=typeof e||"function"!=typeof t)throw TypeError("Not a function");n.resolve=e,n.reject=t}),o.chain.push(n),0!==o.state&&i(c,o),n.promise},this.catch=function(e){return this.then(void 0,e)};try{e.call(void 0,function(e){(function e(n){var o,r=this;if(!r.triggered){r.triggered=!0,r.def&&(r=r.def);try{(o=u(n))?i(function(){var t=new f(r);try{o.call(n,function(){e.apply(t,arguments)},function(){s.apply(t,arguments)})}catch(e){s.call(t,e)}}):(r.msg=n,r.state=1,0<r.chain.length&&i(c,r))}catch(e){s.call(new f(r),e)}}}).call(o,e)},function(e){s.call(o,e)})}catch(e){s.call(o,e)}}n=function(){var n,o,r;function i(e,t){this.fn=e,this.self=t,this.next=void 0}return{add:function(e,t){r=new i(e,t),o?o.next=r:n=r,o=r,r=void 0},drain:function(){var e=n;for(n=o=a=void 0;e;)e.fn.call(e.self),e=e.next}}}();var v=t({},"constructor",h,!1);return t(h.prototype=v,"__NPO__",0,!1),t(h,"resolve",function(n){return n&&"object"==typeof n&&1===n.__NPO__?n:new this(function(e,t){if("function"!=typeof e||"function"!=typeof t)throw TypeError("Not a function");e(n)})}),t(h,"reject",function(n){return new this(function(e,t){if("function"!=typeof e||"function"!=typeof t)throw TypeError("Not a function");t(n)})}),t(h,"all",function(t){var a=this;return"[object Array]"!=e.call(t)?a.reject(TypeError("Not an array")):0===t.length?a.resolve([]):new a(function(n,e){if("function"!=typeof n||"function"!=typeof e)throw TypeError("Not a function");var o=t.length,r=Array(o),i=0;l(a,t,function(e,t){r[e]=t,++i===o&&n(r)},e)})}),t(h,"race",function(t){var o=this;return"[object Array]"!=e.call(t)?o.reject(TypeError("Not an array")):new o(function(n,e){if("function"!=typeof n||"function"!=typeof e)throw TypeError("Not a function");l(o,t,function(e,t){n(t)},e)})}),h},(n=r)[t="Promise"]=n[t]||o(),e.exports&&(e.exports=n[t])}(a={exports:{}},a.exports),a.exports),l=new WeakMap;function f(e,t,n){var o=l.get(e.element)||{};t in o||(o[t]=[]),o[t].push(n),l.set(e.element,o)}function d(e,t){return(l.get(e.element)||{})[t]||[]}function h(e,t,n){var o=l.get(e.element)||{};if(!o[t])return!0;if(!n)return o[t]=[],l.set(e.element,o),!0;var r=o[t].indexOf(n);return-1!==r&&o[t].splice(r,1),l.set(e.element,o),o[t]&&0===o[t].length}var v=["autopause","autoplay","background","byline","color","height","id","loop","maxheight","maxwidth","muted","playsinline","portrait","responsive","speed","title","transparent","url","width"];function p(o){var e=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{};return v.reduce(function(e,t){var n=o.getAttribute("data-vimeo-".concat(t));return(n||""===n)&&(e[t]=""===n?1:n),e},e)}function y(e,t){var n=e.html;if(!t)throw new TypeError("An element must be provided");if(null!==t.getAttribute("data-vimeo-initialized"))return t.querySelector("iframe");var o=document.createElement("div");return o.innerHTML=n,t.appendChild(o.firstChild),t.setAttribute("data-vimeo-initialized","true"),t.querySelector("iframe")}function m(i){var a=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{},u=2<arguments.length?arguments[2]:void 0;return new Promise(function(t,n){if(!c(i))throw new TypeError("â€œ".concat(i,"â€ is not a vimeo.com url."));var e="https://vimeo.com/api/oembed.json?url=".concat(encodeURIComponent(i),"&domain=").concat(window.location.hostname);for(var o in a)a.hasOwnProperty(o)&&(e+="&".concat(o,"=").concat(encodeURIComponent(a[o])));var r="XDomainRequest"in window?new XDomainRequest:new XMLHttpRequest;r.open("GET",e,!0),r.onload=function(){if(404!==r.status)if(403!==r.status)try{var e=JSON.parse(r.responseText);if(403===e.domain_status_code)return y(e,u),void n(new Error("â€œ".concat(i,"â€ is not embeddable.")));t(e)}catch(e){n(e)}else n(new Error("â€œ".concat(i,"â€ is not embeddable.")));else n(new Error("â€œ".concat(i,"â€ was not found.")))},r.onerror=function(){var e=r.status?" (".concat(r.status,")"):"";n(new Error("There was an error fetching the embed code from Vimeo".concat(e,".")))},r.send()})}function w(e){return"string"==typeof e&&(e=JSON.parse(e)),e}function g(e,t,n){if(e.element.contentWindow&&e.element.contentWindow.postMessage){var o={method:t};void 0!==n&&(o.value=n);var r=parseFloat(navigator.userAgent.toLowerCase().replace(/^.*msie (\d+).*$/,"$1"));8<=r&&r<10&&(o=JSON.stringify(o)),e.element.contentWindow.postMessage(o,e.origin)}}function b(n,o){var t,e=[];if((o=w(o)).event){if("error"===o.event)d(n,o.data.method).forEach(function(e){var t=new Error(o.data.message);t.name=o.data.name,e.reject(t),h(n,o.data.method,e)});e=d(n,"event:".concat(o.event)),t=o.data}else if(o.method){var r=function(e,t){var n=d(e,t);if(n.length<1)return!1;var o=n.shift();return h(e,t,o),o}(n,o.method);r&&(e.push(r),t=o.value)}e.forEach(function(e){try{if("function"==typeof e)return void e.call(n,t);e.resolve(t)}catch(e){}})}var k=new WeakMap,E=new WeakMap,Player=function(){function Player(i){var a=this,o=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{};if(function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,Player),window.jQuery&&i instanceof jQuery&&(1<i.length&&window.console&&console.warn&&console.warn("A jQuery object with multiple elements was passed, using the first element."),i=i[0]),"undefined"!=typeof document&&"string"==typeof i&&(i=document.getElementById(i)),!(i instanceof window.HTMLElement))throw new TypeError("You must pass either a valid element or a valid id.");if("IFRAME"!==i.nodeName){var e=i.querySelector("iframe");e&&(i=e)}if("IFRAME"===i.nodeName&&!c(i.getAttribute("src")||""))throw new Error("The player element passed isnâ€™t a Vimeo embed.");if(k.has(i))return k.get(i);this.element=i,this.origin="*";var t=new s(function(r,t){var e=function(e){if(c(e.origin)&&a.element.contentWindow===e.source){"*"===a.origin&&(a.origin=e.origin);var t=w(e.data),n="event"in t&&"ready"===t.event,o="method"in t&&"ping"===t.method;if(n||o)return a.element.setAttribute("data-ready","true"),void r();b(a,t)}};if(window.addEventListener?window.addEventListener("message",e,!1):window.attachEvent&&window.attachEvent("onmessage",e),"IFRAME"!==a.element.nodeName){var n=p(i,o);m(u(n),n,i).then(function(e){var t,n,o,r=y(e,i);return a.element=r,a._originalElement=i,t=i,n=r,o=l.get(t),l.set(n,o),l.delete(t),k.set(a.element,a),e}).catch(function(e){return t(e)})}});return E.set(this,t),k.set(this.element,this),"IFRAME"===this.element.nodeName&&g(this,"ping"),this}var e,t,n;return e=Player,(t=[{key:"callMethod",value:function(n){var o=this,r=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{};return new s(function(e,t){return o.ready().then(function(){f(o,n,{resolve:e,reject:t}),g(o,n,r)}).catch(function(e){t(e)})})}},{key:"get",value:function(n){var o=this;return new s(function(e,t){return n=i(n,"get"),o.ready().then(function(){f(o,n,{resolve:e,reject:t}),g(o,n)})})}},{key:"set",value:function(o,e){var r=this;return s.resolve(e).then(function(n){if(o=i(o,"set"),null==n)throw new TypeError("There must be a value to set.");return r.ready().then(function(){return new s(function(e,t){f(r,o,{resolve:e,reject:t}),g(r,o,n)})})})}},{key:"on",value:function(e,t){if(!e)throw new TypeError("You must pass an event name.");if(!t)throw new TypeError("You must pass a callback function.");if("function"!=typeof t)throw new TypeError("The callback must be a function.");0===d(this,"event:".concat(e)).length&&this.callMethod("addEventListener",e).catch(function(){}),f(this,"event:".concat(e),t)}},{key:"off",value:function(e,t){if(!e)throw new TypeError("You must pass an event name.");if(t&&"function"!=typeof t)throw new TypeError("The callback must be a function.");h(this,"event:".concat(e),t)&&this.callMethod("removeEventListener",e).catch(function(e){})}},{key:"loadVideo",value:function(e){return this.callMethod("loadVideo",e)}},{key:"ready",value:function(){var e=E.get(this)||new s(function(e,t){t(new Error("Unknown player. Probably unloaded."))});return s.resolve(e)}},{key:"addCuePoint",value:function(e){var t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{};return this.callMethod("addCuePoint",{time:e,data:t})}},{key:"removeCuePoint",value:function(e){return this.callMethod("removeCuePoint",e)}},{key:"enableTextTrack",value:function(e,t){if(!e)throw new TypeError("You must pass a language.");return this.callMethod("enableTextTrack",{language:e,kind:t})}},{key:"disableTextTrack",value:function(){return this.callMethod("disableTextTrack")}},{key:"pause",value:function(){return this.callMethod("pause")}},{key:"play",value:function(){return this.callMethod("play")}},{key:"unload",value:function(){return this.callMethod("unload")}},{key:"destroy",value:function(){var t=this;return new s(function(e){E.delete(t),k.delete(t.element),t._originalElement&&(k.delete(t._originalElement),t._originalElement.removeAttribute("data-vimeo-initialized")),t.element&&"IFRAME"===t.element.nodeName&&t.element.remove(),e()})}},{key:"getAutopause",value:function(){return this.get("autopause")}},{key:"setAutopause",value:function(e){return this.set("autopause",e)}},{key:"getColor",value:function(){return this.get("color")}},{key:"setColor",value:function(e){return this.set("color",e)}},{key:"getCuePoints",value:function(){return this.get("cuePoints")}},{key:"getCurrentTime",value:function(){return this.get("currentTime")}},{key:"setCurrentTime",value:function(e){return this.set("currentTime",e)}},{key:"getDuration",value:function(){return this.get("duration")}},{key:"getEnded",value:function(){return this.get("ended")}},{key:"getLoop",value:function(){return this.get("loop")}},{key:"setLoop",value:function(e){return this.set("loop",e)}},{key:"getPaused",value:function(){return this.get("paused")}},{key:"getPlaybackRate",value:function(){return this.get("playbackRate")}},{key:"setPlaybackRate",value:function(e){return this.set("playbackRate",e)}},{key:"getTextTracks",value:function(){return this.get("textTracks")}},{key:"getVideoEmbedCode",value:function(){return this.get("videoEmbedCode")}},{key:"getVideoId",value:function(){return this.get("videoId")}},{key:"getVideoTitle",value:function(){return this.get("videoTitle")}},{key:"getVideoWidth",value:function(){return this.get("videoWidth")}},{key:"getVideoHeight",value:function(){return this.get("videoHeight")}},{key:"getVideoUrl",value:function(){return this.get("videoUrl")}},{key:"getVolume",value:function(){return this.get("volume")}},{key:"setVolume",value:function(e){return this.set("volume",e)}}])&&o(e.prototype,t),n&&o(e,n),Player}();return e||!window.Vimeo||window.Vimeo.Player||(function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document,t=[].slice.call(e.querySelectorAll("[data-vimeo-id], [data-vimeo-url]")),n=function(e){"console"in window&&console.error&&console.error("There was an error creating an embed: ".concat(e))};t.forEach(function(t){try{if(null!==t.getAttribute("data-vimeo-defer"))return;var e=p(t);m(u(e),e,t).then(function(e){return y(e,t)}).catch(n)}catch(e){n(e)}})}(),function(){var o=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document,e=function(e){if(c(e.origin)&&e.data&&"spacechange"===e.data.event)for(var t=o.querySelectorAll("iframe"),n=0;n<t.length;n++)if(t[n].contentWindow===e.source){t[n].parentElement.style.paddingBottom="".concat(e.data.data[0].bottom,"px");break}};window.addEventListener?window.addEventListener("message",e,!1):window.attachEvent&&window.attachEvent("onmessage",e)}()),Player});
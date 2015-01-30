//history.js native html4/html5 bundled with json2
window.JSON||(window.JSON={}),function(){function f(a){return a<10?"0"+a:a}function quote(a){return escapable.lastIndex=0,escapable.test(a)?'"'+a.replace(escapable,function(a){var b=meta[a];return typeof b=="string"?b:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+a+'"'}function str(a,b){var c,d,e,f,g=gap,h,i=b[a];i&&typeof i=="object"&&typeof i.toJSON=="function"&&(i=i.toJSON(a)),typeof rep=="function"&&(i=rep.call(b,a,i));switch(typeof i){case"string":return quote(i);case"number":return isFinite(i)?String(i):"null";case"boolean":case"null":return String(i);case"object":if(!i)return"null";gap+=indent,h=[];if(Object.prototype.toString.apply(i)==="[object Array]"){f=i.length;for(c=0;c<f;c+=1)h[c]=str(c,i)||"null";return e=h.length===0?"[]":gap?"[\n"+gap+h.join(",\n"+gap)+"\n"+g+"]":"["+h.join(",")+"]",gap=g,e}if(rep&&typeof rep=="object"){f=rep.length;for(c=0;c<f;c+=1)d=rep[c],typeof d=="string"&&(e=str(d,i),e&&h.push(quote(d)+(gap?": ":":")+e))}else for(d in i)Object.hasOwnProperty.call(i,d)&&(e=str(d,i),e&&h.push(quote(d)+(gap?": ":":")+e));return e=h.length===0?"{}":gap?"{\n"+gap+h.join(",\n"+gap)+"\n"+g+"}":"{"+h.join(",")+"}",gap=g,e}}"use strict",typeof Date.prototype.toJSON!="function"&&(Date.prototype.toJSON=function(a){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(a){return this.valueOf()});var JSON=window.JSON,cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep;typeof JSON.stringify!="function"&&(JSON.stringify=function(a,b,c){var d;gap="",indent="";if(typeof c=="number")for(d=0;d<c;d+=1)indent+=" ";else typeof c=="string"&&(indent=c);rep=b;if(!b||typeof b=="function"||typeof b=="object"&&typeof b.length=="number")return str("",{"":a});throw new Error("JSON.stringify")}),typeof JSON.parse!="function"&&(JSON.parse=function(text,reviver){function walk(a,b){var c,d,e=a[b];if(e&&typeof e=="object")for(c in e)Object.hasOwnProperty.call(e,c)&&(d=walk(e,c),d!==undefined?e[c]=d:delete e[c]);return reviver.call(a,b,e)}var j;text=String(text),cx.lastIndex=0,cx.test(text)&&(text=text.replace(cx,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))return j=eval("("+text+")"),typeof reviver=="function"?walk({"":j},""):j;throw new SyntaxError("JSON.parse")})}(),function(a,b){"use strict";var c=a.History=a.History||{};if(typeof c.Adapter!="undefined")throw new Error("History.js Adapter has already been loaded...");c.Adapter={handlers:{},_uid:1,uid:function(a){return a._uid||(a._uid=c.Adapter._uid++)},bind:function(a,b,d){var e=c.Adapter.uid(a);c.Adapter.handlers[e]=c.Adapter.handlers[e]||{},c.Adapter.handlers[e][b]=c.Adapter.handlers[e][b]||[],c.Adapter.handlers[e][b].push(d),a["on"+b]=function(a,b){return function(d){c.Adapter.trigger(a,b,d)}}(a,b)},trigger:function(a,b,d){d=d||{};var e=c.Adapter.uid(a),f,g;c.Adapter.handlers[e]=c.Adapter.handlers[e]||{},c.Adapter.handlers[e][b]=c.Adapter.handlers[e][b]||[];for(f=0,g=c.Adapter.handlers[e][b].length;f<g;++f)c.Adapter.handlers[e][b][f].apply(this,[d])},extractEventData:function(a,c){var d=c&&c[a]||b;return d},onDomLoad:function(b){var c=a.setTimeout(function(){b()},2e3);a.onload=function(){clearTimeout(c),b()}}},typeof c.init!="undefined"&&c.init()}(window),function(a,b){"use strict";var c=a.document,d=a.setTimeout||d,e=a.clearTimeout||e,f=a.setInterval||f,g=a.History=a.History||{};if(typeof g.initHtml4!="undefined")throw new Error("History.js HTML4 Support has already been loaded...");g.initHtml4=function(){if(typeof g.initHtml4.initialized!="undefined")return!1;g.initHtml4.initialized=!0,g.enabled=!0,g.savedHashes=[],g.isLastHash=function(a){var b=g.getHashByIndex(),c;return c=a===b,c},g.saveHash=function(a){return g.isLastHash(a)?!1:(g.savedHashes.push(a),!0)},g.getHashByIndex=function(a){var b=null;return typeof a=="undefined"?b=g.savedHashes[g.savedHashes.length-1]:a<0?b=g.savedHashes[g.savedHashes.length+a]:b=g.savedHashes[a],b},g.discardedHashes={},g.discardedStates={},g.discardState=function(a,b,c){var d=g.getHashByState(a),e;return e={discardedState:a,backState:c,forwardState:b},g.discardedStates[d]=e,!0},g.discardHash=function(a,b,c){var d={discardedHash:a,backState:c,forwardState:b};return g.discardedHashes[a]=d,!0},g.discardedState=function(a){var b=g.getHashByState(a),c;return c=g.discardedStates[b]||!1,c},g.discardedHash=function(a){var b=g.discardedHashes[a]||!1;return b},g.recycleState=function(a){var b=g.getHashByState(a);return g.discardedState(a)&&delete g.discardedStates[b],!0},g.emulated.hashChange&&(g.hashChangeInit=function(){g.checkerFunction=null;var b="",d,e,h,i;return g.isInternetExplorer()?(d="historyjs-iframe",e=c.createElement("iframe"),e.setAttribute("id",d),e.style.display="none",c.body.appendChild(e),e.contentWindow.document.open(),e.contentWindow.document.close(),h="",i=!1,g.checkerFunction=function(){if(i)return!1;i=!0;var c=g.getHash()||"",d=g.unescapeHash(e.contentWindow.document.location.hash)||"";return c!==b?(b=c,d!==c&&(h=d=c,e.contentWindow.document.open(),e.contentWindow.document.close(),e.contentWindow.document.location.hash=g.escapeHash(c)),g.Adapter.trigger(a,"hashchange")):d!==h&&(h=d,g.setHash(d,!1)),i=!1,!0}):g.checkerFunction=function(){var c=g.getHash();return c!==b&&(b=c,g.Adapter.trigger(a,"hashchange")),!0},g.intervalList.push(f(g.checkerFunction,g.options.hashChangeInterval)),!0},g.Adapter.onDomLoad(g.hashChangeInit)),g.emulated.pushState&&(g.onHashChange=function(b){var d=b&&b.newURL||c.location.href,e=g.getHashByUrl(d),f=null,h=null,i=null,j;return g.isLastHash(e)?(g.busy(!1),!1):(g.doubleCheckComplete(),g.saveHash(e),e&&g.isTraditionalAnchor(e)?(g.Adapter.trigger(a,"anchorchange"),g.busy(!1),!1):(f=g.extractState(g.getFullUrl(e||c.location.href,!1),!0),g.isLastSavedState(f)?(g.busy(!1),!1):(h=g.getHashByState(f),j=g.discardedState(f),j?(g.getHashByIndex(-2)===g.getHashByState(j.forwardState)?g.back(!1):g.forward(!1),!1):(g.pushState(f.data,f.title,f.url,!1),!0))))},g.Adapter.bind(a,"hashchange",g.onHashChange),g.pushState=function(b,d,e,f){if(g.getHashByUrl(e))throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(f!==!1&&g.busy())return g.pushQueue({scope:g,callback:g.pushState,args:arguments,queue:f}),!1;g.busy(!0);var h=g.createStateObject(b,d,e),i=g.getHashByState(h),j=g.getState(!1),k=g.getHashByState(j),l=g.getHash();return g.storeState(h),g.expectedStateId=h.id,g.recycleState(h),g.setTitle(h),i===k?(g.busy(!1),!1):i!==l&&i!==g.getShortUrl(c.location.href)?(g.setHash(i,!1),!1):(g.saveState(h),g.Adapter.trigger(a,"statechange"),g.busy(!1),!0)},g.replaceState=function(a,b,c,d){if(g.getHashByUrl(c))throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(d!==!1&&g.busy())return g.pushQueue({scope:g,callback:g.replaceState,args:arguments,queue:d}),!1;g.busy(!0);var e=g.createStateObject(a,b,c),f=g.getState(!1),h=g.getStateByIndex(-2);return g.discardState(f,e,h),g.pushState(e.data,e.title,e.url,!1),!0}),g.emulated.pushState&&g.getHash()&&!g.emulated.hashChange&&g.Adapter.onDomLoad(function(){g.Adapter.trigger(a,"hashchange")})},typeof g.init!="undefined"&&g.init()}(window),function(a,b){"use strict";var c=a.console||b,d=a.document,e=a.navigator,f=a.sessionStorage||!1,g=a.setTimeout,h=a.clearTimeout,i=a.setInterval,j=a.clearInterval,k=a.JSON,l=a.alert,m=a.History=a.History||{},n=a.history;k.stringify=k.stringify||k.encode,k.parse=k.parse||k.decode;if(typeof m.init!="undefined")throw new Error("History.js Core has already been loaded...");m.init=function(){return typeof m.Adapter=="undefined"?!1:(typeof m.initCore!="undefined"&&m.initCore(),typeof m.initHtml4!="undefined"&&m.initHtml4(),!0)},m.initCore=function(){if(typeof m.initCore.initialized!="undefined")return!1;m.initCore.initialized=!0,m.options=m.options||{},m.options.hashChangeInterval=m.options.hashChangeInterval||100,m.options.safariPollInterval=m.options.safariPollInterval||500,m.options.doubleCheckInterval=m.options.doubleCheckInterval||500,m.options.storeInterval=m.options.storeInterval||1e3,m.options.busyDelay=m.options.busyDelay||250,m.options.debug=m.options.debug||!1,m.options.initialTitle=m.options.initialTitle||d.title,m.intervalList=[],m.clearAllIntervals=function(){var a,b=m.intervalList;if(typeof b!="undefined"&&b!==null){for(a=0;a<b.length;a++)j(b[a]);m.intervalList=null}},m.debug=function(){(m.options.debug||!1)&&m.log.apply(m,arguments)},m.log=function(){var a=typeof c!="undefined"&&typeof c.log!="undefined"&&typeof c.log.apply!="undefined",b=d.getElementById("log"),e,f,g,h,i;a?(h=Array.prototype.slice.call(arguments),e=h.shift(),typeof c.debug!="undefined"?c.debug.apply(c,[e,h]):c.log.apply(c,[e,h])):e="\n"+arguments[0]+"\n";for(f=1,g=arguments.length;f<g;++f){i=arguments[f];if(typeof i=="object"&&typeof k!="undefined")try{i=k.stringify(i)}catch(j){}e+="\n"+i+"\n"}return b?(b.value+=e+"\n-----\n",b.scrollTop=b.scrollHeight-b.clientHeight):a||l(e),!0},m.getInternetExplorerMajorVersion=function(){var a=m.getInternetExplorerMajorVersion.cached=typeof m.getInternetExplorerMajorVersion.cached!="undefined"?m.getInternetExplorerMajorVersion.cached:function(){var a=3,b=d.createElement("div"),c=b.getElementsByTagName("i");while((b.innerHTML="<!--[if gt IE "+ ++a+"]><i></i><![endif]-->")&&c[0]);return a>4?a:!1}();return a},m.isInternetExplorer=function(){var a=m.isInternetExplorer.cached=typeof m.isInternetExplorer.cached!="undefined"?m.isInternetExplorer.cached:Boolean(m.getInternetExplorerMajorVersion());return a},m.emulated={pushState:!Boolean(a.history&&a.history.pushState&&a.history.replaceState&&!/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i.test(e.userAgent)&&!/AppleWebKit\/5([0-2]|3[0-2])/i.test(e.userAgent)),hashChange:Boolean(!("onhashchange"in a||"onhashchange"in d)||m.isInternetExplorer()&&m.getInternetExplorerMajorVersion()<8)},m.enabled=!m.emulated.pushState,m.bugs={setHash:Boolean(!m.emulated.pushState&&e.vendor==="Apple Computer, Inc."&&/AppleWebKit\/5([0-2]|3[0-3])/.test(e.userAgent)),safariPoll:Boolean(!m.emulated.pushState&&e.vendor==="Apple Computer, Inc."&&/AppleWebKit\/5([0-2]|3[0-3])/.test(e.userAgent)),ieDoubleCheck:Boolean(m.isInternetExplorer()&&m.getInternetExplorerMajorVersion()<8),hashEscape:Boolean(m.isInternetExplorer()&&m.getInternetExplorerMajorVersion()<7)},m.isEmptyObject=function(a){for(var b in a)return!1;return!0},m.cloneObject=function(a){var b,c;return a?(b=k.stringify(a),c=k.parse(b)):c={},c},m.getRootUrl=function(){var a=d.location.protocol+"//"+(d.location.hostname||d.location.host);if(d.location.port||!1)a+=":"+d.location.port;return a+="/",a},m.getBaseHref=function(){var a=d.getElementsByTagName("base"),b=null,c="";return a.length===1&&(b=a[0],c=b.href.replace(/[^\/]+$/,"")),c=c.replace(/\/+$/,""),c&&(c+="/"),c},m.getBaseUrl=function(){var a=m.getBaseHref()||m.getBasePageUrl()||m.getRootUrl();return a},m.getPageUrl=function(){var a=m.getState(!1,!1),b=(a||{}).url||d.location.href,c;return c=b.replace(/\/+$/,"").replace(/[^\/]+$/,function(a,b,c){return/\./.test(a)?a:a+"/"}),c},m.getBasePageUrl=function(){var a=d.location.href.replace(/[#\?].*/,"").replace(/[^\/]+$/,function(a,b,c){return/[^\/]$/.test(a)?"":a}).replace(/\/+$/,"")+"/";return a},m.getFullUrl=function(a,b){var c=a,d=a.substring(0,1);return b=typeof b=="undefined"?!0:b,/[a-z]+\:\/\//.test(a)||(d==="/"?c=m.getRootUrl()+a.replace(/^\/+/,""):d==="#"?c=m.getPageUrl().replace(/#.*/,"")+a:d==="?"?c=m.getPageUrl().replace(/[\?#].*/,"")+a:b?c=m.getBaseUrl()+a.replace(/^(\.\/)+/,""):c=m.getBasePageUrl()+a.replace(/^(\.\/)+/,"")),c.replace(/\#$/,"")},m.getShortUrl=function(a){var b=a,c=m.getBaseUrl(),d=m.getRootUrl();return m.emulated.pushState&&(b=b.replace(c,"")),b=b.replace(d,"/"),m.isTraditionalAnchor(b)&&(b="./"+b),b=b.replace(/^(\.\/)+/g,"./").replace(/\#$/,""),b},m.store={},m.idToState=m.idToState||{},m.stateToId=m.stateToId||{},m.urlToId=m.urlToId||{},m.storedStates=m.storedStates||[],m.savedStates=m.savedStates||[],m.normalizeStore=function(){m.store.idToState=m.store.idToState||{},m.store.urlToId=m.store.urlToId||{},m.store.stateToId=m.store.stateToId||{}},m.getState=function(a,b){typeof a=="undefined"&&(a=!0),typeof b=="undefined"&&(b=!0);var c=m.getLastSavedState();return!c&&b&&(c=m.createStateObject()),a&&(c=m.cloneObject(c),c.url=c.cleanUrl||c.url),c},m.getIdByState=function(a){var b=m.extractId(a.url),c;if(!b){c=m.getStateString(a);if(typeof m.stateToId[c]!="undefined")b=m.stateToId[c];else if(typeof m.store.stateToId[c]!="undefined")b=m.store.stateToId[c];else{for(;;){b=(new Date).getTime()+String(Math.random()).replace(/\D/g,"");if(typeof m.idToState[b]=="undefined"&&typeof m.store.idToState[b]=="undefined")break}m.stateToId[c]=b,m.idToState[b]=a}}return b},m.normalizeState=function(a){var b,c;if(!a||typeof a!="object")a={};if(typeof a.normalized!="undefined")return a;if(!a.data||typeof a.data!="object")a.data={};b={},b.normalized=!0,b.title=a.title||"",b.url=m.getFullUrl(m.unescapeString(a.url||d.location.href)),b.hash=m.getShortUrl(b.url),b.data=m.cloneObject(a.data),b.id=m.getIdByState(b),b.cleanUrl=b.url.replace(/\??\&_suid.*/,""),b.url=b.cleanUrl,c=!m.isEmptyObject(b.data);if(b.title||c)b.hash=m.getShortUrl(b.url).replace(/\??\&_suid.*/,""),/\?/.test(b.hash)||(b.hash+="?"),b.hash+="&_suid="+b.id;return b.hashedUrl=m.getFullUrl(b.hash),(m.emulated.pushState||m.bugs.safariPoll)&&m.hasUrlDuplicate(b)&&(b.url=b.hashedUrl),b},m.createStateObject=function(a,b,c){var d={data:a,title:b,url:c};return d=m.normalizeState(d),d},m.getStateById=function(a){a=String(a);var c=m.idToState[a]||m.store.idToState[a]||b;return c},m.getStateString=function(a){var b,c,d;return b=m.normalizeState(a),c={data:b.data,title:a.title,url:a.url},d=k.stringify(c),d},m.getStateId=function(a){var b,c;return b=m.normalizeState(a),c=b.id,c},m.getHashByState=function(a){var b,c;return b=m.normalizeState(a),c=b.hash,c},m.extractId=function(a){var b,c,d;return c=/(.*)\&_suid=([0-9]+)$/.exec(a),d=c?c[1]||a:a,b=c?String(c[2]||""):"",b||!1},m.isTraditionalAnchor=function(a){var b=!/[\/\?\.]/.test(a);return b},m.extractState=function(a,b){var c=null,d,e;return b=b||!1,d=m.extractId(a),d&&(c=m.getStateById(d)),c||(e=m.getFullUrl(a),d=m.getIdByUrl(e)||!1,d&&(c=m.getStateById(d)),!c&&b&&!m.isTraditionalAnchor(a)&&(c=m.createStateObject(null,null,e))),c},m.getIdByUrl=function(a){var c=m.urlToId[a]||m.store.urlToId[a]||b;return c},m.getLastSavedState=function(){return m.savedStates[m.savedStates.length-1]||b},m.getLastStoredState=function(){return m.storedStates[m.storedStates.length-1]||b},m.hasUrlDuplicate=function(a){var b=!1,c;return c=m.extractState(a.url),b=c&&c.id!==a.id,b},m.storeState=function(a){return m.urlToId[a.url]=a.id,m.storedStates.push(m.cloneObject(a)),a},m.isLastSavedState=function(a){var b=!1,c,d,e;return m.savedStates.length&&(c=a.id,d=m.getLastSavedState(),e=d.id,b=c===e),b},m.saveState=function(a){return m.isLastSavedState(a)?!1:(m.savedStates.push(m.cloneObject(a)),!0)},m.getStateByIndex=function(a){var b=null;return typeof a=="undefined"?b=m.savedStates[m.savedStates.length-1]:a<0?b=m.savedStates[m.savedStates.length+a]:b=m.savedStates[a],b},m.getHash=function(){var a=m.unescapeHash(d.location.hash);return a},m.unescapeString=function(b){var c=b,d;for(;;){d=a.unescape(c);if(d===c)break;c=d}return c},m.unescapeHash=function(a){var b=m.normalizeHash(a);return b=m.unescapeString(b),b},m.normalizeHash=function(a){var b=a.replace(/[^#]*#/,"").replace(/#.*/,"");return b},m.setHash=function(a,b){var c,e,f;return b!==!1&&m.busy()?(m.pushQueue({scope:m,callback:m.setHash,args:arguments,queue:b}),!1):(c=m.escapeHash(a),m.busy(!0),e=m.extractState(a,!0),e&&!m.emulated.pushState?m.pushState(e.data,e.title,e.url,!1):d.location.hash!==c&&(m.bugs.setHash?(f=m.getPageUrl(),m.pushState(null,null,f+"#"+c,!1)):d.location.hash=c),m)},m.escapeHash=function(b){var c=m.normalizeHash(b);return c=a.escape(c),m.bugs.hashEscape||(c=c.replace(/\%21/g,"!").replace(/\%26/g,"&").replace(/\%3D/g,"=").replace(/\%3F/g,"?")),c},m.getHashByUrl=function(a){var b=String(a).replace(/([^#]*)#?([^#]*)#?(.*)/,"$2");return b=m.unescapeHash(b),b},m.setTitle=function(a){var b=a.title,c;b||(c=m.getStateByIndex(0),c&&c.url===a.url&&(b=c.title||m.options.initialTitle));try{d.getElementsByTagName("title")[0].innerHTML=b.replace("<","&lt;").replace(">","&gt;").replace(" & "," &amp; ")}catch(e){}return d.title=b,m},m.queues=[],m.busy=function(a){typeof a!="undefined"?m.busy.flag=a:typeof m.busy.flag=="undefined"&&(m.busy.flag=!1);if(!m.busy.flag){h(m.busy.timeout);var b=function(){var a,c,d;if(m.busy.flag)return;for(a=m.queues.length-1;a>=0;--a){c=m.queues[a];if(c.length===0)continue;d=c.shift(),m.fireQueueItem(d),m.busy.timeout=g(b,m.options.busyDelay)}};m.busy.timeout=g(b,m.options.busyDelay)}return m.busy.flag},m.busy.flag=!1,m.fireQueueItem=function(a){return a.callback.apply(a.scope||m,a.args||[])},m.pushQueue=function(a){return m.queues[a.queue||0]=m.queues[a.queue||0]||[],m.queues[a.queue||0].push(a),m},m.queue=function(a,b){return typeof a=="function"&&(a={callback:a}),typeof b!="undefined"&&(a.queue=b),m.busy()?m.pushQueue(a):m.fireQueueItem(a),m},m.clearQueue=function(){return m.busy.flag=!1,m.queues=[],m},m.stateChanged=!1,m.doubleChecker=!1,m.doubleCheckComplete=function(){return m.stateChanged=!0,m.doubleCheckClear(),m},m.doubleCheckClear=function(){return m.doubleChecker&&(h(m.doubleChecker),m.doubleChecker=!1),m},m.doubleCheck=function(a){return m.stateChanged=!1,m.doubleCheckClear(),m.bugs.ieDoubleCheck&&(m.doubleChecker=g(function(){return m.doubleCheckClear(),m.stateChanged||a(),!0},m.options.doubleCheckInterval)),m},m.safariStatePoll=function(){var b=m.extractState(d.location.href),c;if(!m.isLastSavedState(b))c=b;else return;return c||(c=m.createStateObject()),m.Adapter.trigger(a,"popstate"),m},m.back=function(a){return a!==!1&&m.busy()?(m.pushQueue({scope:m,callback:m.back,args:arguments,queue:a}),!1):(m.busy(!0),m.doubleCheck(function(){m.back(!1)}),n.go(-1),!0)},m.forward=function(a){return a!==!1&&m.busy()?(m.pushQueue({scope:m,callback:m.forward,args:arguments,queue:a}),!1):(m.busy(!0),m.doubleCheck(function(){m.forward(!1)}),n.go(1),!0)},m.go=function(a,b){var c;if(a>0)for(c=1;c<=a;++c)m.forward(b);else{if(!(a<0))throw new Error("History.go: History.go requires a positive or negative integer passed.");for(c=-1;c>=a;--c)m.back(b)}return m};if(m.emulated.pushState){var o=function(){};m.pushState=m.pushState||o,m.replaceState=m.replaceState||o}else m.onPopState=function(b,c){var e=!1,f=!1,g,h;return m.doubleCheckComplete(),g=m.getHash(),g?(h=m.extractState(g||d.location.href,!0),h?m.replaceState(h.data,h.title,h.url,!1):(m.Adapter.trigger(a,"anchorchange"),m.busy(!1)),m.expectedStateId=!1,!1):(e=m.Adapter.extractEventData("state",b,c)||!1,e?f=m.getStateById(e):m.expectedStateId?f=m.getStateById(m.expectedStateId):f=m.extractState(d.location.href),f||(f=m.createStateObject(null,null,d.location.href)),m.expectedStateId=!1,m.isLastSavedState(f)?(m.busy(!1),!1):(m.storeState(f),m.saveState(f),m.setTitle(f),m.Adapter.trigger(a,"statechange"),m.busy(!1),!0))},m.Adapter.bind(a,"popstate",m.onPopState),m.pushState=function(b,c,d,e){if(m.getHashByUrl(d)&&m.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(e!==!1&&m.busy())return m.pushQueue({scope:m,callback:m.pushState,args:arguments,queue:e}),!1;m.busy(!0);var f=m.createStateObject(b,c,d);return m.isLastSavedState(f)?m.busy(!1):(m.storeState(f),m.expectedStateId=f.id,n.pushState(f.id,f.title,f.url),m.Adapter.trigger(a,"popstate")),!0},m.replaceState=function(b,c,d,e){if(m.getHashByUrl(d)&&m.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(e!==!1&&m.busy())return m.pushQueue({scope:m,callback:m.replaceState,args:arguments,queue:e}),!1;m.busy(!0);var f=m.createStateObject(b,c,d);return m.isLastSavedState(f)?m.busy(!1):(m.storeState(f),m.expectedStateId=f.id,n.replaceState(f.id,f.title,f.url),m.Adapter.trigger(a,"popstate")),!0};if(f){try{m.store=k.parse(f.getItem("History.store"))||{}}catch(p){m.store={}}m.normalizeStore()}else m.store={},m.normalizeStore();m.Adapter.bind(a,"beforeunload",m.clearAllIntervals),m.Adapter.bind(a,"unload",m.clearAllIntervals),m.saveState(m.storeState(m.extractState(d.location.href,!0))),f&&(m.onUnload=function(){var a,b;try{a=k.parse(f.getItem("History.store"))||{}}catch(c){a={}}a.idToState=a.idToState||{},a.urlToId=a.urlToId||{},a.stateToId=a.stateToId||{};for(b in m.idToState){if(!m.idToState.hasOwnProperty(b))continue;a.idToState[b]=m.idToState[b]}for(b in m.urlToId){if(!m.urlToId.hasOwnProperty(b))continue;a.urlToId[b]=m.urlToId[b]}for(b in m.stateToId){if(!m.stateToId.hasOwnProperty(b))continue;a.stateToId[b]=m.stateToId[b]}m.store=a,m.normalizeStore(),f.setItem("History.store",k.stringify(a))},m.intervalList.push(i(m.onUnload,m.options.storeInterval)),m.Adapter.bind(a,"beforeunload",m.onUnload),m.Adapter.bind(a,"unload",m.onUnload));if(!m.emulated.pushState){m.bugs.safariPoll&&m.intervalList.push(i(m.safariStatePoll,m.options.safariPollInterval));if(e.vendor==="Apple Computer, Inc."||(e.appCodeName||"")==="Mozilla")m.Adapter.bind(a,"hashchange",function(){m.Adapter.trigger(a,"popstate")}),m.getHash()&&m.Adapter.onDomLoad(function(){m.Adapter.trigger(a,"hashchange")})}},m.init()}(window)
var History = window.History;
/*jshint browser:true, devel:true, prototypejs:true */
(function () {
    "use strict";

    /**
     * @name Locator
     * @namespace
     */
    var Locator = window.Locator = {};

    Locator.defaultSearchSettings = {
        //css selectors search uses to attach its components too
        selectors : {
            map : '.loc-srch-res-map',
            list : '.loc-srch-res-list',
            teaser : '.loc-teaser',
            form : '.loc-srch-form',
            loader : '.loc-loader',
            trigger : '.loc-trigger',
            results : '.loc-srch-res'
        },
        // if 1 map will be fixed to top of viewport when page is scrolled
        stickyMap : 0,
        baseUrl : '/'
    };

    /**
     * @class Form
     */
    Locator.Form = Class.create({

        /**
         * @constructor
         * @param {HTMLElement} el
         * @param {Locator.Search} search
         */
        initialize: function (el, search) {

            this.settings = {
                selectors : {
                    loader : '.loc-loader'
                }
            };

            this.el = el;
            this.search = search;
            var self = this;


            Event.observe(el, 'submit', function (event) {

                var params = self.getParams();

                if (self.isValid()) {
                    self.submit(params);
                }

                Event.stop(event);
            });
        },


        /**
         * validate form, none by default
         * @returns {boolean}
         */
        isValid: function () {
            return true;
        },


        /**
         * get the form parameters
         *
         * @returns {*}
         */
        getParams: function() {
            var params = this.el.serialize().toQueryParams();

            //unset empty parameters
            for (var key in params) {
                if (params.hasOwnProperty(key) && params[key] != 'undefined' && params[key] == '') {
                    delete params[key];
                }
            }

            return params;
        },


        /**
         * Submit search form
         * @param params
         */
        submit: function(params) {
            var self = this;
            self.startLoader();
            self.search.findLocations(params, function () {
                self.stopLoader();
            });
        },

        /**
         * Make loader visible to user
         */
        startLoader: function () {
            this.el.select(this.settings.selectors.loader).each(function (el) {
                el.addClassName('is-loading');
            });
        },

        /**
         * Hide loader from user
         */
        stopLoader: function () {
            this.el.select(this.settings.selectors.loader).each(function (el) {
                el.removeClassName('is-loading');
            });
        }
    });

    /**
     * @class Map
     */
    Locator.Map = Class.create({

        /**
         * @constructor
         * @param {HTMLElement} el
         */
        initialize: function (el) {
            var theme = this.getTheme();

            this.el = el;
            this.defaults = {
                zoom: 15,
                scrollwheel : false,
                mapTypeId : google.maps.MapTypeId.ROADMAP,
                mapTypeControl : false,
                mapTypeControlOptions : {
                    style : google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position : google.maps.ControlPosition.BOTTOM_CENTER,
                    mapTypeIds : [google.maps.MapTypeId.ROADMAP, 'locator']
                },
                streetViewControl : false,
                streetViewControlOptions : {
                    position : google.maps.ControlPosition.LEFT_TOP
                }
            };
            this.map = new google.maps.Map(el, this.defaults);
            this.markers = [];
            this.infowindows = [];

            this.stoppers = [];

            this.settings = {
                maxZoom : 15,
                baseUrl : Locator.defaultSearchSettings.baseUrl,
                markerIcon : 'M25,0C11.191,0,0,11.194,0,25c0,23.87,25,55,25,55s25-31.13,25-55C50,11.194,38.807,0,25,0z M25,38.8c-7.457,0-13.5-6.044-13.5-13.5S17.543,11.8,25,11.8c7.455,0,13.5,6.044,13.5,13.5S32.455,38.8,25,38.8z',
                markerColour : '#3399cc',
                crosshairIcon: 'M27.5,56.738V62h9v-5.307c10.207-1.9,18.24-9.968,20.09-20.193h5.16v-9h-5.16C54.74,17.275,46.707,9.208,36.5,7.307V2h-9v5.262C17.174,9.077,9.025,17.192,7.16,27.5H2v9h5.16C9.025,46.808,17.174,54.923,27.5,56.738z M31.875,13.875C41.869,13.875,50,22.006,50,32c0,9.994-8.131,18.125-18.125,18.125S13.75,41.994,13.75,32C13.75,22.006,21.881,13.875,31.875,13.875z'
            };



            if (theme) {
                var styledMap = new google.maps.StyledMapType(theme, { name: "Locator" });
                this.map.mapTypes.set('locator', styledMap);
                this.map.setMapTypeId('locator');
            }
        },

        /**
         * Render locations on the map and trigger actions
         *
         * @param locations
         */
        renderLocations: function (locations, coords) {

            var latlngbounds = new google.maps.LatLngBounds(),
                show = 0,
                self = this;

            this.clearOverlays();

            // If point coords have been passed render the point marker
            if (coords) {
                var lat = coords[1], long = coords[0];

                var loc = new google.maps.LatLng(lat, long);

                self.markers.point = new google.maps.Marker({
                    position: loc,
                    icon: self.getCrosshairImage(),
                    map: self.map,
                    zIndex: 0
                });

                latlngbounds.extend( loc );
            }

            for (var key in locations) {
                if (locations.hasOwnProperty(key)) {

                    var l = locations[key];
                    var loc = new google.maps.LatLng(l.latitude, l.longitude);

                    self.infowindows[l.id] = new google.maps.InfoWindow({
                        content: '<div id="content"><span class="loader loc-infowindow-loader is-loading">loading</span></div>'
                    });
                    self.markers[l.id] = new google.maps.Marker({
                        position: loc,
                        map: self.map,
                        title: l.title,
                        icon: self.getMarkerImage(l),
                        // shadow: shadow,
                        // shape: shape,
                        animation: google.maps.Animation.DROP
                    });


                    self.markers[l.id].id = l.id;

                    google.maps.event.addListener(self.markers[l.id], 'click', function() {
                        self.showInfoWindow(this.id);
                    });

                    latlngbounds.extend( loc );
                    show = 1;
                }
            }

            self.map.fitBounds( latlngbounds );
            self.checkMaxZoom();

            google.maps.event.trigger(self.map, 'resize');
            self.loadInfoWindows();
        },

        /**
         * Check that the map is not zoomed in to far
         */
        checkMaxZoom: function(){
            var self = this;
            if (self.settings.maxZoom) {
                //when the map loads, make sure it hasn't zoomed in to far, if it has zoom out
                //@todo, configure the zoom level in admin
                var listener = google.maps.event.addListener(self.map, "idle", function() {
                    if (self.map.getZoom() > self.settings.maxZoom) self.map.setZoom(self.settings.maxZoom);
                    google.maps.event.removeListener(listener);
                });
            }
        },

        /**
         * Clear current markers from map
         */
        clearOverlays: function(){
            for (var key in this.markers) {
                if (this.markers.hasOwnProperty(key)) {
                    this.markers[key].setMap(null);
                }
            }
        },

        /**
         * Hide all currently visible info windows
         */
        hideInfoWindows: function(){
            for (var key in this.infowindows) {
                if (this.infowindows.hasOwnProperty(key)) {
                    this.infowindows[key].close();
                }
            }
        },

        /**
         * Show an info window based on an id
         *
         * @param {int} id
         */
        showInfoWindow: function(id){
            var self = this;

            self.hideInfoWindows();
            self.infowindows[id].open(self.map,self.markers[id]);

            if (!self.infowindows[id].isSet) {
                new Ajax.Request(this.settings.baseUrl+'locator/search/infowindow/id/'+id, {
                    method : 'get',
                    onFailure: function () {
                        alert('failed');
                    },
                    onSuccess: function (t) {
                        self.setInfoWindowContent(id, t.responseText);
                    }
                });
            }
        },

        /**
         * Set the content of an info window for a given location
         *
         * @param {int} id
         * @param {string} content
         */
        setInfoWindowContent: function(id, content){
            this.infowindows[id].setContent(content);
            this.infowindows[id].isSet = 1;
        },

        /**
         * Load content for all infowindows
         */
        loadInfoWindows: function(){
            var self = this,
                ids = [];

            for (var key in self.infowindows) {
                if (self.infowindows.hasOwnProperty(key)) {
                    if (!self.infowindows[key].isSet && self.markers[key]) {
                        ids.push(key);
                    }
                }
            }

            new Ajax.Request(this.settings.baseUrl+'locator/search/infowindows/?ids='+ids.join(), {
                method : 'get',
                onFailure: function () {
                    alert('failed');
                },
                onSuccess: function (t) {
                    var windows = JSON.parse(t.responseText);

                    for (var key in windows) {
                        if (windows.hasOwnProperty(key) && windows[key] != 'undefined') {
                            self.setInfoWindowContent(key,windows[key]);
                        }
                    }
                }
            });
        },

        /**
         * Bounce a marker to highlight it
         *
         * @param {int} id
         */
        highlightMarker: function(id){
            var self = this;
            if (self.markers[id].getAnimation() === null) {
                self.markers[id].setAnimation(google.maps.Animation.BOUNCE);
                self.stoppers[id] = setTimeout(function(){
                    self.markers[id].setAnimation(null);
                }, 720);
            }
        },

        /**
         * Get a google maps marker
         *
         * @param {Object} l object containing location data
         * @returns {google.maps.MarkerImage}
         */
        getMarkerImage: function(){

            return {
                path: this.settings.markerIcon,
                scale:.5,
                strokeWeight: 1,
                strokeColor: '#666',
                strokeOpacity:.5,
                fillColor: this.settings.markerColour,
                anchor: new google.maps.Point(25,75),
                fillOpacity: 1
            };
        },


        /**
         * Get a google maps marker for point of search crosshair
         */
        getCrosshairImage: function ()
        {
            return {
                path: this.settings.crosshairIcon,
                scale: 0.4,
                strokeWeight: 0,
                strokeColor: 'black',
                strokeOpacity: 0,
                fillColor: '#676157',
                fillOpacity: 1,
                anchor: new google.maps.Point(31, 31)
            };
        },

        /**
         * Return theme settings, returns false by default but can be overridden to theme map
         *
         * @returns {boolean | Object}
         */
        getTheme : function() {
            return false;
        }
    });

    /**
     * @class List
     */
    Locator.List = Class.create({

        /**
         * @constructor
         * @param {HTMLElement} el
         */
        initialize: function (el) {
            this.el = el;
        },

        /**
         * Update list content
         *
         * @param {string} text
         */
        update: function (text) {
            this.el.update(text);
        }
    });


    /**
     * @class Search
     */
    Locator.Search = Class.create({

        /**
         * Construct the search class
         *
         * @constructor
         * @param {Object} options
         */
        initialize: function(options) {

            if (options) {
                //if override settings are
                if (options.settings) {
                    this.settings = $H(Locator.defaultSearchSettings).merge(options.settings);
                }

                if (options.map) {
                    this.map = options.map;
                }
            }

            //if options were not passed to the search class, use locator default
            if (!this.settings) {
                this.settings = Locator.defaultSearchSettings;
            }

            //if map has not already been set from options set it now
            if (!this.map && $$(this.settings.selectors.map).first()) {
                this.map = new Locator.Map($$(this.settings.selectors.map).first());
            }

            if ($$(this.settings.selectors.list).first()) {
                this.list = new Locator.List($$(this.settings.selectors.list).first());
            }

            this.forms = [];

            var self = this;

            this.initScroll();

            $$(this.settings.selectors.form).each(function (el) {
                self.forms.push(new Locator.Form(el, self));
            });

            //Bind map rendering to StateChange Event
            window.History.Adapter.bind(window, 'statechange', function () {

                var State = History.getState();

                if (State.data.locations && State.data.locations.length) {

                    if (self.list) {
                        self.list.update(State.data.output);
                    }

                    if (self.map) {
                        if (State.data.search_point) {
                            self.map.renderLocations(State.data.locations, State.data.search_point.coords);
                        } else {
                            self.map.renderLocations(State.data.locations);
                        }
                    }
                }
                self.initEvents();
            });
        },

        /**
         * Set initial history state when locations are not loaded from search, this will trigger map render
         *
         * @param {Object} data
         * @returns {Object}
         */
        initState: function(data){
            //inject a random parameter to query string so state always changes on first load
            var href = window.location.href+'&rand='+Math.random();
            var state = {};

            state.output = this.list.el.innerHTML;
            state.href = href.toQueryParams();

            if (data.locations !== '') {
                state.locations = this.parseLocationsJson(data.locations);
            }

            if (data.search_point) {
                state.search_point = data.search_point;
            }

            if (!state.locations.length) {
                this.toggleNoResults(true);
            }

            //reset the hash for old browsers to stop history.js errors
            if (History.getHash()) {
                window.location.hash = '';
            }

            History.replaceState(state,
                this.getSearchTitle(state.locations),
                window.location.search
            );

            return this;
        },

        /**
         * Make an ajax request to the server to find locations based on given query params
         *
         * @param {(string|Object)} query
         * @param callback
         * @returns {Object}
         */
        findLocations: function (query, callback) {

            var self = this;
            var href;

            if (typeof (query) === 'object') {
                query = $H(query).toQueryString();
            }

            href = this.settings.baseUrl+"locator/search/?" + query;

            new Ajax.Request(href +'&xhr=1', {

                method : 'get',
                onFailure: function () {
                    alert('search failed');
                },
                onSuccess: function (t) {
                    var result = self.parseSearchJson(t.responseText);
                    result.search = href.toQueryParams();

                    self.toggleNoResults(false);
                    if (result.error === true) {
                        if (result.error_type === 'noresults') {
                            self.toggleNoResults(true);
                        } else {
                            alert(result.message);
                        }
                    } else if (result.locations.length) {
                        History.pushState(result, self.getSearchTitle(result.locations), '?' + query);
                    } else {
                        alert('an error occured');
                    }

                    if (typeof callback === "function") {
                        callback.call();
                    }
                }
            });

            return this;
        },

        /**
         * Parse search result from server
         *
         * @param {string} string
         * @returns {Object}
         */
        parseSearchJson: function (string) {
            var search = JSON.parse(string);
            if (search.locations) {
                search.locations = this.parseLocationsJson(search.locations);
            }
            return search;
        },

        /**
         * Parse location json object
         *
         * @param {string} string
         * @returns {Array}
         */
        parseLocationsJson: function (string) {

            var locations = JSON.parse(string);
            var temp = [];

            for (var key in locations) {
                if (locations.hasOwnProperty(key) && locations[key] != 'undefined') {
                    temp.push(locations[key]);
                }
            }
            return temp;
        },

        /**
         * Show or hide no results page based on boolean parameter
         *
         * @param {boolean} empty
         * @returns {Object}
         */
        toggleNoResults: function (empty) {
            var els = $$(this.settings.selectors.results);
            if (empty) {
                els.each(function(el){
                    el.addClassName('is-no-results');
                });
            } else {
                els.each(function(el){
                    el.removeClassName('is-no-results');
                });
            }
            return this;
        },

        /**
         * Attach events to search UI
         *
         * @returns {Object}
         */
        initEvents: function() {
            var self = this;

            $$(self.settings.selectors.teaser).invoke('observe', 'click', function(){
                var id = this.readAttribute('data-id');
                self.map.showInfoWindow(id);
            });

            $$(self.settings.selectors.teaser).invoke('observe', 'mouseover', function() {
                var id = this.readAttribute('data-id');
                self.map.highlightMarker(id);
            });

            // Attach onclick events to search triggers
            $$(self.settings.selectors.trigger).invoke('observe', 'click', function(event){
                var el = Event.element(event);

                if (!el.readAttribute('href')) {
                    for (var i=0;i<10;i++) {
                        el = el.up();
                        if (el.readAttribute('href')) {
                            break;
                        }
                    }
                }

                var href = el.readAttribute('href');

                self.forms[0].startLoader();
                self.findLocations( href.toQueryParams(), function () {
                    self.forms[0].stopLoader();
                });
                Event.stop(event);
            });
            setTimeout(function(){
                google.maps.event.addListener(self.map.map, 'zoom_changed', function() {
                    self.hideNonVisible();
                });

                google.maps.event.addListener(self.map.map, 'dragend', function() {
                    self.hideNonVisible();
                });
            }, 1000);

            return this;
        },

        /**
         * Hide all markers not currently in view port and the matching item in list
         *
         * @returns {Object}
         */
        hideNonVisible: function(){
            var map = this.map;

            for (var key in map.markers) {
                if (map.markers.hasOwnProperty(key)) {
                    var marker = map.markers[key];
                    var teaser = $$(this.settings.selectors.list+' '+this.settings.selectors.teaser+'[data-id='+key+']').first();

                    if (!map.map.getBounds().contains(marker.getPosition())) {
                        if (teaser && !teaser.hasClassName('loc-closest')) {
                            teaser.addClassName('is-hidden');
                        }
                        marker.setVisible(false);
                    } else {
                        if (teaser) {
                            teaser.removeClassName('is-hidden');
                        }

                        marker.setVisible(true);
                    }
                }
            }

            return this;
        },

        /**
         * Create string to be displayed in the page title after a search has been performed
         *
         * @param {Array} locations
         * @returns {string}
         */
        getSearchTitle:function (locations){
            return "Search: " + locations.length + " Locations";
        },

        /**
         * Init scroll behaviour
         *
         * @returns {Object}
         */
        initScroll: function(){

            if (this.settings.stickyMap) {
                var map = $$('.loc-srch-res-map-wrap').first();
                var results = $$(this.settings.selectors.results).first();

                Event.observe(document, "scroll", function() {
                    if (results.viewportOffset().top < 1) {
                        map.addClassName('is-fixed');
                    } else {
                        map.removeClassName('is-fixed');
                    }
                });
            }
            return this;
        }
    });
})();
/* eslint-disable */
var md5;
export default md5;

(function () {
  var HxOverrides = function () { }
  HxOverrides.__name__ = true;
  HxOverrides.dateStr = function (date) {
    var m = date.getMonth() + 1;
    var d = date.getDate();
    var h = date.getHours();
    var mi = date.getMinutes();
    var s = date.getSeconds();
    return date.getFullYear() + "-" + (m < 10 ? "0" + m : "" + m) + "-" + (d < 10 ? "0" + d : "" + d) + " " + (h < 10 ? "0" + h : "" + h) + ":" + (mi < 10 ? "0" + mi : "" + mi) + ":" + (s < 10 ? "0" + s : "" + s);
  }
  HxOverrides.strDate = function (s) {
    switch (s.length) {
      case 8:
        var k = s.split(":");
        var d = new Date();
        d.setTime(0);
        d.setUTCHours(k[0]);
        d.setUTCMinutes(k[1]);
        d.setUTCSeconds(k[2]);
        return d;
      case 10:
        var k = s.split("-");
        return new Date(k[0], k[1] - 1, k[2], 0, 0, 0);
      case 19:
        var k = s.split(" ");
        var y = k[0].split("-");
        var t = k[1].split(":");
        return new Date(y[0], y[1] - 1, y[2], t[0], t[1], t[2]);
      default:
        throw "Invalid date format : " + s;
    }
  }
  HxOverrides.cca = function (s, index) {
    var x = s.charCodeAt(index);
    if (x != x) return undefined;
    return x;
  }
  HxOverrides.substr = function (s, pos, len) {
    if (pos != null && pos != 0 && len != null && len < 0) return "";
    if (len == null) len = s.length;
    if (pos < 0) {
      pos = s.length + pos;
      if (pos < 0) pos = 0;
    } else if (len < 0) len = s.length + len - pos;
    return s.substr(pos, len);
  }
  HxOverrides.remove = function (a, obj) {
    var i = 0;
    var l = a.length;
    while (i < l) {
      if (a[i] == obj) {
        a.splice(i, 1);
        return true;
      }
      i++;
    }
    return false;
  }
  HxOverrides.iter = function (a) {
    return {
      cur: 0, arr: a, hasNext: function () {
        return this.cur < this.arr.length;
      }, next: function () {
        return this.arr[this.cur++];
      }
    };
  }
  var IntIter = function (min, max) {
    this.min = min;
    this.max = max;
  };
  IntIter.__name__ = true;
  IntIter.prototype = {
    next: function () {
      return this.min++;
    }
    , hasNext: function () {
      return this.min < this.max;
    }
    , __class__: IntIter
  }
  var Std = function () { }
  Std.__name__ = true;
  Std["is"] = function (v, t) {
    return js.Boot.__instanceof(v, t);
  }
  Std.string = function (s) {
    return js.Boot.__string_rec(s, "");
  }
  Std["int"] = function (x) {
    return x | 0;
  }
  Std.parseInt = function (x) {
    var v = parseInt(x, 10);
    if (v == 0 && (HxOverrides.cca(x, 1) == 120 || HxOverrides.cca(x, 1) == 88)) v = parseInt(x);
    if (isNaN(v)) return null;
    return v;
  }
  Std.parseFloat = function (x) {
    return parseFloat(x);
  }
  Std.random = function (x) {
    return Math.floor(Math.random() * x);
  }
  var com = com || {}
  if (!com.wiris) com.wiris = {}
  if (!com.wiris.js) com.wiris.js = {}
  com.wiris.js.JsPluginTools = function () {
    this.tryReady();
  };
  com.wiris.js.JsPluginTools.__name__ = true;
  com.wiris.js.JsPluginTools.main = function () {
    var ev;
    ev = com.wiris.js.JsPluginTools.getInstance();
    haxe.Timer.delay($bind(ev, ev.tryReady), 100);
  }
  com.wiris.js.JsPluginTools.getInstance = function () {
    if (com.wiris.js.JsPluginTools.instance == null) com.wiris.js.JsPluginTools.instance = new com.wiris.js.JsPluginTools();
    return com.wiris.js.JsPluginTools.instance;
  }
  com.wiris.js.JsPluginTools.bypassEncapsulation = function () {
    if (window.com == null) window.com = {};
    if (window.com.wiris == null) window.com.wiris = {};
    if (window.com.wiris.js == null) window.com.wiris.js = {};
    if (window.com.wiris.js.JsPluginTools == null) window.com.wiris.js.JsPluginTools = com.wiris.js.JsPluginTools.getInstance();
  }
  com.wiris.js.JsPluginTools.prototype = {
    md5encode: function (content) {
      return haxe.Md5.encode(content);
    }
    , doLoad: function () {
      this.ready = true;
      com.wiris.js.JsPluginTools.instance = this;
      com.wiris.js.JsPluginTools.bypassEncapsulation();
    }
    , tryReady: function () {
      this.ready = false;
      if (js.Lib.document.readyState) {
        this.doLoad();
        this.ready = true;
      }
      if (!this.ready) haxe.Timer.delay($bind(this, this.tryReady), 100);
    }
    , __class__: com.wiris.js.JsPluginTools
  }
  var haxe = haxe || {}
  haxe.Log = function () { }
  haxe.Log.__name__ = true;
  haxe.Log.trace = function (v, infos) {
    js.Boot.__trace(v, infos);
  }
  haxe.Log.clear = function () {
    js.Boot.__clear_trace();
  }
  haxe.Md5 = function () {
  };
  haxe.Md5.__name__ = true;
  haxe.Md5.encode = function (s) {
    return new haxe.Md5().doEncode(s);
  }
  haxe.Md5.prototype = {
    doEncode: function (str) {
      var x = this.str2blks(str);
      var a = 1732584193;
      var b = -271733879;
      var c = -1732584194;
      var d = 271733878;
      var step;
      var i = 0;
      while (i < x.length) {
        var olda = a;
        var oldb = b;
        var oldc = c;
        var oldd = d;
        step = 0;
        a = this.ff(a, b, c, d, x[i], 7, -680876936);
        d = this.ff(d, a, b, c, x[i + 1], 12, -389564586);
        c = this.ff(c, d, a, b, x[i + 2], 17, 606105819);
        b = this.ff(b, c, d, a, x[i + 3], 22, -1044525330);
        a = this.ff(a, b, c, d, x[i + 4], 7, -176418897);
        d = this.ff(d, a, b, c, x[i + 5], 12, 1200080426);
        c = this.ff(c, d, a, b, x[i + 6], 17, -1473231341);
        b = this.ff(b, c, d, a, x[i + 7], 22, -45705983);
        a = this.ff(a, b, c, d, x[i + 8], 7, 1770035416);
        d = this.ff(d, a, b, c, x[i + 9], 12, -1958414417);
        c = this.ff(c, d, a, b, x[i + 10], 17, -42063);
        b = this.ff(b, c, d, a, x[i + 11], 22, -1990404162);
        a = this.ff(a, b, c, d, x[i + 12], 7, 1804603682);
        d = this.ff(d, a, b, c, x[i + 13], 12, -40341101);
        c = this.ff(c, d, a, b, x[i + 14], 17, -1502002290);
        b = this.ff(b, c, d, a, x[i + 15], 22, 1236535329);
        a = this.gg(a, b, c, d, x[i + 1], 5, -165796510);
        d = this.gg(d, a, b, c, x[i + 6], 9, -1069501632);
        c = this.gg(c, d, a, b, x[i + 11], 14, 643717713);
        b = this.gg(b, c, d, a, x[i], 20, -373897302);
        a = this.gg(a, b, c, d, x[i + 5], 5, -701558691);
        d = this.gg(d, a, b, c, x[i + 10], 9, 38016083);
        c = this.gg(c, d, a, b, x[i + 15], 14, -660478335);
        b = this.gg(b, c, d, a, x[i + 4], 20, -405537848);
        a = this.gg(a, b, c, d, x[i + 9], 5, 568446438);
        d = this.gg(d, a, b, c, x[i + 14], 9, -1019803690);
        c = this.gg(c, d, a, b, x[i + 3], 14, -187363961);
        b = this.gg(b, c, d, a, x[i + 8], 20, 1163531501);
        a = this.gg(a, b, c, d, x[i + 13], 5, -1444681467);
        d = this.gg(d, a, b, c, x[i + 2], 9, -51403784);
        c = this.gg(c, d, a, b, x[i + 7], 14, 1735328473);
        b = this.gg(b, c, d, a, x[i + 12], 20, -1926607734);
        a = this.hh(a, b, c, d, x[i + 5], 4, -378558);
        d = this.hh(d, a, b, c, x[i + 8], 11, -2022574463);
        c = this.hh(c, d, a, b, x[i + 11], 16, 1839030562);
        b = this.hh(b, c, d, a, x[i + 14], 23, -35309556);
        a = this.hh(a, b, c, d, x[i + 1], 4, -1530992060);
        d = this.hh(d, a, b, c, x[i + 4], 11, 1272893353);
        c = this.hh(c, d, a, b, x[i + 7], 16, -155497632);
        b = this.hh(b, c, d, a, x[i + 10], 23, -1094730640);
        a = this.hh(a, b, c, d, x[i + 13], 4, 681279174);
        d = this.hh(d, a, b, c, x[i], 11, -358537222);
        c = this.hh(c, d, a, b, x[i + 3], 16, -722521979);
        b = this.hh(b, c, d, a, x[i + 6], 23, 76029189);
        a = this.hh(a, b, c, d, x[i + 9], 4, -640364487);
        d = this.hh(d, a, b, c, x[i + 12], 11, -421815835);
        c = this.hh(c, d, a, b, x[i + 15], 16, 530742520);
        b = this.hh(b, c, d, a, x[i + 2], 23, -995338651);
        a = this.ii(a, b, c, d, x[i], 6, -198630844);
        d = this.ii(d, a, b, c, x[i + 7], 10, 1126891415);
        c = this.ii(c, d, a, b, x[i + 14], 15, -1416354905);
        b = this.ii(b, c, d, a, x[i + 5], 21, -57434055);
        a = this.ii(a, b, c, d, x[i + 12], 6, 1700485571);
        d = this.ii(d, a, b, c, x[i + 3], 10, -1894986606);
        c = this.ii(c, d, a, b, x[i + 10], 15, -1051523);
        b = this.ii(b, c, d, a, x[i + 1], 21, -2054922799);
        a = this.ii(a, b, c, d, x[i + 8], 6, 1873313359);
        d = this.ii(d, a, b, c, x[i + 15], 10, -30611744);
        c = this.ii(c, d, a, b, x[i + 6], 15, -1560198380);
        b = this.ii(b, c, d, a, x[i + 13], 21, 1309151649);
        a = this.ii(a, b, c, d, x[i + 4], 6, -145523070);
        d = this.ii(d, a, b, c, x[i + 11], 10, -1120210379);
        c = this.ii(c, d, a, b, x[i + 2], 15, 718787259);
        b = this.ii(b, c, d, a, x[i + 9], 21, -343485551);
        a = this.addme(a, olda);
        b = this.addme(b, oldb);
        c = this.addme(c, oldc);
        d = this.addme(d, oldd);
        i += 16;
      }
      return this.rhex(a) + this.rhex(b) + this.rhex(c) + this.rhex(d);
    }
    , ii: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitXOR(c, this.bitOR(b, ~d)), a, b, x, s, t);
    }
    , hh: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitXOR(this.bitXOR(b, c), d), a, b, x, s, t);
    }
    , gg: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitOR(this.bitAND(b, d), this.bitAND(c, ~d)), a, b, x, s, t);
    }
    , ff: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitOR(this.bitAND(b, c), this.bitAND(~b, d)), a, b, x, s, t);
    }
    , cmn: function (q, a, b, x, s, t) {
      return this.addme(this.rol(this.addme(this.addme(a, q), this.addme(x, t)), s), b);
    }
    , rol: function (num, cnt) {
      return num << cnt | num >>> 32 - cnt;
    }
    , str2blks: function (str) {
      var nblk = (str.length + 8 >> 6) + 1;
      var blks = new Array();
      var _g1 = 0, _g = nblk * 16;
      while (_g1 < _g) {
        var i = _g1++;
        blks[i] = 0;
      }
      var i = 0;
      while (i < str.length) {
        blks[i >> 2] |= HxOverrides.cca(str, i) << (str.length * 8 + i) % 4 * 8;
        i++;
      }
      blks[i >> 2] |= 128 << (str.length * 8 + i) % 4 * 8;
      var l = str.length * 8;
      var k = nblk * 16 - 2;
      blks[k] = l & 255;
      blks[k] |= (l >>> 8 & 255) << 8;
      blks[k] |= (l >>> 16 & 255) << 16;
      blks[k] |= (l >>> 24 & 255) << 24;
      return blks;
    }
    , rhex: function (num) {
      var str = "";
      var hex_chr = "0123456789abcdef";
      var _g = 0;
      while (_g < 4) {
        var j = _g++;
        str += hex_chr.charAt(num >> j * 8 + 4 & 15) + hex_chr.charAt(num >> j * 8 & 15);
      }
      return str;
    }
    , addme: function (x, y) {
      var lsw = (x & 65535) + (y & 65535);
      var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
      return msw << 16 | lsw & 65535;
    }
    , bitAND: function (a, b) {
      var lsb = a & 1 & (b & 1);
      var msb31 = a >>> 1 & b >>> 1;
      return msb31 << 1 | lsb;
    }
    , bitXOR: function (a, b) {
      var lsb = a & 1 ^ b & 1;
      var msb31 = a >>> 1 ^ b >>> 1;
      return msb31 << 1 | lsb;
    }
    , bitOR: function (a, b) {
      var lsb = a & 1 | b & 1;
      var msb31 = a >>> 1 | b >>> 1;
      return msb31 << 1 | lsb;
    }
    , __class__: haxe.Md5
  }
  haxe.Timer = function (time_ms) {
    var me = this;
    this.id = window.setInterval(function () {
      me.run();
    }, time_ms);
  };
  haxe.Timer.__name__ = true;
  haxe.Timer.delay = function (f, time_ms) {
    var t = new haxe.Timer(time_ms);
    t.run = function () {
      t.stop();
      f();
    };
    return t;
  }
  haxe.Timer.measure = function (f, pos) {
    var t0 = haxe.Timer.stamp();
    var r = f();
    haxe.Log.trace(haxe.Timer.stamp() - t0 + "s", pos);
    return r;
  }
  haxe.Timer.stamp = function () {
    return new Date().getTime() / 1000;
  }
  haxe.Timer.prototype = {
    run: function () {
    }
    , stop: function () {
      if (this.id == null) return;
      window.clearInterval(this.id);
      this.id = null;
    }
    , __class__: haxe.Timer
  }
  var js = js || {}
  js.Boot = function () { }
  js.Boot.__name__ = true;
  js.Boot.__unhtml = function (s) {
    return s.split("&").join("&amp;").split("<").join("&lt;").split(">").join("&gt;");
  }
  js.Boot.__trace = function (v, i) {
    var msg = i != null ? i.fileName + ":" + i.lineNumber + ": " : "";
    msg += js.Boot.__string_rec(v, "");
    var d;
    if (typeof (document) != "undefined" && (d = document.getElementById("haxe:trace")) != null) d.innerHTML += js.Boot.__unhtml(msg) + "<br/>"; else if (typeof (console) != "undefined" && console.log != null) console.log(msg);
  }
  js.Boot.__clear_trace = function () {
    var d = document.getElementById("haxe:trace");
    if (d != null) d.innerHTML = "";
  }
  js.Boot.isClass = function (o) {
    return o.__name__;
  }
  js.Boot.isEnum = function (e) {
    return e.__ename__;
  }
  js.Boot.getClass = function (o) {
    return o.__class__;
  }
  js.Boot.__string_rec = function (o, s) {
    if (o == null) return "null";
    if (s.length >= 5) return "<...>";
    var t = typeof (o);
    if (t == "function" && (o.__name__ || o.__ename__)) t = "object";
    switch (t) {
      case "object":
        if (o instanceof Array) {
          if (o.__enum__) {
            if (o.length == 2) return o[0];
            var str = o[0] + "(";
            s += "\t";
            var _g1 = 2, _g = o.length;
            while (_g1 < _g) {
              var i = _g1++;
              if (i != 2) str += "," + js.Boot.__string_rec(o[i], s); else str += js.Boot.__string_rec(o[i], s);
            }
            return str + ")";
          }
          var l = o.length;
          var i;
          var str = "[";
          s += "\t";
          var _g = 0;
          while (_g < l) {
            var i1 = _g++;
            str += (i1 > 0 ? "," : "") + js.Boot.__string_rec(o[i1], s);
          }
          str += "]";
          return str;
        }
        var tostr;
        try {
          tostr = o.toString;
        } catch (e) {
          return "???";
        }
        if (tostr != null && tostr != Object.toString) {
          var s2 = o.toString();
          if (s2 != "[object Object]") return s2;
        }
        var k = null;
        var str = "{\n";
        s += "\t";
        var hasp = o.hasOwnProperty != null;
        for (var k in o) {
          ;
          if (hasp && !o.hasOwnProperty(k)) {
            continue;
          }
          if (k == "prototype" || k == "__class__" || k == "__super__" || k == "__interfaces__" || k == "__properties__") {
            continue;
          }
          if (str.length != 2) str += ", \n";
          str += s + k + " : " + js.Boot.__string_rec(o[k], s);
        }
        s = s.substring(1);
        str += "\n" + s + "}";
        return str;
      case "function":
        return "<function>";
      case "string":
        return o;
      default:
        return String(o);
    }
  }
  js.Boot.__interfLoop = function (cc, cl) {
    if (cc == null) return false;
    if (cc == cl) return true;
    var intf = cc.__interfaces__;
    if (intf != null) {
      var _g1 = 0, _g = intf.length;
      while (_g1 < _g) {
        var i = _g1++;
        var i1 = intf[i];
        if (i1 == cl || js.Boot.__interfLoop(i1, cl)) return true;
      }
    }
    return js.Boot.__interfLoop(cc.__super__, cl);
  }
  js.Boot.__instanceof = function (o, cl) {
    try {
      if (o instanceof cl) {
        if (cl == Array) return o.__enum__ == null;
        return true;
      }
      if (js.Boot.__interfLoop(o.__class__, cl)) return true;
    } catch (e) {
      if (cl == null) return false;
    }
    switch (cl) {
      case Int:
        return Math.ceil(o % 2147483648.0) === o;
      case Float:
        return typeof (o) == "number";
      case Bool:
        return o === true || o === false;
      case String:
        return typeof (o) == "string";
      case Dynamic:
        return true;
      default:
        if (o == null) return false;
        if (cl == Class && o.__name__ != null) return true; else null;
        if (cl == Enum && o.__ename__ != null) return true; else null;
        return o.__enum__ == cl;
    }
  }
  js.Boot.__cast = function (o, t) {
    if (js.Boot.__instanceof(o, t)) return o; else throw "Cannot cast " + Std.string(o) + " to " + Std.string(t);
  }
  js.Lib = function () { }
  js.Lib.__name__ = true;
  js.Lib.debug = function () {
    debugger;
  }
  js.Lib.alert = function (v) {
    alert(js.Boot.__string_rec(v, ""));
  }
  js.Lib.eval = function (code) {
    return eval(code);
  }
  js.Lib.setErrorHandler = function (f) {
    js.Lib.onerror = f;
  }
  var $_;
  function $bind(o, m) { var f = function () { return f.method.apply(f.scope, arguments); }; f.scope = o; f.method = m; return f; };
  if (Array.prototype.indexOf) HxOverrides.remove = function (a, o) {
    var i = a.indexOf(o);
    if (i == -1) return false;
    a.splice(i, 1);
    return true;
  }; else null;
  Math.__name__ = ["Math"];
  Math.NaN = Number.NaN;
  Math.NEGATIVE_INFINITY = Number.NEGATIVE_INFINITY;
  Math.POSITIVE_INFINITY = Number.POSITIVE_INFINITY;
  Math.isFinite = function (i) {
    return isFinite(i);
  };
  Math.isNaN = function (i) {
    return isNaN(i);
  };
  String.prototype.__class__ = String;
  String.__name__ = true;
  Array.prototype.__class__ = Array;
  Array.__name__ = true;
  Date.prototype.__class__ = Date;
  Date.__name__ = ["Date"];
  var Int = { __name__: ["Int"] };
  var Dynamic = { __name__: ["Dynamic"] };
  var Float = Number;
  Float.__name__ = ["Float"];
  var Bool = Boolean;
  Bool.__ename__ = ["Bool"];
  var Class = { __name__: ["Class"] };
  var Enum = {};
  var Void = { __ename__: ["Void"] };
  if (typeof document != "undefined") js.Lib.document = document;
  if (typeof window != "undefined") {
    js.Lib.window = window;
    js.Lib.window.onerror = function (msg, url, line) {
      var f = js.Lib.onerror;
      if (f == null) return false;
      return f(msg, [url + ":" + line]);
    };
  }
  com.wiris.js.JsPluginTools.main();
  delete Array.prototype.__class__;
}());


(function () {
  var HxOverrides = function () { }
  HxOverrides.__name__ = true;
  HxOverrides.dateStr = function (date) {
    var m = date.getMonth() + 1;
    var d = date.getDate();
    var h = date.getHours();
    var mi = date.getMinutes();
    var s = date.getSeconds();
    return date.getFullYear() + "-" + (m < 10 ? "0" + m : "" + m) + "-" + (d < 10 ? "0" + d : "" + d) + " " + (h < 10 ? "0" + h : "" + h) + ":" + (mi < 10 ? "0" + mi : "" + mi) + ":" + (s < 10 ? "0" + s : "" + s);
  }
  HxOverrides.strDate = function (s) {
    switch (s.length) {
      case 8:
        var k = s.split(":");
        var d = new Date();
        d.setTime(0);
        d.setUTCHours(k[0]);
        d.setUTCMinutes(k[1]);
        d.setUTCSeconds(k[2]);
        return d;
      case 10:
        var k = s.split("-");
        return new Date(k[0], k[1] - 1, k[2], 0, 0, 0);
      case 19:
        var k = s.split(" ");
        var y = k[0].split("-");
        var t = k[1].split(":");
        return new Date(y[0], y[1] - 1, y[2], t[0], t[1], t[2]);
      default:
        throw "Invalid date format : " + s;
    }
  }
  HxOverrides.cca = function (s, index) {
    var x = s.charCodeAt(index);
    if (x != x) return undefined;
    return x;
  }
  HxOverrides.substr = function (s, pos, len) {
    if (pos != null && pos != 0 && len != null && len < 0) return "";
    if (len == null) len = s.length;
    if (pos < 0) {
      pos = s.length + pos;
      if (pos < 0) pos = 0;
    } else if (len < 0) len = s.length + len - pos;
    return s.substr(pos, len);
  }
  HxOverrides.remove = function (a, obj) {
    var i = 0;
    var l = a.length;
    while (i < l) {
      if (a[i] == obj) {
        a.splice(i, 1);
        return true;
      }
      i++;
    }
    return false;
  }
  HxOverrides.iter = function (a) {
    return {
      cur: 0, arr: a, hasNext: function () {
        return this.cur < this.arr.length;
      }, next: function () {
        return this.arr[this.cur++];
      }
    };
  }
  var IntIter = function (min, max) {
    this.min = min;
    this.max = max;
  };
  IntIter.__name__ = true;
  IntIter.prototype = {
    next: function () {
      return this.min++;
    }
    , hasNext: function () {
      return this.min < this.max;
    }
    , __class__: IntIter
  }
  var Std = function () { }
  Std.__name__ = true;
  Std["is"] = function (v, t) {
    return js.Boot.__instanceof(v, t);
  }
  Std.string = function (s) {
    return js.Boot.__string_rec(s, "");
  }
  Std["int"] = function (x) {
    return x | 0;
  }
  Std.parseInt = function (x) {
    var v = parseInt(x, 10);
    if (v == 0 && (HxOverrides.cca(x, 1) == 120 || HxOverrides.cca(x, 1) == 88)) v = parseInt(x);
    if (isNaN(v)) return null;
    return v;
  }
  Std.parseFloat = function (x) {
    return parseFloat(x);
  }
  Std.random = function (x) {
    return Math.floor(Math.random() * x);
  }
  var com = com || {}
  if (!com.wiris) com.wiris = {}
  if (!com.wiris.js) com.wiris.js = {}
  com.wiris.js.JsPluginTools = function () {
    this.tryReady();
  };
  com.wiris.js.JsPluginTools.__name__ = true;
  com.wiris.js.JsPluginTools.main = function () {
    var ev;
    ev = com.wiris.js.JsPluginTools.getInstance();
    haxe.Timer.delay($bind(ev, ev.tryReady), 100);
  }
  com.wiris.js.JsPluginTools.getInstance = function () {
    if (com.wiris.js.JsPluginTools.instance == null) com.wiris.js.JsPluginTools.instance = new com.wiris.js.JsPluginTools();
    return com.wiris.js.JsPluginTools.instance;
  }
  com.wiris.js.JsPluginTools.bypassEncapsulation = function () {
    if (window.com == null) window.com = {};
    if (window.com.wiris == null) window.com.wiris = {};
    if (window.com.wiris.js == null) window.com.wiris.js = {};
    if (window.com.wiris.js.JsPluginTools == null) window.com.wiris.js.JsPluginTools = com.wiris.js.JsPluginTools.getInstance();
  }
  com.wiris.js.JsPluginTools.prototype = {
    md5encode: function (content) {
      return haxe.Md5.encode(content);
    }
    , doLoad: function () {
      this.ready = true;
      com.wiris.js.JsPluginTools.instance = this;
      com.wiris.js.JsPluginTools.bypassEncapsulation();
    }
    , tryReady: function () {
      this.ready = false;
      if (js.Lib.document.readyState) {
        this.doLoad();
        this.ready = true;
      }
      if (!this.ready) haxe.Timer.delay($bind(this, this.tryReady), 100);
    }
    , __class__: com.wiris.js.JsPluginTools
  }
  var haxe = haxe || {}
  haxe.Log = function () { }
  haxe.Log.__name__ = true;
  haxe.Log.trace = function (v, infos) {
    js.Boot.__trace(v, infos);
  }
  haxe.Log.clear = function () {
    js.Boot.__clear_trace();
  }
  haxe.Md5 = function () {
  };
  haxe.Md5.__name__ = true;
  haxe.Md5.encode = function (s) {
    return new haxe.Md5().doEncode(s);
  }
  haxe.Md5.prototype = {
    doEncode: function (str) {
      var x = this.str2blks(str);
      var a = 1732584193;
      var b = -271733879;
      var c = -1732584194;
      var d = 271733878;
      var step;
      var i = 0;
      while (i < x.length) {
        var olda = a;
        var oldb = b;
        var oldc = c;
        var oldd = d;
        step = 0;
        a = this.ff(a, b, c, d, x[i], 7, -680876936);
        d = this.ff(d, a, b, c, x[i + 1], 12, -389564586);
        c = this.ff(c, d, a, b, x[i + 2], 17, 606105819);
        b = this.ff(b, c, d, a, x[i + 3], 22, -1044525330);
        a = this.ff(a, b, c, d, x[i + 4], 7, -176418897);
        d = this.ff(d, a, b, c, x[i + 5], 12, 1200080426);
        c = this.ff(c, d, a, b, x[i + 6], 17, -1473231341);
        b = this.ff(b, c, d, a, x[i + 7], 22, -45705983);
        a = this.ff(a, b, c, d, x[i + 8], 7, 1770035416);
        d = this.ff(d, a, b, c, x[i + 9], 12, -1958414417);
        c = this.ff(c, d, a, b, x[i + 10], 17, -42063);
        b = this.ff(b, c, d, a, x[i + 11], 22, -1990404162);
        a = this.ff(a, b, c, d, x[i + 12], 7, 1804603682);
        d = this.ff(d, a, b, c, x[i + 13], 12, -40341101);
        c = this.ff(c, d, a, b, x[i + 14], 17, -1502002290);
        b = this.ff(b, c, d, a, x[i + 15], 22, 1236535329);
        a = this.gg(a, b, c, d, x[i + 1], 5, -165796510);
        d = this.gg(d, a, b, c, x[i + 6], 9, -1069501632);
        c = this.gg(c, d, a, b, x[i + 11], 14, 643717713);
        b = this.gg(b, c, d, a, x[i], 20, -373897302);
        a = this.gg(a, b, c, d, x[i + 5], 5, -701558691);
        d = this.gg(d, a, b, c, x[i + 10], 9, 38016083);
        c = this.gg(c, d, a, b, x[i + 15], 14, -660478335);
        b = this.gg(b, c, d, a, x[i + 4], 20, -405537848);
        a = this.gg(a, b, c, d, x[i + 9], 5, 568446438);
        d = this.gg(d, a, b, c, x[i + 14], 9, -1019803690);
        c = this.gg(c, d, a, b, x[i + 3], 14, -187363961);
        b = this.gg(b, c, d, a, x[i + 8], 20, 1163531501);
        a = this.gg(a, b, c, d, x[i + 13], 5, -1444681467);
        d = this.gg(d, a, b, c, x[i + 2], 9, -51403784);
        c = this.gg(c, d, a, b, x[i + 7], 14, 1735328473);
        b = this.gg(b, c, d, a, x[i + 12], 20, -1926607734);
        a = this.hh(a, b, c, d, x[i + 5], 4, -378558);
        d = this.hh(d, a, b, c, x[i + 8], 11, -2022574463);
        c = this.hh(c, d, a, b, x[i + 11], 16, 1839030562);
        b = this.hh(b, c, d, a, x[i + 14], 23, -35309556);
        a = this.hh(a, b, c, d, x[i + 1], 4, -1530992060);
        d = this.hh(d, a, b, c, x[i + 4], 11, 1272893353);
        c = this.hh(c, d, a, b, x[i + 7], 16, -155497632);
        b = this.hh(b, c, d, a, x[i + 10], 23, -1094730640);
        a = this.hh(a, b, c, d, x[i + 13], 4, 681279174);
        d = this.hh(d, a, b, c, x[i], 11, -358537222);
        c = this.hh(c, d, a, b, x[i + 3], 16, -722521979);
        b = this.hh(b, c, d, a, x[i + 6], 23, 76029189);
        a = this.hh(a, b, c, d, x[i + 9], 4, -640364487);
        d = this.hh(d, a, b, c, x[i + 12], 11, -421815835);
        c = this.hh(c, d, a, b, x[i + 15], 16, 530742520);
        b = this.hh(b, c, d, a, x[i + 2], 23, -995338651);
        a = this.ii(a, b, c, d, x[i], 6, -198630844);
        d = this.ii(d, a, b, c, x[i + 7], 10, 1126891415);
        c = this.ii(c, d, a, b, x[i + 14], 15, -1416354905);
        b = this.ii(b, c, d, a, x[i + 5], 21, -57434055);
        a = this.ii(a, b, c, d, x[i + 12], 6, 1700485571);
        d = this.ii(d, a, b, c, x[i + 3], 10, -1894986606);
        c = this.ii(c, d, a, b, x[i + 10], 15, -1051523);
        b = this.ii(b, c, d, a, x[i + 1], 21, -2054922799);
        a = this.ii(a, b, c, d, x[i + 8], 6, 1873313359);
        d = this.ii(d, a, b, c, x[i + 15], 10, -30611744);
        c = this.ii(c, d, a, b, x[i + 6], 15, -1560198380);
        b = this.ii(b, c, d, a, x[i + 13], 21, 1309151649);
        a = this.ii(a, b, c, d, x[i + 4], 6, -145523070);
        d = this.ii(d, a, b, c, x[i + 11], 10, -1120210379);
        c = this.ii(c, d, a, b, x[i + 2], 15, 718787259);
        b = this.ii(b, c, d, a, x[i + 9], 21, -343485551);
        a = this.addme(a, olda);
        b = this.addme(b, oldb);
        c = this.addme(c, oldc);
        d = this.addme(d, oldd);
        i += 16;
      }
      return this.rhex(a) + this.rhex(b) + this.rhex(c) + this.rhex(d);
    }
    , ii: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitXOR(c, this.bitOR(b, ~d)), a, b, x, s, t);
    }
    , hh: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitXOR(this.bitXOR(b, c), d), a, b, x, s, t);
    }
    , gg: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitOR(this.bitAND(b, d), this.bitAND(c, ~d)), a, b, x, s, t);
    }
    , ff: function (a, b, c, d, x, s, t) {
      return this.cmn(this.bitOR(this.bitAND(b, c), this.bitAND(~b, d)), a, b, x, s, t);
    }
    , cmn: function (q, a, b, x, s, t) {
      return this.addme(this.rol(this.addme(this.addme(a, q), this.addme(x, t)), s), b);
    }
    , rol: function (num, cnt) {
      return num << cnt | num >>> 32 - cnt;
    }
    , str2blks: function (str) {
      var nblk = (str.length + 8 >> 6) + 1;
      var blks = new Array();
      var _g1 = 0, _g = nblk * 16;
      while (_g1 < _g) {
        var i = _g1++;
        blks[i] = 0;
      }
      var i = 0;
      while (i < str.length) {
        blks[i >> 2] |= HxOverrides.cca(str, i) << (str.length * 8 + i) % 4 * 8;
        i++;
      }
      blks[i >> 2] |= 128 << (str.length * 8 + i) % 4 * 8;
      var l = str.length * 8;
      var k = nblk * 16 - 2;
      blks[k] = l & 255;
      blks[k] |= (l >>> 8 & 255) << 8;
      blks[k] |= (l >>> 16 & 255) << 16;
      blks[k] |= (l >>> 24 & 255) << 24;
      return blks;
    }
    , rhex: function (num) {
      var str = "";
      var hex_chr = "0123456789abcdef";
      var _g = 0;
      while (_g < 4) {
        var j = _g++;
        str += hex_chr.charAt(num >> j * 8 + 4 & 15) + hex_chr.charAt(num >> j * 8 & 15);
      }
      return str;
    }
    , addme: function (x, y) {
      var lsw = (x & 65535) + (y & 65535);
      var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
      return msw << 16 | lsw & 65535;
    }
    , bitAND: function (a, b) {
      var lsb = a & 1 & (b & 1);
      var msb31 = a >>> 1 & b >>> 1;
      return msb31 << 1 | lsb;
    }
    , bitXOR: function (a, b) {
      var lsb = a & 1 ^ b & 1;
      var msb31 = a >>> 1 ^ b >>> 1;
      return msb31 << 1 | lsb;
    }
    , bitOR: function (a, b) {
      var lsb = a & 1 | b & 1;
      var msb31 = a >>> 1 | b >>> 1;
      return msb31 << 1 | lsb;
    }
    , __class__: haxe.Md5
  }
  haxe.Timer = function (time_ms) {
    var me = this;
    this.id = window.setInterval(function () {
      me.run();
    }, time_ms);
  };
  haxe.Timer.__name__ = true;
  haxe.Timer.delay = function (f, time_ms) {
    var t = new haxe.Timer(time_ms);
    t.run = function () {
      t.stop();
      f();
    };
    return t;
  }
  haxe.Timer.measure = function (f, pos) {
    var t0 = haxe.Timer.stamp();
    var r = f();
    haxe.Log.trace(haxe.Timer.stamp() - t0 + "s", pos);
    return r;
  }
  haxe.Timer.stamp = function () {
    return new Date().getTime() / 1000;
  }
  haxe.Timer.prototype = {
    run: function () {
    }
    , stop: function () {
      if (this.id == null) return;
      window.clearInterval(this.id);
      this.id = null;
    }
    , __class__: haxe.Timer
  }
  var js = js || {}
  js.Boot = function () { }
  js.Boot.__name__ = true;
  js.Boot.__unhtml = function (s) {
    return s.split("&").join("&amp;").split("<").join("&lt;").split(">").join("&gt;");
  }
  js.Boot.__trace = function (v, i) {
    var msg = i != null ? i.fileName + ":" + i.lineNumber + ": " : "";
    msg += js.Boot.__string_rec(v, "");
    var d;
    if (typeof (document) != "undefined" && (d = document.getElementById("haxe:trace")) != null) d.innerHTML += js.Boot.__unhtml(msg) + "<br/>"; else if (typeof (console) != "undefined" && console.log != null) console.log(msg);
  }
  js.Boot.__clear_trace = function () {
    var d = document.getElementById("haxe:trace");
    if (d != null) d.innerHTML = "";
  }
  js.Boot.isClass = function (o) {
    return o.__name__;
  }
  js.Boot.isEnum = function (e) {
    return e.__ename__;
  }
  js.Boot.getClass = function (o) {
    return o.__class__;
  }
  js.Boot.__string_rec = function (o, s) {
    if (o == null) return "null";
    if (s.length >= 5) return "<...>";
    var t = typeof (o);
    if (t == "function" && (o.__name__ || o.__ename__)) t = "object";
    switch (t) {
      case "object":
        if (o instanceof Array) {
          if (o.__enum__) {
            if (o.length == 2) return o[0];
            var str = o[0] + "(";
            s += "\t";
            var _g1 = 2, _g = o.length;
            while (_g1 < _g) {
              var i = _g1++;
              if (i != 2) str += "," + js.Boot.__string_rec(o[i], s); else str += js.Boot.__string_rec(o[i], s);
            }
            return str + ")";
          }
          var l = o.length;
          var i;
          var str = "[";
          s += "\t";
          var _g = 0;
          while (_g < l) {
            var i1 = _g++;
            str += (i1 > 0 ? "," : "") + js.Boot.__string_rec(o[i1], s);
          }
          str += "]";
          return str;
        }
        var tostr;
        try {
          tostr = o.toString;
        } catch (e) {
          return "???";
        }
        if (tostr != null && tostr != Object.toString) {
          var s2 = o.toString();
          if (s2 != "[object Object]") return s2;
        }
        var k = null;
        var str = "{\n";
        s += "\t";
        var hasp = o.hasOwnProperty != null;
        for (var k in o) {
          ;
          if (hasp && !o.hasOwnProperty(k)) {
            continue;
          }
          if (k == "prototype" || k == "__class__" || k == "__super__" || k == "__interfaces__" || k == "__properties__") {
            continue;
          }
          if (str.length != 2) str += ", \n";
          str += s + k + " : " + js.Boot.__string_rec(o[k], s);
        }
        s = s.substring(1);
        str += "\n" + s + "}";
        return str;
      case "function":
        return "<function>";
      case "string":
        return o;
      default:
        return String(o);
    }
  }
  js.Boot.__interfLoop = function (cc, cl) {
    if (cc == null) return false;
    if (cc == cl) return true;
    var intf = cc.__interfaces__;
    if (intf != null) {
      var _g1 = 0, _g = intf.length;
      while (_g1 < _g) {
        var i = _g1++;
        var i1 = intf[i];
        if (i1 == cl || js.Boot.__interfLoop(i1, cl)) return true;
      }
    }
    return js.Boot.__interfLoop(cc.__super__, cl);
  }
  js.Boot.__instanceof = function (o, cl) {
    try {
      if (o instanceof cl) {
        if (cl == Array) return o.__enum__ == null;
        return true;
      }
      if (js.Boot.__interfLoop(o.__class__, cl)) return true;
    } catch (e) {
      if (cl == null) return false;
    }
    switch (cl) {
      case Int:
        return Math.ceil(o % 2147483648.0) === o;
      case Float:
        return typeof (o) == "number";
      case Bool:
        return o === true || o === false;
      case String:
        return typeof (o) == "string";
      case Dynamic:
        return true;
      default:
        if (o == null) return false;
        if (cl == Class && o.__name__ != null) return true; else null;
        if (cl == Enum && o.__ename__ != null) return true; else null;
        return o.__enum__ == cl;
    }
  }
  js.Boot.__cast = function (o, t) {
    if (js.Boot.__instanceof(o, t)) return o; else throw "Cannot cast " + Std.string(o) + " to " + Std.string(t);
  }
  js.Lib = function () { }
  js.Lib.__name__ = true;
  js.Lib.debug = function () {
    debugger;
  }
  js.Lib.alert = function (v) {
    alert(js.Boot.__string_rec(v, ""));
  }
  js.Lib.eval = function (code) {
    return eval(code);
  }
  js.Lib.setErrorHandler = function (f) {
    js.Lib.onerror = f;
  }
  var $_;
  function $bind(o, m) { var f = function () { return f.method.apply(f.scope, arguments); }; f.scope = o; f.method = m; return f; };
  if (Array.prototype.indexOf) HxOverrides.remove = function (a, o) {
    var i = a.indexOf(o);
    if (i == -1) return false;
    a.splice(i, 1);
    return true;
  }; else null;
  Math.__name__ = ["Math"];
  Math.NaN = Number.NaN;
  Math.NEGATIVE_INFINITY = Number.NEGATIVE_INFINITY;
  Math.POSITIVE_INFINITY = Number.POSITIVE_INFINITY;
  Math.isFinite = function (i) {
    return isFinite(i);
  };
  Math.isNaN = function (i) {
    return isNaN(i);
  };
  String.prototype.__class__ = String;
  String.__name__ = true;
  Array.prototype.__class__ = Array;
  Array.__name__ = true;
  Date.prototype.__class__ = Date;
  Date.__name__ = ["Date"];
  var Int = { __name__: ["Int"] };
  var Dynamic = { __name__: ["Dynamic"] };
  var Float = Number;
  Float.__name__ = ["Float"];
  var Bool = Boolean;
  Bool.__ename__ = ["Bool"];
  var Class = { __name__: ["Class"] };
  var Enum = {};
  var Void = { __ename__: ["Void"] };
  if (typeof document != "undefined") js.Lib.document = document;
  if (typeof window != "undefined") {
    js.Lib.window = window;
    js.Lib.window.onerror = function (msg, url, line) {
      var f = js.Lib.onerror;
      if (f == null) return false;
      return f(msg, [url + ":" + line]);
    };
  }
  com.wiris.js.JsPluginTools.main();
}());
delete Array.prototype.__class__;
// @codingStandardsIgnoreEnd

/**
 * PDW Toggle Toolbars v1.2
 * Url: http://www.neele.name
 * Author: Guido Neele
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * Based on TinyMCE Wordpress plugin (Kitchen Sink)
 */
(function(){var d=tinymce.DOM;tinymce.PluginManager.requireLangPack("pdw");tinymce.create("tinymce.plugins.pdw",{init:function(a,h){var e=this,i=[],j=[],c;j=a.settings.pdw_toggle_toolbars.split(",");for(c=0;c<j.length;c++)i[c]=a.getParam("","toolbar"+j[c].replace(" ",""));a.addCommand("mcePDWToggleToolbars",function(){var f=a.controlManager,b,g,l=tinymce.util.Cookie,k,m=l.getHash("TinyMCE_toggle")||{};for(g=0;g<i.length;g++){obj=a.controlManager.get(i[g]);if(typeof obj!="undefined"){b=obj.id;if(d.isHidden(b)){k=
0;d.show(b);e._resizeIframe(a,i[g],-26)}else{k=1;d.hide(b);e._resizeIframe(a,i[g],26)}}}f.setActive("pdw_toggle",k);a.settings.pdw_toggle_on=k;m[a.id]=k;l.setHash("TinyMCE_toggle",m)});a.addButton("pdw_toggle",{title:a.getLang("pdw.desc",0),cmd:"mcePDWToggleToolbars",image:h+"/img/toolbars.gif"});a.onPostRender.add(function(){var f=tinymce.util.Cookie.getHash("TinyMCE_toggle")||{},b=false;if(f[a.id]==null)b=a.settings.pdw_toggle_on==1?true:false;else if(f[a.id]==1)b=true;if(b){f=a.controlManager;
for(c=0;c<j.length;c++){tbId=a.getParam("","toolbar"+j[c].replace(" ",""));b=a.controlManager.get(tbId).id;f.setActive("pdw_toggle",1);d.hide(b);e._resizeIframe(a,tbId,26)}}})},_resizeIframe:function(a,h,e){h=a.getContentAreaContainer().firstChild;d.setStyle(h,"height",d.getSize(h).h+e);a.theme.deltaHeight+=e},getInfo:function(){return{longname:"PDW Toggle Toolbars",author:"Guido Neele",authorurl:"http://www.neele.name/",infourl:"http://www.neele.name/pdw_toggle_toolbars",version:"1.2"}}});tinymce.PluginManager.add("pdw",
tinymce.plugins.pdw)})();
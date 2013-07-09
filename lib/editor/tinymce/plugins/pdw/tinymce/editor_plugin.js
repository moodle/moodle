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
!function(){var DOM=tinymce.DOM;tinymce.PluginManager.requireLangPack("pdw");tinymce.create("tinymce.plugins.pdw",{init:function(ed,url){var t=this,tbIds=new Array,toolbars=new Array,i;toolbars=ed.settings.pdw_toggle_toolbars.split(",");for(i=0;i<toolbars.length;i++){tbIds[i]=ed.getParam("","toolbar"+toolbars[i].replace(" ",""))}ed.addCommand("mcePDWToggleToolbars",function(){var cm=ed.controlManager,id,j,Cookie=tinymce.util.Cookie,Toggle_PDW,Toggle=Cookie.getHash("TinyMCE_toggle")||new Object;for(j=0;j<tbIds.length;j++){obj=ed.controlManager.get(tbIds[j]);if(typeof obj=="undefined"){continue}id=obj.id;if(DOM.isHidden(id)){Toggle_PDW=0;var e=document.getElementById(id);if(e){e.style.display="table";t._resizeIframe(ed,tbIds[j],-26)}}else{Toggle_PDW=1;var e=document.getElementById(id);if(e){e.style.display="none"}t._resizeIframe(ed,tbIds[j],26)}}cm.setActive("pdw_toggle",Toggle_PDW);ed.settings.pdw_toggle_on=Toggle_PDW;Toggle[ed.id]=Toggle_PDW;Cookie.setHash("TinyMCE_toggle",Toggle)});ed.addButton("pdw_toggle",{title:ed.getLang("pdw.desc",0),cmd:"mcePDWToggleToolbars",image:url+"/img/toolbars.gif"});ed.onPostRender.add(function(){var toggle=tinymce.util.Cookie.getHash("TinyMCE_toggle")||new Object;var run=false;if(toggle[ed.id]==null){run=ed.settings.pdw_toggle_on==1?true:false}else if(toggle[ed.id]==1){run=true}if(run){var cm=ed.controlManager,tdId,id;for(i=0;i<toolbars.length;i++){tbId=ed.getParam("","toolbar"+toolbars[i].replace(" ",""));id=ed.controlManager.get(tbId).id;cm.setActive("pdw_toggle",1);DOM.hide(id);t._resizeIframe(ed,tbId,26)}}})},_resizeIframe:function(ed,tb_id,dy){var ifr=ed.getContentAreaContainer().firstChild;DOM.setStyle(ifr,"height",DOM.getSize(ifr).h+dy);ed.theme.deltaHeight+=dy},getInfo:function(){return{longname:"PDW Toggle Toolbars",author:"Guido Neele",authorurl:"http://www.neele.name/",infourl:"http://www.neele.name/pdw_toggle_toolbars",version:"1.2"}}});tinymce.PluginManager.add("pdw",tinymce.plugins.pdw)}();
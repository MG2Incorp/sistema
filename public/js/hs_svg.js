!function(t){"use strict";t.HSCore.components.HSSVGIngector={_baseConfig:{},pageCollection:t(),init:function(e,i){if(this.collection=e&&t(e).length?t(e):t(),t(e).length)return this.config=i&&t.isPlainObject(i)?t.extend({},this._baseConfig,i):this._baseConfig,this.config.itemSelector=e,this.initSVGInjector(),this.pageCollection},initSVGInjector:function(){var e=this.pageCollection;this.collection.each(function(i,n){var o,c,s=t(n),a=JSON.parse(n.getAttribute("data-img-paths")),r=a?a.length:0,h=t(s.data("parent"));h.css("height",h.outerHeight()),SVGInjector(s,{each:function(e){if(r>0)for(i=0;i<r;i++)o=a[i].targetId,c=a[i].newPath,t(e).find(o).attr("xlink:href",c);h.removeClass("svg-preloader").css("height","")}}),e=e.add(s)})}}}(jQuery);
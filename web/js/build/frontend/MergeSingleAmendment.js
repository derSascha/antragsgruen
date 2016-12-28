define(["require","exports","../shared/AntragsgruenEditor"],function(i,t,e){"use strict";var a=function(){function i(){var i=this;this.editors=[],this.collissionEditors={},this.$form=$("#amendmentMergeForm"),this.$collissionHolder=$(".amendmentCollissionsHolder"),this.$checkCollissions=$(".checkAmendmentCollissions"),this.$affectedParagraphs=$(".affectedParagraphs > .paragraph"),this.$checkCollissions.click(function(t){t.preventDefault(),i.loadCollissions()}),this.$affectedParagraphs.each(function(t,e){i.initAffectedParagraph(e)}),this.$form.submit(this.onSubmit.bind(this))}return i.prototype.initAffectedParagraph=function(i){var t=$(i);t.find(".modifySelector input").change(function(){"1"==t.find(".modifySelector input:checked").val()?t.addClass("modified").removeClass("unmodified"):t.removeClass("modified").addClass("unmodified")}).trigger("change");var a=t.data("section-id")+"_"+t.data("paragraph-no");this.editors[a]=new e.AntragsgruenEditor(t.find(".affectedBlock > .texteditor").attr("id"))},i.prototype.loadCollissions=function(){var i=this,t=this.$checkCollissions.data("url"),e={};this.$affectedParagraphs.each(function(t,a){var n,o=$(a),s=o.find(".modifySelector input:checked").val(),d=o.data("section-id"),r=o.data("paragraph-no");if(s){var l=i.editors[d+"_"+r].getEditor(),c=l.getData();"undefined"!=typeof l.plugins.lite?(l.plugins.lite.findPlugin(l).acceptAll(),n=l.getData(),l.setData(c)):n=l.getData()}else n=o.data("unchanged-amendment");void 0===e[o.data("section-id")]&&(e[o.data("section-id")]={}),e[o.data("section-id")][o.data("paragraph-no")]=n}),$.post(t,{newSections:e,_csrf:this.$form.find("> input[name=_csrf]").val()},this.collissionsLoaded.bind(this))},i.prototype.collissionsLoaded=function(i){var t=this;this.collissionEditors={},this.$collissionHolder.html(i);var a=this.$collissionHolder.find(".amendmentOverrideBlock > .texteditor");a.length>0&&(a.each(function(i,a){var n=$(a).attr("id");t.collissionEditors[n]=new e.AntragsgruenEditor(n)}),this.$collissionHolder.scrollintoview({top_offset:-50})),this.$checkCollissions.hide(),$(".saveholder .save").prop("disabled",!1).show()},i.prototype.onSubmit=function(){var i=this;this.$affectedParagraphs.each(function(t,e){var a=$(e),n=a.find(".modifiedText");if("1"==a.find(".modifySelector input:checked").val()){var o=a.data("section-id")+"_"+a.data("paragraph-no"),s=i.editors[o].getEditor();"undefined"!=typeof s.plugins.lite&&s.plugins.lite.findPlugin(s).acceptAll(),n.val(s.getData())}else n.val(a.data("unchanged-amendment"))});for(var t in this.collissionEditors){var e=this.collissionEditors[t].getEditor();"undefined"!=typeof e.plugins.lite&&e.plugins.lite.findPlugin(e).acceptAll();var a=e.getData();$("#"+t).parents(".amendmentOverrideBlock").first().find("> textarea").val(a)}},i}();new a});
//# sourceMappingURL=MergeSingleAmendment.js.map
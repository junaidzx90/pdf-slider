!function(){"use strict";var e=window.wp.element;const{registerBlockType:t}=wp.blocks;t("pdf-slider/pdf-block",{title:"PDF Slider",description:"Show pdf contents as slides.",icon:"dashicons dashicons-pdf",category:"widgets",attributes:{pdf:{type:"string"},img:{type:"string"}},edit:function(t){var i,n="";if(t.attributes.pdf){n=t.attributes.pdf.split("/").pop();var a=window["pdfjs-dist/build/pdf"];a.GlobalWorkerOptions.workerSrc="pdf.warker.js";var s,r=(i=document.createElement("CANVAS")).getContext("2d");a.getDocument(t.attributes.pdf).promise.then((function(e){e.getPage(1).then((function(e){s=e.getViewport({scale:1}),i.height=400,i.width=s.width;var n={canvasContext:r,viewport:s};e.render(n).promise.then((function(){t.setAttributes({img:i.toDataURL("image/jpeg")})}))}))}))}return(0,e.createElement)("div",null,(0,e.createElement)("div",{className:"pdf-slider"},void 0!==t.attributes.pdf?(0,e.createElement)("div",{className:"fileinfo"},(0,e.createElement)("span",{className:"filename"},(0,e.createElement)("img",{src:t.attributes.img}),(0,e.createElement)("br",null),n)):"No selected",(0,e.createElement)("button",{onClick:function(){var e,i;e||(e=wp.media.frames.file_frame=wp.media({title:"Choose PDF",button:{text:"Choose PDF"},library:{type:["application/pdf","image","video"]},multiple:!1})).on("select",(function(){i=e.state().get("selection").first().toJSON(),t.setAttributes({pdf:i.url})})),e.open()},className:"uploadpdf button-secondary"},t.attributes.pdf?"Change PDF":"Upload a PDF")))},save:()=>null})}();
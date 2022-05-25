/**
 * PDF Slider,
 * Created by Developer Junayed
 */
let canvaces = document.querySelectorAll('.the-canvas');
canvaces.forEach(canvas => {
  var url = canvas.attributes.data.value;
  if (url) {
    // Loaded via <script> tag, create shortcut to access PDF.js exports.
    var pdfjsLib = window['pdfjs-dist/build/pdf'];

    // The workerSrc property shall be specified.
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'pdf.warker.js';

    var pdfDoc = null,
      pageNum = 1,
      pageRendering = false,
      pageNumPending = null,
      scale = 3,
      ctx = canvas.getContext('2d'),
      widthset = false,
      viewport;

    /**
     * Get page info from document, resize canvas accordingly, and render page.
     * @param num Page number.
     */
    function renderPage(num) {
      pageRendering = true;
      num = parseInt(num)
      // Using promise to fetch the page
      pdfDoc.getPage(num).then(function (page) {
        viewport = page.getViewport({ scale: scale });
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        if (!widthset) {
          canvas.style.width = '100%';
          canvas.style.height = "100%";
        }

        // Render PDF page into canvas context
        var renderContext = {
          canvasContext: ctx,
          viewport: viewport
        };
        var renderTask = page.render(renderContext);
        // Wait for rendering to finish
        renderTask.promise.then(function () {
          pageRendering = false;
          if (pageNumPending !== null) {
            // New page rendering is pending
            renderPage(pageNumPending);
            pageNumPending = null;
          }
        });
      });

      // Update page counters
      jQuery(canvas).parent().parent().find('.page_num').text(num)
    }

    /**
     * If another page rendering in progress, waits until the rendering is
     * finised. Otherwise, executes rendering immediately.
     */
    function queueRenderPage(num) {
      if (pageRendering) {
        pageNumPending = num;
      } else {
        renderPage(num);
      }
    }

    /**
     * Displays previous page.
     */
    function onPrevPage() {
      if (pageNum <= 1) {
        return;
      }
      pageNum--;
      queueRenderPage(pageNum);
      
      disablearrows()
    }

    jQuery(canvas).parent().parent().find('.prevSlide').each(function () {
      jQuery(this).on("click", function () { onPrevPage() });
    })

    function disablearrows() {
      if ((pageNum + 1) == pdfDoc.numPages + 1) {
        jQuery(canvas).parent().parent().find('.nextSlide').css({ 'pointer-events': 'none', 'opacity': '.2' })
      } else {
        jQuery(canvas).parent().parent().find('.nextSlide').css({ 'pointer-events': 'unset', 'opacity': '1' })
      }
      if (pageNum == 1) {
        jQuery(canvas).parent().parent().find('.prevSlide').css({ 'pointer-events': 'none', 'opacity': '.2' })
      } else {
        jQuery(canvas).parent().parent().find('.prevSlide').css({ 'pointer-events': 'unset', 'opacity': '1' })
      }
    }

    /**
     * Displays next page.
     */
    function onNextPage() {
      
      if (pageNum >= pdfDoc.numPages) {
        return;
      }
      pageNum++;
      queueRenderPage(pageNum);
      disablearrows()
      
    }

    jQuery(canvas).parent().parent().find('.nextSlide').each(function () {
      jQuery(this).on("click", function () { onNextPage() });
    })

    /**
     * Asynchronously downloads PDF.
     */
    pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
      pdfDoc = pdfDoc_;
      if (pdfDoc) {
        jQuery(canvas).parent().parent('.pdfslider').css('display', 'flex');
        jQuery(canvas).parent().parent().find('.page_count').text(pdfDoc.numPages);
        // Initial/first page rendering
        renderPage(pageNum);
      } else {
        console.log("Server Slow");
      }
    });


    /* Get into full screen */
    function GoInFullscreen(element) {
      if (element.requestFullScreen) {
        element.requestFullScreen();
      } else if (element.webkitRequestFullScreen) {
          element.webkitRequestFullScreen();
      } else if (element.mozRequestFullScreen) {
          element.mozRequestFullScreen();
      } else if (element.msRequestFullscreen) {
          element.msRequestFullscreen();
      } else if (element.webkitEnterFullscreen) {
          element.webkitEnterFullscreen();
      }
    }

    /* Get out of full screen */
    function GoOutFullscreen() {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitIsFullScreen) {
          document.webkitCancelFullScreen();
      } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
      }
    }

    /* Is currently in full screen or not */
    function IsFullScreenCurrently() {
      var full_screen_element = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement || document.webkitIsFullScreen || null;
                
      // If no element is in full-screen
      if (full_screen_element === null)
          return false;
      else
          return true;
    } 
    
    function fullscreenMacanism(fullBtn) {
      let icon = '';
      if (IsFullScreenCurrently()) {
        canvas.style.height = "100%";
        canvas.style.width = "100%";
        widthset = false;
        icon = '<i class="fas fa-expand"></i>';
        fullBtn.html(icon);
        jQuery(canvas).parent().parent('.pdfslider').removeClass('fullsize')
        GoOutFullscreen();
      } else {
        canvas.style.height = '100%';
        canvas.style.width = 'initial';
        widthset = true;
        icon = '<i class="fas fa-compress"></i>';
        fullBtn.html(icon);
        jQuery(canvas).parent().parent('.pdfslider').addClass('fullsize')
        GoInFullscreen(jQuery(canvas).parent().parent('.pdfslider')[0]);
      }

      renderPage(jQuery(canvas).parent().parent().find('.page_num').text());
    }

    
    let fullscreenBtn = jQuery(canvas).parent().parent().find('.fullscreen');
    fullscreenBtn.on('click', function () {
      fullscreenMacanism(jQuery(this));
    });

    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('mozfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
      if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        let icon = '';
       
        canvas.style.height = "100%";
        canvas.style.width = "100%";
        widthset = false;
        icon = '<i class="fas fa-expand"></i>';
        fullscreenBtn.html(icon);
        jQuery(canvas).parent().parent('.pdfslider').removeClass('fullsize');
        
        //renderPage(jQuery(canvas).parent().parent().find('.page_num').text());
      }
    } 
  }
});

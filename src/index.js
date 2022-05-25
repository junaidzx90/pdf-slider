const { registerBlockType } = wp.blocks

registerBlockType('pdf-slider/pdf-block', {
    title: "PDF Slider",
    description: "Show pdf contents as slides.",
    icon: 'dashicons dashicons-pdf',
    category: "widgets",
    attributes :{
        pdf: {
            type: 'string',
        },
        img: {
            type: 'string'
        }
    },

    edit: function (props) {

        function loadFile() {
			var pdfFile, selectedFile;
            // If the frame already exists, re-open it.
            if ( pdfFile ) {
                pdfFile.open();
                return;
            }
            //Extend the wp.media object
            pdfFile = wp.media.frames.file_frame = wp.media({
                title: 'Choose PDF',
                button: {
                    text: 'Choose PDF'
                },
                library: {
                    type:['application/pdf', 'image', 'video']
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            pdfFile.on('select', function() {
                selectedFile = pdfFile.state().get('selection').first().toJSON();
                props.setAttributes({
                    pdf: selectedFile.url
                })
            });

            //Open the uploader dialog
            pdfFile.open();
        }
        var filename = '', canvas;
        if (props.attributes.pdf) {
            filename = props.attributes.pdf.split('/').pop()

            var pdfjsLib = window['pdfjs-dist/build/pdf'];

            // The workerSrc property shall be specified.
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'pdf.warker.js';
            canvas = document.createElement("CANVAS");
            var pdfDoc = null,
            scale = 1,
            viwheight = 400, viewport,
            ctx = canvas.getContext('2d');
            
            function renderPage() {
                // Using promise to fetch the page
                pdfDoc.getPage(1).then(function (page) {
                    viewport = page.getViewport({ scale: scale });
                    canvas.height = viwheight;
                    canvas.width = viewport.width;
            
                    // Render PDF page into canvas context
                    var renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    var renderTask = page.render(renderContext);
                    // Wait for rendering to finish
                    renderTask.promise.then(function () {
                        props.setAttributes({
                            img: canvas.toDataURL('image/jpeg')
                        });
                    });
                });
            }

            pdfjsLib.getDocument(props.attributes.pdf).promise.then(function (pdfDoc_) {
                pdfDoc = pdfDoc_;
                renderPage();
            });
        }


        return (
            <div>
                <div className="pdf-slider">
                    {props.attributes.pdf !== undefined ? <div className="fileinfo"><span className="filename"><img src={props.attributes.img} /><br/>{ filename }</span></div> : 'No selected'}
                    <button onClick={loadFile} className="uploadpdf button-secondary">{ props.attributes.pdf ? 'Change PDF' : 'Upload a PDF' }</button>
                </div>
            </div>
        )
    },
    
    save() {
        return null
    },
})
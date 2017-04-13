CKEDITOR.editorConfig = function( config ) {
    config.toolbar = [
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote' ] },
        { name: 'styles', items: [ 'Format' ] },
        { name: 'clipboard', items: [ 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
        { name: 'insert', items: [ 'Image', 'HorizontalRule', 'SpecialChar' ] },
        { name: 'document', items: [ 'Source' ] }
    ];
};
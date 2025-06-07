jQuery(document).ready(function ($) {
    const textareaSelector = '#edforce_custom_editor';
    console.log('Initializing TinyMCE for:', textareaSelector);
    // Remove if already initialized (useful for dynamic reloads)
    if (tinymce.get(textareaSelector.replace('#', ''))) {
        tinymce.remove(tinymce.get(textareaSelector.replace('#', '')));
    }

    // Initialize TinyMCE
    tinymce.init({
        selector: textareaSelector,
        height: 300,
        menubar: false,
        toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
        plugins: 'lists link',
        setup: function (editor) {
            console.log('TinyMCE loaded for:', editor.id);
        }
    });

    // Example: Access localized PHP data
    if (typeof edforceEditor !== 'undefined') {
        console.log('Nonce:', edforceEditor.nonce);
        console.log('Is Slug Enabled:', edforceEditor.isSlugEnabled);
    }
});

jQuery(document).ready(function($) {

    // Auto-generate slug on title input
    $('#subcategory_title').on('input', function() {
        const rawTitle = $(this).val();

        const slug = rawTitle
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s_-]/g, '')    // Remove invalid chars
            .replace(/[\s_]+/g, '-')          // Replace spaces/underscores with -
            .replace(/-+/g, '-')              // Collapse multiple hyphens
            .replace(/^-+|-+$/g, '');         // Trim hyphens from start/end

        $('#subcategory_slug').val(slug);
    });

    // Handle form submit
    $('#subcategory-form form').on('submit', function(e) {
        e.preventDefault();

        const title = $('#subcategory_title').val().trim();
        const slug = $('#subcategory_slug').val().trim();
        const description = $('#subcategory_description').val().trim();
        const image = $('#subcategory_image').val().trim();

        if (!title || !slug) {
            alert('Please enter both title and slug.');
            return;
        }

        console.log(title, slug, description, image); // for debugging

        // Uncomment and update this block when backend is ready
        /*
        $.post(ajaxurl, {
            action: 'add_edforce_subcategory',
            subcategory_title: title,
            subcategory_slug: slug,
            subcategory_description: description,
            subcategory_image: image
        }, function(response) {
            if (response.success) {
                alert('Subcategory added successfully!');
                const option = $('<option></option>')
                    .val(response.data.slug)
                    .text(response.data.title);
                $('#data_subcategory, #show_data_subcategory').append(option);
                $('#subcategory-form form')[0].reset();
                $('#subcategory-form').hide();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
        */
    });
});

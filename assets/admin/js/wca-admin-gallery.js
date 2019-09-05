jQuery(document).ready(function (jQuery) {
    var media_uploader = null;

    jQuery('.wca-upload-gallery').click(function (event) {
        var current_gallery = jQuery(this).closest('div.inside');

        if (event.currentTarget.id === 'wca-clear-gallery') {
            //remove value from input
            current_gallery.find('.wca-gallery-values').val('').trigger('change');

            //remove preview images
            current_gallery.find('.wca-image-gallery').html('');
            return;
        }

        // Make sure the media gallery API exists
        if (typeof wp === 'undefined' || !wp.media || !wp.media.gallery) {
            return;
        }
        event.preventDefault();

        var wca_gallery_values = current_gallery.find('.wca-gallery-values').val();
        var selection = null;
        if (wca_gallery_values) {
            // Retrieving previously added image data
            var attachments = wp.media.query({
                order: 'ASC',
                orderby: 'post__in',
                post__in: wca_gallery_values.split(','),
                posts_per_page: -1,
                query: true,
                type: 'image'
            });

            selection = new wp.media.model.Selection(attachments.models, {
                props: attachments.props.toJSON(),
                multiple: true
            });
        }

        // initialization
        media_uploader = wp.media({
            frame: "post",
            state: "gallery-edit",
            selection: selection,
            multiple: true
        });

        media_uploader.on("update", function () {
            var length = media_uploader.state().attributes.library.length;
            var images = media_uploader.state().attributes.library.models;
            console.log(images);

            //clear screenshot div so we can append new selected images
            current_gallery.find('.wca-image-gallery').html('');
            var element, preview_html = '', preview_img, ids = [];
            for (var i = 0; i < length; i++)
            {
                element = images[i].attributes;
                ids[i] = images[i].id;
                preview_img = typeof element.sizes.thumbnail !== 'undefined' ? element.sizes.thumbnail.url : element.url;
                preview_html = "<div class='wca-image-thumb'><img src='" + preview_img + "'/></div>";
                current_gallery.find('.wca-image-gallery').append(preview_html);
            }
            current_gallery.find('.wca-gallery-values').val(ids.join(',')).trigger('change');
        });

        media_uploader.open();

        return false;
    });

});
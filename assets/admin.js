(function($){
    function initBrandMedia() {
        var frame;

        function openFrame(button) {
            if ( frame ) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: button.data('title') || button.text(),
                button: {
                    text: button.data('button') || button.text()
                },
                multiple: false
            });

            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                button.siblings('input#wbt_brand_logo_id').val(attachment.id);
                button.siblings('.wbt-brand-logo-preview').html('<img src="' + attachment.url + '" alt="" />');
                button.siblings('.wbt-remove-logo').removeClass('hidden');
            });

            frame.open();
        }

        $(document).on('click', '.wbt-upload-logo', function(e){
            e.preventDefault();
            openFrame($(this));
        });

        $(document).on('click', '.wbt-remove-logo', function(e){
            e.preventDefault();
            var $button = $(this);
            $button.addClass('hidden');
            $button.siblings('input#wbt_brand_logo_id').val('');
            $button.siblings('.wbt-brand-logo-preview').empty();
        });
    }

    $(document).ready(function(){
        initBrandMedia();
    });
})(jQuery);

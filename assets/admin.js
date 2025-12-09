jQuery(document).ready(function ($) {

    /**
     * tabs
     */
    $('.nav-tab-wrapper a').click(function (event) {

        event.preventDefault();

        var context = $(this).closest('.nav-tab-wrapper').parent();
        $('.nav-tab-wrapper a', context).removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.nav-tab-panel', context).hide();
        $($(this).attr('href'), context).show();

    });

    /**
     * tabs active
     */
    $('.nav-tab-wrapper').each(function () {

        if ($('.nav-tab-active', this).length) {
            $('.nav-tab-active', this).click();
        } else {
            $('a', this).first().click();
        }

    });

    /**
     * fallback
     */
    if ($('[name="ar404_settings[fallback][type]"]').length > 0 && $('[name="ar404_settings[fallback][url]"]').length > 0 && $('[name="ar404_settings[fallback][home_url]"]').length > 0) {

        $('[name="ar404_settings[fallback][type]"]').change(function () {

            $('[name="ar404_settings[fallback][url]"]').val($('[name="ar404_settings[fallback][home_url]"]').val());
            $('[name="ar404_settings[fallback][url]"]').prop('readOnly', true);
            $('[name="ar404_settings[fallback][url]"]').removeClass('hidden');
            $('[name="ar404_settings[fallback][url]"]').addClass('disabled');

            if ($(this).val() == 'custom') {

                $('[name="ar404_settings[fallback][url]"]').prop('readOnly', false);
                $('[name="ar404_settings[fallback][url]"]').removeClass('disabled');

            } else if ($(this).val() == 'disabled') {
                $('[name="ar404_settings[fallback][url]"]').addClass('hidden');

            }
        });

    }

    /**
     * ar404_disable_taxonomies
     *
     * @param checkbox
     */
    function ar404_disable_taxonomies(checkbox) {

        if (checkbox.is(':checked')) {
            $('.ar404_settings_taxonomies').hide();
        } else {
            $('.ar404_settings_taxonomies').show();
        }

    }

    /**
     * taxonomies
     */
    if ($('#ar404_settings_rules_redirection_disable_taxonomies').length > 0) {

        ar404_disable_taxonomies($('#ar404_settings_rules_redirection_disable_taxonomies'));

        $('#ar404_settings_rules_redirection_disable_taxonomies').change(function () {
            ar404_disable_taxonomies($(this));
        });

    }

    /**
     * preview
     */
    if ($('#ar404_settings_redirection_preview').length > 0) {

        $('#ar404_settings_redirection_preview .button').click(function (event) {

            // prevent default
            event.preventDefault();

            // vars
            $button = $(this);
            $loading = $('#ar404_settings_redirection_preview .loading');
            $button.prop('disabled', true);
            $loading.addClass('is-active');
            $request = $('#ar404_settings_redirection_preview input[type=text]').val();

            // check length
            if ($request.length == 0) {
                return;
            }

            // prepend slash
            if ($request.substring(0, 1) != '/') {

                $request = '/' + $request;
                $('#ar404_settings_redirection_preview input[type=text]').val($request);

            }

            // ajax request
            $.post(ajaxurl, {
                nonce: $('#ar404_settings_redirection_preview input[name="nonce"]').val(),
                action: 'ar404_ajax_preview',
                request: $request,
            })
                .done(function (response) {

                    $button.prop('disabled', false);
                    $loading.removeClass('is-active');
                    $('#ar404_settings_redirection_preview .results').html(response);

                });

        });

    }

});
/*
 * Behavior for the automatic file upload
 */

(function ($) {
    Drupal.behaviors.image = {
        attach: function(context) {
            $('.form-item input.form-submit[value=Drupal.t("Upload")]', context).hide();
            $('.form-item input.form-file', context).change(function() {
                $parent = $(this).closest('.form-item');
                setTimeout(function() {
                    if(!$('.error', $parent).length) {
                        $('input.form-submit[value=Drupal.t("Upload")]', $parent).mousedown();
                    }
                }, 100);
            });
        }
    };
})(jQuery);

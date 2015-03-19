/**
 *
 */

(function($) {

    Drupal.behaviors.seven = {
        attach : function(context, settings) {
            // Change html for radiobutton
            $("input[type=radio]").each(function() {
                var label = $(this).next("label");
                //var furn = label.attr('for');
                var radio = $(this);
                label.removeClass("option").addClass("radio");
                $(this).appendTo(label);
                radio.radio();
            });

            $(".form-type-password-confirm input[type=password]").each(function(){
                $(this).after('<span class="add-on glyphicon glyphicon-eye-open"></span>');
            });

            // See/Hide the password
            $(".glyphicon-eye-open").on("click", function() {
                $(".add-on").toggleClass("glyphicon-eye-close");
                var field = $(".password-field");
                var confirm = $(".password-confirm");
                var type = field.attr("type");
                if (type == "text") {
                    field[0].type = 'password';
                    confirm[1].type = 'password';
                }
                else {
                    field[0].type = 'text';
                    confirm[1].type = 'text';
                }
            });
        }
    };
})(jQuery);
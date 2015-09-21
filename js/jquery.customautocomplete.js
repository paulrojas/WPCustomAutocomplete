(function($) {
    $(document).ready(function () {
        var options = $.extend({
            'autocompleteUrl': '',
            'minLength': 3
        }, WPCustomAutocomplete);

        $("#location").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: options.autocompleteUrl,
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 3,
            focus: function (event, ui) {
                $(this).val(ui.item.location);
                return false;
            },
            select: function (event, ui) {
                location = "#" + ui.item.slug;
                return false;
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        })
        .autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append("<a>" + item.location + "<br>Population:" + item.population + "</a>")
                .appendTo(ul);
        };
    });
})(jQuery);
$(document).ready(
    function ()
    {
        checkFormat();
        $("#id_category").hide();
        $("#tree_div").hide();
        var selected_cat_html = "<div class='row col-md-12'>" + $("#tree_div").attr('data-select-label')+"<span id='selected_category' style='font-weight: bold'> "+$("#tree_div").attr('data-default-name')+"</span></div><br/>";
        $("#id_category").parents(".felement").html(selected_cat_html+"<div class='col-12'>"+$("#tree_div").html()+"</div>"+$("#id_category").parents(".felement").html());

        //Selection of a category
        $('.course_category_tree').on('click', '.content a',function(e){
            var link = $(this);
            link.parent().click();
            $(".bold").each(function(){
                $(this).removeClass('bold');

            });
            link.addClass('bold');
            $("#selected_category").text(link.html());

            //We get the id of the category to put in the hidden field (necessary for form validation)
            var catid = link.attr('href').split('=')[1];
            $("#id_category").val(catid);
            // //$('.collapsible-actions a')[0].click();
            // $("#delete_category").show();
            // $('html, body').animate({scrollTop: $('#selected_category').offset().top-100}, 'slow');
            return false;
        });

        $("#select_default_category").click(function(e) {
            e.preventDefault();
            $(".bold").each(function(){
                // $(this).click();
                $(this).removeClass('bold');
            });
            var default_name = $(this).attr('data-default-name');
            $("#id_category").val($(this).attr('data-default-id'));
            $("#selected_category").text(default_name);
        });

        $('#id_format').change(checkFormat);
    }
);

/**
 * Display of fields in fonction of the chosen format
 */
function checkFormat() {
    if($('#id_format').val() == "singleactivity") {
        $('#id_activitytype').closest('.fitem').show();
        $('#id_numsections').closest('.fitem').hide();
    }
    else {
        $('#id_activitytype').closest('.fitem').hide();
        $('#id_numsections').closest('.fitem').show();
    }
}
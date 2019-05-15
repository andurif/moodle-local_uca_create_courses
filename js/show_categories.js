/**
 *
 * @package    local_uca_create_courses
 * @author     Université Clermont Auvergne - Anthony Durif
 * @copyright  2019 Université Clermont Auvergne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$(document).ready(
    function ()
    {
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
            var default_name = $("#tree_div").attr('data-default-name');
            $("#id_category").val($("#tree_div").attr('data-default-id'));
            $("#selected_category").text(default_name);
            var def_cat = $('a:visible:contains("' + default_name + '")');
            if(def_cat.length > 0) {
                def_cat.first().addClass('bold');
            }

        });
    }
);
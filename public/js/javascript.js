$(document).ready(function (){
    var nombreItems = $(".loop").length;
    var limitPerPage = $('#limitPerPage').val();
    $(".loop:gt(" + (limitPerPage - 1) + ")").hide();

    var totalPages = Math.round(nombreItems / limitPerPage);

    $(".pagination").append("<li class='page-item current-page active'><a class='page-link' href='javascript:void(0)'>" + 1 + "</a></li>");

    for(var i = 2; i <= totalPages; i++){
        $(".pagination").append("<li class='page-item current-page'><a class='page-link' href='javascript:void(0)'>" + i + "</a></li>");
    }

    $(".pagination").append("<li id='next-page' class='page-item'><a class='page-link' href='javascript:void(0)'>&raquo;</a></li>");

    $(".pagination li.current-page").on("click", function(){
        if($(this).hasClass("active")){
            return false;
        } else {
            var currentPage = $(this).index();
            $(".pagination li").removeClass("active");
            $(this).addClass("active");
            $(".loop").hide();

            var grandTotal = limitPerPage * currentPage;

            for (var i = grandTotal - limitPerPage; i < grandTotal; i++){
                $(".loop:eq(" + i + ")").fadeIn();
            }
        }

    });
    
    $("#next-page").on("click", function(){
        var currentPage = $(".pagination li.active").index();
        if(currentPage === totalPages){
            return false;
        } else {
            currentPage++;
            $(".pagination li").removeClass("active");
            $(".loop").hide();

            var grandTotal = limitPerPage * currentPage;

            for (var i = grandTotal - limitPerPage; i < grandTotal; i++) {
                $(".loop:eq(" + i + ")").fadeIn("slow")
            }
            $(".pagination li.current-page:eq(" + (currentPage - 1) + ")").addClass("active");
        }
    });

    $("#previous-page").on("click", function(){
        var currentPage = $(".pagination li.active").index();
        if(currentPage === 1){
            return false;
        } else {
            currentPage--;
            $(".pagination li").removeClass("active");
            $(".loop").hide();

            var grandTotal = limitPerPage * currentPage;

            for (var i = grandTotal - limitPerPage; i < grandTotal; i++) {
                $(".loop:eq(" + i + ")").fadeIn("slow")
            }
            $(".pagination li.current-page:eq(" + (currentPage - 1) + ")").addClass("active");
        }
    });

    ////////////////////////////////////////////////////
    /////////////////SCROLL SMOOTH//////////////////////
    ////////////////////////////////////////////////////
    $('a[href^="#AnchComment"]').click(function () {
        var the_id = $(this).attr("href");
        if (the_id === '#') {
            return;
        }
        $('html, body').animate({
            scrollTop: $(the_id).offset().top
        }, 'slow');
        return false;
    });
});
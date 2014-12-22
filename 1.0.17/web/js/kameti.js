$( document ).ready(function() {
    $("[rel='tooltip']").tooltip();

    $('#hover-cap-4col .kameti-thumbnail').hover(
        function(){
            $(this).find('.kameti-caption').slideDown(250); //.fadeIn(250)
        },
        function(){
            $(this).find('.kameti-caption').slideUp(250); //.fadeOut(205)
        }
    );
});


/* show lightbox when clicking a thumbnail */
$('a.thumb').click(function(event){
	event.preventDefault();
	var content = $('.modal-body');
	content.empty();
  	var title = $(this).attr("title");
  	$('.modal-title').html(title);
  	content.html($(this).html());
  	$(".modal-profile").modal({show:true});
});



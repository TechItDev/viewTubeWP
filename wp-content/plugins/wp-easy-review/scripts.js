(function($) {
  "use strict";
  
	  function loadDaBars() {
	  	$thescores.each(function() {
	  		var $score = $(this);
	  		var $width = $score.attr('data-score');
	  		$score.animate({width:$width+'%'}, 1000 );
	  	})
	  }  

	try {
	    var $thescores = $(".thescore");
	    var $section = $('.post-review');
	    var $queue = $({});	   		
	    var scrollOffset = $(document).scrollTop();
	    var containerOffset = $section.offset().top - window.innerHeight;
	    if (scrollOffset > containerOffset) {
	    	loadDaBars();
	    }
		


	    $(document).bind('scroll', function(ev) {
	        var scrollOffset = $(document).scrollTop();
	        var containerOffset = $section.offset().top - window.innerHeight;
	        if (scrollOffset > containerOffset) {
	            loadDaBars();
	            // unbind event not to load scrolsl again
	            $(document).unbind('scroll');
	        }
	    });		
	} catch (e) {
		// TODO: handle exception
		console.log( e.message );
	}
})(jQuery);
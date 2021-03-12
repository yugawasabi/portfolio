
/*--------------------------------------------------------------
	
	Script Name : Slide Down
	Author      : FIRSTSTEP - Motohiro Tani
	Author URL  : https://www.1-firststep.com
	Create Date : 2018/10/28
	Version     : 1.0
	Last Update : 2018/10/28
	
--------------------------------------------------------------*/


(function( $ ) {
	
	// function slide_click
	function slide_click() {
		
		var slide_class  = $( this ).attr( 'class' );
		var data_slide   = $( this ).attr( 'data-slide' );
		
		
		if ( slide_class.indexOf( 'active' ) !== -1 ) {
			$( 'div.'+ data_slide ).slideUp();
			$( this ).removeClass( 'active' );
		} else {
			$( 'div.'+ data_slide ).slideDown();
			$( this ).addClass( 'active' );
		}
		
	}
	
	
	
	
	$( '.slide-down' ).on( 'click', slide_click );
	
})( jQuery );
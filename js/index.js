//index,htmlの動作を記述
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
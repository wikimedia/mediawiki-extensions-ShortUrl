jQuery( function( $ ) {
	if( $( '#t-shorturl' ).length ) {
		var url = $( '#t-shorturl a' ).attr( 'href' );
		$( '#firstHeading' ).append( $( '<div class="title-shortlink-container"></div>').append( $( '<a>' ).addClass( 'title-shortlink' ).attr( 'href', url ).text( url ) ) );
	}
});

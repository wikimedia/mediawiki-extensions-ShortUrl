( function () {
	'use strict';

	$( function () {
		if ( $( '#t-shorturl' ).length ) {
			var url = $( '#t-shorturl a' ).attr( 'href' ),
				/* Add protocol for proto-relative urls */
				protoNonRelative = ( new mw.Uri( url ) ).toString();
			$( '#firstHeading' ).after(
				$( '<div>' )
					.addClass( 'title-shortlink-container' )
					.append( $( '<a>' )
						.addClass( 'title-shortlink' )
						.attr( 'href', url )
						.text( protoNonRelative )
					)
			);
		}
	} );

}() );

( function ( mw, $ ) {
	'use strict';

	$( function () {
		if ( $( '#t-shorturl' ).length ) {
			var url = $( '#t-shorturl a' ).attr( 'href' ),
				/* Add protocol for proto-relative urls */
				protoNonRelative = ( new mw.Uri( url ) ).toString();
			$( '#firstHeading' ).after(
				$( '<div class="title-shortlink-container"></div>')
					.append( $( '<a>' )
					.addClass( 'title-shortlink' )
					.attr( 'href', url )
					.text( protoNonRelative )
				)
			);
		}
	});

} ( mediaWiki, jQuery ) );

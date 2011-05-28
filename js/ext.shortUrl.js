jQuery( function ( $ ) {    
	if( $("#t-shorturl").length ) {
        var url = $("#t-shorturl a").attr("href");
        $("#firstHeading").append('<a class="title-shortlink" href="' + url + '">' + url + '</a>');
    }
});

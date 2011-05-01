function snippy_openFileInEditor( url )
{
	if( window.XMLHttpRequest ) {
		httpRequest = new XMLHttpRequest();
		
	} else if( window.ActiveXObject ) {
		try {
			httpRequest = new ActiveXObject( "Msxml2.XMLHTTP" );
			
		} catch( e ) {
			try {
				httpRequest = new ActiveXObject( "Microsoft.XMLHTTP" );
				
			} catch( e ) {}
		}
	}
	
	if( !httpRequest ) {
		return false;
	}
	
	httpRequest.open( 'GET', url );
	httpRequest.send();
}

function snippy_toggleBlock( block ) {
	var el = document.getElementById( block );

	if( el.className.indexOf( 'collapsed' ) > -1 )
		el.className = el.className.replace(/[\s]*collapsed[\s]*/, ' ');
	else
		el.className = (el.className + ' collapsed');
}
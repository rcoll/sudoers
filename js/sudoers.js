! ( function() {

	sudoers.utils = [];

	sudoers.utils.setCookie = function( k, v, h ) {
		sudoers.utils.debug( 'Set cookie ' + k + ' = ' + v );

		var expires = '';

		if ( h ) {
			var date = new Date();
			date.setTime( date.getTime() + ( h * 60 * 60 * 1000 ) );
			expires = '; expires=' + date.toGMTString();
		}

		var cookie = k + '=' + v + expires + '; path=/';

		document.cookie = cookie;
	};

	sudoers.utils.getCookie = function( k ) {
		sudoers.utils.debug( 'Get cookie ' + k );
		var nameEQ = k + '=';
		var ca = document.cookie.split( ';' );

		for ( var i = 0; i < ca.length; i++ ) {
			var c = ca[i];

			while ( ' ' == c.charAt( 0 ) ) {
				c = c.substring( 1, c.length );
			}

			if ( 0 == c.indexOf( nameEQ ) ) {
				return c.substring( nameEQ.length, c.length );
			}
		}

		return false;
	};

	sudoers.utils.deleteCookie = function( k ) {
		sudoers.utils.debug( 'Delete cookie ' + k );
		sudoers.utils.setCookie( k, '', -1 );
	};

	sudoers.utils.getEnvironment = function() {
		return sudoers.environment;
	};

	sudoers.utils.getQueryVars = function() {
		var string = window.location.href;
		var vars = {};
		var hash = [];

		if ( -1 === string.search( /\?/i ) ) {
			return [];
		}

		var hashes = string.slice( string.indexOf( '?' ) + 1 ).split( '&' );

		for ( var i = 0; i < hashes.length; i++ ) {
			hash = hashes[i].split( '=' );
			vars[hash[0]] = hash[1];
		}

		return vars;
	};

	sudoers.utils.getQueryVar = function( k ) {
		var vars = sudoers.utils.getQueryVars();

		return vars[k];
	};

	sudoers.utils.debug = function( d ) {
		if ( sudoers.debug_js ) {
			console.log( d );
		}
	};

})();
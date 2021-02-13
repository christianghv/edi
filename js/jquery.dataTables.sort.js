		jQuery.fn.dataTableExt.aTypes.unshift( 
					function ( sData )
					{
						var deformatted = sData.replace(/[^\d\-\.\,\/a-zA-Z]/g,'');
						if ( $.isNumeric( deformatted ) || deformatted === "-" ) {
							return 'formatted-num';
						}
						return null;
					}
				);
				
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"formatted-num-pre": function ( a ) {
				a = (a === "-" || a === "") ? 0 : a.replace( /[^\d\-\.\,]/g, "," );
				return parseFloat( a );
			},
			
			"formatted-num-asc": function ( a, b ) {
				return a - b;
			},
			
			"formatted-num-desc": function ( a, b ) {
				return b - a;
			}
		} );

		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"numeric-comma-pre": function ( a ) {
				var x = (a == "-") ? 0 : a.replace( /./, "," );
				return parseInt( x );
			},
		 
			"numeric-comma-asc": function ( a, b ) {
				return ((a < b) ? -1 : ((a > b) ? 1 : 0));
			},
		 
			"numeric-comma-desc": function ( a, b ) {
				return ((a < b) ? 1 : ((a > b) ? -1 : 0));
			}
		} );
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"date-uk-pre": function ( a ) {
				var ukDatea = a.split('/');
				var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
				if (isNaN(x)) { return 99999999; }
				return x;
				//return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
			},
		 
			"date-uk-asc": function ( a, b ) {
					return ((a < b) ? -1 : ((a > b) ? 1 : 0));
				},
		 
			"date-uk-desc": function ( a, b ) {
				return ((a < b) ? 1 : ((a > b) ? -1 : 0));
			}
		} );
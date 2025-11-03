// Title: Demo code for jQuery Datatables
// Location: tables.data.html
// Dependency File(s):
// assets/vendor/datatables.net/js/jquery.dataTables.js
// assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.js
// assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.css
// -----------------------------------------------------------------------------
var global_html;
(function(window, document, $, undefined) {
  	"use strict";
    $(function() {
		global_html = $('#bs4-table').DataTable({
      		'columnDefs': [{
	        	'targets': [1,-1], /* column index */
	        	// 'orderable': false, /* true or false */
	    	}];
      	});
    });
})(window, document, window.jQuery);

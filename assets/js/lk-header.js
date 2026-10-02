/**
 * Lila Kora — header mobile-menu toggle.
 *
 * Event-delegated on document rather than queried once at load, so it
 * keeps working even if Elementor re-renders this widget's markup
 * inside the editor after a settings change (a fresh querySelector
 * grab at parse time would otherwise go stale).
 */
( function () {
	function findHeader( toggleEl ) {
		return toggleEl.closest( '[data-lk-header]' );
	}

	document.addEventListener( 'click', function ( event ) {
		var toggle = event.target.closest( '.lk-menu-toggle' );
		if ( toggle ) {
			var header = findHeader( toggle );
			if ( ! header ) {
				return;
			}
			var menu = header.querySelector( '.lk-mobile-menu' );
			if ( ! menu ) {
				return;
			}
			var open = toggle.getAttribute( 'aria-expanded' ) === 'true';
			toggle.setAttribute( 'aria-expanded', String( ! open ) );
			toggle.setAttribute( 'aria-label', open ? 'Open menu' : 'Close menu' );
			menu.hidden = open;
			return;
		}

		// Close the mobile menu after any link inside it is clicked.
		var mobileLink = event.target.closest( '.lk-mobile-menu a' );
		if ( mobileLink ) {
			var header2 = mobileLink.closest( '[data-lk-header]' );
			if ( ! header2 ) {
				return;
			}
			var toggle2 = header2.querySelector( '.lk-menu-toggle' );
			var menu2 = header2.querySelector( '.lk-mobile-menu' );
			if ( toggle2 ) {
				toggle2.setAttribute( 'aria-expanded', 'false' );
				toggle2.setAttribute( 'aria-label', 'Open menu' );
			}
			if ( menu2 ) {
				menu2.hidden = true;
			}
		}
	} );
} )();

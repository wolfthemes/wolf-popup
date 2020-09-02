/*!
 * ModalWindow
 *
 * Popup Manager 1.0.1
 */
/* jshint -W062 */

/* global WVC, WolfPopupParams */
var WolfPopup = function( $ ) {

	'use strict';

	return {

		isMobile : false,
		animated : false,
		exitIntentFlag : false,
		open: false,
		timeDelayedClock : 0,
		timeDelayedCount : 0,
		exitIntentOpen : false,
		exitIntentClock : 0,

		/**
		 * Init UI
		 */
		init : function () {

			this.isMobile = WolfPopupParams.isMobile;

			if ( this.isMobile ) {
				//return;
			}

			var _this = this;

			this.timeDelayedClock = sessionStorage.getItem( 'wolf-popup-time-delayed-timer' );
			this.timeDelayedCount = sessionStorage.getItem( 'wolf-popup-time-delayed-count' ) || 0;
			
			this.exitIntentClock = sessionStorage.getItem( 'wolf-popup-exit-intent-timer' );
			this.exitIntentCount = sessionStorage.getItem( 'wolf-popup-exit-intent-count' ) || 0;

			this.timeDelayedPopUp();
			this.exitIntentPopUp();
			this.closeButton();

			// Reset and reinit anim effect
			$( window ).on( 'wvc_fullpage_changed', function() {
				WVC.resetAOS( $( '.wolf-popup' ) );
			} );

			WVC.resetAOS( $( '.wolf-popup' ) );

			/* Set popup height */
			$( window ).resize( function() {
				$( '.wolf-popup-overlay' ).css( {
					'height' : $( '.wolf-popup-container' ).height()
				} );
			} );
		},

		timeDelayedPopUp : function () {
			
			if ( $( '#wolf-popup-overlay-time-delayed' ).length ) {

				if ( 'opt-out' === Cookies.get( 'wolf_popup_time_delayed' ) ) {
					//console.log( 'cookie set' );
					return;
				}

				/**
				 * remove default opt-out link if set in content
				 */
				if ( $( '#wolf-popup-time-delayed' ).find( '.wolf-popup-close-opt-out' ).length ) {
					$( '#wolf-popup-overlay-time-delayed' ).find( '.wolf-popup-bottom-close' ).remove();
				}

				var _this = this,
					timer = null,
					$overlay = $( '#wolf-popup-overlay-time-delayed' ),
					delay = parseInt( $overlay.data( 'wolf-popup-delay' ) ),
					clookieTime = $overlay.data( 'wolf-popup-cookie-time' ),
					count = $overlay.data( 'wolf-popup-count' );

				//console.log( _this.timeDelayedCount );

				if ( count < _this.timeDelayedCount ) {
					//console.log( 'already displayed ' + count );
					return;
				}

				/* Start clock */
				timer = setInterval( function() {

					//console.log( delay );
					//console.log( _this.clock );
					//console.log( _this.closed );

					if ( count > _this.timeDelayedCount && delay < _this.timeDelayedClock && _this.open === false ) {

						_this.timeDelayedClock = 0;
						
						clearInterval( timer );
						sessionStorage.setItem( 'wolf-popup-time-delayed-timer', 0 );
						
						/* Show popup */
						_this.showPopup( 'time-delayed' );
					
					} else {
						_this.timeDelayedClock++;
						sessionStorage.setItem( 'wolf-popup-time-delayed-timer', _this.timeDelayedClock );
						//console.log( _this.timeDelayedClock );
					}
				
				}, 1000 );
			}
		},

		exitIntentPopUp : function () {
			
			if ( $( '#wolf-popup-overlay-exit-intent' ).length ) {

				if ( 'opt-out' === Cookies.get( 'wolf_popup_exit_intent' ) ) {
					//console.log( 'exit intent cookie set' );
					return;
				}

				/**
				 * remove default opt-out link if set in content
				 */
				if ( $( '#wolf-popup-exit-intent' ).find( '.wolf-popup-close-opt-out' ).length ) {
					$( '#wolf-popup-overlay-exit-intent' ).find( '.wolf-popup-bottom-close' ).remove();
				}

				var _this = this,
					timer = null,
					$overlay = $( '#wolf-popup-overlay-exit-intent' ),
					delay = parseInt( $overlay.data( 'wolf-popup-delay' ) ),
					clookieTime = $overlay.data( 'wolf-popup-cookie-time' ),
					count = $overlay.data( 'wolf-popup-count' );

				//console.log( _this.exitIntentCount );

				if ( count < _this.exitIntentCount ) {
					//console.log( 'already displayed ' + count );
					return;
				}

				/* Start clock */
				timer = setInterval( function() {

					//console.log( delay );
					//console.log( _this.clock );
					//console.log( _this.closed );

					if ( count > _this.exitIntentCount && delay < _this.exitIntentClock && _this.open === false ) {

						_this.exitIntentClock = 0;
						
						clearInterval( timer );
						sessionStorage.setItem( 'wolf-popup-exit-intent-timer', 0 );
						
						/* Show popup */
						$( document ).on( 'mouseleave', function() {
							//console.log( 'open ' + _this.open );
							_this.showPopup( 'exit-intent' );
						} );
					
					} else {
						_this.exitIntentClock++;
						sessionStorage.setItem( 'wolf-popup-exit-intent-timer', _this.exitIntentClock );
						//console.log( _this.exitIntentClock );
					}
				
				}, 1000 );
			}
		},

		/**
		 * Close button
		 */
		closeButton : function () {

			var _this = this,
				$closeButton,
				$overlay,
				type,
				cookieTime,
				count;

			$( document ).on( 'click', '.wolf-popup-close', function( event ) {
				
				event.preventDefault();

				$closeButton = $( this ),
				$overlay = $closeButton.closest( '.wolf-popup-overlay' );
				type = $overlay.data( 'wolf-popup-type' );
				cookieTime = $overlay.data( 'wolf-popup-cookie-time' );
				count = $overlay.data( 'wolf-popup-count' );

				if ( 'time-delayed' === type ) {
					
					/* Set cookie */
					if ( $closeButton.hasClass( 'wolf-popup-close-opt-out' ) ) {
						Cookies.set( 'wolf_popup_time_delayed', 'opt-out', { expires: cookieTime, path: '/' } );
					}

					_this.timeDelayedCount++;
					sessionStorage.setItem( 'wolf-popup-time-delayed-count', _this.timeDelayedCount );

					if ( count === _this.timeDelayedCount ) {
						Cookies.set( 'wolf_popup_time_delayed', 'opt-out', { expires: cookieTime, path: '/' } );
					}
				}

				if ( 'exit-intent' === type ) {
					_this.exitIntentFlag = true;

					if ( $closeButton.hasClass( 'wolf-popup-close-opt-out' ) ) {
						Cookies.set( 'wolf_popup_exit_intent', 'opt-out', { expires: cookieTime, path: '/' } );
					}

					_this.exitIntentCount++;
					sessionStorage.setItem( 'wolf-popup-exit-intent-count', _this.exitIntentCount );

					if ( count === _this.exitIntentCount ) {
						Cookies.set( 'wolf_popup_exit_intent', 'opt-out', { expires: cookieTime, path: '/' } );
					}
				}

				_this.closeAllPopups();
			} );
		},

		closeAllPopups : function( $this ) {

			var _this = this,
				$this = $this || $( '.wolf-popup-show' ),
				$parentOverlay = $this.closest( '.wolf-popup-overlay' );

			$( '.wolf-popup-overlay' ).removeClass( 'wolf-popup-overlay-visible' );
			$( '.wolf-popup-overlay' ).one( WVC.transitionEventEnd(), function() {
				$( this ).removeClass( 'wolf-popup-overlay-show' );
				setTimeout( function() {
					WVC.delayWow( $( '.wolf-popup' ) );
					WVC.resetAOS( $( '.wolf-popup' ) );
					_this.open = false;
					_this.animated = false;
				}, 200 );
			} );
		},

		/**
		 * Detect transition ending
		 */
		transitionEventEnd : function () {

			var t, el = document.createElement( 'transitionDetector' ),
				transEndEventNames = {
					'WebkitTransition' : 'webkitTransitionEnd',// Saf 6, Android Browser
					'MozTransition' : 'transitionend',  // only for FF < 15
					'transition' : 'transitionend'       // IE10, Opera, Chrome, FF 15+, Saf 7+
				};

			for ( t in transEndEventNames ) {
				if ( el.style[t] !== undefined ) {
					return transEndEventNames[t];
				}
			}
		},

		showPopup : function ( type ) {

			var _this = this,
				$overlay = $( '#wolf-popup-overlay-' + type );

			if ( true === this.open ) {
				return;
			}

			if ( this.exitIntentFlag && 'exit-intent' === type ) {
				return;
			}

			if ( ! _this.animated ) {
				WVC.delayWow( $( '#wolf-popup-' + type ) );
				WVC.resetAOS( $( '#wolf-popup-' + type ) );
			}

			$overlay.addClass( 'wolf-popup-overlay-visible' );

			_this.open = true;
			//console.log( 'open' );

			$overlay.one( WVC.transitionEventEnd(), function() {
				
				$overlay.addClass( 'wolf-popup-overlay-show' );
				
				$( '#wolf-popup-' + type ).one( WVC.transitionEventEnd(), function() {
					
					if ( ! _this.animated ) {
						WVC.doWow();
						WVC.doAOS( $( this ) );
						_this.animated = true;

						window.dispatchEvent( new Event( 'resize' ) );
						window.dispatchEvent( new Event( 'scroll' ) ); // Force WOW effect
					}
				} );
			} );
			
		}
	};

}( jQuery );

( function( $ ) {

	'use strict';

	$( window ).on( 'load', function() {
		WolfPopup.init();
	} );

} )( jQuery );
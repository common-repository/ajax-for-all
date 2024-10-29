/**
 * TODO
 *
 * Back/forward history
 * Forms ?
 */
jQuery(document).ready(function($) {

	/**
	 * Dispatcher class.
	 *
	 * @since 0.3
	 */
	function AjaxForAllDispatcher() {
		this.completed	= 0;
		this.request	= new AjaxForAllRequest;
		this.tmp		= '';
		this.fire		= function(href) {
			if ( this.completed == 0 ) {
				this.step(false);
				div.hide();
				this.content = this.request.get(href);
			}
		}
		this.step		= function(content) {
			this.completed = this.completed + 1;
			if ( content != false ) {
				this.tmp = content;
			}
			if ( this.completed == 3 ) {
				div.reveal(this.tmp);
				this.completed = 0;
			}
		}
	}

	/**
	 * Div class. The div that we will replace.
	 *
	 * @since 0.3
	 */
	function AjaxForAllDiv() {
		this.width		= 0;
		this.height		= 0;
		this.id			= afa_id;
		this.preserve	= afa_preserve_size;
		this.transition	= ajax_for_all_transition;
		this.transtime	= ajax_for_all_transtime;
		this.scrolltop	= ajax_for_all_scrolltop;
		this.scrolltime	= ajax_for_all_scrolltime;
		this.hide		= function() {
			if ( this.preserve == true ) {
				this.height = $('#content').css( 'height' );
				this.width = $('#content').css( 'width' );
				$('#' + this.id ).wrapInner( '<div id="afasize" />' );
				$('#afasize').wrapInner( '<div id="afacontent" />' );
				$('#afasize').css( 'height', this.height );
				$('#afasize').css( 'width', this.width );
			}
			else {
				$('#' + this.id ).wrapInner( '<div id="afacontent" />' );
			}
			$('#' + this.id ).prepend( '<div id="afaspinner"> </div>' );
			$('#afaspinner').fadeIn();
			// scroll to top
			if (this.scrolltop) {
				$('html, body').animate({
					scrollTop: 0
				}, this.scrolltime);
			}
			// transition
			switch (this.transition) {
				case 'none':
					dispatcher.step(false);
				break;
				case 'fade':
					$('#afacontent').fadeOut(
						this.transtime,
						function() {
							dispatcher.step(false);
						}
					);
				break;
				default:
				case 'slide':
					$('#afacontent').slideUp(
						this.transtime,
						function() {
							dispatcher.step(false);
						}
					);
				break;
			}
		}
		this.reveal = function(tmp) {
			if ( typeof AjaxForAllCallback == 'function' ) {
				AjaxForAllCallback(tmp); // callback that gets all the data
			}
			// transition
			switch (this.transition) {
				case 'none':
					$('#' + this.id ).html(tmp.content);
				break;
				case 'fade':
					$('#' + this.id ).html(tmp.content);
					$('#' + this.id ).fadeIn(this.transtime);
				break;
				default:
				case 'slide':
					$('#' + this.id ).hide();
					$('#' + this.id ).html(tmp.content);
					$('#' + this.id ).slideDown(this.transtime);
				break;
			}
			// The position seems to be accurate even when the slideDown isn't
			// finished yet:
			if ( tmp.jump ) {
				anchor	= $('a[name|=' + tmp.jumpto + ']');
				offset	= anchor.offset();
				ytop	= offset.top;
				$('html, body').animate({
					scrollTop: ytop
				}, this.scrolltime);
			}
		}
	}

	/**
	 * Request class. Gets the content we will inject.
	 *
	 * @since 0.3
	 */
	function AjaxForAllRequest() {
		this.ajaxurl	= ajaxurl;
		this.user		= ajax_for_all_curl_user;
		this.nonce		= ajax_for_all_curl_nonce;
		this.get		= function(href) {
			$.getJSON(
				this.ajaxurl, {
					action: 'ajax_for_all',
					href: href,
					user: this.user,
					nonce: this.nonce
				}, function(data) {
					if (data.success) {
						dispatcher.step(data);
					}
					else {
						// just follow the link then
						window.location = href;
					}
				}
			);
		}
	}

	/**
	 * Instantiate the unique objects
	 */
	dispatcher = new AjaxForAllDispatcher();
	div = new AjaxForAllDiv();

	/**
	 * Event binding
	 */
	$('a').live( 'click',
		function() {
			if ( $(this).attr('target') ) {
				return true;
			}
			var href = $(this).attr('href');
			$.bbq.pushState({ url: href });
			if ( href.indexOf('#') == 0 ) {
				return true;
			}
			dispatcher.fire(href);
			return false;
		}
	);

	// Bind a callback that executes when document.location.hash changes.
	$(window).bind( "hashchange", function(e) {
		var hash = e.getState( 'url' );
		if ( hash !== undefined ) {
			dispatcher.fire(hash);
		}
		else if ( window.location != afa_root ) {
			dispatcher.fire(afa_root);
		}
	});

	if ( afa_nodeeplink != 'on' ) {
		$(window).trigger( 'hashchange' );
	}

});

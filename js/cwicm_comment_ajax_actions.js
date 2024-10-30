jQuery( document ).ready( function ( $ ) {
	// Use '' to disable the delete's confirmation; and do that at your own risks..
	var confirmDeleteText = 'Are you sure you want to permanently delete this comment?? This action cannot be reversed.';

	// Removes the spam/trashed/deleted comment's element from the DOM. Make sure
	// the selectors like LI.comment and .comment-body exist in your comments
	// list.
	function removeComment( link ) {
		var $comment  = $( link ).closest( 'li.comment' );
		var $children = $comment.find( 'li.comment' );
		var $parent   = $comment.closest( 'li.parent' );

		var $el = $comment;
		if ( $children.length >= 1 ) {
			$el = $( link ).closest( '.comment-body' );
		}

		// Fade-out the comment or comment body element upon removing it. Just
		// replace the background-color's value to whatever you like, or if
		// you don't want that color effect, then remove it.
		$el.css( 'background-color', '#CCEEBB' ).fadeOut( 350, function () {
			$( this ).remove();

			// If the parent element no longer contains any comment elements,
			// then the parent element will be deleted.
			if ( $parent.find( 'li.comment' ).length < 1 ) {
				$parent.remove();
			}
		} );
	}

	// Updates a comment's status, or delete the comment, via the REST API.
	function commentApiRequest( link ) {
		var action     = $( link ).data( 'action' );
		var comment_id = $( link ).data( 'id' );
		var isDeleting = ( 'delete' === action );

		if ( confirmDeleteText && isDeleting && ! confirm( confirmDeleteText ) ) {
			return false;
		}

		wp.apiRequest( {
			path: 'wp/v2/comments/' + comment_id,
			method: isDeleting ? 'DELETE' : 'POST',
			data: isDeleting ? { force: true } : { status: action }
		} ).done( function () {
			removeComment( link );
		} );
	}

	// Add the AJAX action links (Spam, Trash and Delete) next to the comment's
	// edit link. Just change the HTML to your own liking.
	$( '.comment-edit-link', 'li.comment' ).after( function () {
		var matches    = this.href.match( /[\?&]c=(\d+)/ );
		var comment_id = matches[1] || 0;

		return ' &nbsp;&nbsp;<a href="#" class="CWIMC_comment_ajax_action" data-id="' + comment_id + '" data-action="spam" title="Move Comment to Spam">(Spam)</a>' +
			' &nbsp;&nbsp;<a href="#" class="CWIMC_comment_ajax_action" data-id="' + comment_id + '" data-action="trash" title="Move Comment to Trash">(Trash)</a>' +
			' &nbsp;&nbsp;<a href="#" class="CWIMC_comment_ajax_action" data-id="' + comment_id + '" data-action="delete" title="Permanently Delete Comment">(Delete)</a>';
	} );

	// If you changed the action link's class (default: CWIMC_comment_ajax_action),
	// then be sure to also change the one below!
	$( '.CWIMC_comment_ajax_action' ).on( 'click', function ( e ) {
		e.preventDefault();
		commentApiRequest( this );
	} );
} );
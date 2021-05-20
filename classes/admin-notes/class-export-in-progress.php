<?php
namespace WooCommerceCustobar\Admin\Notes;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

defined( 'ABSPATH' ) || exit;

/**
 * The initial Storefront inbox Message.
 */
class Export_In_Progress {

	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'woocommerce-custobar-export-in-progress-notice';
    
    // WooCommerce update notice works so that it updates the existing notice data based on the update status...
    // What we could do is set this up based on actions... 
    // By default WooCommerce cretes only 

    // Let's add a different note for each content type separately.
    
    // Also todo force actions

    /**
	 * Get the note.
	 *
	 * @return Note
	 */
    public static function get_note() {
		$note = new Note();
		$pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=woocommerce_run_update&status=pending' );
		$note->set_title( __( 'Custobar export in progress', 'woocommerce-custobar' ) );
		$note->set_content( __( 'We are currently uploading information to Custobar in the background. A separate notification will be shown once the upload is complete. ', 'woocommerce-custobar' ) );
        $note->set_type( Note::E_WC_ADMIN_NOTE_UPDATE );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-custobar' );
		$note->add_action(
			'woocommerce-custobar-see-progress',
			__( 'Check progress', 'woocommerce-custobar' ),
			admin_url( 'admin.php?page=wc-settings&tab=checkout&tab=custobar' ),
			'unactioned',
			true
		);
		$note->add_action(
			'woocommerce-custobar-in-progress-note-hide',
			__( 'Hide', 'woocommerce' ),
			'',
			'actioned',
			false
		);

		
        // Allow hide only once complete and link to status page
        return $note;
	}
	
}

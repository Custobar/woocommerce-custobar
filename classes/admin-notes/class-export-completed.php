<?php
namespace WooCommerceCustobar\Admin\Notes;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

defined( 'ABSPATH' ) || exit;

/**
 * The initial Storefront inbox Message.
 */
class Export_Completed {

	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'woocommerce-custobar-export-completed-note';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$note = new Note();
		$note->set_title( __( 'Custobar export completed', 'woocommerce-custobar' ) );
		$note->set_content( __( 'All Custobar exports that were in progress are now completed.', 'woocommerce-custobar' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_UPDATE );
		// This is called automatically by possibly_add_notes if note with same name does not exist.
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-custobar' );
		$note->add_action(
			'woocommerce-custobar-see-progress',
			__( 'See overview', 'woocommerce-custobar' ),
			admin_url( 'admin.php?page=wc-settings&tab=checkout&tab=custobar' ),
			'actioned',
			true
		);
		// Allow hide only once complete and link to status page
		return $note;
	}

	/**
	 * In comparison to other notes, this note can be added multiple times.
	 *
	 * @return bool
	 */
	public static function can_be_added() {
		$note = self::get_note();

		if ( ! $note instanceof Note && ! $note instanceof WC_Admin_Note ) {
			return;
		}

		return true;
	}

}

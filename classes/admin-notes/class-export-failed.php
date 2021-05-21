<?php
namespace WooCommerceCustobar\Admin\Notes;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\NoteTraits;

defined( 'ABSPATH' ) || exit;

/**
 * The initial Storefront inbox Message.
 */
class Export_Failed {

	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'woocommerce-custobar-export-failed';
	
	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {
		$note                = new Note();
		$note->set_title( __( 'Custobar export failed', 'woocommerce-custobar' ) );
		$note->set_content( __( 'A custobar export process has failed. Please make sure that you have entered Custobar API credentials.', 'woocommerce-custobar' ) );
		$note->set_type( Note::E_WC_ADMIN_NOTE_ERROR );
		$note->set_name( self::NOTE_NAME );
		$note->set_content_data( (object) array() );
		$note->set_source( 'woocommerce-custobar' );
		$note->add_action(
			'woocommerce-custobar-failed-note-see-progress',
			__( 'Check status', 'woocommerce-custobar' ),
			admin_url( 'admin.php?page=wc-settings&tab=checkout&tab=custobar' ),
			'actioned',
			true
		);
		$note->add_action(
			'woocommerce-custobar-failed-note-hide',
			__( 'Hide', 'woocommerce-custobar' ),
			'',
			'actioned',
			false
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

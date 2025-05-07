<?php

namespace App;

// TODO: Improve the readability of this file through refactoring and documentation.

require_once dirname( __DIR__ ) . '/globals.php';

class App {

	public function save( $title, $body ) {
		error_log( "Saving article $title, success!" );
		file_put_contents( $title, $body );
	}

	public function update( $title, $body ) {
		$this->save( $title, $body );
	}

	public function fetch( $get ) {
		$title = $get['title'] ?? null;
		return is_array( $get ) ? file_get_contents( sprintf( 'articles/%s', $get['title'] ) ) :
			file_get_contents( sprintf( 'articles/%s', $_GET['title'] ) );
	}

	public function getListOfArticles() {
		global $wgBaseArticlePath;
		return array_diff( scandir( $wgBaseArticlePath ), [ '.', '..', '.DS_Store' ] );
	}
}

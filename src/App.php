<?php

namespace App;

// TODO: Improve the readability of this file through refactoring and documentation.

require_once dirname( __DIR__ ) . '/globals.php';

class App {

	public function saveArticle( $title, $body ) {
		error_log( "Saving article $title, success!" );
		file_put_contents( $title, $body );
	}

	public function updateArticle( $title, $body ) {
		$this->saveArticle( $title, $body );
	}

	public function fetchArticle( $get ) {
		$title = $get['title'] ?? null;
		return is_array( $get ) ? file_get_contents( sprintf( 'articles/%s', $get['title'] ) ) :
			file_get_contents( sprintf( 'articles/%s', $_GET['title'] ) );
	}

	public function getCurrentArticles() {
		global $wgBaseArticlePath;
		return array_diff( scandir( $wgBaseArticlePath ), [ '.', '..', '.DS_Store' ] );
	}
}

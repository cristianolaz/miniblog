<?php
namespace Acme\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Admin {

	/**
	 * dashboard
	 */
	public function dashboard( Request $request, Application $app ) {
		$last_posts = $app[ 'db' ]->fetchAll( 'SELECT * FROM posts ORDER BY id DESC LIMIT 10' );
		$last_comments = $app[ 'db' ]->fetchAll( 'SELECT * FROM comments ORDER BY id DESC LIMIT 10' );
		return $app[ 'twig' ]->render( 'admin/dashboard.twig', array( 'last_posts' => $last_posts, 'last_comments' => $last_comments ));
	}
}
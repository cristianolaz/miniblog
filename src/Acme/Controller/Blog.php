<?php
namespace Acme\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Blog {

	/**
	 * home
	 */
	public function home( Application $app ) {
		$posts = $app[ 'db' ]->fetchAll( 'SELECT * FROM posts ORDER BY id DESC LIMIT 15' );
		$popular_posts = $app[ 'db' ]->fetchAll( 'SELECT p.id, p.title, p.slug, count(c.posts_id) as comments_count FROM posts p LEFT JOIN comments c ON c.posts_id = p.id GROUP BY p.id ORDER BY comments_count DESC LIMIT 5' );
		
		return $app[ 'twig' ]->render( 'index.twig', array( 'posts' => $posts, 'popular_posts' => $popular_posts ));
	}

	/**
	 * contact
	 */
	public function pagina_contacto( Application $app ) {
		$popular_posts = $app[ 'db' ]->fetchAll( 'SELECT p.id, p.title, p.slug, count(c.posts_id) as comments_count FROM posts p LEFT JOIN comments c ON c.posts_id = p.id GROUP BY p.id ORDER BY comments_count DESC LIMIT 5' );
		
		return $app[ 'twig' ]->render( 'contact.twig', array( 'popular_posts' => $popular_posts ));
	}

	/**
	 * view_post
	 */
	public function view_post( $id, $slug, Application $app ) {
		$post = $app[ 'db' ]->executeQuery( 'SELECT p.*, c.category as category FROM posts p LEFT JOIN categories c ON c.id = p.categories_id WHERE p.id = ?', array( $id ))->fetch();
		if( ! $post ) $app->abort( 404 );
		$comments = $app[ 'db' ]->fetchAll( 'SELECT * FROM comments WHERE posts_id = ?', array( $id ));
		$popular_posts = $app[ 'db' ]->fetchAll( 'SELECT p.id, p.title, p.slug, count(c.posts_id) as comments_count FROM posts p LEFT JOIN comments c ON c.posts_id = p.id GROUP BY p.id ORDER BY comments_count DESC LIMIT 5' );
		
		return $app[ 'twig' ]->render( 'view-post.twig', array( 'post' => $post, 'comments' => $comments, 'popular_posts' => $popular_posts ));
	}

	/**
	 * leave-comment
	 */
	public function leave_comment( $post_id, Request $request, Application $app ) {
		$app[ 'db' ]->insert( 'comments', array( 'posts_id' => $post_id, 'content' => $request->get( 'content' )));
		$app[ 'session' ]->getFlashBag()->add( 'message', 'Gracias por tu comentario' );
		return $app->redirect( $request->headers->get( 'referer' ));
	}
}
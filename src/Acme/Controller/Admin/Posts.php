<?php
namespace Acme\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use \Acme\Model\Post;

class Posts {

	/**
	 * read
	 */
	public function read( Request $request, Application $app ) {
		$qb = $app[ 'db' ]->createQueryBuilder();
		$page = ( int ) $request->get( 'page' );
		if( $page < 1 ) $page = 1;
		$posts = $qb->select( '*')
			->from( 'posts', 'p' )
			->orderBy( 'p.id', 'DESC' )
			->setFirstResult(( $page - 1 ) * 20 )
			->setMaxResults( 20 )
			->execute()
			->fetchAll();
		return $app[ 'twig' ]->render( 'admin/posts/read.twig', array( 'posts' => $posts, 'page' => $page ));
	}
	
	/**
	 * add
	 */
	public function add( Request $request, Application $app ) {
		$categories = $app[ 'db' ]->fetchAll( 'SELECT * FROM categories' );
		return $app[ 'twig' ]->render( 'admin/posts/add.twig', array( 'categories' => $categories ));
	}
	
	/**
	 * post: add
	 */
	public function post_add( Request $request, Application $app ) {
		$post = new \Acme\Model\Post( $app );
		$post->setTitle( $request->get( 'title'));
		$post->setSlug( $request->get( 'slug' ));
		$post->setContent( $request->get( 'content' ));
		$post->setCategory( $request->get( 'categories_id' ));
		$post->setThumbnail( $request->get( 'thumbnail' ));
		$errors = $post->validate();
		if( count( $errors) == 0 ) {
			$post->save();
			$app[ 'session' ]->getFlashBag()->add( 'message', 'Se creó la publicación' );
			return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/dashboard' ));
		}
		foreach( $errors as $error ) {
			$app[ 'session' ]->getFlashBag()->add( 'validation.post', $error->getPropertyPath() . ': ' . $error->getMessage() );
		}
		return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/posts/add' ));
	}
	
	/**
	 * update
	 */
	public function update( $id, Request $request, Application $app ) {
		$post = $app[ 'db' ]->executeQuery( 'SELECT * FROM posts WHERE id = ?', array( $id ))->fetch();
		if( ! $post ) $app->abort( 404 );
		$categories = $app[ 'db' ]->fetchAll( 'SELECT * FROM categories' );
		return $app[ 'twig' ]->render( 'admin/posts/update.twig', array( 'post' => $post, 'categories' => $categories ));
	}
	
	/**
	 * post: update
	 */
	public function post_update( $id, Request $request, Application $app ) {
		$post = new \Acme\Model\Post( $app );
		$post->setId( $id );
		$post->setTitle( $request->get( 'title'));
		$post->setSlug( $request->get( 'slug' ));
		$post->setContent( $request->get( 'content' ));
		$post->setCategory( $request->get( 'categories_id' ));
		$post->setThumbnail( $request->get( 'thumbnail' ));
		$errors = $post->validate();
		if( count( $errors) == 0 ) {
			$post->update();
			$app[ 'session' ]->getFlashBag()->add( 'message', 'Se editó la publicación' );
			return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/dashboard' ));
		}
		foreach( $errors as $error ) {
			$app[ 'session' ]->getFlashBag()->add( 'validation.post', $error->getPropertyPath() . ': ' . $error->getMessage() );
		}
		return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/posts/update', array( 'id' => $id )));
	}
	
	/**
	 * delete
	 */
	public function delete( Request $request, Application $app ) {
		$app[ 'db' ]->delete( 'comments', array( 'posts_id' => $request->get( 'post_id' )));
		$app[ 'db' ]->delete( 'posts', array( 'id' => $request->get( 'post_id' )));
		$app['session']->getFlashBag()->add( 'message', 'Se borró la publicación' );
		return $app->redirect( $request->headers->get( 'referer' ));
	}
}
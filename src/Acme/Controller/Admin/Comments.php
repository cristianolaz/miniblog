<?php
namespace Acme\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use \Acme\Model\Comment;

class Comments {

	/**
	 * read
	 */
	public function read( Request $request, Application $app ) {
		$qb = $app[ 'db' ]->createQueryBuilder();
		$page = ( int ) $request->get( 'page' );
		if( $page < 1 ) $page = 1;
		$comments = $qb->select( '*')
			->from( 'comments', 'c' )
			->orderBy( 'c.id', 'DESC' )
			->setFirstResult(( $page - 1 ) * 20 )
			->setMaxResults( 20 )
			->execute()
			->fetchAll();
		return $app[ 'twig' ]->render( 'admin/comments/read.twig', array( 'comments' => $comments, 'page' => $page ));
	}
	
	/**
	 * update
	 */
	public function update( $id, Request $request, Application $app ) {
		$comment = $app[ 'db' ]->executeQuery( 'SELECT * FROM comments WHERE id = ?', array( $id ))->fetch();
		if( ! $comment ) $app->abort( 404 );
		return $app[ 'twig' ]->render( 'admin/comments/update.twig', array( 'comment' => $comment ));
	}
	
	/**
	 * post: update
	 */
	public function post_update( $id, Request $request, Application $app ) {
		$comment = new \Acme\Model\Comment( $app );
		$comment->setId( $id );
		$comment->setContent( $request->get( 'content' ));
		$errors = $comment->validate();
		if( count( $errors) == 0 ) {
			$comment->update();
			$app[ 'session' ]->getFlashBag()->add( 'message', 'Se editó el comentario' );
			return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/dashboard' ));
		}
		foreach( $errors as $error ) {
			$app[ 'session' ]->getFlashBag()->add( 'validation.comment', $error->getPropertyPath() . ': ' . $error->getMessage() );
		}
		return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/comments/update', array( 'id' => $id )));
	}
	
	/**
	 * delete
	 */
	public function delete( Request $request, Application $app ) {
		$app[ 'db' ]->delete( 'comments', array( 'id' => $request->get( 'comment_id' )));
		$app['session']->getFlashBag()->add( 'message', 'Se borró el comentario' );
		return $app->redirect( $request->headers->get( 'referer' ));
	}
}
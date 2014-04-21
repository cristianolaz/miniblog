<?php
namespace Acme\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use \Acme\Model\Category;

class Categories {

	/**
	 * read
	 */
	public function read( Request $request, Application $app ) {
		$qb = $app[ 'db' ]->createQueryBuilder();
		$page = ( int ) $request->get( 'page' );
		if( $page < 1 ) $page = 1;
		$categories = $qb->select( '*')
			->from( 'categories', 'c' )
			->orderBy( 'c.id', 'DESC' )
			->setFirstResult(( $page - 1 ) * 20 )
			->setMaxResults( 20 )
			->execute()
			->fetchAll();
		return $app[ 'twig' ]->render( 'admin/categories/read.twig', array( 'categories' => $categories, 'page' => $page ));
	}
	
	/**
	 * add
	 */
	public function add( Request $request, Application $app ) {
		return $app[ 'twig' ]->render( 'admin/categories/add.twig' );
	}
	
	/**
	 * post: add
	 */
	public function post_add( Request $request, Application $app ) {
		$category = new \Acme\Model\Category( $app );
		$category->setSlug( $request->get( 'slug' ));
		$category->setCategory( $request->get( 'category' ));
		$errors = $category->validate();
		if( count( $errors) == 0 ) {
			$category->save();
			$app[ 'session' ]->getFlashBag()->add( 'message', 'Se creó la categoría' );
			return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/dashboard' ));
		}
		foreach( $errors as $error ) {
			$app[ 'session' ]->getFlashBag()->add( 'validation.category', $error->getPropertyPath() . ': ' . $error->getMessage() );
		}
		return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/categories/add' ));
	}
	
	/**
	 * update
	 */
	public function update( $id, Request $request, Application $app ) {
		$category = $app[ 'db' ]->executeQuery( 'SELECT * FROM categories WHERE id = ?', array( $id ))->fetch();
		if( ! $category ) $app->abort( 404 );
		return $app[ 'twig' ]->render( 'admin/categories/update.twig', array( 'category' => $category ));
	}
	
	/**
	 * post: update
	 */
	public function post_update( $id, Request $request, Application $app ) {
		$category = new \Acme\Model\Category( $app );
		$category->setId( $id );
		$category->setSlug( $request->get( 'slug' ));
		$category->setCategory( $request->get( 'category' ));
		$errors = $category->validate();
		if( count( $errors) == 0 ) {
			$category->update();
			$app[ 'session' ]->getFlashBag()->add( 'message', 'Se editó la categoría' );
			return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/dashboard' ));
		}
		foreach( $errors as $error ) {
			$app[ 'session' ]->getFlashBag()->add( 'validation.category', $error->getPropertyPath() . ': ' . $error->getMessage() );
		}
		return $app->redirect( $app[ 'url_generator' ]->generate( 'admin/categories/update', array( 'id' => $id )));
	}
	
	/**
	 * delete
	 */
	public function delete( Request $request, Application $app ) {
		$app[ 'db' ]->update( 'posts', array( 'categories_id' => NULL ), array( 'categories_id' => $request->get( 'category_id' )));
		$app[ 'db' ]->delete( 'categories', array( 'id' => $request->get( 'category_id' )));
		$app[ 'session' ]->getFlashBag()->add( 'message', 'Se borró la cateegoría' );
		return $app->redirect( $request->headers->get( 'referer' ));
	}
}
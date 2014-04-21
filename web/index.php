<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Silex\Provider;
$app = new Silex\Application();
$app->register( new Silex\Provider\TwigServiceProvider(), array( 'twig.path' => __DIR__.'/../src/Acme/Views' ));
$app->register( new DerAlex\Silex\YamlConfigServiceProvider( __DIR__ . '/../config/settings.yml' ));
$app->register( new Silex\Provider\DoctrineServiceProvider(), array( 'db.options' => $app[ 'config' ][ 'database' ]));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());
$app->before(function () use ($app) { $app[ 'twig' ]->addGlobal( 'settings', $app[ 'config' ][ 'user' ]); });


/**
 * Routes
 */
// home
$app->get( '/', 'Acme\\Controller\\Blog::home' )->bind( 'home' );
// contact
$app->get( '/paginas/contacto/', 'Acme\\Controller\\Blog::pagina_contacto' )->bind( 'paginas/contacto' );
// view-post
$app->get( '/{id}/{slug}/', 'Acme\\Controller\\Blog::view_post' )->assert( 'id', '\d+' )->bind( 'view-post' );
// leave-comment
$app->post( '/leave-comment/{post_id}/', 'Acme\\Controller\\Blog::leave_comment' )->assert( 'post_id', '\d+' )->bind( 'leave-comment' );


// dashboard
$app->get( '/admin/dashboard/', 'Acme\\Controller\\Admin::dashboard' )->bind( 'admin/dashboard' );

// posts table
$app->get( '/admin/posts/read/', 'Acme\\Controller\\Admin\\Posts::read' )->bind( 'admin/posts/read' );
$app->get( '/admin/posts/add/', 'Acme\\Controller\\Admin\\Posts::add' )->bind( 'admin/posts/add' );
$app->post( '/admin/posts/add/', 'Acme\\Controller\\Admin\\Posts::post_add' );
$app->get( '/admin/posts/update/{id}/', 'Acme\\Controller\\Admin\\Posts::update' )->bind( 'admin/posts/update' );
$app->post( '/admin/posts/update/{id}/', 'Acme\\Controller\\Admin\\Posts::post_update' );
$app->post( '/admin/posts/delete/', 'Acme\\Controller\\Admin\\Posts::delete' )->bind( 'admin/posts/delete' );

// comments table
$app->get( '/admin/comments/read/', 'Acme\\Controller\\Admin\\Comments::read' )->bind( 'admin/comments/read' );
$app->get( '/admin/comments/update/{id}/', 'Acme\\Controller\\Admin\\Comments::update' )->bind( 'admin/comments/update' );
$app->post( '/admin/comments/update/{id}/', 'Acme\\Controller\\Admin\\Comments::post_update' );
$app->post( '/admin/comments/delete/', 'Acme\\Controller\\Admin\\Comments::delete' )->bind( 'admin/comments/delete' );

// categories table
$app->get( '/admin/categories/read/', 'Acme\\Controller\\Admin\\Categories::read' )->bind( 'admin/categories/read' );
$app->get( '/admin/categories/add/', 'Acme\\Controller\\Admin\\Categories::add' )->bind( 'admin/categories/add' );
$app->post( '/admin/categories/add/', 'Acme\\Controller\\Admin\\Categories::post_add' );
$app->get( '/admin/categories/update/{id}/', 'Acme\\Controller\\Admin\\Categories::update' )->bind( 'admin/categories/update' );
$app->post( '/admin/categories/update/{id}/', 'Acme\\Controller\\Admin\\Categories::post_update' );
$app->post( '/admin/categories/delete/', 'Acme\\Controller\\Admin\\Categories::delete' )->bind( 'admin/categories/delete' );

$app->run();
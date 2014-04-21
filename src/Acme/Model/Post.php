<?php
namespace Acme\Model;
use \Silex\Application;
use \Symfony\Component\Validator\Mapping\ClassMetadata;
use \Symfony\Component\Validator\Constraints as Assert;

class Post {
	protected $id;
	protected $title;
	protected $slug;
	protected $content;
	protected $thumbnail;
	protected $category;
	protected $_app;
	
	/**
	 * Constructor
	 */
	public function __construct( $app ) {
		$this->_app = $app;
	}
	
	/**
	 * getAsserts
	 */
	static public function loadValidatorMetadata(ClassMetadata $metadata) {
		$metadata->addPropertyConstraint( 'title', new Assert\NotBlank());
		$metadata->addPropertyConstraint( 'title', new Assert\Length( array( 'max' => 100 )));
		$metadata->addPropertyConstraint( 'slug', new Assert\NotBlank());
		$metadata->addPropertyConstraint( 'slug', new Assert\Length( array( 'max' => 100 )));
		$metadata->addPropertyConstraint( 'content', new Assert\NotBlank());
		$metadata->addPropertyConstraint( 'content', new Assert\Length( array( 'max' => 100000 )));
		$metadata->addPropertyConstraint( 'thumbnail', new Assert\Length( array( 'max' => 200 )));
	}
	
	/**
	 * Validate
	 */
	public function validate() {
		return $this->_app['validator']->validate( $this );
	}
	
	/**
	 * Save
	 */
	public function save() {
		$this->_app[ 'db' ]->insert( 'posts', array(
			'title' => $this->title,
			'slug' => $this->slug,
			'content' => $this->content,
			'categories_id' => $this->category,
			'thumbnail' => $this->thumbnail
		));
	}
	
	/**
	 * Update
	 */
	public function update() {
		$this->_app[ 'db' ]->update( 'posts', array(
			'title' => $this->title,
			'slug' => $this->slug,
			'content' => $this->content,
			'categories_id' => $this->category,
			'thumbnail' => $this->thumbnail
		), array( 'id' => $this->id ));
	}
	
	public function getId() { return $this->id; }
	
	public function getTitle() { return $this->title; }
	
	public function getSlug() { return $this->slug; }
	
	public function getContent() { return $this->content; }
	
	public function getThumbnail() { return $this->thumbnail; }
	
	public function getCategory() { return $this->category; }
	
	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}
	
	public function setTitle( $title ) {
		$this->title = $title;
		return $this;
	}
	
	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}
	
	public function setContent( $content ) {
		$this->content = $content;
		return $this;
	}
	
	public function setThumbnail( $thumbnail ) {
		$this->thumbnail = $thumbnail;
		return $this;
	}
	
	public function setCategory( $category ) {
		$this->category = $category;
		return $this;
	}
}
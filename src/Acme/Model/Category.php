<?php
namespace Acme\Model;
use \Silex\Application;
use \Symfony\Component\Validator\Mapping\ClassMetadata;
use \Symfony\Component\Validator\Constraints as Assert;

class Category {
	protected $id;
	protected $slug;
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
		$metadata->addPropertyConstraint( 'slug', new Assert\NotBlank());
		$metadata->addPropertyConstraint( 'slug', new Assert\Length( array( 'max' => 100 )));
		$metadata->addPropertyConstraint( 'category', new Assert\NotBlank());
		$metadata->addPropertyConstraint( 'category', new Assert\Length( array( 'max' => 100 )));
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
		$this->_app[ 'db' ]->insert( 'categories', array(
			'slug' => $this->slug,
			'category' => $this->category
		));
	}
	
	/**
	 * Update
	 */
	public function update() {
		$this->_app[ 'db' ]->update( 'categories', array(
			'slug' => $this->slug,
			'category' => $this->category
		), array( 'id' => $this->id ));
	}
	
	public function getId() { return $this->id; }
	
	public function getSlug() { return $this->slug; }
	
	public function getCategory() { return $this->category; }
	
	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}
	
	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}
	
	public function setCategory( $category ) {
		$this->category = $category;
		return $this;
	}
}
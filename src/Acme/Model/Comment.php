<?php
namespace Acme\Model;
use \Silex\Application;
use \Symfony\Component\Validator\Mapping\ClassMetadata;
use \Symfony\Component\Validator\Constraints as Assert;

class Comment {
	protected $id;
	protected $content;
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
		$metadata->addPropertyConstraint( 'content', new Assert\NotBlank());
		$metadata->addPropertyConstraint( 'content', new Assert\Length( array( 'max' => 500 )));
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
		$this->_app[ 'db' ]->insert( 'comments', array(
			'content' => $this->content,
		));
	}
	
	/**
	 * Update
	 */
	public function update() {
		$this->_app[ 'db' ]->update( 'comments', array(
			'content' => $this->content,
		), array( 'id' => $this->id ));
	}
	
	public function getId() { return $this->id; }
	
	public function getContent() { return $this->content; }
	
	public function setId( $id ) {
		$this->id = $id;
		return $this;
	}
	
	public function setContent( $content ) {
		$this->content = $content;
		return $this;
	}
}
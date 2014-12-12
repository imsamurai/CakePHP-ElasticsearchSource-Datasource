<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 12.12.2014
 * Time: 11:03:22
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('ElasticsearchTest', 'ElasticsearchSource.Test/');
App::uses('ElasticsearchIndex', 'ElasticsearchSource.Model/');

/**
 * ElasticsearchIndexTest
 * 
 * @package ElasticsearchSourceTest
 * @subpackage Model
 */
class ElasticsearchIndexTest extends ElasticsearchTest {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.ElasticsearchSource.ElasticsearchIndex',
	);

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->loadFixtures('ElasticsearchIndex');
		$this->Elasticsearch = new ElasticsearchIndex(false, null, 'elasticsearchTest');
		$this->Elasticsearch->setSource('index');
	}

	/**
	 * Test find
	 */
	public function testFind() {
		$indexes = $this->Elasticsearch->find('all');
		$this->assertCount(3, $indexes);
		$indexesNames = $this->Elasticsearch->find('list', array('fields' => array('name')));
		sort($indexesNames);
		$this->assertSame(array('test_index1', 'test_index2', 'test_index3'), array_values($indexesNames));
	}

	/**
	 * Test save
	 */
	public function testSave() {
		$name = 'test_index4';
		$this->assertTrue((bool)$this->Elasticsearch->save(compact('name')));
		$this->assertNotEmpty($this->Elasticsearch->find('first', array('conditions' => compact('name'))));
	}

	/**
	 * Test exists
	 */
	public function testExists() {
		$this->assertTrue($this->Elasticsearch->exists('test_index1', array(), true));
		$this->assertFalse($this->Elasticsearch->exists('test_index11', array(), true));
	}

	/**
	 * Test update
	 */
	public function testUpdate() {
		$name = 'test_index5';
		$this->Elasticsearch->id = $name;
		$this->assertTrue((bool)$this->Elasticsearch->save(compact('name')));
		$this->assertNotEmpty($this->Elasticsearch->find('first', array('conditions' => compact('name'))));
	}

	/**
	 * Test delete
	 */
	public function testDelete() {
		$this->assertTrue((bool)$this->Elasticsearch->delete('test_index1'));
		$this->assertFalse((bool)$this->Elasticsearch->delete('test_index1'));
	}

}

<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 18.10.2013
 * Time: 16:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 */
App::uses('ElasticsearchTest', 'ElasticsearchSource.Test/');
App::uses('ElasticsearchIndicesStatus', 'ElasticsearchSource.Model');

/**
 * Tests indices status api
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
class ElasticsearchIndicesStatusTest extends ElasticsearchTest {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.ElasticsearchSource.ElasticsearchArticle',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @param array $config_name
	 * @param array $config
	 */
	protected function _loadModel($config_name = 'testElasticsearchSource', $config = array()) {
		parent::_loadModel($config_name, $config);
		$this->Elasticsearch = new ElasticsearchIndicesStatus(false, null, $config_name);
	}

	public function test_status() {
		$result = $this->Elasticsearch->find('all', array('fields'=> array('name')));
		$this->assertNotEqual($result, false);
		$this->assertTrue(in_array('test_index', Hash::extract($result, "{n}.{$this->Elasticsearch->alias}.name"), true));
		debug($result);
	}


}

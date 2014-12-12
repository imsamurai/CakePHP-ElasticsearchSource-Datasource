<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 12.12.2014
 * Time: 11:03:22
 */
App::uses("HttpSourceTestFixture", "HttpSource.TestSuite/Fixture");

/**
 * Index fixture
 *
 * @package ElasticsearchSourceTest
 * @subpackage Test.Fixture
 */
class ElasticsearchIndexFixture extends HttpSourceTestFixture {

	/**
	 * Fixture Datasource
	 *
	 * @var string
	 */
	public $useDbConfig = "elasticsearchTest";

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $model = "ElasticsearchSource.ElasticsearchIndex";

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		array("name" => 'test_index1'),
		array("name" => 'test_index2'),
		array("name" => 'test_index3')
	);

	/**
	 * {@inheritdoc}
	 *
	 * @return void
	 * @throws MissingModelException Whe importing from a model that does not exist.
	 */
	public function init() {
		ElasticsearchTest::setConfig();
		parent::init();
		$this->_Model->setSource('index');
	}

}

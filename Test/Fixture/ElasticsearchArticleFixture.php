<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
App::uses("HttpSourceTestFixture", "HttpSource.TestSuite/Fixture");

/**
 * Main fixture
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
class ElasticsearchArticleFixture extends HttpSourceTestFixture {

	/**
	 * Fixture Datasource
	 *
	 * @var string
	 */
	public $useDbConfig ="testElasticsearchSource";

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $model ="ElasticsearchSource.Elasticsearch";

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		array("id" => 1, "title" => "guratabaata 1", "description" => "test article 1", "index" =>"test_index", "type" =>"test_type", "rank" => 1, "refresh" => 1),
		array("id" => 2, "title" => "guratabaata 2", "description" => "test article 2", "index" =>"test_index", "type" =>"test_type", "rank" => 2, "refresh" => 1),
		array("id" => 3, "title" => "guratabaata 3", "description" => "test article 3", "index" =>"test_index", "type" =>"test_type", "rank" => 3, "refresh" => 1)
	);

}

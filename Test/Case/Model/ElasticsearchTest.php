<?

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
require_once dirname(__FILE__) . DS . 'models.php';

class ElasticsearchTest extends CakeTestCase {

	/**
	 * Elasticsearch Model
	 *
	 * @var Elasticsearch
	 */
	public $Elasticsearch = null;

	public function setUp() {
		parent::setUp();
		$this->_setConfig();
	}

	protected function _setConfig() {
		Configure::delete('ElasticsearchSource');
		Configure::load('ElasticsearchSource.ElasticsearchSource');
	}

	protected function _loadModel($config_name = 'elasticsearchSource', $config = array()) {
		$db_configs = ConnectionManager::enumConnectionObjects();

		if (!empty($db_configs['elasticsearchTest'])) {
			$TestDS = ConnectionManager::getDataSource('elasticsearchTest');
			$config += $TestDS->config;
		} else {
			$config += array(
				'datasource' => 'ElasticsearchSource.Http/ElasticsearchSource',
				'host' => 'example.com',
				'path' => 'yourpath',
				'port' => 80,
				'timeout' => 5
			);
		}

		ConnectionManager::create($config_name, $config);
		$this->Elasticsearch = new Elasticsearch(false, null, $config_name);
	}

	public function test_simple_query() {
		$this->_loadModel();
		$this->Elasticsearch->setSource('_search');
		$params = array(
			'conditions' => array(
				'query' => array(
					"term" => array("title" => "apple")
				),
				'index' => 'news'
			),
			'fields' => array('title', 'rank'),
			'order' => array('rank' => 'desc'),
			'offset' => 2
		);

		$result = $this->Elasticsearch->find('first', $params);
		$this->assertNotEqual($result, false);
		$this->assertCount(1, $result);
		debug($result);
	}

}

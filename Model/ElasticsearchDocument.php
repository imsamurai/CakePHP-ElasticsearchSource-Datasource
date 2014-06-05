<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2013
 * Time: 18:19:36
 * Format: http://book.cakephp.org/2.0/en/models.html
 *
 * @package ElasticsearchSource
 * @subpackage Document
 */
App::uses('ElasticsearchModel', 'ElasticsearchSource.Model');

/**
 * Indices Status
 *
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-get.html
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search.html
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-index_.html
 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-delete.html
 * @package ElasticsearchSource
 * @subpackage Indices
 */
class ElasticsearchDocument extends ElasticsearchModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'ElasticsearchDocument';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useTable = 'document';

	/**
	 * {@inheritdoc}
	 * 
	 * @param array $queryData
	 * @return array
	 */
	public function beforeFind($queryData) {
		$queryData = parent::beforeFind($queryData);
		$this->_mapMultipleIds($queryData);

		return $queryData;
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @param mixed $id
	 * @param array $conditions
	 * @return bool
	 */
	public function exists($id = null, array $conditions = array()) {
		$ex = parent::exists($id, array(
					'index' => $this->useIndex,
					'type' => $this->useType,
		));
		return $ex;
	}

	/**
	 * Runs an explain on a query
	 *
	 * @param string $connection Connection name
	 * @param string $query RAW query to explain / find query plan for.
	 * @return array Array of explain information or empty array if connection is unsupported.
	 */
	public function explainQuery($connection, $query) {
		$patterns = array(
			'#^\s*GET /(?P<index>[^/]*)/(?P<type>[^/]*)/(?P<id>[^/?=_\s]*)(:?\?(?P<query>[^\s]*)|)#',
			'#^\s*GET /(?P<index>[^/]*)/(?P<type>[^/]*)/_search(:?\?(?P<query>[^\s]*)|)#',
			'#^\s*GET /(?P<index>[^/]*)/_search(:?\?(?P<query>[^\s]*)|)#',
			'#^\s*GET /(?P<id>_search)(:?\?(?P<query>[^\s]*)|)#',
			'#(?P<data>{.*})#'
		);
		$queryData = array();
		foreach ($patterns as $pattern) {
			preg_match($pattern, $query, $matches);
			$queryData+=$matches;
		}
		if (!$queryData) {
			return array();
		}
		$queryData += array(
			'query' => null,
			'data' => null,
			'id' => null,
			'index' => null,
			'type' => null
		);

		if (preg_match('#^_#', $queryData['id'])) {
			$queryData['id'] = null;
		}

		parse_str($queryData['query'], $query);
		$queryData['query'] = $query ? $query : array();
		$queryData['data'] = $queryData['data'] ? json_decode($queryData['data'], true) : array();

		$Model = new static(false, false, $connection);
		$Model->setDataSource($connection);
		$Model->setSource($this->useTable, $queryData['index'], $queryData['type']);
		$explanations = $Model->find('all', array(
			'fields' => array(
				'id',
				'explanation'
			),
			'conditions' => array(
		'explain' => true
			) + $queryData['data'] + $queryData['query']
		));
		if (!$explanations) {
			return array();
		}
		return Hash::extract($explanations, '{n}.{s}');
	}

	/**
	 * Put ids in right place in case of multiple ids
	 * 
	 * @param array $queryData
	 */
	protected function _mapMultipleIds(array &$queryData) {
		$idsKeys = array(
			$this->primaryKey,
			"{$this->alias}.{$this->primaryKey}"
		);

		foreach ($idsKeys as $idsKey) {
			if (!isset($queryData['conditions'][$idsKey])) {
				continue;
			}

			$queryData['conditions'] = Hash::insert($queryData['conditions'], 'query.ids.values', (array)$queryData['conditions'][$idsKey]);
			$queryData['limit'] = count($queryData['conditions'][$idsKey]);
			unset($queryData['conditions'][$idsKey]);
		}
	}

}

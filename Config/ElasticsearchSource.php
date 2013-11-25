<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: https://github.com/imsamurai/cakephp-httpsource-datasource
 *
 * @package ElasticsearchSource
 * @subpackage Config
 */
$config['ElasticsearchSource']['config_version'] = 2;

$CF = HttpSourceConfigFactory::instance();
$Config = $CF->config();

$Config/*
		 * Get api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-get.html
		 */
		->add(
				$CF->endpoint()
				->id(1)
				->methodRead()
				->table('document')
				->path(':index/:type/:id/:_source')
				->addCondition($CF->condition()->name('index')->required())
				->addCondition($CF->condition()->name('type')->required())
				->addCondition($CF->condition()->name('id')->required())
				->addCondition($CF->condition()->name('realtime'))
				->addCondition($CF->condition()->name('fields')->map(function($fields) {
							return implode(',', $fields);
						}))
				->addCondition($CF->condition()->name('routing'))
				->addCondition($CF->condition()->name('preference'))
				->addCondition($CF->condition()->name('refresh'))
				->addCondition($CF->condition()->name('distributed'))
				->addCondition($CF->condition()->name('_source')->defaults(''))
				->readParams(array(
					'fields' => 'fields'
				))
				->result($CF->result()->map(function($data) {
							return array((array) Hash::get($data, '_source') + array(
							'id' => $data['_id'],
							'type' => $data['_type'],
							'index' => $data['_index']
							));
						}))
		)
		/*
		 * Search api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search.html
		 */
		->add(
				$CF->endpoint()
				->id(2)
				->methodRead()
				->table('document')
				->path(':index/:type/_search')
				->addCondition($CF->condition()->name('query')->sendInBody())
				->addCondition($CF->condition()->name('filter')->sendInBody())
				->addCondition($CF->condition()->name('facets')->sendInBody())
				->addCondition($CF->condition()->name('hobbies')->sendInBody())
				->addCondition($CF->condition()->name('terms')->sendInBody())
				->addCondition($CF->condition()->name('fields')->sendInBody())
				->addCondition($CF->condition()->name('size')->sendInBody())
				->addCondition($CF->condition()->name('from')->sendInBody())
				->addCondition($CF->condition()->name('sort')->sendInBody())
				->addCondition($CF->condition()->name('fields')->sendInBody())
				->addCondition($CF->condition()->name('highlight')->sendInBody())
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults(''))
				->addCondition($CF->condition()->name('type')->sendInQuery()->defaults(''))
				->readParams(array(
					'size' => 'limit',
					'from' => 'offset',
					'sort' => 'order.0',
					'fields' => 'fields'
				))
				->result($CF->result()->map(function($data) {
							$result = array();
							foreach ((array) Hash::get($data, 'hits.hits') as $item) {
								$result[] = array(
									'id' => $item['_id'],
									'type' => $item['_type'],
									'index' => $item['_index'],
									'score' => $item['_score'],
									'highlight' => (array) Hash::get($item, 'highlight'),
								) +  (array) Hash::get($item, '_source') + (array) Hash::get($item, 'fields');
							}
							return $result;
						}))
		)

		/**
		 * Indexing
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-index_.html
		 */
		->add(
				$CF->endpoint()
				->id(3)
				->methodUpdate()
				->table('document')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->length(100)->required())
				->addCondition($CF->condition()->name('version')->sendInQuery())
				->addCondition($CF->condition()->name('op_type')->sendInQuery())
				->addCondition($CF->condition()->name('routing')->sendInQuery())
				->addCondition($CF->condition()->name('parent')->sendInQuery())
				->addCondition($CF->condition()->name('timestamp')->sendInQuery())
				->addCondition($CF->condition()->name('ttl')->sendInQuery())
				->addCondition($CF->condition()->name('percolate')->sendInQuery())
				->addCondition($CF->condition()->name('distributed')->sendInQuery())
				->addCondition($CF->condition()->name('consistency')->sendInQuery())
				->addCondition($CF->condition()->name('replication')->sendInQuery())
				->addCondition($CF->condition()->name('refresh')->sendInQuery())
				->addCondition($CF->condition()->name('timeout')->sendInQuery())
		)
		->add(
				$CF->endpoint()
				->id(4)
				->methodCreate()
				->table('document')
				->path(':index/:type')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->length(100))
				->addCondition($CF->condition()->name('routing')->sendInQuery())
				->addCondition($CF->condition()->name('parent')->sendInQuery())
				->addCondition($CF->condition()->name('timestamp')->sendInQuery())
				->addCondition($CF->condition()->name('ttl')->sendInQuery())
				->addCondition($CF->condition()->name('percolate')->sendInQuery())
				->addCondition($CF->condition()->name('distributed')->sendInQuery())
				->addCondition($CF->condition()->name('consistency')->sendInQuery())
				->addCondition($CF->condition()->name('replication')->sendInQuery())
				->addCondition($CF->condition()->name('refresh')->sendInQuery())
				->addCondition($CF->condition()->name('timeout')->sendInQuery())
				->result($CF->result()->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								$Model->id = $data['_id'];
								return array('ok' => $data['ok']);
							}
							return false;
						}))
		)
		/**
		 * Indexing
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-delete.html
		 */
		->add(
				$CF->endpoint()
				->id(5)
				->methodDelete()
				->table('document')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->length(100)->required())
				->addCondition($CF->condition()->name('version')->sendInQuery())
				->addCondition($CF->condition()->name('routing')->sendInQuery())
				->addCondition($CF->condition()->name('parent')->sendInQuery())
				->addCondition($CF->condition()->name('distributed')->sendInQuery())
				->addCondition($CF->condition()->name('consistency')->sendInQuery())
				->addCondition($CF->condition()->name('replication')->sendInQuery())
				->addCondition($CF->condition()->name('refresh')->sendInQuery())
				->result($CF->result()->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								$Model->id = $data['_id'];
								return array('ok' => $data['ok']);
							}
							return false;
						}))
		)

		/**
		 *  Indices status
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/indices-status.html
		 */
		->add(
				$CF->endpoint()
				->id(6)
				->methodRead()
				->table('indices_status')
				->path(':index/_status')
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults(''))
				->result($CF->result()->map(function($data, Model $Model) {
							$results = array();
							foreach ((array)Hash::get($data, 'indices') as $name => $info) {
								$results[] = compact('name') + $info;
							}
							return $results;
						}))
		)
;


$config['ElasticsearchSource']['config'] = $Config;
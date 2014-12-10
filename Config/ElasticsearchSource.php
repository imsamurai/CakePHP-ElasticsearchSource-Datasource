<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 * Format: https://github.com/imsamurai/cakephp-httpsource-datasource
 */
$config['ElasticsearchSource']['config_version'] = 2;

$CF = HttpSourceConfigFactory::instance();
$Config = $CF->config();

$TimeIdField = $CF->field()
		->name('id')
		->map(function() {
			return microtime(true) . '.' . mt_srand();
		});

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
				->addCondition($CF->condition()->name('fields')
						->map(function($fields) {
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
				->result($CF->result()
						->map(function($data) {
							return array((array)Hash::get($data, '_source') + array(
							'id' => $data['_id'],
							'type' => $data['_type'],
							'index' => $data['_index']
							));
						})
				)
		)
		/*
		 * Check api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/docs-get.html
		 */
		->add(
				$CF->endpoint()
				->id(15)
				->methodCheck()
				->table('document')
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->required())
				->addCondition($CF->condition()->name('type')->required())
				->addCondition($CF->condition()->name('id')->required())
				->result($CF->result()
						->map(function() {
							return array('ok' => true);
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
				->path(':index/:type/_search/:scroll_search')
				->addCondition($CF->condition()->name('query')->sendInBody())
				->addCondition($CF->condition()->name('filter')->sendInBody())
				->addCondition($CF->condition()->name('facets')->sendInBody())
				->addCondition($CF->condition()->name('hobbies')->sendInBody())
				->addCondition($CF->condition()->name('terms')->sendInBody())
				->addCondition($CF->condition()->name('fields')->sendInBody())
				->addCondition($CF->condition()->name('size')->sendInQuery())
				->addCondition($CF->condition()->name('from')->sendInQuery())
				->addCondition($CF->condition()->name('scroll')->sendInQuery())
				->addCondition($CF->condition()->name('search_type')->sendInQuery())
				->addCondition($CF->condition()->name('scroll_id')->sendInQuery())
				->addCondition($CF->condition()->name('scroll_search')->sendInQuery()->defaults(''))
				->addCondition($CF->condition()->name('sort')->sendInBody())
				->addCondition($CF->condition()->name('highlight')->sendInBody())
				->addCondition($CF->condition()->name('version')->sendInBody())
				->addCondition($CF->condition()->name('track_scores')->sendInBody())
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults(''))
				->addCondition($CF->condition()->name('type')->sendInQuery()->defaults(''))
				->addCondition($CF->condition()->name('explain')->sendInQuery()->defaults(false))
				->readParams(array(
					'size' => 'limit',
					'from' => 'offset',
					'sort' => 'order.0',
					'fields' => 'fields'
				))
				->result($CF->result()
						->map(function($data) {
							$result = array();
							foreach ((array)Hash::get($data, 'hits.hits') as $item) {
								$result[] = array(
									'id' => $item['_id'],
									'type' => $item['_type'],
									'index' => $item['_index'],
									'score' => $item['_score'],
									'explanation' => isset($item['_explanation']) ? $item['_explanation'] : null,
									'version' => isset($item['_version']) ? $item['_version'] : 0,
									'highlight' => (array)Hash::get($item, 'highlight'),
										) + (array)Hash::get($item, '_source') + (array)Hash::get($item, 'fields');
							};
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
				->path(':index/:type/:id')
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('id')->sendInQuery()->length(100)->defaults(''))
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
				->result($CF->result()
						->map(function($data, Model $Model) {
							if (!empty($data['created'])) {
								$Model->id = $data['_id'];
								return array('ok' => $data['created']);
							}
							return false;
						})
				)
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
				->result($CF->result()
						->map(function($data, Model $Model) {
							if (!empty($data['found'])) {
								$Model->id = $data['_id'];
								return array('ok' => $data['found']);
							}
							return false;
						})
				)
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
				->result($CF->result()
						->map(function($data, Model $Model) {
							$results = array();
							foreach ((array)Hash::get($data, 'indices') as $name => $info) {
								$results[] = compact('name') + $info;
							}
							return $results;
						})
				)
		)
		/*
		 * Search facets api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-facets.html
		 */
		->add(
				$CF->endpoint()
				->id(7)
				->methodRead()
				->table('facets')
				->path(':index/:type/_search')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('query')->sendInBody())
				->addCondition($CF->condition()->name('filter')->sendInBody())
				->addCondition($CF->condition()->name('facets')->sendInBody())
				->addCondition($CF->condition()->name('terms')->sendInBody())
				->addCondition($CF->condition()->name('fields')->sendInBody())
				->addCondition($CF->condition()->name('size')->sendInQuery()->defaults('10'))
				->addCondition($CF->condition()->name('from')->sendInQuery())
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults(''))
				->addCondition($CF->condition()->name('type')->sendInQuery()->defaults(''))
				->result($CF->result()
						->map(function($data) {
							$result = array();
							$tmp = (array)Hash::get($data, 'facets');
							if (!empty($tmp)) {
								$result[] = $tmp;
								return $result;
							}
							return false;
						})
				)
		)
		/*
		 * Search facets api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-facets.html
		 */
		->add(
				$CF->endpoint()
				->id(8)
				->methodRead()
				->table('mapping')
				->path(':index/:type/_mapping')
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults('_all'))
				->addCondition($CF->condition()->name('type')->sendInQuery()->defaults('_all'))
				->result($CF->result()
						->map(function($data) {
							$result = array();
							foreach ($data as $index => $mappingOptions) {
								foreach ($mappingOptions['mappings'] as $type => $mapping) {
									$result[] = array(
										'id' => $index . '/' . $type,
										'index' => $index,
										'type' => $type,
										'mapping' => $mapping
									);
								}
							}

							return $result ? $result : false;
						}))
		)
		/*
		 * Count api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-count.html
		 */
		->add(
				$CF->endpoint()
				->id(9)
				->methodRead()
				->table('count')
				->path(':index/:type/_count')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('query')->sendInQuery())
				->addCondition($CF->condition()->name('index')->sendInQuery()->defaults('_all'))
				->addCondition($CF->condition()->name('type')->sendInQuery()->defaults(''))
				->result($CF->result()
						->map(function($data) {
							$result = array();
							if (isset($data['count'])) {
								$result[] = array('count' => $data['count']);
								return $result;
							}

							return false;
						}))
		)

		/*
		 * Create index api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/indices-create-index.html
		 */
		->add(
				$CF->endpoint()
				->id(10)
				->methodCreate()
				->table('index')
				->path(':index')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('settings')->sendInBody())
				->addCondition($CF->condition()->name('mappings')->sendInBody())
				->addCondition($CF->condition()->name('warmers')->sendInBody())
				->result($CF->result()
						->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								return array('ok' => $data['ok']);
							}
							return false;
						})
				)
		)

		/*
		 * Delete index api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/indices-delete-index.html
		 */
		->add(
				$CF->endpoint()
				->id(11)
				->methodDelete()
				->table('index')
				->path(':index')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->result($CF->result()
						->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								return array('ok' => $data['ok']);
							}
							return false;
						})
				)
		)

		/*
		 * Exists index api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/indices-exists.html
		 */
		->add(
				$CF->endpoint()
				->id(12)
				->methodRead()
				->table('index')
				->path(':index')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->result($CF->result()
						->map(function($data, Model $Model) {
							return $data ? array(array('ok' => true)) : false;
						})
				)
		)

		/*
		 * Create type api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/indices-create-index.html
		 */
		->add(
				$CF->endpoint()
				->id(13)
				->methodCreate()
				->table('mapping')
				->path(':index/:type/_mapping')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->addCondition($CF->condition()->name('mapping')->required()->extract(true))
				->result($CF->result()
						->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								return array('ok' => $data['ok']);
							}
							return false;
						})
				)
		)
		/*
		 * Create delete api
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/0.90/indices-create-index.html
		 */
		->add(
				$CF->endpoint()
				->id(14)
				->methodDelete()
				->table('mapping')
				->path(':index/:type/')
				->addField($TimeIdField)
				->addCondition($CF->condition()->name('index')->sendInQuery()->required())
				->addCondition($CF->condition()->name('type')->sendInQuery()->required())
				->result($CF->result()
						->map(function($data, Model $Model) {
							if (!empty($data['ok'])) {
								return array('ok' => $data['ok']);
							}
							return false;
						})
				)
		);

$config['ElasticsearchSource']['config'] = $Config;

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

$CF = HttpSourceConfigFactory::instance('ElasticsearchSource.ElasticsearchSourceConfigFactory');
$Config = $CF->config();

$Config
		/*
		 * Search apis
		 *
		 * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search.html
		 */
		->add(
				$CF->endpoint()
				->id(1)
				->methodRead()
				->addCondition($CF->condition()->name('query'))
				->addCondition($CF->condition()->name('filter'))
				->addCondition($CF->condition()->name('facets'))
				->addCondition($CF->condition()->name('hobbies'))
				->addCondition($CF->condition()->name('terms'))
				->addCondition($CF->condition()->name('fields'))
				->table('_search')
		)
		->result($CF->result()->map(function($result) {debug($result);
					return Hash::extract($result, 'hits.hits.{n}._source') + Hash::extract($result, 'hits.hits.{n}.fields');
				}))

;


$config['ElasticsearchSource']['config'] = $Config;

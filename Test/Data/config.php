<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 16.10.2013
 * Time: 15:50:03
 *
 * Extends default plugin config for example and testing
 *
 * @package ElasticsearchSource
 * @subpackage Test
 */
$CF = HttpSourceConfigFactory::instance();
$Config = $CF->load('ElasticsearchSource');
$Config->endpoint(3)
		->addCondition($CF->condition()->name('title'))
		->addCondition($CF->condition()->name('description'))
		->addCondition($CF->condition()->name('rank'))
		->addCondition($CF->condition()->name('summary'));

$Config->endpoint(4)
		->addCondition($CF->condition()->name('title'))
		->addCondition($CF->condition()->name('description'))
		->addCondition($CF->condition()->name('rank'))
		->addCondition($CF->condition()->name('summary'));
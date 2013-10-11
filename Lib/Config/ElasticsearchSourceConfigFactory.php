<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2013
 * Time: 18:00:00
 *
 * @package ElasticsearchSource
 * @subpackage Config
 */
App::uses('HttpSourceConfigFactory', 'HttpSource.Lib/Config');
App::uses('ElasticsearchSourceEndpoint', 'ElasticsearchSource.Lib/Config');

/**
 * Factory to make ElasticsearchSource configuration
 */
class ElasticsearchSourceConfigFactory extends HttpSourceConfigFactory {

	/**
	 * Create endpoint
	 *
	 * @return ElasticsearchSourceEndpoint
	 */
	public function endpoint() {
		return new ElasticsearchSourceEndpoint($this);
	}

}
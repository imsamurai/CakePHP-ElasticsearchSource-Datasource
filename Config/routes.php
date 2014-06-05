<?php

/**
 * Routes configuration
 */
Router::connect('/elasticsearch', array('controller' => 'elasticsearch', 'action' => 'explain', 'plugin' => 'ElasticsearchSource'));
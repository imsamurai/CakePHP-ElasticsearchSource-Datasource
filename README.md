ElasticsearchSource Plugin
==========================

CakePHP ElasticsearchSource is DataSource Plugin  for [Elasticsearch](http://www.elasticsearch.org/)

## Installation

### Step 1: Clone or download [HttpSource](https://github.com/imsamurai/cakephp-httpsource-datasource)

### Step 2: Clone or download to `Plugin/ElasticsearchSource`

  cd my_cake_app/app git://github.com/imsamurai/CakePHP-ElasticsearchSource-Datasource.git Plugin/ElasticsearchSource

or if you use git add as submodule:

	cd my_cake_app
	git submodule add "git://github.com/imsamurai/CakePHP-ElasticsearchSource-Datasource.git" "app/Plugin/ElasticsearchSource"

then update submodules:

	git submodule init
	git submodule update

### Step 3: Add your configuration to `database.php` and set it to the model

```
:: database.php ::
```
```php
public $elasticsearch = array(
  'datasource' => 'ElasticsearchSource.Http/ElasticsearchSource',
        'host' => 'example.com',
        'port' => 'some port'
);

Then make model

```
:: Elasticsearch.php ::
```
```php
public $useDbConfig = 'elasticsearch';
public $useTable = '<desired endpoint, for ex: "_search">';

```

### Step 4: Load plugin

```
:: bootstrap.php ::
```
```php
CakePlugin::load('HttpSource', array('bootstrap' => true, 'routes' => false));
CakePlugin::load('ElasticsearchSource');

```
#Tests

To run tests add and fill $elasticsearchTest in `database.php`

#Usage

You can use elasticsearch almost as db tables:
```php
$this->Elasticsearch->setSource('search');
	$params = array(
		'conditions' => array(
			'query' => array(
				"term" => array("title" => "apple")
			)
		),
		'fields' => array('title', 'rank'),
		'order' => array('rank' => 'desc'),
		'offset' => 2
	);

$result = $this->Elasticsearch->find('first', $params);
```

#Documentation

Please read [HttpSource Plugin README](https://github.com/imsamurai/cakephp-httpsource-datasource/blob/master/README.md)

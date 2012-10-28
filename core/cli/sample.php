<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(dirname(__FILE__).'/../coxis.php');
\BundlesManager::loadBundles();

require 'vendors/phpQuery/phpQuery/phpQuery.php';

function _pq($html, $selector) {
	$doc = phpQuery::newDocument($html);
	phpQuery::selectDocument($doc);
	return pq($selector);
}

class AppTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		\Schema::dropAll();
		ORMManager::autobuild();
		\BundlesManager::loadModelFixturesAll();
	}

	public function tearDown(){}

	public function test0() {
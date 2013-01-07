<?php
namespace Coxis\Core;

interface Collection {
	public function sync($ids);
	public function add($ids);
	public function remove($ids);
}
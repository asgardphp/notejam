<?php
// loadClass
// 	returns file

namespace {
	function from($from='') {
		return new \Coxis\Core\Importer($from);
	}
	function import($what, $into='') {
		return from()->import($what, $into);
	}
}

namespace Coxis\Core {
	class Importer {
		public $from = '';
		// public $preimported = array();

		public $basedir = 'vendor/';

		public function __construct($from='') {
			$this->from = $from;
		}
		
		public function import($what, $into='') {
			$imports = explode(',', $what);
			foreach($imports as $import) {
				$import = trim($import);
				
				$class = $what;
				$alias = \Coxis\Core\NamespaceUtils::basename($class);
				$vals = explode(' as ', $import);
				if(isset($vals[1])) {
					$class = trim($vals[0]);
					$alias = trim($vals[1]);
				}
			
				$alias = preg_replace('/^\\\+/', '', $into.'\\'.$alias);
				$class = preg_replace('/^\\\+/', '', $this->from.'\\'.$class);
				$this->preimported[$alias] = $class;
			}
		
			return $this;
		}
		
		public function _import($class, $params=array()) {
			$class = preg_replace('/^\\\+/', '', $class);
			$alias = isset($params['as']) ? $params['as']:null;
			$intoNamespace= isset($params['into']) ? $params['into']:null;
			
			if($intoNamespace == '.')
				$intoNamespace = '';

			if(!$alias && $alias !== false) 
				$alias = ($intoNamespace ? $intoNamespace.'\\':'').\Coxis\Core\NamespaceUtils::basename($class);

			#look for the class
			if($res=$this->loadClass($class)) {
				if($alias !== false)
					return static::createAlias($class, $alias);
				return true;
			}
			#go to upper level
			else {
				$dir = \Coxis\Core\NamespaceUtils::dirname($class);

				if($dir != '.') {
					$base = \Coxis\Core\NamespaceUtils::basename($class);
					if(\Coxis\Core\NamespaceUtils::dirname($dir) == '.')
						$next = $base;
					else
						$next = str_replace(DIRECTORY_SEPARATOR, '\\', \Coxis\Core\NamespaceUtils::dirname($dir)).'\\'.$base;

					return static::_import($next, array('into'=>$intoNamespace, 'as'=>$alias));
				}
			
				return false;
			}
		}

		public function loadClass($class) {
			#already loaded
			if(class_exists($class, false) || interface_exists($class, false))
				// return static::createAlias($class, $alias);
				return true;
			#file map
			elseif(isset(Autoloader::$map[strtolower($class)]))
				return static::loadClassFile(Autoloader::$map[strtolower($class)], $class);
			else {
				#directory map
				foreach(Autoloader::$directories as $prefix=>$dir) {
					if(preg_match('/^'.preg_quote($prefix).'/', $class)) {
						$rest = preg_replace('/^'.preg_quote($prefix).'\\\?/', '', $class);
						$path = $dir.DIRECTORY_SEPARATOR.static::class2path($rest);
						
						if(file_exists(_DIR_.$path))
							return static::loadClassFile($path, $class);
					}
				}

				if(file_exists(_DIR_.$this->basedir.($path = static::class2path($class))))
					return static::loadClassFile($this->basedir.$path, $class);
				
				// d($class);#only to test importer

				#lookup for global classes
				if(\Coxis\Core\NamespaceUtils::dirname($class) == '.') {
					$classes = array();
					
					#check if there is any corresponding class already loaded
					foreach(array_merge(get_declared_classes(), get_declared_interfaces()) as $v)
						if(strtolower(\Coxis\Core\NamespaceUtils::basename($class)) == strtolower(\Coxis\Core\NamespaceUtils::basename($v)))
							return static::createAlias($v, $class);
					
					#remove, only for testing class loading
					// d();
					foreach(Autoloader::$preloaded as $v)
						if(strtolower(\Coxis\Core\NamespaceUtils::basename($class)) == $v[0])
							$classes[] = $v;
					if(sizeof($classes) == 1)
						return static::loadClassFile($classes[0][1], $class);
					#if multiple classes, don't load
					elseif(sizeof($classes) > 1) {
						$classfiles = array();
						foreach($classes as $classname)
							$classfiles[] = $classname[1];
						throw new \Exception('There are multiple classes '.$class.': '.implode(', ', $classfiles));
					}
					#if no class, don't load
					else
						return false;
				}
			}
			
			return false;
		}
		
		public static function loadClassFile($file, $alias=null) {
			$before = array_merge(get_declared_classes(), get_declared_interfaces());
			require_once $file;
			$after = array_merge(get_declared_classes(), get_declared_interfaces());
			
			$diff = array_diff($after, $before);
			foreach($diff as $class) {
				if(method_exists($class, '_autoload')) {
					try {
						call_user_func(array($class, '_autoload'));
					} catch(\Exception $e) {
						d($e); #todo error report this exception cause autoloader does not let it bubble up
					}
				}
			}
			if($alias) {
				$result = \Coxis\Utils\Tools::get(array_values($diff), sizeof($diff)-1);
				if(static::createAlias($result, $alias))
					return $file;
				return false;
				// return static::createAlias($result, $alias);
			}
		}
		
		public static function class2path($class) {
			$className = \Coxis\Core\NamespaceUtils::basename($class);
			$namespace = strtolower(\Coxis\Core\NamespaceUtils::dirname($class));

			$namespace = str_replace('\\', DIRECTORY_SEPARATOR , $namespace );

			if($namespace != '.')
				$path = $namespace.DIRECTORY_SEPARATOR;
			else
				$path = '';
			$path .= str_replace('_', DIRECTORY_SEPARATOR , $className);				

			return $path.'.php';
		}

		public static function createAlias($loadedClass, $class) {
			if(strtolower(\Coxis\Core\NamespaceUtils::basename($class)) != strtolower(\Coxis\Core\NamespaceUtils::basename($loadedClass)))
				return false;
			try {
				if($loadedClass !== $class)
					class_alias($loadedClass, $class);
				return true;
			} catch(\ErrorException $e) {
				return false;
			}
		}
	}
}

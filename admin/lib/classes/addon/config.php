<?php

class addonConfig {
	
	static $all = array();
	static $allConfig = array();
	
	public static function isSaved($addon, $save = true) {
	
		$sql = new sql();
		$num = $sql->num('SELECT 1 FROM '.sql::table('addons').' WHERE `name` = "'.$addon.'"');
		if(!$num && $save) {
			$save = new sql();
			$save->setTable('addons');
			$save->addPost('name', $addon);
			$save->save();
		}
		
		return $num;
		
	}
	
	public static function getAll() {
		
		if(!count(self::$all)) {	
		
			$sql = new sql();		
			$sql->query('SELECT name FROM '.sql::table('addons').' WHERE `install` = 1  AND `active` = 1')->result();
			while($sql->isNext()) {
				self::$all[] = $sql->get('name');
				$sql->next();		
			}
			
		}
				
		return self::$all;
		
	}
	
	public static function includeAllConfig() {
		
		foreach(self::getAll() as $name) {
			$configFile = dir::addon($name, 'config.php');
			include_once($configFile);
		}
		
	}
	
	public static function includeAllLangFiles() {
		
		foreach(self::getAll() as $name) {
			
			$file = dir::addon($name, 'lang/'.lang::getLang());
			if(file_exists($file.'.json')) {				
				lang::loadLang($file);
			}
			
			$defaultFile = dir::addon($name, 'lang/'.lang::getDefaultLang());
			if(file_exists($defaultFile.'.json')) {				
				lang::loadLang($defaultFile, true);
			}
			
		}
		
	}
	
	public static function getAllConfig() {
		
		if(!count(self::$allConfig)) {
		
			foreach(self::getAll() as $name) {
				$configFile = dir::addon($name, 'config.json');
				self::$allConfig[$name] = json_decode(file_get_contents($configFile), true);
			}
		
		}
		
		return self::$allConfig;
		
	}
	
}

?>
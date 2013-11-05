<?php

class metainfos {
	
	static $multiTypes = array('select', 'radio', 'checkbox');
	
	public static function getMetaInfos($type) {
		
		$return = array();
		
		$sql = new sql();
		$sql->query('SELECT * FROM '.sql::table('metainfos').' WHERE `type` = "'.$type.'" ORDER BY `sort`')->result();
		while($sql->isNext()) {
		
			$return[] = self::getElement($sql->getRow(), null);
		
			$sql->next();	
		}
		
		return $return;
		
	}
	
	public static function getElement($attributes, $default = null) {
		
		if(is_null($default)) {
			$default = $attributes['default'];	
		}
		
		$class = self::getElementClass($attributes, $default);
		$class->fieldName($attributes['label']);
		$class = self::convertAttributes($class, $attributes['attributes']);
		
		if(in_array($attributes['formtype'], self::$multiTypes)) {
			$class = self::convertParams($class, $attributes['params']);
		}
		
		return $class;
		
	}
	
	protected static function getElementClass($attributes, $default) {
		
		if($attributes['formtype'] == 'text') {
			$class = new formInput($attributes['name'], $default);
			$class->addAttribute('type', 'text');
			return $class;
		}
		
		// formSelect, formCheckbox, formTextarea, ..
		$class = 'form'.ucfirst($attributes['formtype']);
		
		return new $class($attributes['name'], $default);
		
	}
	
	protected static function convertAttributes($element, $attributes) {
		
		if(trim($attributes) == '')
			return $element;
			
		// Serverseitig
		$attributes = str_replace("\n\r", "\n", $attributes);
		
		$attr = explode("\n", $attributes);
		foreach($attr as $attrString) {
			
			preg_match("/([^=]*)=(.*)/", $attrString, $attrArray);	
			$element->addAttribute($attrArray[1], $attrArray[2]);
			
		}
		
		return $element;
		
	}
	
	protected static function convertParams($element, $params) {
		
		if(trim($params) == '')
			return $element;
			
		$params = explode('|', $params);
		foreach($params as $paramString) {
			
			preg_match('/([^:]*):(\w*)/', $paramString, $parmArray);
			
			if(empty($parmArray)) {
				$element->add($paramString, $paramString);	
			} else {
				$element->add($parmArray[1], $parmArray[2]);
			}
			
		}
		
		return $element;
			
	}
	
}

?>
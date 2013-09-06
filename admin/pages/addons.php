<?php
$action = type::super('action', 'string');
$addon = type::super('addon', 'string');

backend::addSubnavi('Übersicht',	url::backend('addons'), 	'archive');

if($action == 'online') {

	$addonClass= new addon($addon, false);	
	$online = ($addonClass->isOnline()) ? 0 : 1;
	
	$sql = new sql();
	$sql->setTable('addons');
	$sql->setWhere('`name` = "'.$addon.'"');
	$sql->addPost('online', $online);
	
	if(!$online)
		$sql->addPost('active', 0);
	
	$sql->update();
	
	echo message::success('Addon erfolgreich gespeichert');
	
}

if($action == 'active') {
	
	$addonClass = new addon($addon, false);	
	$active = ($addonClass->isActive()) ? 0 : 1;
	
	if(!$addonClass->isOnline()) {
		
		echo message::danger('Bitte installieren Sie das Addon '.$addon.' zuerst');
			
	} else {
	
		$sql = new sql();
		$sql->setTable('addons');
		$sql->setWhere('`name` = "'.$addon.'"');
		$sql->addPost('active', $active);
		$sql->update();
		
		echo message::success('Addon erfolgreich gespeichert');
		
	}
	
}

$table = new table();
$table->addCollsLayout('20,*,300');

$table->addRow()
->addCell('')
->addCell('Name')
->addCell('Aktionen');

$table->addSection('tbody');

$addons = scandir(dir::backend('addons/'));

foreach($addons as $dir) {
	
	if(in_array($dir, array('.', '..', '.htaccess'))) 
		continue;
	
	$curAddon = new addon($dir);
	
	$online_url = url::backend('addons', array('addon'=>$dir, 'action'=>'online'));
	
	if($curAddon->isOnline()) {
		$online = '<a href="'.$online_url.'" class="btn btn-sm structure-online">installiert</a>';
	} else {
		$online = '<a href="'.$online_url.'" class="btn btn-sm structure-offline">nicht installiert</a>';
	}
	$active_url = url::backend('addons', array('addon'=>$dir, 'action'=>'active'));
	
	if($curAddon->isActive()) {
		$active = '<a href="'.$active_url.'" class="btn btn-sm structure-online">aktiviert</a>';
	} else {
		$active = '<a href="'.$active_url.'" class="btn btn-sm structure-offline">nicht aktiviert</a>';
	}
	
	$delete = '<a href="#" class="btn btn-sm btn-danger">Entfernen</a>';
	
	$table->addRow()
	->addCell('<a href="" class="icon-question"></a>')
	->addCell($curAddon->get('name').' <small>'.$curAddon->get('version').'</small>')
	->addCell('<span class="btn-group">'.$online.$active.$delete.'</span>');
		
}

echo $table->show();
?>
<?php

$db = new SQLite3('prawo_jazdy_2022.db');
$db->exec('CREATE TABLE IF NOT EXISTS media (id INTEGER, file TEXT, path TEXT)');
$root_folder = "media/";
define('ROOT_FOLDER_LEN', strlen($root_folder));


function scan_folder($path, &$output) {
	if (is_dir($path)) {
		$handle  = opendir($path);
		if ($handle) {
            while (($entry = readdir($handle)) !== FALSE) {
				if ($entry  != '.' && $entry  != '..') {
					if (is_dir($path . $entry )) {
						scan_folder($path . $entry . '/', $output);
					} else {
						$output[] = [ 'path' => substr($path, ROOT_FOLDER_LEN), 'file' => $entry  ];
					}
				}
			}
		}
		closedir($handle);
	}
}

$output = [];
scan_folder($root_folder, $output);
$stm = $db->prepare('INSERT INTO media (file, path) VALUES(:file, :path)');
foreach($output as $key => $entry) {
	$stm->bindParam(':file', $entry['file'], SQLITE3_TEXT);
	$stm->bindParam(':path', $entry['path'], SQLITE3_TEXT);
	$stm->execute();
	echo $key . PHP_EOL;
}
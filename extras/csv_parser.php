<?php
$category_id = [
	'A' => 1,
	'B' => 2,
	'C' => 3,
	'D' => 4,
	'T' => 5,
	'AM' => 6,
	'A1' => 7,
	'A2' => 8,
	'B1' => 9,
	'C1' => 10,
	'D1' => 11,
	'PT' => 12
];
$db = new SQLite3('prawo_jazdy_2022.db');
$db->exec('CREATE TABLE IF NOT EXISTS categories (id INTEGER, category TEXT)');
$db->exec('INSERT INTO categories(id, category) VALUES(1,"A"),(2,"B"),(3,"C"),(4,"D"),(5,"T"),(6,"AM"),(7,"A1"),(8,"A2"),(9,"B1"),(10,"C1"),(11,"D1"),(12,"PT")');
$db->exec('CREATE TABLE IF NOT EXISTS question2category (q_id INTEGER, c_id INTEGER)');
$db->exec("CREATE TABLE IF NOT EXISTS questions_2022(
	id INTEGER PRIMARY KEY,
	q_name TEXT, q_no TEXT,
	q_PL TEXT, a_A_PL TEXT, a_B_PL TEXT, a_C_PL TEXT,
	q_EN TEXT, a_A_EN TEXT, a_B_EN TEXT, a_C_EN TEXT,
	q_DE TEXT, a_A_DE TEXT, a_B_DE TEXT, a_C_DE TEXT,
	a TEXT, media TEXT, type TEXT, points INTEGER, block TEXT, source TEXT, why TEXT, safety TEXT, status TEXT, unit TEXT,
	q_PJM TEXT, a_A_PJM TEXT, a_B_PJM TEXT, a_C_PJM TEXT,
	q_SJM TEXT, a_A_SJM TEXT, a_B_SJM TEXT, a_C_SJM TEXT)");

$stm = $db->prepare('INSERT INTO questions_2022(q_name, q_no, q_PL, a_A_PL, a_B_PL, a_C_PL, q_EN, a_A_EN, a_B_EN, a_C_EN, q_DE, a_A_DE, a_B_DE, a_C_DE, a, media, type, points, block, source, why, safety, status, unit, q_PJM, a_A_PJM, a_B_PJM, a_C_PJM, q_SJM, a_A_SJM, a_B_SJM, a_C_SJM) VALUES(:q_name, :q_no, :q_PL, :a_A_PL, :a_B_PL, :a_C_PL, :q_EN, :a_A_EN, :a_B_EN, :a_C_EN, :q_DE, :a_A_DE, :a_B_DE, :a_C_DE, :a, :media, :type, :points, :block, :source, :why, :safety, :status, :unit, :q_PJM, :a_A_PJM, :a_B_PJM, :a_C_PJM, :q_SJM, :a_A_SJM, :a_B_SJM, :a_C_SJM)');
$stm_cat = $db->prepare('INSERT INTO question2category (q_id, c_id) VALUES(:q_id, :c_id)');
// select q.id, q.q_PL, c.category from questions_2022 q left join question2category q2c on q2c.q_id = q.id left join categories c on q2c.c_id = c.id where c.category = 'A';
$row = 0;
if (($handle = fopen("Baza_pytań_na_egzamin_na_prawo_jazdy_22_02_2022r.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        $num = count($data);
		if ( $num < 33) continue;
        $row++;
		$type = strtoupper(trim($data[16]));
		$stm->bindParam(':q_name',$data[0], SQLITE3_TEXT);
		$stm->bindParam(':q_no',$data[1], SQLITE3_TEXT);
		$stm->bindParam(':q_PL',$data[2], SQLITE3_TEXT);
		$stm->bindParam(':a_A_PL',$data[3], SQLITE3_TEXT);
		$stm->bindParam(':a_B_PL',$data[4], SQLITE3_TEXT);
		$stm->bindParam(':a_C_PL',$data[5], SQLITE3_TEXT);
		$stm->bindParam(':q_EN',$data[6], SQLITE3_TEXT);
		$stm->bindParam(':a_A_EN',$data[7], SQLITE3_TEXT);
		$stm->bindParam(':a_B_EN',$data[8], SQLITE3_TEXT);
		$stm->bindParam(':a_C_EN',$data[9], SQLITE3_TEXT);
		$stm->bindParam(':q_DE',$data[10], SQLITE3_TEXT);
		$stm->bindParam(':a_A_DE',$data[11], SQLITE3_TEXT);
		$stm->bindParam(':a_B_DE',$data[12], SQLITE3_TEXT);
		$stm->bindParam(':a_C_DE',$data[13], SQLITE3_TEXT);
		$stm->bindParam(':a',$data[14], SQLITE3_TEXT);
		$stm->bindParam(':media',$data[15], SQLITE3_TEXT);
		$stm->bindParam(':type', $type, SQLITE3_TEXT);
		$stm->bindParam(':points',$data[17], SQLITE3_INTEGER);
		// $stm->bindParam(':categories',$data[18], SQLITE3_TEXT);
		$stm->bindParam(':block',$data[19], SQLITE3_TEXT);
		$stm->bindParam(':source',$data[20], SQLITE3_TEXT);
		$stm->bindParam(':why',$data[21], SQLITE3_TEXT);
		$stm->bindParam(':safety',$data[22], SQLITE3_TEXT);
		$stm->bindParam(':status',$data[23], SQLITE3_TEXT);
		$stm->bindParam(':unit',$data[24], SQLITE3_TEXT);
		$stm->bindParam(':q_PJM',$data[25], SQLITE3_TEXT);
		$stm->bindParam(':a_A_PJM',$data[26], SQLITE3_TEXT);
		$stm->bindParam(':a_B_PJM',$data[27], SQLITE3_TEXT);
		$stm->bindParam(':a_C_PJM',$data[28], SQLITE3_TEXT);
		$stm->bindParam(':q_SJM',$data[29], SQLITE3_TEXT);
		$stm->bindParam(':a_A_SJM',$data[30], SQLITE3_TEXT);
		$stm->bindParam(':a_B_sJM',$data[31], SQLITE3_TEXT);
		$stm->bindParam(':a_C_SJM',$data[32], SQLITE3_TEXT);
		$stm->execute();
		$q_id = $db->lastInsertRowID();
		$categories = explode(',', $data[18]);
		foreach($categories as $cat) {
			$c_id = $category_id[ strtoupper(trim($cat)) ];
			if ($c_id) {
				$stm_cat->bindParam(':q_id', $q_id, SQLITE3_INTEGER);
				$stm_cat->bindParam(':c_id', $c_id, SQLITE3_INTEGER);
				$stm_cat->execute();
			}
		}
		echo $row . ' -> cats: ' . count($categories) . PHP_EOL;
    }
    fclose($handle);
}


/*
Nazwa pytania
Numer pytania
Pytanie
Odpowiedź A
Odpowiedź B
Odpowiedź C
Pytanie ENG
Odpowiedź ENG A
Odpowiedź ENG B
Odpowiedź ENG C
Pytanie DE
Odpowiedź DE A
Odpowiedź DE B
Odpowiedź DE C
Poprawna odp
Media
Zakres struktury
Liczba punktów
Kategorie
Nazwa bloku
Źródło pytania
O co chcemy zapytać
Jaki ma związek z bezpieczeństwem
Status
Podmiot
Nazwa media tłumaczenie migowe (PJM) treść pyt
Nazwa media tłumaczenie migowe (PJM) treść odp A
Nazwa media tłumaczenie migowe (PJM) treść odp B
Nazwa media tłumaczenie migowe (PJM) treść odp C
Nazwa media tłumaczenie migowe (SJM) treść pyt
Nazwa media tłumaczenie migowe (SJM) treść odp A
Nazwa media tłumaczenie migowe (SJM) treść odp B
Nazwa media tłumaczenie migowe (SJM) treść odp C
*/
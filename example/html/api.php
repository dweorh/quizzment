<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: *");
define('DEBUG', false);

function getSessionDb() {
    $db = new PDO('sqlite:../dbs/sessions.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function getQuestionsDb() {
    $db = new PDO('sqlite:../dbs/prawo_jazdy_2022.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function initSessionDb(PDO $db) {
    $db->exec("CREATE TABLE IF NOT EXISTS session (
        id INTEGER PRIMARY KEY, 
        uniqid TEXT,
        created_at INTEGER)");
    
    $db->exec("CREATE TABLE IF NOT EXISTS session_history (
        id INTEGER PRIMARY KEY, 
        session_id TEXT,
        category text,
        type text,
        created_at INTEGER)");
		
	$db->exec("CREATE TABLE IF NOT EXISTS session_result (
        id INTEGER PRIMARY KEY, 
		result_id TEXT,
        session_id TEXT,
        result text,
        created_at INTEGER)");
}

function newSessionId(PDO $db) {
    $id = uniqid("", true);
    $stm = $db->prepare('INSERT INTO session (uniqid, created_at) VALUES(:uniqid, :created_at)');
    $stm->bindParam(':uniqid', $id);
    $stm->bindValue(':created_at', time());
    $stm->execute();
    return $id;
}

function updateSession(PDO $db, $id, $category, $type) {
    $stm = $db->prepare('INSERT INTO session_history (session_id, category, type, created_at) VALUES(:session_id, :category, :type, :created_at)');
    $stm->bindParam(':session_id', $id);
    $stm->bindParam(':category', $category);
    $stm->bindParam(':type', $type);
    $stm->bindValue(':created_at', time());
    $stm->execute();
    return $id;
}

function newResult(PDO $db, $result_id, $session_id, $result) {
    $stm = $db->prepare('INSERT INTO session_result (result_id, session_id, result, created_at) VALUES(:result_id, :session_id, :result, :created_at)');
    $stm->bindParam(':result_id', $result_id);
	$stm->bindParam(':session_id', $session_id);
    $stm->bindParam(':result', $result);
    $stm->bindValue(':created_at', time());
    $stm->execute();
    return $db->lastInsertId();	
}

function getResult(PDO $db, $result_id) {
	$stm = $db->prepare('SELECT * FROM session_result WHERE result_id = :result_id');
	$stm->bindParam(':result_id', $result_id);
	$stm->execute();
	return $stm->fetch(PDO::FETCH_ASSOC);
}

function examQuestions(PDO $db, $category) {
    $stm = $db->prepare('SELECT q.id, q.q_name, q.a, q.q_no, q.q_PL, q.a_A_PL, q.a_B_PL, q.a_C_PL, q.media, q.type, q.points, q.block, q.source, q.why, q.safety, q.unit, m.path FROM questions_2022 q 
        LEFT JOIN question2category q2c on q2c.q_id = q.id
        LEFT JOIN categories c on q2c.c_id = c.id 
        LEFT JOIN media m ON m.file = q.media
        WHERE c.category = :category AND q.type = :type AND q.points = :points
        ORDER BY random()       
        LIMIT :limit');
    $stm->bindParam(':category', $category);
    $stm->bindValue(':type', 'PODSTAWOWY');
    $stm->bindValue(':points', 3);
    $stm->bindValue(':limit', 10);
    $stm->execute();
    $qp3 = $stm->fetchAll(PDO::FETCH_ASSOC);

    $stm->bindValue(':points', 2);
    $stm->bindValue(':limit', 6);
    $stm->execute();
    $qp2 = $stm->fetchAll(PDO::FETCH_ASSOC);
    
    $stm->bindValue(':points', 1);
    $stm->bindValue(':limit', 4);
    $stm->execute();
    $qp1 = $stm->fetchAll(PDO::FETCH_ASSOC);

    $stm->bindValue(':type', 'SPECJALISTYCZNY');
    $stm->bindValue(':points', 3);
    $stm->bindValue(':limit', 6);
    $stm->execute();
    $qs3 = $stm->fetchAll(PDO::FETCH_ASSOC);

    $stm->bindValue(':points', 2);
    $stm->bindValue(':limit', 4);
    $stm->execute();
    $qs2 = $stm->fetchAll(PDO::FETCH_ASSOC);

    $stm->bindValue(':points', 1);
    $stm->bindValue(':limit', 2);
    $stm->execute();
    $qs1 = $stm->fetchAll(PDO::FETCH_ASSOC);
    $qp = array_merge($qp3, $qp2, $qp1);
    $qs = array_merge($qs3, $qs2, $qs1);
    shuffle($qp);
    shuffle($qs);
    return DEBUG ? $qs1 : array_merge($qp, $qs);
    //return $qs1;
}

function practiceQuestions(PDO $db, $category, $page = 0) {
    $per_page = 15;
    $offset = $page * $per_page;
    $stm = $db->prepare('SELECT q.id, q.q_name, q.a, q.q_no, q.q_PL, q.a_A_PL, q.a_B_PL, q.a_C_PL, q.media, q.type, q.points, q.block, q.source, q.why, q.safety, q.unit, m.path FROM questions_2022 q 
        LEFT JOIN question2category q2c on q2c.q_id = q.id
        LEFT JOIN categories c on q2c.c_id = c.id
        LEFT JOIN media m ON m.file = q.media
        WHERE c.category = :category
        ORDER BY q.id
        LIMIT :offset, :limit');
    $stm->bindParam(':category', $category);
    $stm->bindParam(':offset', $offset);
    $stm->bindParam(':limit', $per_page);
    $stm->execute();    
    return $stm->fetchAll(\PDO::FETCH_ASSOC);
}

function getSessionStats(PDO $db) {
	$stm = $db->prepare('SELECT id, uniqid, created_at FROM session ORDER BY created_at DESC');
	$stm->execute();
	$sessions = $stm->fetchAll(\PDO::FETCH_ASSOC);
	
	$stm = $db->prepare('SELECT id, session_id, category, type, created_at FROM session_history WHERE session_id = :session_id ORDER BY created_at');
	foreach($sessions as $idx => $session) {
		$stm->bindParam(':session_id', $session['uniqid']);
		$stm->execute();
		$history = $stm->fetchAll(\PDO::FETCH_ASSOC);
		foreach($history as $h_idx => $entry) {
			$history[$h_idx]['created_at'] = date('Y-m-d H:i:s', $entry['created_at']);
		}
		$sessions[$idx]['created_at'] = date('Y-m-d H:i:s', $session['created_at']);
		$sessions[$idx]['history'] = $history;
	}
	return $sessions;
}


$action = $_GET['action'];

$res = [ 'status' => 0 ];
$flag = 0;
switch ($action) {
    case 'new_session':
        $db = getSessionDb();
        initSessionDb($db);
        $res = [
            'status' => 1,
            'id' => newSessionId($db)
        ];
    break;
    case 'update_session':
        $db = getSessionDb();
        initSessionDb($db);
		$aRequest = file_get_contents("php://input", "r");
		$aRequest = json_decode($aRequest, true);
        updateSession($db, $aRequest['session_id'], $aRequest['category'], $aRequest['type']);
        $res = [
            'status' => 1
        ];
    break;
    case 'exam':
        $db = getQuestionsDb();
        $res = [
            'status' => 1,
            'questions' => examQuestions($db, $_GET['category'])
        ];
    break;
    case 'practice':
        $db = getQuestionsDb();
        $res = [
            'status' => 1,
            'questions' => practiceQuestions($db, $_GET['category'], $_GET['page'] ?? 0)
        ];
    break;
	case 'stats': 
		if ($_GET['pass'] === 'TU_MOZECIE_WSTAWIC_SWOJE_HASLO_JAK_CHCECIE_:)') {
			$db = getSessionDb();
			$res = [
				'stats' => getSessionStats($db)
			];
			$flag = JSON_PRETTY_PRINT;
		} else {
			$res = [
				'msg' => 'No stats yet'
			];
		}
	break;
	case 'store_result':
		$db = getSessionDb();
        initSessionDb($db);
		$aRequest = file_get_contents("php://input", "r");
		$aRequest = json_decode($aRequest, true);
        $res = [
            'status' => 1,
            'id' => newResult($db, $aRequest['result_id'], $aRequest['session_id'], $aRequest['result'])
        ];
	break;
	case 'get_result': 
		$db = getSessionDb();
        $res = [
            'status' => 1,
            'result' => getResult($db, $_GET['result_id'])
        ];
	break;
}
// var_export($res);
echo json_encode($res, $flag);

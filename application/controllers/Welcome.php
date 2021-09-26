<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Level_model');
		$this->load->library('session');
	    // Pengaturan Waktu Indonesia 
		date_default_timezone_set("ASIA/Jakarta"); 

		// Token & API Telegram 
		$aksesToken = '1372386017:AAHXKr3UCE5ryzBQNdaaeeSqZu7k600YCy8'; 
		$userNameBot= "@byTap_bot";
		$this->api['connect'] = 'https://api.telegram.org/bot' . $aksesToken; 
		$this->outputBot = json_decode(file_get_contents('php://input'), TRUE); 
		 
	}

	public function index()
	{
		$output = $this->outputBot;
		$outputrev = (json_encode($output));
		$ChatID = $output['message']['chat']['id']; 
		$UserID = $output['message']['from']['id'];
		$message = $output['message']['text'];
		if ($this->LastCommand($UserID, $message)==1) {
			$this->edit($UserID,$message);
		}elseif($this->LastCommand($UserID, $message)==2){
			$this->quiz($UserID,$message);
		}else{
			$this->NonEdit();
		}
		
	} 

	public function LastCommand($id='', $command='')
	{
		$status = $this->Level_model->GetAdminLastCommand($id);
		

		if (strpos($status['command'], "TAMBAH MENU PADA") !== FALSE) {
			return 1;
		}elseif (strpos($command, "IKUTI KUIS") !== FALSE || $command == 'A' || $command == 'B' || $command == 'C' || $command == 'D' || $command == 'E'){
			$quiz = $this->Level_model->GetUserQuizStatus($id);
			if ($quiz == 0) {
				$this->Level_model->AddUserToQuiz($id);
			}
			return 2;
		}else{
			return 0;
		}
		
	}

	public function quiz($ChatID='',$message='')
	{
		$progress = $this->Level_model->GetUserQuizProgress($ChatID);
		if ($progress['status'] == 0) {
			if ($message=='A' || $message == 'B' || $message == 'C' || $message == 'D' || $message == 'E' ) {
				$this->Level_model->AddAnswer($ChatID, $message);
				$question = $this->Level_model->GetQuestion($ChatID);
				$this->sendMessage($ChatID, $question['question'].PHP_EOL.$question['answer_a'].PHP_EOL.$question['answer_b'].PHP_EOL.$question['answer_c'].PHP_EOL.$question['answer_d'].PHP_EOL.$question['answer_e'], $this->KeyboardAnswer(''));
			}else{
				$question = $this->Level_model->GetQuestion($ChatID);
				$this->sendMessage($ChatID, 'SELAMAT MENGERJAKAN KUIS INI'.PHP_EOL.$question['question'].PHP_EOL.$question['answer_a'].PHP_EOL.$question['answer_b'].PHP_EOL.$question['answer_c'].PHP_EOL.$question['answer_d'].PHP_EOL.$question['answer_e'], $this->KeyboardAnswer(''));
			}
			
		}else{
			if ($message=='A' || $message == 'B' || $message == 'C' || $message == 'D' || $message == 'E' ) {
				$this->Level_model->AddAnswer($ChatID, $message);
				$question = $this->Level_model->GetQuestion($ChatID);
				if ($question==0) {
					$score = $this->CountScore($ChatID);
					$this->sendMessage($ChatID, 'Selamat Anda Telah Menyelesaikan Kuis Ini, Score anda adalah '.$score, $this->KeyboardCustom('KEMBALI KE START'));
				}else{
					$this->sendMessage($ChatID, $question['question'].PHP_EOL.$question['answer_a'].PHP_EOL.$question['answer_b'].PHP_EOL.$question['answer_c'].PHP_EOL.$question['answer_d'].PHP_EOL.$question['answer_e'], $this->KeyboardAnswer(''));
				}
				
			}else{
				$score = $this->CountScore($ChatID);
				$this->sendMessage($ChatID, 'Selamat Anda Telah Menyelesaikan Kuis Ini, Score anda adalah '.$score, $this->KeyboardCustom('KEMBALI KE START'));
			}
		}
	} 

	public function CountScore($id='')
	{
		$poin = 0;
		$key = $this->Level_model->GetKey($id);
		$answer = $this->Level_model->GetAnswer($id);
		$keyArray = str_split($key);
		$answerArray = str_split($answer['answer_data']);
		for ($i=0; $i < strlen($key); $i++) { 
			if ($keyArray[$i]==$answerArray[$i]) {
				$poin++;
			}
		}
		$this->Level_model->AddScoreToDB($id, ($poin/strlen($key)*100));
		return ($poin/strlen($key)*100);
	}

	public function edit($ChatID='',$message='')
	{
		$this->sendMessage($ChatID, 'ANDA TELAH MEMASUKKAN NAMA..',$this->KeyboardCustom("SIMPAN"));
		$this->Level_model->AddAdminLog($ChatID, $message);
	}

	public function NonEdit($value='')
	{
		$output = $this->outputBot;
		$outputrev = (json_encode($output));
		$ChatID = $output['message']['chat']['id']; 
		$UserID = $output['message']['from']['id'];
		$message = $output['message']['text'];
		$callbackData = $output['callback_query']['data'];

		// $message = 'PROFIL';
		// $ChatID = '792046509';
 		if ($message == "KEMBALI KE START") {
			$message = "/start";
		}elseif (strpos($message, "KEMBALI KE") !== FALSE ) {
			$message = substr($message, 11);
		}
		
		$userStatus = $this->GetUserSatus($UserID);
		if ($message == "RESET" OR $message == "RESET PROFIL") {
			$this->sendLog($UserID, $message, $output['message']['from']['first_name']." Do Reset");
			$this->sendMessage($ChatID, 'Data Anda Telah Direset..',$this->KeyboardCustom("DAFTAR SEKARANG"));
			$userData = array(
				'id_tg' => $userStatus["id_tg"],
				'name' => NULL,
				'id_school' => NULL,
				'id_class' => NULL,
				'status' => 0,
			);
			$this->Level_model->UpdateUser($userData);
		}elseif (strpos($message, "EDIT") !== FALSE){ 
			if($userStatus['admin']==1) {
				$this->Level_model->AddAdminLog($UserID, $message);
				if (strpos($message, "EDIT") !== FALSE){
					$message = ltrim($message, "EDIT");
				}
				$this->sendMessage($ChatID, 'Menu Edit', $this->KeyboardEdit($message));
			}
		}elseif (strpos($message, "HAPUS MENU") !== FALSE){ 
			if($userStatus['admin']==1) {
				$this->Level_model->AddAdminLog($UserID, $message);
				if (strpos($message, "HAPUS MENU") !== FALSE){
					$message = ltrim($message, "HAPUS MENU ");
				}
				$this->Level_model->DeleteMenu($message);
				$this->sendMessage($ChatID, 'Menu '.$message.'Berhasil Dihapus', $this->KeyboardCustom('KEMBALI KE START'));
			}
		}elseif (strpos($message, "TAMBAH MENU PADA") !== FALSE){ 
			if($userStatus['admin']==1) {
				$this->Level_model->AddAdminLog($UserID, $message);
				if (strpos($message, "TAMBAH MENU PADA") !== FALSE){
					$message = ltrim($message, "TAMBAH MENU PADA ");
				}
				$this->sendMessage($ChatID, 'Masukkan Nama Menu', $this->KeyboardCustom('TAMBAHKAN'));
			}
		}elseif ($userStatus !== FALSE AND $userStatus['status']==3) {
			if ($message == "/start") {
				$this->sendLog($UserID, $message, $output['message']['from']['first_name']." Start");
				$this->sendMessage($ChatID, 'Selamat Datang Kawan..', $this->keyboard(-1,-1,NULL));
			}elseif (strpos($message, "LIHAT KUIS") !== FALSE){ 
				$this->sendMessage($ChatID, 'KUIS TERSEDIA', $this->KeyboardQuiz($UserID));
			}else{
				$data = array('name' => $message);
				$item = $this->Level_model->GetLevelItem($data);
				$video = $this->Level_model->GetVideo($item['id']);
				$this->sendLog($UserID, $message, $output['message']['from']['first_name']." do command ".$message);
				if ($video!=FALSE) {
					$this->sendMessage($ChatID, $item['content'], $this->keyboard($item['name'],$item['level'],$item['parent_id']),$video);
				} else {
					$this->sendMessage($ChatID, $item['content'], $this->keyboard($item['name'],$item['level'],$item['parent_id']));
				}
			}
		}else{
			$userStatus = $this->GetUserSatus($UserID);
			if ($message == "/start") {
				$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Enter Bot");
				$this->sendMessage($ChatID, 'Selamat Datang Kawan Daftar Dulu..', $this->KeyboardCustom("DAFTAR SEKARANG"));
			}elseif ($message == "DAFTAR SEKARANG" OR $userStatus['status']==0) {
				if($userStatus===FALSE){
					$this->Level_model->AddUser($UserID);
					$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Atempt To Sign Up");
					$this->sendMessage($ChatID, 'Masukkan Nama Anda..',$this->KeyboardCustom("RESET"));
				}elseif ($userStatus['status']==0) {
					if ($message == "DAFTAR SEKARANG") {
						$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Atempt To Sign Up");
						$this->sendMessage($ChatID, 'Masukkan nama anda..',$this->KeyboardCustom("RESET"));
					}else{
						$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Add Name");
						$this->sendMessage($ChatID, 'Masukkan nama sekolah..',$this->KeyboardCustom("RESET"));
						$userData = array(
							'id_tg' => $userStatus["id_tg"],
							'name' => $message,
							'status' => 1,
						);
						$this->Level_model->UpdateUser($userData);
					}
					
				}
				
				/* Status
					0 ID_TG Terdaftar
					1 Name Terdaftar
					2 School Terdaftar
					3 Class Terdaftar (Complete) 
				*/
			}else{
				if ($userStatus['status']==1) {
					if ($message == "DAFTAR SEKARANG") {
						$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Atempt To Sign Up And Name Is Filled");
						$this->sendMessage($ChatID, 'Masukkan sekolah..',$this->KeyboardCustom("RESET"));
					}else{
						$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Add School");
						$this->sendMessage($ChatID, 'Masukkan kelas..',$this->KeyboardCustom("RESET"));
						$userData = array(
							'id_tg' => $userStatus["id_tg"],
							'id_school' => 1,
							'status' => 2,
						);
						$this->Level_model->UpdateUser($userData);
					}
					
				}
				if ($userStatus['status']==2) {
					if ($message == "DAFTAR SEKARANG") {
						$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Atempt To Sign Up And School Is Filled");
						$this->sendMessage($ChatID, 'Masukkan kelas..',$this->KeyboardCustom("RESET"));
					}else{
						$this->sendLog($UserID, $message, $output['message']['from']['first_name']." New Member Add Class And SignUp Complete");
						$this->sendMessage($ChatID, 'Complete!!!, Klik button dibawah untuk mulai belajar',$this->KeyboardCustom("KEMBALI KE START"));
						$userData = array(
							'id_tg' => $userStatus["id_tg"],
							'id_class' => 1,
							'status' => 3,
						);
						$this->Level_model->UpdateUser($userData);
					}
					
				}
			}
		}
	}

	public function GetUserSatus($ChatID='')
	{
		return $this->Level_model->GetUserByTGID($ChatID);
	}

	public function KeyboardCustom($value='')
	{
		$keyboardItem = array();
		array_push(
			$keyboardItem,
			array("text" => $value, "callback_data" => $value)
		);
		$keyboard = array(
			"keyboard" => array($keyboardItem),
			'resize_keyboard' => true,
			'one_time_keyboard' => false
		);
		
		$keyboard = json_encode($keyboard, TRUE);
		return ($keyboard);
	}

	public function KeyboardEdit($value='')
	{
		$keyboardItem = array();
		array_push(
			$keyboardItem,
			array("text" => 'HAPUS MENU '.$value, "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'TAMBAH MENU PADA '.$value, "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'GANTI NAMA MENU '.$value, "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'KEMBALI KE '.$value, "callback_data" => $value)
		);
		$keyboard = array(
			"keyboard" => array($keyboardItem),
			'resize_keyboard' => true,
			'one_time_keyboard' => false
		);
		
		$keyboard = json_encode($keyboard, TRUE);
		return ($keyboard);
	}

	public function KeyboardQuiz($id='')
	{
		$keyboardItem = array();
		$QuizAvailable = $this->Level_model->GetQuizAvailable($id);
	
		foreach ($QuizAvailable as $item) {
			$quizData = $this->Level_model->GetQuizData($item['id_quiz']);
			array_push(
				$keyboardItem,
				array("text" => "IKUTI KUIS ".$quizData['quiz_name'], "callback_data" => $item['id_quiz'])
			);
			array_push(
				$keyboardItem,
				array("text" => "KEMBALI KE START", "callback_data" => "kembali")
			);
		}
		
		$keyboard = array(
			"keyboard" => array($keyboardItem),
			'resize_keyboard' => true,
			'one_time_keyboard' => false
		);
		
		$keyboard = json_encode($keyboard, TRUE);
		return ($keyboard);
	}

	public function KeyboardAnswer($value='')
	{
		$keyboardItem = array();
		array_push(
			$keyboardItem,
			array("text" => 'A', "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'B', "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'C', "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'D', "callback_data" => $value)
		);
		array_push(
			$keyboardItem,
			array("text" => 'E', "callback_data" => $value)
		);
		$keyboard = array(
			"keyboard" => array($keyboardItem),
			'resize_keyboard' => true,
			'one_time_keyboard' => false
		);
		
		$keyboard = json_encode($keyboard, TRUE);
		return ($keyboard);
	}

	public function keyboard($data='',$currentLevel='', $parentID = '')
	{
		$output = $this->outputBot;
		$UserID = $output['message']['from']['id'];
		$nameSelf = array('name' => $data, );
		$parentData = $this->Level_model->GetLevelItemByID($parentID);
		$selfData = $this->Level_model->GetLevelItem($nameSelf);
		$childLevelItem = $this->Level_model->GetLevel($currentLevel+1);
		$keyboardItem = array();
		$keyboardItem2 = array();
		foreach ($childLevelItem as $item) {

			if ($item['parent_id'] == $selfData['id']) {
				array_push(
					$keyboardItem,
					array("text" => $item['name'], "callback_data" => $item['name'])
				);
			}elseif($data == -1 AND $item['parent_id'] == -1){
				array_push(
					$keyboardItem,
					array("text" => $item['name'], "callback_data" => $item['name'])
				);
			}	
		}
		if ($currentLevel == 0 AND $parentID != NULL) {
			array_push(
				$keyboardItem2,
				array("text" => "KEMBALI KE START", "callback_data" => 'kembali')
			);	
		}elseif($currentLevel>0){
			array_push(
				$keyboardItem2,
				array("text" => "KEMBALI KE START", "callback_data" => 'kembali')
			);	
			array_push(
				$keyboardItem2,
				array("text" => "KEMBALI KE ".$parentData['name'], "callback_data" => 'kembali')
			);
		}
		$userStatus = $this->GetUserSatus($UserID);
		if ($userStatus['admin']==1) {
			array_push(
				$keyboardItem,
				array("text" => "EDIT ".$nameSelf['name'], "callback_data" => 'kembali')
			);
		}
		$keyboard = array(
			"keyboard" => array($keyboardItem,$keyboardItem2),
			'resize_keyboard' => true,
			'one_time_keyboard' => false
		);
		
		$keyboard = json_encode($keyboard, TRUE);
		return ($keyboard);
	}

	public function sendMessage($ChatID, $message, $keyboard='', $video='') 
	{ 
		if ($video== FALSE) {
			$url = ($this->api['connect'] . '/sendMessage?chat_id=' . $ChatID . '&text=' . urlencode($message) . '&parse_mode=html&reply_markup=' . $keyboard . '&disable_web_page_preview=FALSE'); 
		}else{
			$url = ($this->api['connect'] . '/sendVideo?chat_id=' . $ChatID ."&caption=".$video['caption']. '&video=' . $video['link'] . '&parse_mode=html&reply_markup=' . $keyboard); 
		}
		if (!function_exists('curl_init')){ 
			die('CURL is not installed!');
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;	
	}

	public function sendLog($idUser='', $command='', $log='')
	{
		$this->Level_model->AddLog($idUser, $command, $log);
	}
}



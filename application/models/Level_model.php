<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Level_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}

	public function GetLevel($data='')
	{
		if(isset($data)){
			$this->db->where('level',$data);
		}
		$query = $this->db->get('menu');
		$post = $query->result_array();
		return $post;
	}
	public function GetLevelItem($data='')
	{
		if(isset($data['name'])){	
			$this->db->where('name',$data['name']);
		}
		$query = $this->db->get('menu');
		$post = $query->row_array();
		return $post;
	}
	public function GetLevelItemByID($data='')
	{
		if(isset($data)){	
			$this->db->where('id',$data);
		}
		$query = $this->db->get('menu');
		$post = $query->row_array();
		return $post;
	}
	public function GetVideo($data='')
	{
		if(isset($data)){	
			$this->db->where('id_menu',$data);
		}
		$query = $this->db->get('menu_video');
		$post = $query->row_array();

		if($post['id_video']!=NULL){
			$this->db->where('id',$post['id_video']);
			$query = $this->db->get('video');
			$post = $query->row_array();
		}

		if($post['id']!=NULL){
			return $post;
		}else{
			return FALSE;
		}
	}
	public function GetUserByTGID($data='')
	{
		if(isset($data)){	
			$this->db->where('id_tg',$data);
		}
		$query = $this->db->get('user');
		$post = $query->row_array();
		
		if($post['id']!=NULL){
			return $post;
		}else{
			return FALSE;
		}
	}
	
	public function AddUser($data='')
	{
		$dataUser = array(
	        'id_tg' => $data,
		);
		if (isset($data)) {
			$this->db->insert('user', $dataUser);
		}
	}
	public function UpdateUser($data='')
	{
		$dataUser = $data;
		unset($dataUser['id_tg']);
		$this->db->set($dataUser);
		$this->db->where('id_tg', $data["id_tg"]);
		$this->db->update('user');
	}
	public function AddLog($idUser='', $command='', $log='')
	{
		if (isset($idUser) AND isset($command) AND isset($log)) {
			$data = array(
				'id_admin' => $idUser, 
				'command' => $command,
				'log' => $log, 
			);
			$this->db->insert('log', $data);
		}
	}
	public function DeleteMenu($data='')
	{
		$this->db->where('name', $data);
		$this->db->delete('menu');
	}

	public function AddAdminLog($idUser='', $command='')
	{
		if (isset($idUser) AND isset($command)) {
			$data = array(
				'id_admin' => $idUser, 
				'command' => $command,
			);
			$this->db->insert('admin_log', $data);
		}
	}

	public function GetAdminLastCommand($id='')
	{
		$query = $this->db->query("SELECT * FROM admin_log ORDER BY id DESC LIMIT 1");
		$post = $query->row_array();
		return $post;
	}

	public function GetQuizAvailable($id='')
	{
		$user = $this->GetUserByTGID($id);
		$this->db->where('id_class', 1);
		$quiz = $this->db->get('quiz_class');
		return $quiz->result_array();
	}

	public function GetUserQuizStatus($id='')
	{
		$user = $this->GetUserByTGID($id);
		$this->db->where('id_user', $user['id']);
		$this->db->where('id_quiz', 1);
		$this->db->where('status !=', '99');
		$quiz = $this->db->get('score');
		return $quiz->num_rows();
	}

	public function GetUserQuizProgress($id='')
	{
		$user = $this->GetUserByTGID($id);
		$this->db->where('id_user', $user['id']);
		$this->db->where('id_quiz', 1);
		$this->db->where('status !=', '99');
		$quiz = $this->db->get('score');
		return $quiz->row_array();
	}

	public function GetQuestion($id='')
	{
		$user = $this->GetUserByTGID($id);
		$userQuizProgress = $this->GetUserQuizProgress($id);

		$this->db->where('id_quiz', $userQuizProgress['id_quiz']);
		$this->db->where('number', $userQuizProgress['status']+1);
		$quiz = $this->db->get('question_quiz')->row_array();
		$this->db->where('id_question', $quiz['id_question']);
		$question = $this->db->get('question');
		if ($question->num_rows()!=0) {
			return $question->row_array();
		}else{
			return 0;
		}
		
	}

	public function AddUserToQuiz($id='')
	{
		$user = $this->GetUserByTGID($id);
		$data = array(
			'id_user' => $user['id'], 
			'id_quiz' => 1,
		);
		$this->db->insert('score', $data);
		return 1;
	}

	public function AddAnswer($id='', $answer='')
	{
		$user = $this->GetUserByTGID($id);
		$userQuizProgress = $this->GetUserQuizProgress($id);
		$data2 = array(
			'id_user' => $user['id'], 
			'id_quiz' => 1,
		);
		$input = array(
			'status' => $userQuizProgress['status']+1,
			'answer_data' => $userQuizProgress['answer_data'].$answer,
		);
		$this->db->where($data2);
		$this->db->update('score', $input);
		return 1;
	}

	public function AddScoreToDB($id='', $score='')
	{
		$user = $this->GetUserByTGID($id);
		$userQuizProgress = $this->GetUserQuizProgress($id);
		$data2 = array(
			'id_user' => $user['id'], 
			'id_quiz' => 1,
		);
		$input = array(
			'score' => $score,
		);
		$this->db->where($data2);
		$this->db->update('score', $input);
		return 1;
	}

	public function GetKey($id='')
	{
		$user = $this->GetUserByTGID($id);
		$this->db->where('id_class', $user['id_class']);
		$this->db->where('id_quiz', 1);
		$quiz = $this->db->get('quiz_class')->row_array();
		$this->db->where('id_quiz', $quiz['id_quiz']);
		$quizMember = $this->db->get('question_quiz')->result_array();
		$key = '';
		foreach ($quizMember as $quizItem) {
			$this->db->where('id_question', $quizItem['id_question']);
			$question = $this->db->get('question')->row_array();
			if ($question['answer_key']==1) {
				$key = $key.'A';
			}elseif ($question['answer_key']==2) {
				$key = $key.'B';
			}elseif ($question['answer_key']==3) {
				$key = $key.'C';
			}elseif ($question['answer_key']==4) {
				$key = $key.'D';
			}elseif ($question['answer_key']==5) {
				$key = $key.'E';
			}
		}
		return $key;
	}

	public function GetAnswer($id='')
	{
		$user = $this->GetUserByTGID($id);
		$this->db->where('id_user', $user['id']);
		$this->db->where('id_quiz', 1);
		
		return $this->db->get('score')->row_array();
	}

	public function GetQuizData($id='')
	{
		$this->db->where('id_quiz', $id);
		
		return $this->db->get('quiz')->row_array();
	}
}

?>

<?php
namespace Controllers;

use \Core\Controller;
use \Models\Users;
use \Models\History;


class UsersController extends Controller {

	public function index() {}

	// Faz login na API
	public function login() {
		$array = array('error'=>'');

		// Verifica o método da requisição
		$method = $this->getMethod();

		// Pega os dados enviado pela chamada 
		$data = $this->getRequestData();


		if($method == 'POST') {
			// Verifica se e-mail e senha foram enviados
			if(!empty($data['email']) && !empty($data['pass'])) {
				$users = new Users();
				$h = new History();
				// Verifica se existe usário com os dados enviados
				if($users->checkCredentials($data['email'], $data['pass'])) {
					$array['jwt'] = $users->createJwt();
					// Grava o login no historico
					$h->new_action($users->getId(), $data['email']." (Acabou de logar)");
				} else {
					$array['error'] = 'Acesso negado';
				}
			} else {
				$array['error'] = 'E-mail e/ou senha não preenchido.';
			}
		} else {
			$array['error'] = 'Método de requisição incompatível';
		}

		$this->returnJson($array);
	}

	public function new_record() {
		$array = array('error' => '');

		$method = $this->getMethod();
		$data = $this->getRequestData();

		if($method == 'POST') {

			// Verifica se todos os campos foram preenchidos
			if(!empty($data['name']) && !empty($data['email']) && !empty($data['pass'])) {

				// Verifica se o e-mail enviado estar no padrão de e-mail
				if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
					$users = new Users();
					$h = new History();
					// Verifica se o e-mail ainda não existe e cria o usuário
					if($users->create($data['name'], $data['email'], $data['pass'])) {
						// Cria o JWT para validar a sessão
						$h->new_action($users->getId(), $data['email']." (Acabou de ser cadastrado)");
						$array['jwt'] = $users->createJwt();
					} else {
						$array['error'] = 'E-mail já existente';
					}
				} else {
					$array['error'] = 'E-mail inválido';
				}
			} else {
				$array['error'] = 'Dados não preenchidos';
			}
		} else {
			$array['error'] = 'Método de requisição incompatível';
		}

		$this->returnJson($array);
	}

	public function view($id) {
		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod();
		$data = $this->getRequestData();

		$users = new Users();

		// Verifica se foi enviado JWT na requição e valida o JWT
		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			// Verifica se está vendo as informações do usuário logado ou de outro usuário
			$array['is_me'] = false;
			if($id == $users->getId()) {
				$array['is_me'] = true;
			}

			// Verifica os métodos para chamar a função correspondente
			switch($method) {
				case 'GET':
				$array['data'] = $users->getInfo($id);
					// Verifica se existe usuário com o ID informado
				if(count($array['data']) === 0) {
					$array['error'] = 'Usuário não existe';
				}
				break;
				case 'PUT':
				$array['error'] = $users->editInfo($id, $data);
				break;
				case 'DELETE':
				$array['error'] = $users->delete($id);
				break;
				default:
				$array['error'] = 'Método '.$method.' não disponível';
				break;
			}


		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);
	}


	public function historico($id) {
		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod(); 
		$data = $this->getRequestData();

		$users = new Users();
		$h = new History();					

		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			$array['is_me'] = false;
			if($id == $users->getId()) {
				$array['is_me'] = true;
			}


			if($method === 'GET'){

				$array['data'] = $h->get_history($id);
			}
			

		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);
	}
	

}




















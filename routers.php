<?php
global $routes;
$routes = array();

// Define as rotas da API endpoints

$routes['/users/login'] = '/users/login'; // Faz login na API
$routes['/users/new'] = '/users/new_record'; // Cadastra usuário na API

$routes['/users/historico/{id}'] = '/users/historico/:id';  // Visualiza histórico do usuário

$routes['/users/{id}'] = '/users/view/:id'; //Visualiza dados de usuário da API

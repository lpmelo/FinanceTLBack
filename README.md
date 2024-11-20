# FINANCETL API - Documentação 
## Introdução

A API FINANCETL é uma aplicação PHP 8.1 construída sem o uso de frameworks externos, fornecendo funcionalidades básicas de roteamento para gerenciar suas operações financeiras dentro do aplicativo FINANCETL.

## Rotas

### GET /users

Retorna uma lista de todos os usuários.

### POST /users

Cria usuários

### PUT /users/{id}

Atualiza os detalhes de um usuário.
Corpo da Requisição: (mesmos campos do POST, mas id não é permitido)

### DELETE /users/{id}

Exclui um usuário pelo seu ID.
Estrutura de Respostas

### Sucesso:

Código de Status: 200 (OK) ou 201 (Created)
Corpo: JSON com os dados da transação ou mensagem de sucesso.
Erro:

Código de Status: 400 (Bad Request), 404 (Not Found) ou 500 (Internal Server Error)
Corpo: JSON com mensagem de erro detalhada.

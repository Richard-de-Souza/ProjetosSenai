# 🍕 Sistema de Pizzaria - CRUD Educacional

Este projeto é um sistema fictício de pizzaria desenvolvido com o objetivo de ensinar como criar operações CRUD (Create, Read, Update, Delete) em PHP com MySQL. Cada ação realizada no sistema exibe o SQL correspondente, ajudando os alunos a entenderem a interação entre frontend, backend e banco de dados.

## 🎯 Objetivo Educacional

- Ensinar a estrutura de um sistema web em PHP
- Demonstrar as operações básicas de CRUD com SQL real
- Reforçar a lógica de requisições e respostas entre PHP e MySQL

## 🗂️ Funcionalidades

- Login e autenticação simples
- Cadastro, edição e listagem de clientes
- Registro e edição de pedidos
- Gerenciamento de sabores de pizza
- Templates reutilizáveis para estrutura visual
- Visualização dos comandos SQL executados

## 📁 Estrutura dos Arquivos

- `index.php`: Página inicial
- `login.php` / `logout.php`: Acesso de usuários
- `controller.php`: Gerencia rotas e requisições
- `banco.php`: Conexão com o banco + exibição dos comandos SQL
- `clientes.php`, `cliente_form.php`, `cliente_edit.php`: CRUD de clientes
- `pedido.php`, `pedido_edit.php`: CRUD de pedidos
- `pizza.php`, `pizza_form.php`, `pizza_edit.php`: CRUD de pizzas
- `template_start.php` / `template_end.php`: Layout reutilizável

## ⚙️ Tecnologias Utilizadas

- **PHP** (sem frameworks)
- **MySQL**
- **HTML/CSS**

## 🚀 Como Rodar Localmente

1. Instale XAMPP ou similar com Apache + MySQL.
2. Copie a pasta `ProjetosSenai-master` para `htdocs`.
3. Crie um banco de dados e configure o acesso em `banco.php`.
4. Acesse `http://localhost/ProjetosSenai-master`.

## 📢 Observações

- Cada ação no banco imprime o SQL utilizado, ideal para aprendizado.
- O projeto **não é seguro para produção** — uso exclusivo para ensino.
- Nenhum framework é utilizado para facilitar a compreensão da base do PHP puro.

## 🧑‍🏫 Licença e Uso

Este projeto pode ser livremente copiado, adaptado e utilizado em cursos, aulas e oficinas com fins educacionais.

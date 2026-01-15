# 📚 Biblioteca Municipal

Sistema simples para controle de livros, leitores e empréstimos
de uma biblioteca municipal.

---

## Tecnologias
- PHP
- MySQL / MariaDB
- Bootstrap
- Laragon

---

## Objetivo
Auxiliar o gestor da biblioteca no controle de empréstimos,
substituindo processos manuais.

---

## Status do Projeto
- [x] Layout e navegação
- [x] Telas de livros, leitores e empréstimos
- [x] Estrutura do banco de dados
- [x] Conexão com MySQL
- [x] Funcionalidades básicas (CRUD de livros, leitores e empréstimos)

---

## 🗂 Estrutura do projeto

biblioteca-municipal/
├─ config/
│ └─ database.php # Conexão com o banco de dados
├─ public/
│ ├─ index.php
│ ├─ livros.php
│ ├─ leitores.php
│ ├─ emprestimos.php
│ └─ layout/
│ ├─ header.php
│ └─ footer.php
└─ .gitignore


---

## ⚙️ Configuração do Banco de Dados

- Banco: `biblioteca_municipal`
- Tabelas principais:
  - `livros` (id, titulo, autor, editora, ano, quantidade)
  - `leitores` (id, nome, cpf, telefone, email)
  - `emprestimos` (id, livro_id, leitor_id, data_emprestimo, data_devolucao, devolvido)
- Conexão em `config/database.php`:

```php
$host = 'localhost';
$dbname = 'biblioteca_municipal';
$user = 'root';
$pass = ''; // Laragon padrão

🚀 Como rodar o sistema (PC local ou outro computador)

Instalar Laragon ou XAMPP (com PHP + MySQL)

Iniciar Apache e MySQL

Copiar a pasta biblioteca-municipal para C:\laragon\www\ (ou raiz do Apache)

Importar o banco de dados:

Abrir HeidiSQL

Criar banco biblioteca_municipal

Importar arquivo biblioteca_municipal.sql (estrutura + dados)

Acessar no navegador:

http://localhost/biblioteca-municipal/public/

📌 Funcionalidades

Livros: cadastrar, listar e controlar estoque

Leitores: cadastrar e listar

Empréstimos:

Registrar empréstimo (verifica estoque)

Devolver livro (atualiza estoque e status)

Não permite empréstimo sem estoque

💡 Observações

Sistema local, não online

Backup do banco regularmente

Login não implementado (acesso aberto)

Layout com Bootstrap, facilmente ajustável

Todos os dados podem ser conferidos via HeidiSQL

15/01/2026 16:59

Sistema Biblioteca Municipal

PHP + PDO

MySQL

CRUD de Leitores

CRUD de Livros

Controle de Empréstimos

Relacionamentos com Foreign Key

Validação de exclusão (leitor com empréstimo ativo)

Exclusão segura de registros dependentes

Bootstrap 5 (layout responsivo)

Estrutura com header/footer

Regras de negócio aplicadas no backend
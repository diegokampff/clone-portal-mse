CREATE DATABASE IF NOT EXISTS portal_mse
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE portal_mse;

CREATE TABLE IF NOT EXISTS fornecedores (
  id INT AUTO_INCREMENT PRIMARY KEY,

  cnpj VARCHAR(14) NOT NULL,
  razao_social VARCHAR(255) NOT NULL,
  nome_fantasia VARCHAR(255) NOT NULL,
  inscricao_estadual VARCHAR(80) NOT NULL,

  regime_icms VARCHAR(50) NOT NULL,
  situacao VARCHAR(80) NOT NULL,
  telefone VARCHAR(30) NOT NULL,
  email VARCHAR(255) NOT NULL,

  rua VARCHAR(255) DEFAULT NULL,
  numero VARCHAR(50) DEFAULT NULL,
  bairro VARCHAR(120) DEFAULT NULL,
  complemento VARCHAR(255) DEFAULT NULL,
  cep VARCHAR(8) DEFAULT NULL,
  pais VARCHAR(60) DEFAULT NULL,
  estado VARCHAR(2) DEFAULT NULL,
  municipio VARCHAR(120) DEFAULT NULL,

  fornecedor_de VARCHAR(255) DEFAULT NULL,
  ramos TEXT DEFAULT NULL,

  cnpj_responsavel VARCHAR(14) NOT NULL,
  nome_responsavel VARCHAR(255) NOT NULL,

  senha_hash VARCHAR(255) NOT NULL,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY uk_fornecedores_cnpj (cnpj),
  UNIQUE KEY uk_fornecedores_email (email)
);

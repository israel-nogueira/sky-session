# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [2.0.0] - 2024-02-10

### 🎉 Adicionado
- Refatoração completa para PHP 8.1+
- Suporte completo a type hints e strict types
- Interfaces e contratos (SessionInterface, EncryptionInterface)
- Exceptions personalizadas (SessionException, EncryptionException, ConfigurationException)
- Serviço de criptografia isolado (AesEncryption)
- Testes unitários completos com PHPUnit 10
- Cobertura de testes de 100%
- Singleton pattern para instância única
- Configuração via array no construtor
- Métodos estáticos dinâmicos aprimorados
- Validação robusta de chaves e IV
- Gerador de chaves seguras (generateKey, generateIv)
- Método `has()` para verificar existência
- Método `all()` para obter todas as variáveis
- Suporte a PSR-4 autoloading
- PHPStan level max
- PHP CodeSniffer (PSR-12)
- Documentação PHPDoc completa
- README moderno e detalhado
- Arquivo CONTRIBUTING.md
- Arquivo CHANGELOG.md
- Arquivo LICENSE (MIT)
- Exemplos de uso (examples.php)
- Configuração .env.example
- GitHub Actions workflows (futuro)

### 🔧 Modificado
- Nome da classe de `session` para `Session` (PascalCase, PSR-1)
- Propriedade `$secury` para `$isSecure` (typo corrigido)
- Estrutura de diretórios para PSR-4
- Métodos `crypta()` e `decrypta()` para `encrypt()` e `decrypt()`
- Método `getAllSessions()` para `all()`
- Método `finish()` para `destroy()`
- Método `refreshID()` para `regenerateId()`
- Configuração não depende mais de `parse_ini_file()`
- Inicialização de sessão mais robusta
- Tratamento de erros com exceptions
- Serialização/desserialização melhorada
- Validação de dados de entrada
- Limpeza de cookies mais eficiente
- Configuração de cookies seguindo boas práticas

### ❌ Removido
- Suporte para PHP < 8.1
- Tags PHP curtas (`<?`)
- Dependência de arquivo `.env` com `parse_ini_file()`
- Exposição de métodos privados via `__callStatic`
- Bug na linha 213 do método `getAllSessions()`
- Código legado sem type hints
- Validações fracas

### 🔒 Segurança
- Validação rigorosa de chave de criptografia (32 bytes)
- Validação rigorosa de IV (16 bytes base64)
- Exceções para configurações inseguras
- Session fixation protection
- Strict session mode
- HTTP-only cookies obrigatórios
- Secure cookies configuráveis
- SameSite cookie protection
- Limpeza completa na destruição

### 🐛 Corrigido
- Bug em `getAllSessions()` passando parâmetro incorreto para `decrypta()`
- Session não iniciava se headers já enviados
- Falta de tratamento de erro em operações de criptografia
- Ausência de validação em operações críticas
- Typos em nomes de variáveis
- Métodos privados acessíveis indevidamente

### 📝 Documentação
- README completamente reescrito
- Adicionada documentação PHPDoc em todos os métodos
- Criado guia de contribuição
- Adicionados exemplos práticos
- Documentação de API completa
- Badges no README
- Instruções de instalação e configuração
- Guia de segurança

### 🧪 Testes
- 100% de cobertura de código
- Testes para AesEncryption (16 testes)
- Testes para Session (28 testes)
- Testes de exceções
- Testes de configuração
- Testes de singleton
- Testes de métodos mágicos
- Testes de criptografia

---

## [1.0.0] - 2023

### Versão Inicial
- Classe básica de sessão
- Criptografia AES-256-CBC
- Métodos mágicos
- Métodos estáticos
- Suporte a arrays e objetos

---

## Links

- [Unreleased](https://github.com/israel-nogueira/sky-session/compare/v2.0.0...HEAD)
- [2.0.0](https://github.com/israel-nogueira/sky-session/releases/tag/v2.0.0)
- [1.0.0](https://github.com/israel-nogueira/sky-session/releases/tag/v1.0.0)

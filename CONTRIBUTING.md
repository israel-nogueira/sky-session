# Contribuindo para Sky Session

Obrigado por considerar contribuir com o Sky Session! 🎉

## 📋 Como Contribuir

### 1. Reportar Bugs

Abra uma issue com:
- Descrição clara do problema
- Passos para reproduzir
- Comportamento esperado vs atual
- Versão do PHP e Sky Session
- Mensagens de erro completas

### 2. Sugerir Funcionalidades

Abra uma issue descrevendo:
- O problema que resolve
- Como funcionaria
- Exemplos de uso
- Possíveis alternativas consideradas

### 3. Pull Requests

#### Antes de começar:
1. Fork o repositório
2. Clone seu fork
3. Crie uma branch: `git checkout -b feature/minha-funcionalidade`

#### Durante o desenvolvimento:
- Siga o PSR-12 Code Style
- Adicione testes para novas funcionalidades
- Atualize a documentação se necessário
- Use type hints e return types
- Adicione PHPDoc completo
- Mantenha compatibilidade com PHP 8.1+

#### Antes de submeter:
```bash
# Execute os testes
composer test

# Verifique code style
composer cs:check

# Execute análise estática
composer phpstan
```

#### Ao submeter:
1. Commit com mensagem clara (Conventional Commits)
2. Push para seu fork
3. Abra Pull Request com:
   - Descrição do que foi feito
   - Por que é necessário
   - Como testar
   - Screenshots (se aplicável)

## 📝 Padrões de Código

### PSR-12
```php
<?php

declare(strict_types=1);

namespace IsraelNogueira\SkySession;

class MinhaClasse
{
    private string $propriedade;

    public function __construct(string $propriedade)
    {
        $this->propriedade = $propriedade;
    }

    public function metodo(string $parametro): string
    {
        return $parametro;
    }
}
```

### Conventional Commits
```
feat: adiciona nova funcionalidade X
fix: corrige bug Y
docs: atualiza documentação Z
test: adiciona teste para W
refactor: refatora classe V
style: ajusta formatação
chore: atualiza dependências
```

### PHPDoc
```php
/**
 * Descrição breve do método
 *
 * Descrição detalhada se necessário
 *
 * @param string $parametro Descrição do parâmetro
 * @return bool Descrição do retorno
 * @throws ExceptionType Quando ocorre
 */
public function exemplo(string $parametro): bool
{
    // código
}
```

## ✅ Checklist do PR

- [ ] Código segue PSR-12
- [ ] Testes adicionados/atualizados
- [ ] Testes passando (`composer test`)
- [ ] PHPStan sem erros (`composer phpstan`)
- [ ] Code style correto (`composer cs:check`)
- [ ] Documentação atualizada
- [ ] CHANGELOG.md atualizado
- [ ] Commits seguem Conventional Commits

## 🔒 Segurança

Para reportar vulnerabilidades de segurança, **NÃO abra issues públicas**.

Envie email para: **israel@feats.com.br**

## 💬 Dúvidas?

Abra uma issue com a tag `question`.

---

**Obrigado por contribuir! 🙌**

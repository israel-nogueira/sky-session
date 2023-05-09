![GalaxyDB](https://raw.githubusercontent.com/israel-nogueira/sky-session/main/src/topo_README.jpg)

---

[![Latest Stable Version](https://poser.pugx.org/israel-nogueira/sky-session/v/stable.svg)](https://packagist.org/packages/israel-nogueira/sky-session)
[![Total Downloads](https://poser.pugx.org/israel-nogueira/sky-session/downloads)](https://packagist.org/packages/israel-nogueira/sky-session)
[![License](https://poser.pugx.org/israel-nogueira/sky-session/license.svg)](https://packagist.org/packages/israel-nogueira/sky-session)


Se você está procurando uma solução simples e fácil para trabalhar com sessões criptografadas em PHP, a classe de sessões que desenvolvi pode ser a escolha certa para você. Com ela, você pode facilmente armazenar e recuperar dados sensíveis em suas sessões, mantendo-os protegidos contra invasões e vazamentos de informações.

A classe é extremamente simples de usar, com um construtor que permite configurar facilmente a criptografia da sessão e um conjunto de métodos intuitivos para armazenar e recuperar dados. Com uma documentação clara e completa, você pode começar a usar a classe em questão de minutos, sem ter que se preocupar com complexidades desnecessárias.


## Instalação

Instale via composer.

```plaintext
    composer require israel-nogueira/sky-session
```

Acrescente em seu ```.env```:

```env

    #vendor/israel-nogueira/sky-session/src/.env

    SESSION_SECRET=4ddbd246d4a9848eb67df3e7b98c32a25b91f57760d57eeaccecf808c48731ed
    SESSION_CRYPT_KEY=0723489yheuhf724fgoewygf
    SESSION_CRYPT_IV=0123456789abcdef
    SESSION_NAME=sesion_name_kkk

```

# Suap OAuth2 PHP
Biblioteca para conexão com a API do SUAP para o IFRN.

## Sobre

O **Suap OAuth2 php** implementa a integração com o SUAP, tendo 2 principais funcionalidades:

- Logar com SUAP via OAuth2
- Consumir API (via OAuth2) obtendo recursos em nome do usuário

## Requisitos

- cURL;

---

## Instalação

 Antes da instalação verifique se atende aos resquisitos.

> Mova os arquivos deste pacote para seu servidor

```shell
Root                          # → Root Project Directory
├── class/
│   └── Suap-OAuth2.php  
├── includes/
│   └── constants.php
├── suap_logs
│   └── index.html
└── index.php                 # → Arquivo para teste de funções  
```

---

## Instruções

### Crie sua Aplicação no SUAP

Crie sua aplicação em https://suap.ifrn.edu.br/api/ com as seguintes informações:

- **Client Type:** Confidential
- **Authorization Grant Type:** authorization-code
- **Redicert URIs**: **SEU_HOST**/suap_auth/ (Alterar para o seu servidor)
- Configure o **Client_id**, **Client_secret** e **Redirect_uri** no arquivo */includes/constants.php*


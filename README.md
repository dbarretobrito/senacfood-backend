# 🥗 SENACFOOD

## Sobre
Este projeto é uma API Back-end desenvolvida em Laravel, cronstruída com o objetivo de cadastro de receitas, ingredientes culinários e integração com modelos agentes de IA. A proposta é oferecer uma plataforma prática e intuitiva que facilite o dia a dia de quem gosta ou precisa cozinhar, permitindo que os usuários cadastrem, consultem e organizem suas receitas de forma simples e eficiente.

## 1. Instalação e Execução
```
git clone https://github.com/seuRepositorio/senacFoodBackend.git
cd senacFoodBackend
composer install //instalar dependências
cp .env.example .env
php artisan key:generate
php artisan migrate //para gerar a database e as tabelas
```

## 2. Verificação
- Verifique se o .env foi criado e esta correto
- Verifique se as tabelas foram criadas corretamente

## 3. Rotas para teste
- Praticamente todas as rotas estão protegidas para serem acessadas apenas com login.
```
http://localhost/api/register
{
	"name": "usuario",
	"email": "usuario@gmail",
	"password": "123456789",
	"password_confirmation": "123456789"
}
--
http://localhost/api/login
{
	"email": "usuario@gmail",
	"password": "123456789"
}

```
para acessar a seguinte rota, deve consumir o Token disponibilizado pela rota de login
http://localhost/api/users
## 4. Integração com modelos de IA
- A integração com modelos de IA ocorre com a API do Groq Cloud
- cadastre-se no site e pegue a chave da API, colocando no .env
  https://console.groq.com/home 
  ```
  //.env
  GROQ_API_KEY=api_key_aqui

  //terminal
  composer require lucianotonet/groq-php //para instalar a biblioteca
  ```

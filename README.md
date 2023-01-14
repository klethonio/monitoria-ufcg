
<!-- PROJECT SHIELDS -->
![GitHub language count][language-count] ![GitHub top language][top-language] [![LinkedIn][linkedin-shield]][linkedin-url]

<a  name="readme-top"></a>

# Monitoria UFCG

Sistema de correção de atividades desenvolvido para a Universidade Federal de Campina Grande em 2016.

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>



## Descrição do projeto

O sistema gerencia turmas de diferentes disciplinas, professores, monitores e alunos, com diversas listas de exercícios e highlights para diversas linguagens. 

Contando também com relatórios de exercícios disponíveis para visualização online ou download de planilhas

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>


## Tecnologias

* [![PHP][PHP.com]][PHP-url]
* [![MySQL][MySQL.com]][MySQL-url]
* [![HTML5][HTML5.com]][HTML5-url]
* [![CSS3][CSS3.com]][CSS3-url]
* [![JQuery][javascript.com]][javascript-url] [![JQuery][JQuery.com]][JQuery-url]

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>


## Instalação

### Configurações Iniciais

No arquivo `dts/iniSis.example.php` você pode configurar o banco de dados, servidor de e-mail e os demais dados do blog, renomeie esse arquivo para `dts/iniSis.php`. As principais são:

```php
// You need to trim the slashs
define('BASE', 'http://localhost');

// MySQL
define('HOST', 'localhost');
define('USER', 'root');
define('PASS', '');
define('DBSA', 'monitoria');

// Mail
define('MAILPORT', 587);
define('MAILHOST', 'smtp.gmail.com');
```

### Banco de dados

A pasta raiz do projeto tem o arquivo `monitoria.sql`, você pode restaurar o banco de dados no seu servidor a partir desse arquivo.

Os dados do usuário padrão já estão cadastrado nesse backup e podem ser alterados no painel do administrador, `admin` e `123123`, são os respectivos usuário e senha.

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>


## Começando

### Admin

Acessando `/admin` a partir da base do projeto você conseguirá acessar o painel administrador, adicione uma disciplina e a partir daí é possível iniciar turmas.

#### - Dados do Gmail

Você pode adicionar uma conta gmail para enviar e-mails para os alunos cadastrados. E ainda o servidor de e-mail pode ser alterado em `iniSis.php`.

Caso tenha interesse de usar o servidor de e-mail padrão, algumas alterações precisam ser feitas.

### Área do Professor

Na base do projeto, é possível acessar as turmas iniciadas e ativadas com a matrícula e senha do professor cadastrado no momento da criação da turma.

Aqui você poderá cadastrar ou ativar alunos que se cadastraram na disciplina e cadastrar monitores da disciplina, para que possam corrigir as atividades enviadas.

É possível também criar listas de atividades para serem enviadas pelos alunos nos formatos de PDF, planilhas ou ainda diversas outras linguagens de programação (Fortran, Matlab, C, C++, Python) e ainda arquivos compactados.

Depois de criar uma lista é possível requisitar atividades para posteriormente serem corrigidas com notas e anotações, e ao final, impressas em relatórios para registro acadêmico.

Essas atividades podem ser baixadas se forem planilhas, PDF ou arquivos compactados, ou ainda, visualizadas no browser se forem arquivos das extensões restantes permitidas.

### Áreas do aluno

Os alunos podem se cadastrar e solicitar a ativação da sua conta, a partir daí poderá enviar atividades requisitas e visualizar as correções dos monitores, também poderá visualizar e baixar as listas disponíveis.


## Add-ons / Plugins

* [PHPMailer](https://github.com/PHPMailer/PHPMailer)
* [PHPExcel](https://github.com/PHPOffice/PHPExcel)
* [Encoding](https://github.com/neitanod/forceutf8)
* [Generic Syntax Highlighter](https://github.com/GeSHi)

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>


## Versão do PHP

A versão utilizada na criação do projeto foi `php 5.2.6`, mas o projeto foi adaptado para `php 8.0.12`. Alguns plugins foram modificados para se adequarem a versão do PHP.

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>

## Contato

Klethonio Lacerda - klethonio@gmail.com
Linkedin: [https://www.linkedin.com/in/klethonio-lacerda/](https://www.linkedin.com/in/klethonio-lacerda/)
Link do Projeto: [https://github.com/klethonio/monitoria-ufcg](https://github.com/klethonio/monitoria-ufcg)

<p  align="right">(<a  href="#readme-top">back to top</a>)</p>


<!-- MARKDOWN LINKS & IMAGES -->
[top-language]: https://img.shields.io/github/languages/top/klethonio/monitoria-ufcg?style=for-the-badge
[language-count]: https://img.shields.io/github/languages/count/klethonio/monitoria-ufcg?style=for-the-badge
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/othneildrew
[PHP.com]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://php.net
[MySQL.com]: https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white
[MySQL-url]: https://www.mysql.com/
[HTML5.com]: https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white
[HTML5-url]: https://html.com/html5/
[CSS3.com]: https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white
[CSS3-url]: https://developer.mozilla.org/en-US/docs/Web/CSS
[javascript.com]: https://img.shields.io/badge/javascript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=white
[javascript-url]: https://www.javascript.com/
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com
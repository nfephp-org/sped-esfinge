# SPED-ESFINGE

**API PHP para integração com o Sistema de Fiscalização Integrada de Gestão (e-Sfinge) do Tribunal de Contas do Estado de Santa Catarina**

*sped-esfinge* é um framework que permite a integração de um aplicativo com os serviços do projeto e-Sfinge do TCE/SC, realizando a montagem das mensagens SOAP usando Web Services Security (especificação publicada pela OASIS), com username e password fornecidos pelo TCE/SC.

[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

[![Issues][ico-issues]][link-issues]
[![Forks][ico-forks]][link-forks]
[![Stars][ico-stars]][link-stars]


## TCE Santa Catarina

O Sistema de Fiscalização Integrada de Gestão (e-Sfinge) é um conjunto de aplicativos integrados, relacionados à atividade-fim do TCE/SC. O e-Sfinge recebe as informações sobre as contas públicas enviadas pelos agentes públicos e consolida os dados de gestão em remessas unificadas, emite relatórios automáticos de avaliação, analisa a gestão de cada município e do Estado, ampliando a publicidade das informações.
O acesso ao e-Sfinge — incluindo módulos Aposentadoria e Pensão, Instrução Normativa 21/2015, Instrução Normativa 22/2015, e-Sfinge Web, Obras e Sala Virtual — é restrito às unidades jurisdicionadas.

*Esse sistema abrange mais de 60 serviços, porém serão implementados apenas em função da necessidade.*

## Fase 1:

Implementação dos serviços 

- Token (obter, iniciar a transferência, finalizar a tranferência, cancelar a tranferência e obter situação do token)
- Servidor (enviar e listar)
- SituacaoServidorFolhaPagamento (enviar e listar)
- ComponentesFolhaPagamento (enviar e listar)
- FolhaPagamento (enviar e listar)

## FAse 2:

A fase dois com a implementação de outros serviços, será feita apenas quando surgir essas necessidades.

## Install

Via Composer

``` bash
$ composer require nfephp-org/sped-esfinge
```

## Usage

Estes serviços são fornecidos todos pela classe Tools, sem a necessidade de utilização de outras classes.
Para instaciar a classe Tools é necessária a criação de um arquivo (ou string) de configuração no formato json, com a seguinte estrutura:

**config.json**
```json
{
    "tpAmb": 2,
    "username": "fulano",
    "password": "senha",
    "codigoUnidadeGestora": "12345",
    "pathFiles": "\/var\/esfinge",
    "aProxyConf": {
        "proxyIp": "",
        "proxyPort": "",
        "proxyUser": "",
        "proxyPass": ""
    }
}
```
> NOTA: o pathFiles é um diretório, com permissões de acesso, onde serão gravados os LOGS da comunicação SOAP, para posterior analise e verificação de falhas e correção de BUGS, e também permite que em caso de falha na gravação das informações em uma base de dados essas informações possam ser recuperadas.

> IMPORTANTE: periódicamente esses arquivos devem ser eliminados para evitar o excesso de arquivos no espaço de disco.  

Para instanciar a classe Tools:

```php

use NFePHP\Esfinge\Tools;

$tools = new Tools('../config/config.json');

```

## Change log

Acompanhe o [CHANGELOG](CHANGELOG.md) para maiores informações sobre as alterações recentes.

## Testing

``` bash
$ composer test
```

## Contributing

Para contribuir por favor observe o [CONTRIBUTING](CONTRIBUTING.md) e o  [Código de Conduta](CONDUCT.md) parea detalhes.

## Security

Caso você encontre algum problema relativo a segurança, por favor envie um email diretamente aos mantenedores do pacote ao invés de abrir um ISSUE.

## Credits

- Rodrigo Traleski <rodrigo@actuary.com.br>
- Luiz Eduardo Godoy Bueno <luizeduardogodoy@gmail.com>
- Roberto L. Machado <linux.rlm@gmail.com>

O desenvolvimento desse pacote somente foi possivel devido a contribuição e colaboração da 
[ACTUARY Ltda](http://www.actuary.com.br/v2/informatica/index.php) 

## License

Este patote está diponibilizado sob LGPLv3, GPLv3 ou MIT License (MIT). Leia  [Arquivo de Licença](LICENSE.md) para maiores informações.

[ico-stars]: https://img.shields.io/github/stars/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nfephp-org/sped-esfinge/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/nfephp-org/sped-esfinge.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nfephp-org/sped-esfinge
[link-travis]: https://travis-ci.org/nfephp-org/sped-esfinge
[link-scrutinizer]: https://scrutinizer-ci.com/g/nfephp-org/sped-esfinge/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nfephp-org/sped-esfinge
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-esfinge
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/nfephp-org/sped-esfinge/issues
[link-forks]: https://github.com/nfephp-org/sped-esfinge/network
[link-stars]: https://github.com/nfephp-org/sped-esfinge/stargazers

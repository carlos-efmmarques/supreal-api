# Introduction

API interna para comunicação entre sistemas da empresa

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>

    Esta documentação fornece todas as informações necessárias para trabalhar com nossa API interna.

    ## Autenticação
    Todas as requisições para a API devem incluir um token Bearer no header Authorization:
    ```
    Authorization: Bearer SEU_TOKEN_AQUI
    ```

    ## Formato de Resposta
    Todas as respostas seguem o padrão:
    ```json
    {
        "success": true|false,
        "message": "Mensagem descritiva",
        "data": {}
    }
    ```

    ## Rate Limiting
    Por padrão, cada token tem um limite de 60 requisições por minuto.

    <aside>Você verá exemplos de código em diferentes linguagens de programação na área escura à direita (ou como parte do conteúdo no mobile).
    Você pode alternar a linguagem usando as abas no canto superior direito.</aside>


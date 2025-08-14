<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Supreal API - Documentação</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.3.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.3.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-tokens">
                                <a href="#endpoints-GETapi-tokens">GET api/tokens</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-tokens">
                                <a href="#endpoints-POSTapi-tokens">POST api/tokens</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-tokens--id-">
                                <a href="#endpoints-GETapi-tokens--id-">GET api/tokens/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-tokens--id-">
                                <a href="#endpoints-PUTapi-tokens--id-">PUT api/tokens/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-tokens--id-">
                                <a href="#endpoints-DELETEapi-tokens--id-">DELETE api/tokens/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-tokens--id--revoke">
                                <a href="#endpoints-POSTapi-tokens--id--revoke">POST api/tokens/{id}/revoke</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-tokens--id--activate">
                                <a href="#endpoints-POSTapi-tokens--id--activate">POST api/tokens/{id}/activate</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-example" class="tocify-header">
                <li class="tocify-item level-1" data-unique="example">
                    <a href="#example">Example</a>
                </li>
                                    <ul id="tocify-subheader-example" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="example-GETapi-health">
                                <a href="#example-GETapi-health">Endpoint de teste de saúde da API</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="example-GETapi-v1-examples">
                                <a href="#example-GETapi-v1-examples">Lista todos os exemplos</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="example-POSTapi-v1-examples">
                                <a href="#example-POSTapi-v1-examples">Cria um novo exemplo</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="example-GETapi-v1-examples--id-">
                                <a href="#example-GETapi-v1-examples--id-">Exibe um exemplo específico</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="example-PUTapi-v1-examples--id-">
                                <a href="#example-PUTapi-v1-examples--id-">Atualiza um exemplo</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="example-DELETEapi-v1-examples--id-">
                                <a href="#example-DELETEapi-v1-examples--id-">Remove um exemplo</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-site-mercado" class="tocify-header">
                <li class="tocify-item level-1" data-unique="site-mercado">
                    <a href="#site-mercado">Site Mercado</a>
                </li>
                                    <ul id="tocify-subheader-site-mercado" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="site-mercado-POSTapi-v1-site-mercado-pedidos">
                                <a href="#site-mercado-POSTapi-v1-site-mercado-pedidos">Insere um pedido no ERP Oracle usando a procedure sp_inserePedidoSitemercado</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="site-mercado-POSTapi-v1-site-mercado-itens">
                                <a href="#site-mercado-POSTapi-v1-site-mercado-itens">Insere itens de um pedido no ERP Oracle usando a procedure sp_insereItensSitemercado</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: August 14, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>API interna para comunicação entre sistemas da empresa</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>Esta documentação fornece todas as informações necessárias para trabalhar com nossa API interna.

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

&lt;aside&gt;Você verá exemplos de código em diferentes linguagens de programação na área escura à direita (ou como parte do conteúdo no mobile).
Você pode alternar a linguagem usando as abas no canto superior direito.&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-tokens">GET api/tokens</h2>

<p>
</p>



<span id="example-requests-GETapi-tokens">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/tokens" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tokens">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-ratelimit-limit: 60
x-ratelimit-remaining: 59
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Tokens listados com sucesso&quot;,
    &quot;data&quot;: {
        &quot;items&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Token de Teste&quot;,
                &quot;abilities&quot;: [
                    &quot;*&quot;
                ],
                &quot;last_used_at&quot;: &quot;2025-08-14T20:56:22.000000Z&quot;,
                &quot;expires_at&quot;: null,
                &quot;is_active&quot;: true,
                &quot;created_at&quot;: &quot;2025-08-14T20:56:16.000000Z&quot;
            }
        ],
        &quot;pagination&quot;: {
            &quot;total&quot;: 1,
            &quot;per_page&quot;: 20,
            &quot;current_page&quot;: 1,
            &quot;last_page&quot;: 1,
            &quot;from&quot;: 1,
            &quot;to&quot;: 1,
            &quot;next_page_url&quot;: null,
            &quot;prev_page_url&quot;: null
        }
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tokens" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tokens"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tokens"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tokens" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tokens">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tokens" data-method="GET"
      data-path="api/tokens"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tokens', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tokens"
                    onclick="tryItOut('GETapi-tokens');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tokens"
                    onclick="cancelTryOut('GETapi-tokens');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tokens"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tokens</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tokens"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tokens"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-tokens">POST api/tokens</h2>

<p>
</p>



<span id="example-requests-POSTapi-tokens">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/tokens" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"abilities\": [
        \"architecto\"
    ],
    \"expires_at\": \"2051-09-08\",
    \"ip_restriction\": \"1.102.226.211\",
    \"rate_limit\": 7
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "abilities": [
        "architecto"
    ],
    "expires_at": "2051-09-08",
    "ip_restriction": "1.102.226.211",
    "rate_limit": 7
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-tokens">
</span>
<span id="execution-results-POSTapi-tokens" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-tokens"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-tokens"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-tokens" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-tokens">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-tokens" data-method="POST"
      data-path="api/tokens"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-tokens', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-tokens"
                    onclick="tryItOut('POSTapi-tokens');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-tokens"
                    onclick="cancelTryOut('POSTapi-tokens');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-tokens"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/tokens</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-tokens"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-tokens"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-tokens"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>abilities</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="abilities[0]"                data-endpoint="POSTapi-tokens"
               data-component="body">
        <input type="text" style="display: none"
               name="abilities[1]"                data-endpoint="POSTapi-tokens"
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expires_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="expires_at"                data-endpoint="POSTapi-tokens"
               value="2051-09-08"
               data-component="body">
    <br>
<p>Must be a valid date. Must be a date after <code>now</code>. Example: <code>2051-09-08</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ip_restriction</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="ip_restriction"                data-endpoint="POSTapi-tokens"
               value="1.102.226.211"
               data-component="body">
    <br>
<p>Must be a valid IP address. Example: <code>1.102.226.211</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rate_limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="rate_limit"                data-endpoint="POSTapi-tokens"
               value="7"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 1000. Example: <code>7</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metadata</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="metadata"                data-endpoint="POSTapi-tokens"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="endpoints-GETapi-tokens--id-">GET api/tokens/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-tokens--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/tokens/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tokens--id-">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-ratelimit-limit: 60
x-ratelimit-remaining: 59
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Token n&atilde;o encontrado&quot;,
    &quot;data&quot;: null
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tokens--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tokens--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tokens--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tokens--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tokens--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tokens--id-" data-method="GET"
      data-path="api/tokens/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tokens--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tokens--id-"
                    onclick="tryItOut('GETapi-tokens--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tokens--id-"
                    onclick="cancelTryOut('GETapi-tokens--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tokens--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tokens/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tokens--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tokens--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-tokens--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the token. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-tokens--id-">PUT api/tokens/{id}</h2>

<p>
</p>



<span id="example-requests-PUTapi-tokens--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/tokens/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"expires_at\": \"2051-09-08\",
    \"is_active\": false,
    \"ip_restriction\": \"1.102.226.211\",
    \"rate_limit\": 7
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "expires_at": "2051-09-08",
    "is_active": false,
    "ip_restriction": "1.102.226.211",
    "rate_limit": 7
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-tokens--id-">
</span>
<span id="execution-results-PUTapi-tokens--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-tokens--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-tokens--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-tokens--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-tokens--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-tokens--id-" data-method="PUT"
      data-path="api/tokens/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-tokens--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-tokens--id-"
                    onclick="tryItOut('PUTapi-tokens--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-tokens--id-"
                    onclick="cancelTryOut('PUTapi-tokens--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-tokens--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/tokens/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-tokens--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-tokens--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-tokens--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the token. Example: <code>architecto</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-tokens--id-"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>abilities</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="abilities"                data-endpoint="PUTapi-tokens--id-"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expires_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="expires_at"                data-endpoint="PUTapi-tokens--id-"
               value="2051-09-08"
               data-component="body">
    <br>
<p>Must be a valid date. Must be a date after <code>now</code>. Example: <code>2051-09-08</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_active</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="PUTapi-tokens--id-" style="display: none">
            <input type="radio" name="is_active"
                   value="true"
                   data-endpoint="PUTapi-tokens--id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-tokens--id-" style="display: none">
            <input type="radio" name="is_active"
                   value="false"
                   data-endpoint="PUTapi-tokens--id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ip_restriction</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="ip_restriction"                data-endpoint="PUTapi-tokens--id-"
               value="1.102.226.211"
               data-component="body">
    <br>
<p>Must be a valid IP address. Example: <code>1.102.226.211</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rate_limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="rate_limit"                data-endpoint="PUTapi-tokens--id-"
               value="7"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 1000. Example: <code>7</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metadata</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="metadata"                data-endpoint="PUTapi-tokens--id-"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-tokens--id-">DELETE api/tokens/{id}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-tokens--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/tokens/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-tokens--id-">
</span>
<span id="execution-results-DELETEapi-tokens--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-tokens--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-tokens--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-tokens--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-tokens--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-tokens--id-" data-method="DELETE"
      data-path="api/tokens/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-tokens--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-tokens--id-"
                    onclick="tryItOut('DELETEapi-tokens--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-tokens--id-"
                    onclick="cancelTryOut('DELETEapi-tokens--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-tokens--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/tokens/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-tokens--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-tokens--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-tokens--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the token. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-tokens--id--revoke">POST api/tokens/{id}/revoke</h2>

<p>
</p>



<span id="example-requests-POSTapi-tokens--id--revoke">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/tokens/architecto/revoke" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens/architecto/revoke"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-tokens--id--revoke">
</span>
<span id="execution-results-POSTapi-tokens--id--revoke" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-tokens--id--revoke"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-tokens--id--revoke"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-tokens--id--revoke" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-tokens--id--revoke">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-tokens--id--revoke" data-method="POST"
      data-path="api/tokens/{id}/revoke"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-tokens--id--revoke', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-tokens--id--revoke"
                    onclick="tryItOut('POSTapi-tokens--id--revoke');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-tokens--id--revoke"
                    onclick="cancelTryOut('POSTapi-tokens--id--revoke');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-tokens--id--revoke"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/tokens/{id}/revoke</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-tokens--id--revoke"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-tokens--id--revoke"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="POSTapi-tokens--id--revoke"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the token. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-tokens--id--activate">POST api/tokens/{id}/activate</h2>

<p>
</p>



<span id="example-requests-POSTapi-tokens--id--activate">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/tokens/architecto/activate" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tokens/architecto/activate"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-tokens--id--activate">
</span>
<span id="execution-results-POSTapi-tokens--id--activate" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-tokens--id--activate"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-tokens--id--activate"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-tokens--id--activate" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-tokens--id--activate">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-tokens--id--activate" data-method="POST"
      data-path="api/tokens/{id}/activate"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-tokens--id--activate', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-tokens--id--activate"
                    onclick="tryItOut('POSTapi-tokens--id--activate');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-tokens--id--activate"
                    onclick="cancelTryOut('POSTapi-tokens--id--activate');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-tokens--id--activate"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/tokens/{id}/activate</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-tokens--id--activate"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-tokens--id--activate"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="POSTapi-tokens--id--activate"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the token. Example: <code>architecto</code></p>
            </div>
                    </form>

                <h1 id="example">Example</h1>

    

                                <h2 id="example-GETapi-health">Endpoint de teste de saúde da API</h2>

<p>
</p>



<span id="example-requests-GETapi-health">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/health" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/health"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-health">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;API est&aacute; funcionando&quot;,
    &quot;data&quot;: {
        &quot;status&quot;: &quot;healthy&quot;,
        &quot;timestamp&quot;: &quot;2025-01-14 10:00:00&quot;,
        &quot;version&quot;: &quot;1.0.0&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-health" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-health"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-health"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-health" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-health">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-health" data-method="GET"
      data-path="api/health"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-health', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-health"
                    onclick="tryItOut('GETapi-health');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-health"
                    onclick="cancelTryOut('GETapi-health');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-health"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/health</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-health"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-health"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="example-GETapi-v1-examples">Lista todos os exemplos</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-examples">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/examples?search=Item&amp;status=active" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/examples"
);

const params = {
    "search": "Item",
    "status": "active",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-examples">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Dados recuperados com sucesso&quot;,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Item 1&quot;,
            &quot;description&quot;: &quot;Descri&ccedil;&atilde;o do item 1&quot;,
            &quot;status&quot;: &quot;active&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-examples" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-examples"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-examples"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-examples" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-examples">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-examples" data-method="GET"
      data-path="api/v1/examples"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-examples', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-examples"
                    onclick="tryItOut('GETapi-v1-examples');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-examples"
                    onclick="cancelTryOut('GETapi-v1-examples');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-examples"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/examples</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-examples"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-examples"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-v1-examples"
               value="Item"
               data-component="query">
    <br>
<p>Filtrar por nome. Example: <code>Item</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-v1-examples"
               value="active"
               data-component="query">
    <br>
<p>Filtrar por status (active/inactive). Example: <code>active</code></p>
            </div>
                </form>

                    <h2 id="example-POSTapi-v1-examples">Cria um novo exemplo</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-examples">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/v1/examples" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Novo Item\",
    \"description\": \"Uma descrição detalhada\",
    \"status\": \"active\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/examples"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Novo Item",
    "description": "Uma descrição detalhada",
    "status": "active"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-examples">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Item criado com sucesso&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 4,
        &quot;name&quot;: &quot;Novo Item&quot;,
        &quot;description&quot;: &quot;Uma descri&ccedil;&atilde;o detalhada&quot;,
        &quot;status&quot;: &quot;active&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-examples" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-examples"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-examples"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-examples" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-examples">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-examples" data-method="POST"
      data-path="api/v1/examples"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-examples', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-examples"
                    onclick="tryItOut('POSTapi-v1-examples');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-examples"
                    onclick="cancelTryOut('POSTapi-v1-examples');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-examples"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/examples</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-examples"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-examples"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-examples"
               value="Novo Item"
               data-component="body">
    <br>
<p>Nome do item. Example: <code>Novo Item</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-v1-examples"
               value="Uma descrição detalhada"
               data-component="body">
    <br>
<p>Descrição do item. Example: <code>Uma descrição detalhada</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-v1-examples"
               value="active"
               data-component="body">
    <br>
<p>Status do item (active/inactive). Example: <code>active</code></p>
        </div>
        </form>

                    <h2 id="example-GETapi-v1-examples--id-">Exibe um exemplo específico</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-examples--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/v1/examples/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/examples/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-examples--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Item recuperado com sucesso&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Item 1&quot;,
        &quot;description&quot;: &quot;Descri&ccedil;&atilde;o do item 1&quot;,
        &quot;status&quot;: &quot;active&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Item n&atilde;o encontrado&quot;,
    &quot;data&quot;: null
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-examples--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-examples--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-examples--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-examples--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-examples--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-examples--id-" data-method="GET"
      data-path="api/v1/examples/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-examples--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-examples--id-"
                    onclick="tryItOut('GETapi-v1-examples--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-examples--id-"
                    onclick="cancelTryOut('GETapi-v1-examples--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-examples--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/examples/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-examples--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-examples--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-examples--id-"
               value="1"
               data-component="url">
    <br>
<p>ID do item. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="example-PUTapi-v1-examples--id-">Atualiza um exemplo</h2>

<p>
</p>



<span id="example-requests-PUTapi-v1-examples--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/v1/examples/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Item Atualizado\",
    \"description\": \"Descrição atualizada\",
    \"status\": \"inactive\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/examples/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Item Atualizado",
    "description": "Descrição atualizada",
    "status": "inactive"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-examples--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Item atualizado com sucesso&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Item Atualizado&quot;,
        &quot;description&quot;: &quot;Descri&ccedil;&atilde;o atualizada&quot;,
        &quot;status&quot;: &quot;inactive&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Item n&atilde;o encontrado&quot;,
    &quot;data&quot;: null
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-examples--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-examples--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-examples--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-examples--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-examples--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-examples--id-" data-method="PUT"
      data-path="api/v1/examples/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-examples--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-examples--id-"
                    onclick="tryItOut('PUTapi-v1-examples--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-examples--id-"
                    onclick="cancelTryOut('PUTapi-v1-examples--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-examples--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/examples/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-examples--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-examples--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-examples--id-"
               value="1"
               data-component="url">
    <br>
<p>ID do item. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-examples--id-"
               value="Item Atualizado"
               data-component="body">
    <br>
<p>Nome do item. Example: <code>Item Atualizado</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PUTapi-v1-examples--id-"
               value="Descrição atualizada"
               data-component="body">
    <br>
<p>Descrição do item. Example: <code>Descrição atualizada</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-v1-examples--id-"
               value="inactive"
               data-component="body">
    <br>
<p>Status do item (active/inactive). Example: <code>inactive</code></p>
        </div>
        </form>

                    <h2 id="example-DELETEapi-v1-examples--id-">Remove um exemplo</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-examples--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/v1/examples/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/examples/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-examples--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Item removido com sucesso&quot;,
    &quot;data&quot;: null
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Item n&atilde;o encontrado&quot;,
    &quot;data&quot;: null
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-examples--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-examples--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-examples--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-examples--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-examples--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-examples--id-" data-method="DELETE"
      data-path="api/v1/examples/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-examples--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-examples--id-"
                    onclick="tryItOut('DELETEapi-v1-examples--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-examples--id-"
                    onclick="cancelTryOut('DELETEapi-v1-examples--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-examples--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/examples/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-examples--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-examples--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-examples--id-"
               value="1"
               data-component="url">
    <br>
<p>ID do item. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="site-mercado">Site Mercado</h1>

    

                                <h2 id="site-mercado-POSTapi-v1-site-mercado-pedidos">Insere um pedido no ERP Oracle usando a procedure sp_inserePedidoSitemercado</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-site-mercado-pedidos">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/v1/site-mercado/pedidos" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"nropedidoafv\": \"PED123456\",
    \"nroempresa\": 1,
    \"nrocgccpf\": \"12345678901\",
    \"digcgccpf\": \"23\",
    \"nomerazao\": \"João da Silva\",
    \"fantasia\": \"João da Silva\",
    \"fisicajuridica\": \"F\",
    \"sexo\": \"M\",
    \"cidade\": \"São Paulo\",
    \"uf\": \"SP\",
    \"bairro\": \"Centro\",
    \"logradouro\": \"Rua das Flores\",
    \"nrologradouro\": \"123\",
    \"cmpltologradouro\": \"Apto 101\",
    \"cep\": \"01234567\",
    \"foneddd1\": \"11\",
    \"fonenro1\": \"987654321\",
    \"foneddd2\": \"11\",
    \"fonenro2\": \"123456789\",
    \"inscricaorg\": \"123456789\",
    \"dtanascfund\": \"1990-01-01\",
    \"email\": \"joao@email.com\",
    \"emailnfe\": \"joao@email.com\",
    \"indentregaretira\": \"E\",
    \"dtapedidoafv\": \"2025-01-14\",
    \"vlrtotfrete\": 15.5,
    \"valor\": 150.75,
    \"nroformapagto\": 1,
    \"usuinclusao\": \"API_SITEMERCADO\",
    \"nroparcelas\": 1,
    \"codoperadoracartao\": 1,
    \"nrocartao\": \"****1234\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/site-mercado/pedidos"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nropedidoafv": "PED123456",
    "nroempresa": 1,
    "nrocgccpf": "12345678901",
    "digcgccpf": "23",
    "nomerazao": "João da Silva",
    "fantasia": "João da Silva",
    "fisicajuridica": "F",
    "sexo": "M",
    "cidade": "São Paulo",
    "uf": "SP",
    "bairro": "Centro",
    "logradouro": "Rua das Flores",
    "nrologradouro": "123",
    "cmpltologradouro": "Apto 101",
    "cep": "01234567",
    "foneddd1": "11",
    "fonenro1": "987654321",
    "foneddd2": "11",
    "fonenro2": "123456789",
    "inscricaorg": "123456789",
    "dtanascfund": "1990-01-01",
    "email": "joao@email.com",
    "emailnfe": "joao@email.com",
    "indentregaretira": "E",
    "dtapedidoafv": "2025-01-14",
    "vlrtotfrete": 15.5,
    "valor": 150.75,
    "nroformapagto": 1,
    "usuinclusao": "API_SITEMERCADO",
    "nroparcelas": 1,
    "codoperadoracartao": 1,
    "nrocartao": "****1234"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-site-mercado-pedidos">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Pedido inserido com sucesso no ERP&quot;,
    &quot;data&quot;: {
        &quot;nropedidoafv&quot;: &quot;PED123456&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{&quot;success&quot;: false, &quot;message&quot;: &quot;Erro de valida&ccedil;&atilde;o&quot;, &quot;data&quot;: {...}}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Erro ao inserir pedido no ERP&quot;,
    &quot;data&quot;: {
        &quot;error&quot;: &quot;...&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-site-mercado-pedidos" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-site-mercado-pedidos"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-site-mercado-pedidos"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-site-mercado-pedidos" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-site-mercado-pedidos">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-site-mercado-pedidos" data-method="POST"
      data-path="api/v1/site-mercado/pedidos"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-site-mercado-pedidos', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-site-mercado-pedidos"
                    onclick="tryItOut('POSTapi-v1-site-mercado-pedidos');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-site-mercado-pedidos"
                    onclick="cancelTryOut('POSTapi-v1-site-mercado-pedidos');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-site-mercado-pedidos"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/site-mercado/pedidos</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nropedidoafv</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nropedidoafv"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="PED123456"
               data-component="body">
    <br>
<p>Número do pedido AFV. Example: <code>PED123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nroempresa</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="nroempresa"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="1"
               data-component="body">
    <br>
<p>Número da empresa. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nrocgccpf</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nrocgccpf"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="12345678901"
               data-component="body">
    <br>
<p>Número do CPF/CNPJ (sem dígito). Example: <code>12345678901</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>digcgccpf</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="digcgccpf"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="23"
               data-component="body">
    <br>
<p>Dígito verificador do CPF/CNPJ. Example: <code>23</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nomerazao</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nomerazao"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="João da Silva"
               data-component="body">
    <br>
<p>Nome ou razão social. Example: <code>João da Silva</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fantasia</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="fantasia"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="João da Silva"
               data-component="body">
    <br>
<p>Nome fantasia. Example: <code>João da Silva</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fisicajuridica</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fisicajuridica"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="F"
               data-component="body">
    <br>
<p>Tipo de pessoa (F=Física, J=Jurídica). Example: <code>F</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sexo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="sexo"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="M"
               data-component="body">
    <br>
<p>Sexo (M/F). Example: <code>M</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cidade</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cidade"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="São Paulo"
               data-component="body">
    <br>
<p>Cidade. Example: <code>São Paulo</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>uf</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uf"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="SP"
               data-component="body">
    <br>
<p>UF. Example: <code>SP</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>bairro</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="bairro"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="Centro"
               data-component="body">
    <br>
<p>Bairro. Example: <code>Centro</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>logradouro</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="logradouro"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="Rua das Flores"
               data-component="body">
    <br>
<p>Logradouro. Example: <code>Rua das Flores</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nrologradouro</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nrologradouro"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="123"
               data-component="body">
    <br>
<p>Número do logradouro. Example: <code>123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cmpltologradouro</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="cmpltologradouro"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="Apto 101"
               data-component="body">
    <br>
<p>Complemento. Example: <code>Apto 101</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cep</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cep"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="01234567"
               data-component="body">
    <br>
<p>CEP. Example: <code>01234567</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>foneddd1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="foneddd1"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="11"
               data-component="body">
    <br>
<p>DDD do telefone 1. Example: <code>11</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fonenro1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="fonenro1"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="987654321"
               data-component="body">
    <br>
<p>Número do telefone 1. Example: <code>987654321</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>foneddd2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="foneddd2"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="11"
               data-component="body">
    <br>
<p>DDD do telefone 2. Example: <code>11</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fonenro2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="fonenro2"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="123456789"
               data-component="body">
    <br>
<p>Número do telefone 2. Example: <code>123456789</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>inscricaorg</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="inscricaorg"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="123456789"
               data-component="body">
    <br>
<p>Inscrição RG. Example: <code>123456789</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dtanascfund</code></b>&nbsp;&nbsp;
<small>date</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="dtanascfund"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="1990-01-01"
               data-component="body">
    <br>
<p>Data de nascimento/fundação. Example: <code>1990-01-01</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="joao@email.com"
               data-component="body">
    <br>
<p>Email. Example: <code>joao@email.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>emailnfe</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="emailnfe"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="joao@email.com"
               data-component="body">
    <br>
<p>Email para NFe. Example: <code>joao@email.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>indentregaretira</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="indentregaretira"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="E"
               data-component="body">
    <br>
<p>Indicador entrega/retirada (E=Entrega, R=Retirada). Example: <code>E</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dtapedidoafv</code></b>&nbsp;&nbsp;
<small>date</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="dtapedidoafv"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="2025-01-14"
               data-component="body">
    <br>
<p>Data do pedido. Example: <code>2025-01-14</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>vlrtotfrete</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="vlrtotfrete"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="15.5"
               data-component="body">
    <br>
<p>Valor total do frete. Example: <code>15.5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>valor</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="valor"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="150.75"
               data-component="body">
    <br>
<p>Valor total do pedido. Example: <code>150.75</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nroformapagto</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="nroformapagto"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="1"
               data-component="body">
    <br>
<p>Forma de pagamento. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>usuinclusao</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="usuinclusao"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="API_SITEMERCADO"
               data-component="body">
    <br>
<p>Usuário de inclusão. Example: <code>API_SITEMERCADO</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nroparcelas</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="nroparcelas"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="1"
               data-component="body">
    <br>
<p>Número de parcelas. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>codoperadoracartao</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="codoperadoracartao"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="1"
               data-component="body">
    <br>
<p>Código da operadora do cartão. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nrocartao</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="nrocartao"                data-endpoint="POSTapi-v1-site-mercado-pedidos"
               value="****1234"
               data-component="body">
    <br>
<p>Número do cartão. Example: <code>****1234</code></p>
        </div>
        </form>

                    <h2 id="site-mercado-POSTapi-v1-site-mercado-itens">Insere itens de um pedido no ERP Oracle usando a procedure sp_insereItensSitemercado</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-site-mercado-itens">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/v1/site-mercado/itens" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"nropedidoafv\": \"PED123456\",
    \"seqpedvendaitem\": 1,
    \"codacesso\": \"COD12345\",
    \"seqproduto\": 12345,
    \"qtdpedida\": 2.5,
    \"qtdembalagem\": 1,
    \"vlrembtabpreco\": 15.9,
    \"vlrembinformado\": 15.9
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/v1/site-mercado/itens"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nropedidoafv": "PED123456",
    "seqpedvendaitem": 1,
    "codacesso": "COD12345",
    "seqproduto": 12345,
    "qtdpedida": 2.5,
    "qtdembalagem": 1,
    "vlrembtabpreco": 15.9,
    "vlrembinformado": 15.9
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-site-mercado-itens">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Item inserido com sucesso no ERP&quot;,
    &quot;data&quot;: {
        &quot;nropedidoafv&quot;: &quot;PED123456&quot;,
        &quot;seqpedvendaitem&quot;: 1
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{&quot;success&quot;: false, &quot;message&quot;: &quot;Erro de valida&ccedil;&atilde;o&quot;, &quot;data&quot;: {...}}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Erro ao inserir item no ERP&quot;,
    &quot;data&quot;: {
        &quot;error&quot;: &quot;...&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-site-mercado-itens" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-site-mercado-itens"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-site-mercado-itens"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-site-mercado-itens" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-site-mercado-itens">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-site-mercado-itens" data-method="POST"
      data-path="api/v1/site-mercado/itens"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-site-mercado-itens', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-site-mercado-itens"
                    onclick="tryItOut('POSTapi-v1-site-mercado-itens');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-site-mercado-itens"
                    onclick="cancelTryOut('POSTapi-v1-site-mercado-itens');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-site-mercado-itens"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/site-mercado/itens</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nropedidoafv</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nropedidoafv"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="PED123456"
               data-component="body">
    <br>
<p>Número do pedido AFV (usado como referência). Example: <code>PED123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>seqpedvendaitem</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="seqpedvendaitem"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="1"
               data-component="body">
    <br>
<p>Sequência do item no pedido. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>codacesso</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="codacesso"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="COD12345"
               data-component="body">
    <br>
<p>Código de acesso do produto. Example: <code>COD12345</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>seqproduto</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="seqproduto"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="12345"
               data-component="body">
    <br>
<p>Sequência do produto. Example: <code>12345</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>qtdpedida</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="qtdpedida"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="2.5"
               data-component="body">
    <br>
<p>Quantidade pedida. Example: <code>2.5</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>qtdembalagem</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="qtdembalagem"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="1"
               data-component="body">
    <br>
<p>Quantidade da embalagem. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>vlrembtabpreco</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="vlrembtabpreco"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="15.9"
               data-component="body">
    <br>
<p>Valor da embalagem tabela preço. Example: <code>15.9</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>vlrembinformado</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="vlrembinformado"                data-endpoint="POSTapi-v1-site-mercado-itens"
               value="15.9"
               data-component="body">
    <br>
<p>Valor da embalagem informado. Example: <code>15.9</code></p>
        </div>
        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>

<?php

declare(strict_types = 1);

return [
    /*
    |--------------------------------------------------------------------------
    | Prefixo
    |--------------------------------------------------------------------------
    |
    | Prefixo das tags dos componentes. O separador faz parte do valor:
    |
    |   'ui.'  →  <x-ui.button>
    |   'gn-'  →  <x-gn-button>
    |
    | Os componentes se referenciam entre si pelo nome interno fixo
    | (<x-goognet-ui::button>), então trocar o prefixo não quebra nenhum deles.
    |
    */

    'prefix' => env('GOOGNET_UI_PREFIX', 'ui.'),

    /*
    |--------------------------------------------------------------------------
    | Segurança de URLs
    |--------------------------------------------------------------------------
    |
    | Todo href e src dos componentes passa por Goognet\Ui\Support\SafeUrl.
    | Esquema fora destas listas é descartado e o atributo não é emitido, o que
    | bloqueia `javascript:`, `vbscript:`, `data:text/html` e afins, inclusive
    | escritos com tab, quebra de linha ou maiúsculas no meio.
    |
    | Caminho relativo, âncora (#) e query (?) são sempre aceitos em links.
    | Iframe aceita apenas https absoluto, independentemente desta lista.
    |
    */

    'security' => [
        'link_schemes'  => ['http', 'https', 'mailto', 'tel'],
        'media_schemes' => ['http', 'https'],

        /*
         * Hosts que podem ser abertos num <iframe>. Um iframe mostra uma página inteira de
         * outro site dentro da sua; sem lista, um endereço vindo de um painel vira phishing
         * embutido no site do cliente. Lista vazia libera qualquer host https.
         */
        'frame_hosts' => [
            'www.google.com',
            'google.com',
            'maps.google.com',
            'www.openstreetmap.org',
            'openstreetmap.org',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localização
    |--------------------------------------------------------------------------
    |
    | Endereço de embed usado pelo <x-ui.map> quando nenhum `src` é passado.
    |
    */

    'location' => [
        'map' => env('GOOGNET_UI_MAP_SRC'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Empresa
    |--------------------------------------------------------------------------
    |
    | Lido pelo brand, footer e whatsapp. `logo` é um arquivo em
    | resources/images, um caminho, uma URL, ou vazio — vazio rende o nome
    | como letreiro.
    |
    */

    'company' => [
        'name'        => env('APP_NAME'),
        'description' => null,
        'logo'        => null,
        'phone'       => null,
        'mail'        => null,
    ],

    'whatsapp' => [
        'number'       => null,
        'message'      => 'Olá! Vim pelo site e gostaria de mais informações.',
        'country_code' => '55',
    ],

    /*
     * Só as redes preenchidas aparecem no footer.
     */
    'social' => [
        'instagram' => null,
        'facebook'  => null,
        'youtube'   => null,
        'linkedin'  => null,
        'twitter'   => null,
        'tiktok'    => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu
    |--------------------------------------------------------------------------
    |
    | Itens com `label` e `url` ou `route` (nome, ou [nome, parâmetros]).
    | `children` vira dropdown; `groups` vira megamenu. Âncora (#secao)
    | aponta para a home quando renderizada em outra página.
    |
    */

    'menu' => [],

    /*
    |--------------------------------------------------------------------------
    | Crédito e páginas legais
    |--------------------------------------------------------------------------
    */

    'agency' => [
        'name' => null,
        'url'  => null,
    ],

    'legal' => [
        'privacy_route' => 'privacy',
    ],

    /*
     * O cookie é gravado pelo navegador em texto puro, então o pacote o tira da
     * criptografia de cookies do Laravel. Mudando o nome aqui, a exceção acompanha.
     */
    'cookie_consent' => [
        'name' => 'cookie_consent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Catálogo
    |--------------------------------------------------------------------------
    |
    | Página com todos os componentes, exemplos e props. `enabled` null liga fora
    | de produção e desliga em produção. `vite` são as entradas do site que a
    | página carrega, para mostrar os componentes com o CSS e o JS reais.
    |
    */

    'catalogue' => [
        'enabled' => env('GOOGNET_UI_CATALOGUE'),
        'path'    => 'dev/components',
        'vite'    => ['resources/css/app.css', 'resources/js/app.js'],
    ],
];

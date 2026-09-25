<?php

declare(strict_types = 1);

/**
 * Todo texto que os componentes escrevem sozinhos.
 *
 * Um site traduz publicando este arquivo — `php artisan vendor:publish --tag=goognet-ui-lang`
 * — ou criando `lang/vendor/goognet-ui/{locale}/ui.php` com apenas as chaves que quiser
 * trocar. O idioma segue o `app.locale` do request, então um site multi-idioma não precisa
 * fazer nada além de chamar `App::setLocale()`.
 */
return [
    'alert' => [
        'dismiss' => 'Fechar aviso',
    ],

    'cookie' => [
        'accept'    => 'Aceitar',
        'essential' => 'cookies essenciais',
        'message'   => 'Usamos apenas :essential para o funcionamento do site. Ao continuar navegando, você concorda com a nossa política de privacidade.',
        'more'      => 'Saber mais',
    ],

    'footer' => [
        'credit'     => 'Desenvolvido por',
        'label'      => 'Rodapé',
        'navigation' => 'Navegação',
        'rights'     => 'Todos os direitos reservados.',
        'top'        => 'Voltar ao topo',
    ],

    'map' => [
        'title' => 'Mapa de localização',
    ],

    'menu' => [
        'close' => 'Fechar menu',
        'label' => 'Menu principal',
        'open'  => 'Abrir menu',
    ],

    'modal' => [
        'close' => 'Fechar',
    ],

    'pagination' => [
        'goto'     => 'Ir para a página :page',
        'label'    => 'Paginação',
        'next'     => 'Próxima página',
        'previous' => 'Página anterior',
        'summary'  => 'Mostrando :first–:last de :total',
    ],
];

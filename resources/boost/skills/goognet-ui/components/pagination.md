# Pagination — `<x-ui.pagination>`

Navegação entre páginas de um paginador do Laravel. Recebe o próprio `$paginator` e desenha o resumo, as páginas e as setas.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `paginator` | — |  |
| `size` | `null` | `sm`, `base`, `lg` |
| `simple` | `false` |  |
| `summary` | `null` |  |
| `rounded` | `null` |  |

## Exemplos

### Um paginador completo

```blade
<x-ui.pagination :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 300, 10, 7, ['path' => '/exemplo'])" />
```

### Tamanhos e raio

```blade
<x-ui.pagination
    size="sm"
    :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 90, 10, 4, ['path' => '/exemplo'])"
/>
<x-ui.pagination
    size="lg"
    rounded="full"
    :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 90, 10, 4, ['path' => '/exemplo'])"
/>
```

### Só anterior e próxima, sem resumo

```blade
<x-ui.pagination
    simple
    :summary="false"
    :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 300, 10, 7, ['path' => '/exemplo'])"
/>
```

## Notas

- O prop `paginator` é o objeto que o controller já devolve — `Model::query()->paginate()`, `simplePaginate()` ou `cursorPaginate()`. Nada de passar página e total soltos.
- Sem mais de uma página o componente não desenha nada, como o `links()` do Laravel. Uma barra de paginação com uma página só é ruído.
- As páginas numeradas dependem de o paginador saber o total. `simplePaginate()` e `cursorPaginate()` não sabem, então caem sozinhos em anterior/próxima — o `simple` força esse formato também num paginador completo.
- A janela de páginas e as reticências vêm do `UrlWindow` do próprio Laravel, então a régua é a mesma das views de paginação de fábrica. Quantas páginas aparecem de cada lado se ajusta no paginador, com `->onEachSide(2)`.
- Num celular a barra é só a página atual entre as duas setas; a partir de `sm` a janela inteira volta. O padrão do Laravel são até 15 células, que empilhavam em três linhas numa tela estreita. Os números escondidos continuam no HTML, como `hidden`, então um buscador acha todas as páginas do mesmo jeito.
- O resumo é a frase `Mostrando 61–70 de 300`. Desligue com `:summary="false"`; ele já não aparece quando o paginador não sabe o total.
- Cada célula tem a altura e a largura mínima dos tokens de controle (`--spacing-control-sm`), então o alvo de toque passa o mínimo do WCAG 2.2 e a linha não muda de largura entre a primeira página e as outras.
- Para o `$posts->links()` desenhar este componente sem mexer em nenhuma chamada, registre a view adaptadora no `AppServiceProvider`: `Paginator::defaultView('goognet-ui::pagination')`.
- `Ui::pagination()` define padrões e classes por parte: `base`, `summary`, `list`, `item`, `number`, `page`, `current`, `gap`, `arrow` e `disabled`.
- O texto — o resumo, o nome da navegação e os rótulos das setas — vem dos arquivos de idioma, na seção **Idiomas**, no topo.

{{--
    The adapter behind `Paginator::defaultView('goognet-ui::pagination')`: with it registered,
    `$posts->links()` draws this component and no call site changes. Laravel hands a plain view
    `$paginator`, and an anonymous component cannot be rendered as one — it needs `$attributes`.
--}}
<x-goognet-ui::pagination :paginator="$paginator" />

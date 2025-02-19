<div id="desktop-content" class="d-none d-md-block">
    @include('clients.list.desktop')
</div>
<div id="mobile-content" class="d-block d-md-none">
    @include('clients.list.mobile')
</div>
<div id="pagination" class="d-flex justify-content-center mt-4">
    {{ $clients->appends(request()->query())->links() }}
</div>

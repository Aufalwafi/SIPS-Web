<div class="header">
    <div class="header-left">
        <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
        <div class="instansi"><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>{{ $wilayahLabel ?? 'CDK Pusat' }}</div>
    </div>
    <div class="header-right">
        <span class="hi-user">Hi, <b>{{ Auth::user()->username }}</b></span>
        <span class="divider">|</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</div>

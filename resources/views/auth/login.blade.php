<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SI PS ONLINE Dinas Kehutanan Jawa Timur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --brand-green: #0f5132;
            --brand-green-hover: #0a3622;
            --brand-leaf: #198754;
            --dark: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg-page: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-page);
            padding: 24px 16px;
        }

        .login-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 860px;
            border-radius: 20px;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.15fr;
        }

        /* LEFT SIDE BANNER */
        .login-left {
            background: linear-gradient(135deg, rgba(15, 81, 50, 0.9) 0%, rgba(11, 36, 22, 0.95) 100%),
                        url('https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&q=80') center/cover no-repeat;
            color: #ffffff;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .left-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .left-top img {
            width: 42px;
            height: auto;
        }
        .left-top-text h3 {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.3px;
            line-height: 1.1;
        }
        .left-top-text p {
            font-size: 11px;
            color: #bbf7d0;
        }

        .left-middle h2 {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 12px;
        }
        .left-middle p {
            font-size: 13px;
            color: #e2e8f0;
            line-height: 1.6;
        }

        .left-bottom {
            font-size: 11px;
            color: #86efac;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* RIGHT SIDE FORM */
        .login-right {
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .right-header {
            margin-bottom: 24px;
        }
        .right-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }
        .right-header p {
            font-size: 13px;
            color: var(--muted);
        }

        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-box i.input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 14px;
        }
        .input-box input {
            width: 100%;
            height: 44px;
            padding: 0 40px 0 38px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            color: var(--dark);
            background: #ffffff;
            transition: all 0.2s;
        }
        .input-box input:focus {
            border-color: var(--brand-leaf);
            outline: none;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
        }
        .toggle-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
        }
        .toggle-btn:hover { color: var(--dark); }

        .btn-submit {
            width: 100%;
            height: 44px;
            background: var(--brand-green);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(15, 81, 50, 0.2);
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: var(--brand-green-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 81, 50, 0.25);
        }

        .alert-box {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

        .right-footer {
            margin-top: 24px;
            text-align: center;
        }
        .right-footer a {
            color: var(--muted);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .right-footer a:hover { color: var(--brand-green); }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }
        .mobile-brand img {
            width: 38px;
            height: auto;
        }
        .mobile-brand h3 {
            font-size: 15px;
            font-weight: 800;
            color: var(--brand-green);
            line-height: 1.15;
        }
        .mobile-brand p {
            font-size: 10.5px;
            color: var(--muted);
            font-weight: 500;
        }

        @media(max-width: 768px) {
            .login-wrapper { grid-template-columns: 1fr; max-width: 420px; }
            .login-left { display: none; }
            .login-right { padding: 28px 20px; }
            .mobile-brand { display: flex; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <!-- LEFT HERO -->
        <div class="login-left">
            <div class="left-top">
                <img src="{{ asset('assets/logo-jatim.png') }}"
                     onerror="if(!this.dataset.retry){this.dataset.retry='1';this.src='{{ url('/assets/logo-jatim.png') }}';}else if(this.dataset.retry==='1'){this.dataset.retry='2';this.src='/assets/logo-jatim.png';}else if(this.dataset.retry==='2'){this.dataset.retry='3';this.src='/public/assets/logo-jatim.png';}"
                     alt="Logo Jatim">
                <div class="left-top-text">
                    <h3>SI PS ONLINE</h3>
                    <p>Dinas Kehutanan Provinsi Jawa Timur</p>
                </div>
            </div>
            <div class="left-middle">
                <h2>Hutan Lestari,<br>Masyarakat Sejahtera</h2>
                <p>Portal digitalisasi Perhutanan Sosial terpadu untuk percepatan pendataan, persetujuan rencana kerja, dan pengawasan ekonomi komoditas.</p>
            </div>
            <div class="left-bottom">
                <i class="fas fa-shield-halved"></i> Sistem Pelayanan Resmi Pemerintah Provinsi Jawa Timur
            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="login-right">
            <div class="mobile-brand">
                <img src="{{ asset('assets/logo-jatim.png') }}"
                     onerror="if(!this.dataset.retry){this.dataset.retry='1';this.src='{{ url('/assets/logo-jatim.png') }}';}else if(this.dataset.retry==='1'){this.dataset.retry='2';this.src='/assets/logo-jatim.png';}else if(this.dataset.retry==='2'){this.dataset.retry='3';this.src='/public/assets/logo-jatim.png';}"
                     alt="Logo Jatim">
                <div>
                    <h3>SI PS ONLINE</h3>
                    <p>Dinas Kehutanan Provinsi Jawa Timur</p>
                </div>
            </div>

            <div class="right-header">
                <h2>Masuk ke Akun</h2>
                <p>Gunakan akun terdaftar Anda untuk mengakses sistem</p>
            </div>

            @if(session('error'))
                <div class="alert-box alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="fas fa-circle-check"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="form-group">
                    <label for="username">Username Pengguna</label>
                    <div class="input-box">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="input-box">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="Masukkan kata sandi">
                        <button type="button" class="toggle-btn" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-arrow-right-to-bracket"></i> Masuk Sekarang
                </button>
            </form>

            <div class="right-footer">
                <a href="{{ route('landing') }}">
                    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        var pwd = document.getElementById('password');
        var icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
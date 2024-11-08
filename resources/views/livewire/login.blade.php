<div>
    <div class="header">
        <div class="logo">
            <a href="/">
                <img src="/img/logo.png" alt="">
            </a>
        </div>
    </div>

    <div class="login_body">
        <div class="login_box">
            <h2>Entrar</h2>
            <form action="/login" method="POST">
                @csrf

                <div class="input_box">
                    <input required type="email" name="email" placeholder="Email">
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input required type="password" name="password" placeholder="Senha">
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <button class="submit" type="submit">Entrar</button>
                </div>
            </form>

            <div class="support">
                <div class="remember">
                    <span><input type="checkbox" style="margin: 0; padding: 0; height: 13px;"></span>
                    <span>Lembre-se de mim</span>
                </div>
                <div class="help">
                    <a href="#">Precisa de ajuda?</a>
                </div>
            </div>
            <div class="register-link">
                <p>Não possui uma conta? <a href="{{ route('register') }}">Registre-se</a></p>
            </div>
        </div>
    </div>
    <style>
        .error {
            color: red;
           
        }
    </style>
</div>

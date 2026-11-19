<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <div class="inline-flex w-12 h-12 rounded-xl bg-yellow-400 text-blue-950 items-center justify-center font-black text-xl shadow-md mb-2">
            KS
        </div>
        <h2 class="text-xl font-extrabold text-blue-950">Masuk ke KIP-SCOPE</h2>
        <p class="text-xs text-slate-500">Sistem Evaluasi Kelayakan Beasiswa KIP-K</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-bold text-xs text-slate-700" />
            <x-text-input id="email" class="block mt-1 w-full text-xs rounded-lg border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2.5" 
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" class="font-bold text-xs text-slate-700" />
            <x-text-input id="password" class="block mt-1 w-full text-xs rounded-lg border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2.5"
                type="password"
                name="password"
                required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between text-xs pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-700 shadow-sm focus:ring-yellow-400" name="remember">
                <span class="ms-2 text-slate-600 font-medium">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-blue-700 hover:text-blue-900 font-semibold hover:underline" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div>
            <button type="submit" 
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-extrabold py-2.5 px-4 rounded-lg shadow-md border border-yellow-400 text-xs tracking-wider uppercase transition transform hover:-translate-y-0.5">
                {{ __('Masuk ke Sistem') }}
            </button>
        </div>

        <div class="text-center pt-2 text-xs text-slate-600">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-blue-700 hover:underline">
                Daftar Akun Baru
            </a>
        </div>
    </form>
</x-guest-layout>

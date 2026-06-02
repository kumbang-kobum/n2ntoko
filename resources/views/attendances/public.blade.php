<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi — {{ \App\Models\Setting::get('toko_nama', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100 min-h-screen" style="font-family:'Inter',sans-serif;">

{{-- Nav --}}
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-900 text-sm leading-tight">{{ \App\Models\Setting::get('toko_nama', config('app.name')) }}</p>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span id="jam" class="font-mono text-sm font-semibold text-gray-700 tabular-nums"></span>
            <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 transition">Beranda</a>
        </div>
    </div>
</nav>

<div class="max-w-lg mx-auto px-4 py-6 space-y-4"
     x-data="absensiApp({{ json_encode($todayAttendances->map(fn($a) => [
         'check_in'  => $a->check_in,
         'check_out' => $a->check_out,
         'status'    => $a->status,
         'photo_in'  => $a->photo_in  ? asset('storage/'.$a->photo_in)  : null,
         'photo_out' => $a->photo_out ? asset('storage/'.$a->photo_out) : null,
     ])) }})"
     x-init="init()">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-2xl flex items-center gap-2.5 text-sm font-medium shadow-sm">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl flex items-center gap-2.5 text-sm font-medium shadow-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if($employees->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400 text-sm shadow-sm">
        Belum ada data pegawai aktif.
    </div>
    @else

    {{-- ── PILIH NAMA ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2.5">Pilih nama Anda</label>
        <select x-model="selectedId" @change="onSelect"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium bg-white
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— Pilih nama pegawai —</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}">{{ $emp->name }}{{ $emp->jabatan ? ' · '.$emp->jabatan : '' }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── STATUS HARI INI ────────────────────────────────────── --}}
    <div x-show="selectedId" x-cloak>

        {{-- Status card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center justify-between">
            <div class="text-sm">
                <p class="text-gray-500 text-xs mb-1">Status hari ini</p>
                <template x-if="!info().check_in">
                    <span class="text-gray-400 font-medium">Belum absen</span>
                </template>
                <template x-if="info().check_in && !info().check_out">
                    <span class="text-green-700 font-semibold">
                        ✓ Masuk pukul <span x-text="info().check_in.substring(0,5)"></span>
                    </span>
                </template>
                <template x-if="info().check_out">
                    <span class="text-blue-700 font-semibold">
                        ✓ Masuk <span x-text="info().check_in.substring(0,5)"></span>
                        &nbsp;→&nbsp;
                        Pulang <span x-text="info().check_out.substring(0,5)"></span>
                    </span>
                </template>
            </div>
            {{-- Thumbnail selfie --}}
            <div class="flex gap-2">
                <template x-if="info().photo_in">
                    <img :src="info().photo_in" alt="Selfie masuk"
                         class="w-12 h-12 rounded-xl object-cover border-2 border-green-200 shadow-sm"
                         title="Selfie check-in">
                </template>
                <template x-if="info().photo_out">
                    <img :src="info().photo_out" alt="Selfie pulang"
                         class="w-12 h-12 rounded-xl object-cover border-2 border-blue-200 shadow-sm"
                         title="Selfie check-out">
                </template>
            </div>
        </div>

        {{-- ── KAMERA ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-4">

            {{-- Viewport kamera --}}
            <div class="relative bg-black aspect-square" x-show="!captured && !cameraError">
                <video x-ref="video" autoplay playsinline muted
                       class="w-full h-full object-cover"
                       style="transform:scaleX(-1)"></video>
                {{-- Panduan wajah --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-52 h-52 rounded-full border-4 border-white/50 border-dashed"></div>
                </div>
                {{-- Label --}}
                <div class="absolute bottom-3 left-0 right-0 text-center">
                    <span class="bg-black/40 text-white text-xs px-3 py-1 rounded-full">
                        Pastikan wajah Anda terlihat jelas
                    </span>
                </div>
            </div>

            {{-- Pratinjau foto yang sudah diambil --}}
            <div class="relative bg-black aspect-square" x-show="captured">
                <img :src="capturedSrc" class="w-full h-full object-cover" style="transform:scaleX(-1)">
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                    <div class="bg-green-500 text-white rounded-full p-3 shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Error kamera --}}
            <div x-show="cameraError" class="aspect-square flex flex-col items-center justify-center gap-3 bg-gray-50 p-8">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                </svg>
                <p class="text-sm text-gray-500 text-center" x-text="cameraError"></p>
                <button type="button" @click="startCamera"
                        class="text-sm text-blue-600 font-medium hover:underline">
                    Coba lagi
                </button>
            </div>

            {{-- Canvas tersembunyi untuk capture --}}
            <canvas x-ref="canvas" class="hidden"></canvas>

            {{-- Tombol aksi --}}
            <div class="p-4 space-y-3">

                {{-- Ambil foto --}}
                <template x-if="!captured && !cameraError">
                    <button type="button" @click="capture"
                            class="w-full flex items-center justify-center gap-2.5 py-4 bg-gray-900 hover:bg-gray-800
                                   active:bg-black text-white font-semibold text-sm rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Ambil Selfie
                    </button>
                </template>

                {{-- Setelah foto diambil: ulangi atau submit --}}
                <template x-if="captured">
                    <div class="space-y-2.5">
                        {{-- Check In --}}
                        <template x-if="!info().check_in">
                            <form method="POST" action="{{ route('absensi.masuk') }}" @submit="injectPhoto">
                                @csrf
                                <input type="hidden" name="employee_id" :value="selectedId">
                                <input type="hidden" name="photo" x-ref="photoInInput">
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2.5 py-4
                                               bg-green-600 hover:bg-green-700 active:bg-green-800
                                               text-white font-bold text-base rounded-xl transition shadow-sm shadow-green-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Konfirmasi Check In
                                </button>
                            </form>
                        </template>

                        {{-- Check Out --}}
                        <template x-if="info().check_in && !info().check_out">
                            <form method="POST" action="{{ route('absensi.pulang') }}" @submit="injectPhoto">
                                @csrf
                                <input type="hidden" name="employee_id" :value="selectedId">
                                <input type="hidden" name="photo" x-ref="photoOutInput">
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2.5 py-4
                                               bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                               text-white font-bold text-base rounded-xl transition shadow-sm shadow-orange-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Konfirmasi Check Out
                                </button>
                            </form>
                        </template>

                        {{-- Sudah lengkap --}}
                        <template x-if="info().check_in && info().check_out">
                            <div class="w-full py-4 bg-gray-100 text-gray-500 text-sm font-medium rounded-xl text-center">
                                Absensi hari ini sudah lengkap
                            </div>
                        </template>

                        {{-- Ulangi foto --}}
                        <button type="button" @click="retake"
                                class="w-full py-3 border border-gray-200 text-gray-500 hover:bg-gray-50
                                       text-sm font-medium rounded-xl transition">
                            Ulangi Foto
                        </button>
                    </div>
                </template>

            </div>
        </div>
    </div>

    {{-- ── DAFTAR KEHADIRAN HARI INI ──────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <p class="font-semibold text-gray-700 text-sm">Kehadiran Hari Ini</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($employees as $emp)
            @php $a = $todayAttendances->get($emp->id); @endphp
            <div class="flex items-center gap-3 px-5 py-3">
                {{-- Foto selfie kecil --}}
                @if($a?->photo_in)
                <img src="{{ asset('storage/'.$a->photo_in) }}" alt=""
                     class="w-10 h-10 rounded-xl object-cover shrink-0 border border-gray-100">
                @else
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->name }}</p>
                    @if($a?->check_in)
                    <p class="text-xs text-gray-400">
                        Masuk {{ substr($a->check_in,0,5) }}
                        @if($a->check_out) → Pulang {{ substr($a->check_out,0,5) }} @endif
                    </p>
                    @else
                    <p class="text-xs text-gray-300">Belum absen</p>
                    @endif
                </div>

                @if($a)
                @php $colors=['hadir'=>'green','izin'=>'blue','sakit'=>'yellow','alpha'=>'red']; $c=$colors[$a->status]??'gray'; @endphp
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0
                             bg-{{ $c }}-100 text-{{ $c }}-700">
                    {{ $a->status_label }}
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    @endif
</div>

<script>
function absensiApp(todayData) {
    return {
        selectedId:  '',
        captured:    false,
        capturedSrc: '',
        cameraError: '',
        stream:      null,
        data: todayData,

        info() {
            return this.data[this.selectedId] ?? { check_in: null, check_out: null, photo_in: null, photo_out: null };
        },

        init() {
            tickClock();
            setInterval(tickClock, 1000);
        },

        onSelect() {
            this.captured    = false;
            this.capturedSrc = '';
            this.cameraError = '';
            this.stopCamera();
            if (this.selectedId) {
                this.$nextTick(() => this.startCamera());
            }
        },

        async startCamera() {
            this.cameraError = '';
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } },
                    audio: false,
                });
                this.$refs.video.srcObject = this.stream;
            } catch (e) {
                if (e.name === 'NotAllowedError') {
                    this.cameraError = 'Izin kamera ditolak. Aktifkan kamera di pengaturan browser Anda.';
                } else if (e.name === 'NotFoundError') {
                    this.cameraError = 'Kamera tidak ditemukan pada perangkat ini.';
                } else {
                    this.cameraError = 'Kamera tidak dapat dibuka: ' + e.message;
                }
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
        },

        capture() {
            const video  = this.$refs.video;
            const canvas = this.$refs.canvas;
            const size   = Math.min(video.videoWidth, video.videoHeight);
            canvas.width  = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            // Crop tengah (square)
            const sx = (video.videoWidth  - size) / 2;
            const sy = (video.videoHeight - size) / 2;
            ctx.drawImage(video, sx, sy, size, size, 0, 0, size, size);
            this.capturedSrc = canvas.toDataURL('image/jpeg', 0.85);
            this.captured    = true;
            this.stopCamera();
        },

        retake() {
            this.captured    = false;
            this.capturedSrc = '';
            this.$nextTick(() => this.startCamera());
        },

        injectPhoto(event) {
            // Masukkan base64 foto ke input tersembunyi sesuai form yang di-submit
            const form   = event.target;
            const inputs = form.querySelectorAll('input[name="photo"]');
            inputs.forEach(inp => inp.value = this.capturedSrc);
        },
    };
}

function tickClock() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const el  = document.getElementById('jam');
    if (el) el.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
}
</script>
</body>
</html>

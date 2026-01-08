@extends('layouts.app')

@section('title', 'Dashboard Monitoring')

@section('content')
<div class="flex flex-col lg:flex-row items-start gap-6">
    <div class="flex-1">
        <!-- Header -->
        <div class="glass p-6 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Monitoring</h1>
                <p class="text-sm text-white/70">Pemantauan real-time sensor IoT Smart Home Anda</p>
            </div>
            <div id="connectionStatus" class="px-4 py-2 rounded-full bg-white/5 text-sm">Menghubungkan...</div>
        </div>

        <!-- Sensor Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- LDR -->
            <div class="glass p-5 flex flex-col items-center text-center">
                <div class="text-4xl mb-3"><i id="ldrIcon" class="fas fa-sun text-yellow-400"></i></div>
                <h3 class="font-semibold">Sensor Cahaya</h3>
                <div class="text-sm text-white/70">LDR Light Sensor</div>
                <div id="ldrValue" class="text-3xl font-bold mt-3">0</div>
                <div id="ldrStatus" class="text-sm text-white/70 mt-1">Menunggu data...</div>
            </div>

            <!-- RFID -->
            <div class="glass p-5 flex flex-col items-center text-center">
                <div class="text-4xl mb-3"><i id="rfidIcon" class="fas fa-id-card text-indigo-300"></i></div>
                <h3 class="font-semibold">RFID Card</h3>
                <div class="text-sm text-white/70">Access Control</div>
                <div id="rfidValue" class="text-2xl font-medium mt-3">Tidak Ada</div>
                <div id="rfidStatus" class="text-sm text-white/70 mt-1">Menunggu scan...</div>
            </div>

            <!-- MQ2 -->
            <div class="glass p-5 flex flex-col items-center text-center">
                <div class="text-4xl mb-3"><i id="mq2Icon" class="fas fa-wind text-green-400"></i></div>
                <h3 class="font-semibold">Sensor Gas</h3>
                <div class="text-sm text-white/70">MQ2 Gas Detector</div>
                <div id="mq2Value" class="text-3xl font-bold mt-3">0</div>
                <div id="mq2Status" class="text-sm text-white/70 mt-1">Normal</div>
                <span id="mq2Badge" class="mt-3 inline-block px-3 py-1 rounded-full bg-green-600 text-sm">AMAN</span>
            </div>

            <!-- Ultrasonic -->
            <div class="glass p-5 flex flex-col items-center text-center">
                <div class="text-4xl mb-3"><i id="ultrasonicIcon" class="fas fa-ruler-combined text-green-400"></i></div>
                <h3 class="font-semibold">Sensor Jarak</h3>
                <div class="text-sm text-white/70">Ultrasonic Distance</div>
                <div id="ultrasonicValue" class="text-2xl font-medium mt-3">0 cm</div>
                <div id="ultrasonicStatus" class="text-sm text-white/70 mt-1">Menunggu data...</div>
            </div>

            <!-- Lamp Control (span full width on small screens) -->
            <div class="sm:col-span-2 lg:col-span-1 glass p-5 flex flex-col items-center text-center">
                <div class="text-4xl mb-3"><i id="lampuIcon" class="fas fa-lightbulb text-yellow-400"></i></div>
                <h3 class="font-semibold">Kontrol Lampu</h3>
                <div id="lampuStatus" class="text-3xl font-bold mt-3">OFF</div>
                <div class="mt-3 flex gap-3">
                    <button id="btnOn" class="px-3 py-1 rounded bg-green-500 text-black font-semibold">ON</button>
                    <button id="btnOff" class="px-3 py-1 rounded bg-red-500 text-white font-semibold">OFF</button>
                </div>
                <small id="lampuInfo" class="text-white/70 mt-2">Menunggu perintah...</small>
            </div>
        </div>

        <!-- Charts -->
        <div class="glass p-5 mb-6">
            <h4 class="font-semibold mb-4">Grafik Sensor Real-time</h4>
            <div class="w-full h-64">
                <canvas id="sensorChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right column: History -->
    <aside class="w-full lg:w-96">
        <div class="glass p-5 mb-4">
            <h4 class="font-semibold mb-3">Riwayat Pembacaan</h4>
            <div id="historyList" class="space-y-3 max-h-96 overflow-auto">
                <div class="text-center text-white/60">Menunggu data...</div>
            </div>
        </div>

        <div class="glass p-5">
            <h5 class="font-semibold mb-2">Informasi</h5>
            <p class="text-sm text-white/70">Kontrol lampu dan status sensor realtime menggunakan Firebase Realtime Database.</p>
        </div>
    </aside>
</div>

<!-- Alerts container (JS will insert alerts here) -->
<div id="alertContainer" class="fixed bottom-6 right-6 w-full max-w-md z-50"></div>

@endsection
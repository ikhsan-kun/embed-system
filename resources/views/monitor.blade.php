@extends('layouts.app')

@section('title', 'Dashboard Monitoring')

@section('content')
<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt me-3"></i>
                Dashboard Monitoring
            </h1>
            <p class="dashboard-subtitle">
                <i class="fas fa-wifi me-2"></i>Pemantauan real-time sensor IoT Smart Home Anda
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="status-indicator">
                <span class="status-badge online" id="connectionStatus">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Menghubungkan...</span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Sensor Cards Grid -->
<div class="row g-4 mb-5">
    <!-- LDR Sensor Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card-sensor text-white h-100">
            <div class="card-body text-center p-4">
                <div class="sensor-icon">
                    <i class="fas fa-sun text-warning" id="ldrIcon"></i>
                </div>
                <h5 class="card-title">Sensor Cahaya</h5>
                <div class="text-white-50 small mb-2">LDR Light Sensor</div>
                <div class="value-large text-warning" id="ldrValue">0</div>
                <div class="sensor-status" id="ldrStatus">Menunggu data...</div>
            </div>
        </div>
    </div>

    <!-- RFID Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card-sensor text-white h-100">
            <div class="card-body text-center p-4">
                <div class="sensor-icon">
                    <i class="fas fa-id-card text-info" id="rfidIcon"></i>
                </div>
                <h5 class="card-title">RFID Card</h5>
                <div class="text-white-50 small mb-2">Access Control</div>
                <div class="value-large text-info" id="rfidValue">Tidak Ada</div>
                <div class="sensor-status" id="rfidStatus">Menunggu scan...</div>
            </div>
        </div>
    </div>

    <!-- MQ2 Gas Sensor Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card-sensor text-white h-100">
            <div class="card-body text-center p-4">
                <div class="sensor-icon">
                    <i class="fas fa-wind text-success" id="mq2Icon"></i>
                </div>
                <h5 class="card-title">Sensor Gas</h5>
                <div class="text-white-50 small mb-2">MQ2 Gas Detector</div>
                <div class="value-large text-white" id="mq2Value">0</div>
                <div class="sensor-status" id="mq2Status">Normal</div>
                <span class="sensor-badge bg-success" id="mq2Badge">
                    <i class="fas fa-check-circle me-1"></i>AMAN
                </span>
            </div>
        </div>
    </div>

    <!-- Ultrasonic Distance Sensor Card -->
    <div class="col-lg-3 col-md-6">
        <div class="card-sensor text-white h-100">
            <div class="card-body text-center p-4">
                <div class="sensor-icon">
                    <i class="fas fa-ruler-combined text-success" id="ultrasonicIcon"></i>
                </div>
                <h5 class="card-title">Sensor Jarak</h5>
                <div class="text-white-50 small mb-2">Ultrasonic Distance</div>
                <div class="value-large text-success" id="ultrasonicValue">0 cm</div>
                <div class="sensor-status" id="ultrasonicStatus">Menunggu data...</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and History Section -->
<div class="row g-4 mb-4">
    <!-- Real-time Chart -->
    <div class="col-lg-8">
        <div class="chart-container">
            <h5>
                <i class="fas fa-chart-line me-2 text-primary"></i>
                Grafik Sensor Real-time
            </h5>
            <canvas id="sensorChart"></canvas>
        </div>
    </div>

    <!-- History Panel -->
    <div class="col-lg-4">
        <div class="chart-container h-100">
            <h5>
                <i class="fas fa-history me-2 text-primary"></i>
                Riwayat Pembacaan
            </h5>
            <div id="historyList" class="list-group list-group-flush">
                <div class="list-group-item text-center text-muted">
                    <i class="fas fa-clock me-2"></i>Menunggu data...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer" style="display: none;"></div>

@endsection

@section('scripts')
<script>
    // Firebase Configuration - REPLACE WITH YOUR VALUES!
    const firebaseConfig = {
        apiKey: "YOUR_API_KEY_HERE",
        authDomain: "your-project-id.firebaseapp.com",
        databaseURL: "https://your-project-id-default-rtdb.asia-southeast1.firebasedatabase.app/",
        projectId: "your-project-id",
        storageBucket: "your-project-id.appspot.com",
        messagingSenderId: "123456789",
        appId: "your-app-id"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();
    let isConnected = false;

    // Enhanced Chart.js Configuration
    const ctx = document.getElementById('sensorChart').getContext('2d');
    const sensorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Cahaya (LDR)',
                    data: [],
                    borderColor: '#fbbf24',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Gas (MQ2)',
                    data: [],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Jarak (cm)',
                    data: [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: 'Poppins'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Data storage for chart (max 20 points)
    let chartData = {
        ldr: [],
        mq2: [],
        ultrasonic: []
    };

    let historyData = [];
    const MAX_HISTORY = 10;

    // Real-time Firebase listener
    const sensorsRef = db.ref('sensors');
    sensorsRef.on('value', (snapshot) => {
        const data = snapshot.val();
        if (data) {
            updateSensors(data);
            updateChart(data);
            updateHistory(data);
            setConnectionStatus(true);
        }
    });

    // Connection status listener
    db.ref('.info/connected').on('value', (snap) => {
        setConnectionStatus(snap.val() === true);
    });

    function setConnectionStatus(connected) {
        const statusEl = document.getElementById('connectionStatus');
        if (connected) {
            statusEl.className = 'status-badge online';
            statusEl.innerHTML = '<i class="fas fa-wifi me-2"></i><span>Online</span>';
        } else {
            statusEl.className = 'status-badge offline';
            statusEl.innerHTML = '<i class="fas fa-wifi-slash me-2"></i><span>Offline</span>';
        }
        isConnected = connected;
    }

    function updateSensors(data) {
        // Update LDR
        const ldrValue = data.ldr || 0;
        document.getElementById('ldrValue').textContent = ldrValue;
        document.getElementById('ldrStatus').textContent = ldrValue > 500 ? 'Terang' : 'Redup';
        document.getElementById('ldrIcon').className = ldrValue > 500 ? 'fas fa-sun text-warning' : 'fas fa-moon text-warning';

        // Update RFID
        const rfidEl = document.getElementById('rfidValue');
        const rfidStatusEl = document.getElementById('rfidStatus');
        const rfidIconEl = document.getElementById('rfidIcon');
        
        if (data.rfid && data.rfid !== 'Tidak Ada' && data.rfid !== 'None') {
            rfidEl.textContent = data.rfid;
            rfidStatusEl.textContent = 'Card Terdeteksi!';
            rfidIconEl.className = 'fas fa-id-card-alt text-info';
            showAlert(`✅ RFID Card terdeteksi: ${data.rfid}`, 'success');
        } else {
            rfidEl.textContent = 'Tidak Ada';
            rfidStatusEl.textContent = 'Menunggu scan...';
            rfidIconEl.className = 'fas fa-id-card text-info';
        }

        // Update MQ2 with enhanced visual feedback
        const mq2Value = data.mq2 || 0;
        const mq2El = document.getElementById('mq2Value');
        const mq2StatusEl = document.getElementById('mq2Status');
        const mq2BadgeEl = document.getElementById('mq2Badge');
        const mq2IconEl = document.getElementById('mq2Icon');

        mq2El.textContent = mq2Value;
        
        if (mq2Value > 2000) {
            mq2StatusEl.textContent = '⚠️ BAHAYA!';
            mq2BadgeEl.className = 'sensor-badge bg-danger';
            mq2BadgeEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>BAHAYA';
            mq2IconEl.className = 'fas fa-exclamation-triangle text-danger';
            mq2El.className = 'value-large text-danger';
            showAlert('🚨 PERINGATAN! Deteksi gas tinggi! Evakuasi segera dan periksa area!', 'danger');
        } else if (mq2Value > 1000) {
            mq2StatusEl.textContent = 'Sedang - Waspada';
            mq2BadgeEl.className = 'sensor-badge bg-warning';
            mq2BadgeEl.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>WASPADA';
            mq2IconEl.className = 'fas fa-wind text-warning';
            mq2El.className = 'value-large text-warning';
        } else {
            mq2StatusEl.textContent = 'Normal';
            mq2BadgeEl.className = 'sensor-badge bg-success';
            mq2BadgeEl.innerHTML = '<i class="fas fa-check-circle me-1"></i>AMAN';
            mq2IconEl.className = 'fas fa-wind text-success';
            mq2El.className = 'value-large text-white';
        }

        // Update Ultrasonic
        const ultrasonicValue = data.ultrasonic || 0;
        document.getElementById('ultrasonicValue').textContent = ultrasonicValue + ' cm';
        document.getElementById('ultrasonicStatus').textContent = ultrasonicValue < 10 ? 'Objek Dekat!' : 'Tersedia';
        document.getElementById('ultrasonicIcon').className = ultrasonicValue < 10 ? 
            'fas fa-exclamation-circle text-warning' : 'fas fa-ruler-combined text-success';
    }

    function updateChart(data) {
        const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        
        // Add new data points
        chartData.ldr.push(data.ldr || 0);
        chartData.mq2.push(data.mq2 || 0);
        chartData.ultrasonic.push(data.ultrasonic || 0);

        // Keep only last 20 points
        if (chartData.ldr.length > 20) {
            chartData.ldr.shift();
            chartData.mq2.shift();
            chartData.ultrasonic.shift();
            sensorChart.data.labels.shift();
        }

        sensorChart.data.labels.push(now);
        sensorChart.data.datasets[0].data = chartData.ldr;
        sensorChart.data.datasets[1].data = chartData.mq2;
        sensorChart.data.datasets[2].data = chartData.ultrasonic;
        
        sensorChart.update('none');
    }

    function updateHistory(data) {
        const timestamp = new Date().toLocaleString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            day: '2-digit',
            month: 'short'
        });

        // Add to history array
        historyData.unshift({
            time: timestamp,
            ldr: data.ldr || 0,
            mq2: data.mq2 || 0,
            ultrasonic: data.ultrasonic || 0
        });

        // Keep only last 10 entries
        if (historyData.length > MAX_HISTORY) {
            historyData.pop();
        }

        // Render history list
        const historyList = document.getElementById('historyList');
        historyList.innerHTML = historyData.map((entry, index) => `
            <div class="list-group-item" style="animation-delay: ${index * 0.05}s">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold text-primary mb-1">
                            <i class="fas fa-clock me-1"></i>${entry.time}
                        </div>
                        <div class="small text-muted">
                            <span class="me-3">
                                <i class="fas fa-sun text-warning me-1"></i>
                                <strong>${entry.ldr}</strong>
                            </span>
                            <span class="me-3">
                                <i class="fas fa-wind text-danger me-1"></i>
                                <strong>${entry.mq2}</strong>
                            </span>
                            <span>
                                <i class="fas fa-ruler text-success me-1"></i>
                                <strong>${entry.ultrasonic}cm</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        
        const alertId = 'alert-' + Date.now();
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show mb-3" role="alert" id="${alertId}">
                <div class="d-flex align-items-center">
                    <i class="fas ${type === 'danger' ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-3 fs-4"></i>
                    <div>
                        <strong>${type === 'danger' ? 'PERINGATAN!' : 'Notifikasi'}</strong>
                        <div>${message}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        alertContainer.style.display = 'block';
        alertContainer.insertAdjacentHTML('beforeend', alertHTML);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                const bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            }
        }, 5000);

        // Play sound for danger alerts
        if (type === 'danger') {
            playAlertSound();
        }
    }

    function playAlertSound() {
        // Create a simple beep sound using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    // Error handling
    sensorsRef.on('error', (error) => {
        console.error('Firebase Error:', error);
        showAlert('⚠️ Koneksi database bermasalah! Periksa koneksi internet Anda.', 'danger');
        setConnectionStatus(false);
    });

    // Show loading message on page load
    window.addEventListener('load', () => {
        console.log('Dashboard loaded successfully');
    });

    // Heartbeat check every 30 seconds
    setInterval(() => {
        if (!isConnected) {
            console.warn('Connection lost, attempting to reconnect...');
        }
    }, 30000);
</script>
@endsection
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Home IoT Monitor')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(138, 43, 226, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(72, 118, 255, 0.3) 0%, transparent 50%);
            animation: backgroundMove 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes backgroundMove {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(5deg); }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        /* Enhanced Navbar */
        .navbar {
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        /* Header Section */
        .dashboard-header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            font-weight: 300;
        }

        /* Status Badge */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .status-badge.online {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            animation: pulseGlow 2s ease-in-out infinite;
        }

        .status-badge.offline {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            }
            50% {
                box-shadow: 0 4px 25px rgba(16, 185, 129, 0.6);
            }
        }

        /* Enhanced Sensor Cards */
        .card-sensor {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .card-sensor::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card-sensor:hover::before {
            opacity: 1;
        }

        .card-sensor:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .sensor-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .card-sensor:hover .sensor-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .value-large {
            font-size: 3rem;
            font-weight: 700;
            margin: 1rem 0;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            line-height: 1;
        }

        .sensor-status {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
        }

        /* Color Variants */
        .text-warning { color: #fbbf24 !important; }
        .text-info { color: #3b82f6 !important; }
        .text-success { color: #10b981 !important; }
        .text-danger { color: #ef4444 !important; }

        /* Badge Styles */
        .sensor-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .bg-success { background: linear-gradient(135deg, #10b981, #059669) !important; }
        .bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }
        .bg-danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; }

        /* Chart Container */
        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .chart-container h5 {
            color: #1e293b;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
        }

        #sensorChart {
            height: 300px !important;
        }

        /* History List */
        .list-group-item {
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            padding: 1rem;
        }

        .list-group-item:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Alert Styles */
        .alert {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            font-weight: 500;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9));
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95));
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9));
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 1.8rem;
            }
            
            .value-large {
                font-size: 2.2rem;
            }

            .sensor-icon {
                font-size: 2.5rem;
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-sensor,
        .chart-container {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .card-sensor:nth-child(1) { animation-delay: 0.1s; }
        .card-sensor:nth-child(2) { animation-delay: 0.2s; }
        .card-sensor:nth-child(3) { animation-delay: 0.3s; }
        .card-sensor:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Enhanced Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-home me-2"></i>Smart Home IoT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('monitor') }}">
                            <i class="fas fa-chart-line me-1"></i>Monitor
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Firebase JS SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-database-compat.js"></script>
    
    @yield('scripts')
</body>
</html>
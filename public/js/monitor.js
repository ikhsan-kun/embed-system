(function () {
    // Ensure DOM is ready
    document.addEventListener("DOMContentLoaded", function () {
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyDhycfEGu87026MUPvDUSKw4phNSj71m3o",
            authDomain: "embed-system-73444.firebaseapp.com",
            databaseURL:
                "https://embed-system-73444-default-rtdb.firebaseio.com",
            projectId: "embed-system-73444",
            storageBucket: "embed-system-73444.firebasestorage.app",
            messagingSenderId: "771584552846",
            appId: "1:771584552846:web:5dee761c77e042c6913edf",
            measurementId: "G-VLM0H5WZMX",
        };

        // Initialize Firebase (guard if already initialized) and add debug logs
        if (!window.firebase || !firebase.initializeApp) {
            console.error("Firebase SDK not loaded");
            return;
        }

        if (!firebase.apps || !firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
            console.log("Firebase initialized (new)");
        } else {
            console.log("Firebase already initialized (reuse)");
        }

        const db = firebase.database();
        console.log("monitor.js loaded — database object present:", !!db);

        // One-time read test to verify DB access and rules
        db.ref("sensors")
            .once("value")
            .then((snapshot) =>
                console.log("sensors.once value:", snapshot.val())
            )
            .catch((err) => console.error("sensors.once error:", err));

        window.addEventListener("error", (e) =>
            console.error("Runtime error:", e.error || e.message)
        );

        let isConnected = false;

        // Chart.js setup
        const ctx =
            document.getElementById("sensorChart") &&
            document.getElementById("sensorChart").getContext("2d");
        const sensorChart = ctx
            ? new Chart(ctx, {
                  type: "line",
                  data: {
                      labels: [],
                      datasets: [
                          {
                              label: "Cahaya (LDR)",
                              data: [],
                              borderColor: "#fbbf24",
                              backgroundColor: "rgba(251, 191, 36, 0.08)",
                              tension: 0.4,
                              fill: true,
                              borderWidth: 3,
                              pointRadius: 3,
                          },
                          {
                              label: "Gas (MQ2)",
                              data: [],
                              borderColor: "#ef4444",
                              backgroundColor: "rgba(239, 68, 68, 0.08)",
                              tension: 0.4,
                              fill: true,
                              borderWidth: 3,
                              pointRadius: 3,
                          },
                          {
                              label: "Jarak (cm)",
                              data: [],
                              borderColor: "#10b981",
                              backgroundColor: "rgba(16, 185, 129, 0.08)",
                              tension: 0.4,
                              fill: true,
                              borderWidth: 3,
                              pointRadius: 3,
                          },
                      ],
                  },
                  options: {
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: { legend: { position: "top" } },
                      scales: { y: { beginAtZero: true } },
                  },
              })
            : null;

        // Data storage
        let chartData = { ldr: [], mq2: [], ultrasonic: [] };
        let historyData = [];
        const MAX_HISTORY = 10;

        const sensorsRef = db.ref("sensors");
        sensorsRef.on("value", (snapshot) => {
            const data = snapshot.val();
            if (data) {
                updateSensors(data);
                updateChart(data);
                updateHistory(data);
                setConnectionStatus(true);
            }
        });

        db.ref(".info/connected").on("value", (snap) => {
            setConnectionStatus(snap.val() === true);
        });

        // Hot-reload style: react immediately to individual child changes/adds
        sensorsRef.on("child_changed", (snap) => {
            const key = snap.key;
            const value = snap.val();
            console.log("child_changed", key, value);

            // update only the changed part
            const partial = {};
            partial[key] = value;
            updateSensors(partial);

            // if numeric sensor changed, update chart/history using current DOM values
            if (["ldr", "mq2", "ultrasonic"].includes(key)) {
                const fullData = {
                    ldr:
                        key === "ldr"
                            ? Number(value)
                            : getCurrentSensorValue("ldr"),
                    mq2:
                        key === "mq2"
                            ? Number(value)
                            : getCurrentSensorValue("mq2"),
                    ultrasonic:
                        key === "ultrasonic"
                            ? Number(value)
                            : getCurrentSensorValue("ultrasonic"),
                };
                updateChart(fullData);
                updateHistory(fullData);
            }

            highlightElementForKey(key);
        });

        sensorsRef.on("child_added", (snap) => {
            const key = snap.key;
            const value = snap.val();
            console.log("child_added", key, value);
            const partial = {};
            partial[key] = value;
            updateSensors(partial);
            highlightElementForKey(key);
        });

        function getCurrentSensorValue(key) {
            const el = document.getElementById(key + "Value");
            if (!el) return 0;
            const text = (el.textContent || el.innerText || "0").toString();
            const num = parseFloat(text.replace(/[^0-9.-]/g, ""));
            return Number.isFinite(num) ? num : 0;
        }

        function getNumericValue(key, incoming) {
            if (typeof incoming === "number") return incoming;
            const n = parseFloat(incoming);
            return Number.isFinite(n) ? n : getCurrentSensorValue(key);
        }

        function highlightElementForKey(key) {
            const idMap = {
                ldr: "ldrValue",
                mq2: "mq2Value",
                ultrasonic: "ultrasonicValue",
                rfid: "rfidValue",
                lampu: "lampuStatus",
            };
            const elId = idMap[key] || key + "Value";
            const el = document.getElementById(elId);
            if (!el) return;
            const original = {
                boxShadow: el.style.boxShadow,
                transform: el.style.transform,
            };
            el.style.transition = "box-shadow 0.35s, transform 0.35s";
            el.style.boxShadow = "0 10px 30px rgba(99,102,241,0.35)";
            el.style.transform = "translateY(-4px)";
            setTimeout(() => {
                el.style.boxShadow = original.boxShadow || "";
                el.style.transform = original.transform || "";
            }, 600);
        }

        function setConnectionStatus(connected) {
            const statusEl = document.getElementById("connectionStatus");
            if (!statusEl) return;
            if (connected) {
                statusEl.className = "status-badge online";
                statusEl.innerHTML =
                    '<i class="fas fa-wifi me-2"></i><span>Online</span>';
            } else {
                statusEl.className = "status-badge offline";
                statusEl.innerHTML =
                    '<i class="fas fa-wifi-slash me-2"></i><span>Offline</span>';
            }
            isConnected = connected;
        }

        function updateSensors(data) {
            // LDR
            const ldrValue = data.ldr || 0;
            const ldrEl = document.getElementById("ldrValue");
            const ldrStatus = document.getElementById("ldrStatus");
            const ldrIcon = document.getElementById("ldrIcon");
            if (ldrEl) ldrEl.textContent = ldrValue;
            if (ldrStatus)
                ldrStatus.textContent = ldrValue > 500 ? "Terang" : "Redup";
            if (ldrIcon)
                ldrIcon.className =
                    ldrValue > 500
                        ? "fas fa-sun text-yellow-400"
                        : "fas fa-moon text-yellow-400";

            // RFID
            const rfidEl = document.getElementById("rfidValue");
            const rfidStatusEl = document.getElementById("rfidStatus");
            const rfidIconEl = document.getElementById("rfidIcon");
            if (
                data.rfid &&
                data.rfid !== "Tidak Ada" &&
                data.rfid !== "None"
            ) {
                if (rfidEl) rfidEl.textContent = data.rfid;
                if (rfidStatusEl) rfidStatusEl.textContent = "Card Terdeteksi!";
                if (rfidIconEl)
                    rfidIconEl.className = "fas fa-id-card-alt text-indigo-300";
                showAlert(`✅ RFID Card terdeteksi: ${data.rfid}`, "success");
            } else {
                if (rfidEl) rfidEl.textContent = "Tidak Ada";
                if (rfidStatusEl) rfidStatusEl.textContent = "Menunggu scan...";
                if (rfidIconEl)
                    rfidIconEl.className = "fas fa-id-card text-indigo-300";
            }

            // MQ2
            const mq2Value = data.mq2 || 0;
            const mq2El = document.getElementById("mq2Value");
            const mq2StatusEl = document.getElementById("mq2Status");
            const mq2BadgeEl = document.getElementById("mq2Badge");
            const mq2IconEl = document.getElementById("mq2Icon");
            if (mq2El) mq2El.textContent = mq2Value;
            if (mq2Value > 2000) {
                if (mq2StatusEl) mq2StatusEl.textContent = "⚠️ BAHAYA!";
                if (mq2BadgeEl) {
                    mq2BadgeEl.className = "sensor-badge bg-danger";
                    mq2BadgeEl.innerHTML =
                        '<i class="fas fa-exclamation-triangle me-1"></i>BAHAYA';
                }
                if (mq2IconEl)
                    mq2IconEl.className =
                        "fas fa-exclamation-triangle text-red-500";
                if (mq2El) mq2El.className = "value-large text-red-500";
                showAlert(
                    "🚨 PERINGATAN! Deteksi gas tinggi! Evakuasi segera dan periksa area!",
                    "danger"
                );
            } else if (mq2Value > 1000) {
                if (mq2StatusEl) mq2StatusEl.textContent = "Sedang - Waspada";
                if (mq2BadgeEl) {
                    mq2BadgeEl.className = "sensor-badge bg-warning";
                    mq2BadgeEl.innerHTML =
                        '<i class="fas fa-exclamation-circle me-1"></i>WASPADA';
                }
                if (mq2IconEl)
                    mq2IconEl.className = "fas fa-wind text-yellow-400";
                if (mq2El) mq2El.className = "value-large text-yellow-400";
            } else {
                if (mq2StatusEl) mq2StatusEl.textContent = "Normal";
                if (mq2BadgeEl) {
                    mq2BadgeEl.className = "sensor-badge bg-success";
                    mq2BadgeEl.innerHTML =
                        '<i class="fas fa-check-circle me-1"></i>AMAN';
                }
                if (mq2IconEl)
                    mq2IconEl.className = "fas fa-wind text-green-400";
                if (mq2El) mq2El.className = "value-large text-white";
            }

            // Ultrasonic
            const ultrasonicValue = data.ultrasonic || 0;
            const ultrasonicEl = document.getElementById("ultrasonicValue");
            const ultrasonicStatus =
                document.getElementById("ultrasonicStatus");
            const ultrasonicIcon = document.getElementById("ultrasonicIcon");
            if (ultrasonicEl)
                ultrasonicEl.textContent = ultrasonicValue + " cm";
            if (ultrasonicStatus)
                ultrasonicStatus.textContent =
                    ultrasonicValue < 10 ? "Objek Dekat!" : "Tersedia";
            if (ultrasonicIcon)
                ultrasonicIcon.className =
                    ultrasonicValue < 10
                        ? "fas fa-exclamation-circle text-yellow-400"
                        : "fas fa-ruler-combined text-green-400";
        }

        function updateChart(data) {
            if (!sensorChart) return;
            const now = new Date().toLocaleTimeString("id-ID");
            chartData.ldr.push(data.ldr || 0);
            chartData.mq2.push(data.mq2 || 0);
            chartData.ultrasonic.push(data.ultrasonic || 0);

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
            sensorChart.update("none");
        }

        function updateHistory(data) {
            const timestamp = new Date().toLocaleString("id-ID");
            historyData.unshift({
                time: timestamp,
                ldr: data.ldr || 0,
                mq2: data.mq2 || 0,
                ultrasonic: data.ultrasonic || 0,
            });
            if (historyData.length > MAX_HISTORY) historyData.pop();
            const historyList = document.getElementById("historyList");
            if (!historyList) return;
            historyList.innerHTML = historyData
                .map(
                    (entry, index) =>
                        `\n        <div class="list-group-item bg-white/5 rounded mb-3 p-3" style="animation-delay: ${
                            index * 0.05
                        }s">\n          <div class="flex justify-between">\n            <div>\n              <div class="font-bold text-indigo-300 mb-1"><i class=\"fas fa-clock me-1\"></i>${
                            entry.time
                        }</div>\n              <div class=\"text-sm text-white/80\">\n                <span class=\"mr-3\"><i class=\"fas fa-sun text-yellow-400 mr-1\"></i><strong>${
                            entry.ldr
                        }</strong></span>\n                <span class=\"mr-3\"><i class=\"fas fa-wind text-red-400 mr-1\"></i><strong>${
                            entry.mq2
                        }</strong></span>\n                <span><i class=\"fas fa-ruler text-green-400 mr-1\"></i><strong>${
                            entry.ultrasonic
                        }cm</strong></span>\n              </div>\n            </div>\n          </div>\n        </div>\n      `
                )
                .join("");
        }

        function showAlert(message, type) {
            const alertContainer = document.getElementById("alertContainer");
            if (!alertContainer) return;
            const alertId = "alert-" + Date.now();
            const color = type === "danger" ? "bg-red-600" : "bg-green-600";
            const icon =
                type === "danger"
                    ? "fa-exclamation-triangle"
                    : "fa-check-circle";
            const html = `\n        <div id="${alertId}" class="${color} text-white rounded-lg p-3 mb-3 shadow-lg max-w-xl mx-auto">\n          <div class=\"flex items-start gap-3\">\n            <i class=\"fas ${icon} mt-1\"></i>\n            <div>\n              <div class=\"font-semibold\">${
                type === "danger" ? "PERINGATAN!" : "Notifikasi"
            }</div>\n              <div>${message}</div>\n            </div>\n          </div>\n        </div>\n      `;
            alertContainer.style.display = "block";
            alertContainer.insertAdjacentHTML("beforeend", html);
            setTimeout(() => {
                const el = document.getElementById(alertId);
                if (el) {
                    el.style.transition = "opacity 0.5s";
                    el.style.opacity = "0";
                    setTimeout(() => el.remove(), 500);
                }
            }, 5000);
            if (type === "danger") playAlertSound();
        }

        // Lamp control
        const lampuRef = db.ref("controls/lampu");
        let currentLampuState = "OFF";
        lampuRef.on("value", (snapshot) => {
            const state = snapshot.val();
            currentLampuState = state || "OFF";
            updateLampuUI(currentLampuState);
        });
        const btnOn = document.getElementById("btnOn");
        const btnOff = document.getElementById("btnOff");
        if (btnOn) btnOn.addEventListener("click", () => setLampuState("ON"));
        if (btnOff)
            btnOff.addEventListener("click", () => setLampuState("OFF"));

        function setLampuState(state) {
            if (!isConnected) {
                showAlert(
                    "Koneksi terputus! Tidak dapat mengirim perintah.",
                    "danger"
                );
                return;
            }
            lampuRef
                .set(state)
                .then(() => {
                    showAlert(
                        `Lampu berhasil di-${
                            state === "ON" ? "nyalakan" : "matikan"
                        }!`,
                        "success"
                    );
                    updateLampuUI(state);
                })
                .catch((error) => {
                    console.error("Gagal mengirim perintah lampu:", error);
                    showAlert("Gagal mengirim perintah lampu!", "danger");
                });
        }

        function updateLampuUI(state) {
            const statusEl = document.getElementById("lampuStatus");
            const iconEl = document.getElementById("lampuIcon");
            const infoEl = document.getElementById("lampuInfo");
            const btnOnEl = document.getElementById("btnOn");
            const btnOffEl = document.getElementById("btnOff");
            if (!statusEl) return;
            if (state === "ON") {
                statusEl.textContent = "ON";
                statusEl.className = "value-large text-green-400";
                if (iconEl)
                    iconEl.className = "fas fa-lightbulb text-yellow-400";
                if (infoEl) infoEl.textContent = "Lampu menyala";
                if (btnOnEl) btnOnEl.disabled = true;
                if (btnOffEl) btnOffEl.disabled = false;
            } else {
                statusEl.textContent = "OFF";
                statusEl.className = "value-large text-white/70";
                if (iconEl) iconEl.className = "fas fa-lightbulb text-white/70";
                if (infoEl) infoEl.textContent = "Lampu mati";
                if (btnOnEl) btnOnEl.disabled = false;
                if (btnOffEl) btnOffEl.disabled = true;
            }
        }

        function playAlertSound() {
            try {
                const audioContext = new (window.AudioContext ||
                    window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = 800;
                oscillator.type = "sine";

                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(
                    0.01,
                    audioContext.currentTime + 0.5
                );

                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (e) {
                console.log("Audio not supported", e);
            }
        }

        // Debug: one-time read for controls/lampu
        db.ref("controls/lampu")
            .once("value")
            .then((snap) => console.log("controls/lampu once:", snap.val()))
            .catch((err) => console.error("controls/lampu once error:", err));

        // Error handling
        sensorsRef.on("error", (error) => {
            console.error("Firebase Error:", error);
            showAlert(
                "⚠️ Koneksi database bermasalah! Periksa koneksi internet Anda.",
                "danger"
            );
            setConnectionStatus(false);
        });

        // Heartbeat check every 30 seconds
        setInterval(() => {
            if (!isConnected) {
                console.warn("Connection lost, attempting to reconnect...");
            }
        }, 30000);
    });
})();

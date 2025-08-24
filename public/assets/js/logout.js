(async function monitorSessionStatus() {
    const checkIntervalMs = 10000;
    let timeoutMinutes = 5;
    let inactivityLimitMs = 5 * 60 * 1000;
    let warningThresholdMs = 4 * 60 * 1000;

    try {
        const res = await fetch('api/session-config');
        const data = await res.json();
        if (data.value && data.data?.timeout_minutes) {
            timeoutMinutes = parseInt(data.data.timeout_minutes);
            inactivityLimitMs = timeoutMinutes * 60 * 1000;
            warningThresholdMs = (timeoutMinutes - 1) * 60 * 1000;
        }
    } catch (err) {
        console.warn('⚠️ Could not fetch session config, using default timeout.', err);
    }

    let lastActivity = Date.now();
    let warningShown = false;

    // ✅ Función para registrar actividad del usuario
    function registerUserActivity() {
        const now = Date.now();
        lastActivity = now;
        warningShown = false;

        console.log("🟢 Actividad detectada:", new Date(now).toLocaleTimeString());

        try {
            localStorage.setItem('lastUserActivity', now.toString());
        } catch (e) {
            console.warn("❌ Error writing to localStorage:", e);
        }

        // (Opcional) Backend ping para sincronizar entre navegadores/sesiones de la misma cuenta
        /*
        fetch('/session-audit/ping', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ session_id: window.sessionId || null, activity: now })
        });
        */
    }

    // 📌 Eventos de usuario considerados como actividad
    const userEvents = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'];
    userEvents.forEach(event =>
        document.addEventListener(event, registerUserActivity, { capture: true })
    );

    // 🔁 Sincronizar entre pestañas del mismo navegador
    window.addEventListener('storage', event => {
        if (event.key === 'lastUserActivity') {
            const ts = parseInt(event.newValue, 10);
            if (!isNaN(ts)) {
                lastActivity = ts;
                warningShown = false;
                console.log("🔄 Actividad recibida de otra pestaña:", new Date(ts).toLocaleTimeString());
            }
        }
    });

    // ✅ Verificar si el backend ha terminado la sesión (expirada o forzada)
    async function checkSessionStatus() {
        try {
            const res = await fetch('api/session-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });

            if (!res.ok) {
                return console.error('❌ HTTP error when checking session status:', res.status);
            }

            const data = await res.json();
            const status = data?.data?.status;

            console.log("🔎 Estado de sesión actual:", status);

            if (status === 'kicked') {
                Swal.fire({
                    icon: 'warning',
                    title: language.sessionKickedTitle || 'Session Terminated by Administrator',
                    text: language.sessionKickedText || 'Your session was forcibly terminated by an administrator.',
                    confirmButtonText: language.confirmButtonText_helper || 'Acknowledge'
                }).then(() => window.location.href = '/logout');
            } else if (status === 'expired') {
                Swal.fire({
                    icon: 'info',
                    title: language.sessionExpiredTitle || 'Session Expired',
                    text: language.sessionExpiredText || 'Your session expired due to inactivity.',
                    confirmButtonText: language.sessionRestart || 'Restart Session'
                }).then(() => window.location.href = '/logout');
            }
        } catch (error) {
            console.error("❌ Error while checking session status:", error);
        }
    }

    // ✅ Verificación de inactividad (incluso con pestaña en segundo plano)
async function checkInactivity() {
    const now = Date.now();
    const inactivityTime = now - lastActivity;

    // Validación de seguridad: no mostrar si hubo actividad hace menos de 2 segundos
    const recentActivity = now - lastActivity <= 2000;

    // Mostrar advertencia solo si:
    // - aún no se ha mostrado
    // - el tiempo de inactividad supera el umbral de advertencia
    // - no se acaba de detectar actividad (protección contra falsos positivos)
    if (
        !warningShown &&
        inactivityTime >= warningThresholdMs &&
        inactivityTime < inactivityLimitMs &&
        !recentActivity
    ) {
        warningShown = true;
        console.log("⚠️ Advertencia de inactividad en", Math.floor(inactivityTime / 1000), "segundos");

        Swal.fire({
            icon: 'warning',
            title: language.sessionWarningTitle || 'Inactivity Detected',
            text: language.sessionWarningText || 'Your session will be terminated in 1 minute due to inactivity.',
            timer: 5000
        });
    }

    // Cierre por inactividad (no se necesita protección extra aquí)
    if (inactivityTime >= inactivityLimitMs) {
        console.log("⛔ Sesión expirada por inactividad:", Math.floor(inactivityTime / 1000), "segundos");

        try {
            await fetch('api/session-audit/kick/1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_id: window.sessionId || null,
                    inactivity_duration: Math.floor(inactivityTime / 1000),
                    status: 'expired'
                })
            });

            await fetch('api/session-audit/store-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_status: 'expired',
                    inactivity_duration: Math.floor(inactivityTime / 1000)
                })
            });
        } catch (error) {
            console.error("❌ Error during inactivity-based session termination:", error);
        }

        window.location.href = '/logout';
    }
}


    // 🟢 Iniciar verificación continua
    checkSessionStatus();
    setInterval(checkSessionStatus, checkIntervalMs);  // cada 10s
    setInterval(checkInactivity, 10000);                // cada 10s también
})();

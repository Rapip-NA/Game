/**
 * SPEAK UP! — Sound Effects (Web Audio API) & Confetti Engine
 * Bebas dependency eksternal dan 100% dapat di-mute kapan saja.
 */

window.SpeakUpSound = (() => {
  let audioCtx = null;
  let enabled = true;

  function getContext() {
    if (!enabled) return null;
    if (!audioCtx) {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      if (AudioContext) {
        audioCtx = new AudioContext();
      }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
      audioCtx.resume().catch(() => {});
    }
    return audioCtx;
  }

  function tone(freq, duration, type = 'sine', volume = 0.2, delay = 0, slideTo = null) {
    const ctx = getContext();
    if (!ctx) return;

    const startTime = ctx.currentTime + delay;
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();

    osc.type = type;
    osc.frequency.setValueAtTime(freq, startTime);
    if (slideTo) {
      osc.frequency.exponentialRampToValueAtTime(slideTo, startTime + duration);
    }

    gain.gain.setValueAtTime(0.001, startTime);
    gain.gain.exponentialRampToValueAtTime(volume, startTime + 0.015);
    gain.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

    osc.connect(gain);
    gain.connect(ctx.destination);

    osc.start(startTime);
    osc.stop(startTime + duration + 0.02);
  }

  return {
    setEnabled(val) {
      enabled = Boolean(val);
    },

    isEnabled() {
      return enabled;
    },

    // 1. Efek saat shuffle / pemilihan pemain
    playShuffleStep() {
      tone(520, 0.05, 'triangle', 0.12);
    },

    playPlayerPick() {
      tone(523.25, 0.1, 'triangle', 0.22, 0);
      tone(659.25, 0.1, 'triangle', 0.22, 0.08);
      tone(783.99, 0.22, 'triangle', 0.26, 0.16);
    },

    // 2. Kemunculan tantangan baru
    playChallengeAppear() {
      tone(440, 0.12, 'sine', 0.18, 0);
      tone(554.37, 0.12, 'sine', 0.18, 0.07);
      tone(659.25, 0.25, 'triangle', 0.24, 0.14);
    },

    // 3. Countdown tick biasa
    playTick() {
      tone(880, 0.035, 'sine', 0.08);
    },

    // 4. Lima detik terakhir
    playLastFive() {
      tone(987.77, 0.11, 'square', 0.14, 0);
      tone(1174.66, 0.08, 'sine', 0.1, 0.06);
    },

    // 5. Waktu habis
    playTimeUp() {
      tone(440, 0.22, 'sawtooth', 0.18, 0, 330);
      tone(330, 0.45, 'sawtooth', 0.2, 0.22, 220);
    },

    // 6. Pergantian gaya berbicara (Speaking Switch)
    playSwitch() {
      tone(587.33, 0.1, 'triangle', 0.25, 0);
      tone(880, 0.14, 'triangle', 0.28, 0.09);
      tone(1174.66, 0.28, 'sine', 0.24, 0.18);
    },

    // 7. Jawaban berhasil / skor tersimpan
    playSuccess() {
      tone(523.25, 0.1, 'triangle', 0.2, 0);
      tone(659.25, 0.1, 'triangle', 0.2, 0.09);
      tone(783.99, 0.12, 'triangle', 0.22, 0.18);
      tone(1046.5, 0.3, 'triangle', 0.26, 0.27);
    },

    // 8. Kehilangan nyawa (Forbidden Words)
    playLoseLife() {
      tone(300, 0.15, 'sawtooth', 0.22, 0, 180);
      tone(220, 0.25, 'sawtooth', 0.24, 0.15, 110);
    },

    // 9. Pengumuman pemenang (Fanfare)
    playWinner() {
      const notes = [
        [523.25, 0.12, 0],
        [523.25, 0.12, 0.13],
        [523.25, 0.12, 0.26],
        [659.25, 0.24, 0.40],
        [587.33, 0.14, 0.66],
        [659.25, 0.14, 0.82],
        [783.99, 0.45, 0.98],
        [1046.5, 0.55, 1.15],
      ];
      notes.forEach(([freq, dur, delay]) => {
        tone(freq, dur, 'triangle', 0.25, delay);
      });
    },

    // Confetti Canvas ringan & mulus
    launchConfetti(durationMs = 2800) {
      const canvas = document.createElement('canvas');
      canvas.style.position = 'fixed';
      canvas.style.inset = '0';
      canvas.style.width = '100vw';
      canvas.style.height = '100vh';
      canvas.style.pointerEvents = 'none';
      canvas.style.zIndex = '9999';
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      document.body.appendChild(canvas);

      const ctx = canvas.getContext('2d');
      const colors = ['#FACC15', '#111111', '#FFFFFF', '#EB3B24', '#0B44D6', '#22C55E'];
      const particles = Array.from({ length: 130 }, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * -canvas.height * 0.5,
        w: 8 + Math.random() * 8,
        h: 5 + Math.random() * 6,
        color: colors[Math.floor(Math.random() * colors.length)],
        vx: (Math.random() - 0.5) * 6,
        vy: 3 + Math.random() * 5,
        rot: Math.random() * 360,
        vr: (Math.random() - 0.5) * 10,
      }));

      const end = performance.now() + durationMs;

      function frame(now) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach((p) => {
          p.x += p.vx;
          p.y += p.vy;
          p.vy += 0.09;
          p.rot += p.vr;

          ctx.save();
          ctx.translate(p.x, p.y);
          ctx.rotate((p.rot * Math.PI) / 180);
          ctx.fillStyle = p.color;
          ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
          ctx.restore();
        });

        if (now < end) {
          requestAnimationFrame(frame);
        } else {
          canvas.remove();
        }
      }

      requestAnimationFrame(frame);
    },
  };
})();

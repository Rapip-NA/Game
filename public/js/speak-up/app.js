/**
 * SPEAK UP! — Interactive Speaking Game Engine
 * Think Fast. Speak Smart.
 * Modular Vanilla JS + LocalStorage + Accurate Timestamp Timer + Keyboard Shortcuts.
 */

window.SpeakUpApp = (() => {
  const STORAGE_KEY = 'mainyuk_speak_up_v1';
  const DATA = window.SpeakUpData;
  const SOUND = window.SpeakUpSound;

  const DEFAULT_SETTINGS = {
    mode: 'short_vs_long',
    playerType: 'individual', // 'individual' | 'group'
    difficulty: 'medium', // 'easy' | 'medium' | 'hard' | 'extreme'
    prepDuration: 10,
    speakDuration: 60,
    shortDirectDuration: 15,
    shortElabDuration: 90,
    totalRounds: 10,
    awkwardTwoPlayer: false,
    switchAuto: true,
    switchMinSec: 12,
    switchMaxSec: 22,
    soundEnabled: true,
    theme: 'dark', // 'dark' | 'light'
  };

  let state = {
    view: 'home', // home | selection | setup | players | gameplay | result | leaderboard | library | settings
    settings: { ...DEFAULT_SETTINGS },
    players: [],
    challenges: [],
    history: [],
    playerQueues: { individual: [], group: [] },
    challengeQueues: {},
    session: {
      active: false,
      round: 1,
      activePlayerId: null,
      partnerPlayerId: null,
      currentChallenge: null,
      phase: 'idle', // idle | prep | speak_direct | speak_elab | speak | finished
      remainingSec: 0,
      totalPhaseSec: 0,
      isRunning: false,
      endTimestamp: null,
      lives: 3,
      currentStyleIndex: 0,
      nextSwitchAtMs: null,
      randomWordsSlice: [],
      smallTalkConstraint: '',
      fullscreen: false,
    },
    scoring: {
      scores: [15, 15, 15, 15, 15],
      notes: '',
    },
    libraryFilter: {
      mode: 'all',
      category: 'all',
      status: 'all',
      search: '',
    },
    leaderboardTab: 'individual',
  };

  let timerInterval = null;
  let shuffleInterval = null;
  let lastTickSec = null;

  // =========================================================================
  // SWEETALERT2 HELPERS
  // =========================================================================
  function swalBase(opts) {
    if (typeof Swal === 'undefined') {
      return Promise.resolve({ isConfirmed: true });
    }
    return Swal.fire({
      customClass: {
        popup: 'speakup-swal-popup',
        title: 'speakup-swal-title',
        confirmButton: 'speakup-swal-confirm',
        cancelButton: 'speakup-swal-cancel',
      },
      buttonsStyling: false,
      ...opts,
    });
  }

  function toast(title, icon = 'success') {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon,
      title,
      showConfirmButton: false,
      timer: 2600,
      timerProgressBar: true,
      customClass: {
        popup: 'speakup-swal-toast',
      },
    });
  }

  // =========================================================================
  // LOCALSTORAGE PERSISTENCE
  // =========================================================================
  function saveState() {
    try {
      const payload = {
        settings: state.settings,
        players: state.players,
        challenges: state.challenges,
        history: state.history,
        playerQueues: state.playerQueues,
        challengeQueues: state.challengeQueues,
        session: {
          ...state.session,
          isRunning: false,
          endTimestamp: null,
          fullscreen: false,
        },
      };
      localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    } catch (e) {
      console.warn('Gagal menyimpan ke LocalStorage:', e);
    }
  }

  function loadState() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) {
        state.challenges = clone(DATA.DEFAULT_CHALLENGES);
        state.players = [];
        return;
      }
      const parsed = JSON.parse(raw);
      if (parsed.settings) state.settings = { ...DEFAULT_SETTINGS, ...parsed.settings };
      if (Array.isArray(parsed.players)) state.players = parsed.players;
      if (Array.isArray(parsed.challenges) && parsed.challenges.length > 0) {
        state.challenges = parsed.challenges;
      } else {
        state.challenges = clone(DATA.DEFAULT_CHALLENGES);
      }
      if (Array.isArray(parsed.history)) state.history = parsed.history;
      if (parsed.playerQueues) state.playerQueues = parsed.playerQueues;
      if (parsed.challengeQueues) state.challengeQueues = parsed.challengeQueues;
      if (parsed.session && parsed.session.active) {
        state.session = { ...state.session, ...parsed.session, isRunning: false, endTimestamp: null, fullscreen: false };
      }
    } catch (e) {
      console.warn('Gagal memuat LocalStorage:', e);
      state.challenges = clone(DATA.DEFAULT_CHALLENGES);
    }
  }

  function clone(obj) {
    return JSON.parse(JSON.stringify(obj));
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // =========================================================================
  // SHUFFLE & NON-REPEATING QUEUE ENGINE
  // =========================================================================
  function shuffleArray(arr) {
    const copy = [...arr];
    for (let i = copy.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [copy[i], copy[j]] = [copy[j], copy[i]];
    }
    return copy;
  }

  function getPlayersByType(type = state.settings.playerType) {
    return state.players.filter((p) => p.type === type);
  }

  function pickNextPlayerId(excludeId = null) {
    const type = state.settings.playerType;
    const eligible = getPlayersByType(type);
    if (eligible.length === 0) return null;
    if (eligible.length === 1) return eligible[0].id;

    let queue = (state.playerQueues[type] || []).filter((id) =>
      eligible.some((p) => p.id === id)
    );

    if (queue.length === 0) {
      queue = shuffleArray(eligible.map((p) => p.id));
      // Hindari pemain yang baru saja tampil muncul pertama kali saat siklus reset
      if (excludeId && queue[0] === excludeId && queue.length > 1) {
        const last = queue.shift();
        queue.push(last);
      }
    }

    let pickedIndex = queue.findIndex((id) => id !== excludeId);
    if (pickedIndex === -1) pickedIndex = 0;
    const [pickedId] = queue.splice(pickedIndex, 1);

    state.playerQueues[type] = queue;
    saveState();
    return pickedId;
  }

  function pickPartnerPlayerId(activePlayerId) {
    const type = state.settings.playerType;
    const eligible = getPlayersByType(type).filter((p) => p.id !== activePlayerId);
    if (eligible.length === 0) return null;
    return eligible[Math.floor(Math.random() * eligible.length)].id;
  }

  function pickNextChallenge(mode = state.settings.mode, currentId = null) {
    const activePool = state.challenges.filter((c) => c.mode === mode && c.active !== false);
    if (activePool.length === 0) return null;
    if (activePool.length === 1) return activePool[0];

    let queue = (state.challengeQueues[mode] || []).filter((id) =>
      activePool.some((c) => c.id === id)
    );

    if (queue.length === 0) {
      queue = shuffleArray(activePool.map((c) => c.id));
      if (currentId && queue[0] === currentId && queue.length > 1) {
        const first = queue.shift();
        queue.push(first);
      }
    }

    let idx = queue.findIndex((id) => id !== currentId);
    if (idx === -1) idx = 0;
    const [pickedId] = queue.splice(idx, 1);

    state.challengeQueues[mode] = queue;
    saveState();

    return activePool.find((c) => c.id === pickedId) || activePool[0];
  }

  function getWordCountForDifficulty(diff) {
    switch (diff) {
      case 'easy': return 2;
      case 'hard': return 5;
      case 'extreme': return 7;
      case 'medium':
      default: return 3;
    }
  }

  function needsPartnerPlayer() {
    const m = state.settings.mode;
    return m === 'small_talk' || (m === 'awkward_situation' && state.settings.awkwardTwoPlayer);
  }

  // =========================================================================
  // GAMEPLAY & TIMER ENGINE (RESILIENT TO TAB THROTTLING)
  // =========================================================================
  function getSpeakDurationForCurrentMode() {
    const mode = state.settings.mode;
    if (mode === 'short_vs_long') {
      return Number(state.settings.shortDirectDuration) || 15;
    }
    return Number(state.settings.speakDuration) || DATA.GAME_MODES[mode].defaultSpeak;
  }

  function startNewSession() {
    const eligible = getPlayersByType(state.settings.playerType);
    if (eligible.length === 0) {
      swalBase({
        icon: 'warning',
        title: 'Belum Ada Pemain!',
        text: `Tambahkan minimal 1 ${state.settings.playerType === 'group' ? 'kelompok' : 'pemain'} dulu sebelum memulai sesi, atau isi otomatis pemain contoh.`,
        showCancelButton: true,
        confirmButtonText: 'Tambah Pemain Sekarang',
        cancelButtonText: 'Isi 5 Pemain Contoh',
      }).then((res) => {
        if (res.isConfirmed) {
          navigate('players');
        } else if (res.dismiss === Swal.DismissReason.cancel) {
          seedSamplePlayers();
          startNewSession();
        }
      });
      return;
    }

    if (needsPartnerPlayer() && eligible.length < 2) {
      swalBase({
        icon: 'warning',
        title: 'Butuh Minimal 2 Pemain!',
        text: 'Mode ini menggunakan format 2 pemain. Tambahkan minimal 1 pemain lagi.',
        confirmButtonText: 'Kelola Pemain',
      }).then(() => navigate('players'));
      return;
    }

    state.session.active = true;
    state.session.round = 1;
    prepareNextTurn({ pickPlayer: true, pickChallenge: true, autoSound: true });
    navigate('gameplay');
  }

  function prepareNextTurn({ pickPlayer = true, pickChallenge = true, autoSound = true } = {}) {
    stopTimerInternal();

    if (pickPlayer) {
      const nextId = pickNextPlayerId(state.session.activePlayerId);
      state.session.activePlayerId = nextId;
      state.session.partnerPlayerId = needsPartnerPlayer() ? pickPartnerPlayerId(nextId) : null;
    }

    if (pickChallenge) {
      const ch = pickNextChallenge(state.settings.mode, state.session.currentChallenge?.id);
      if (!ch) {
        toast('Tidak ada tantangan aktif di mode ini! Aktifkan di Challenge Library.', 'error');
        return;
      }
      state.session.currentChallenge = clone(ch);
      prepareModeSpecificState(ch);
      if (autoSound) SOUND.playChallengeAppear();
    }

    const prepSec = Number(state.settings.prepDuration) || 0;
    if (prepSec > 0) {
      state.session.phase = 'prep';
      state.session.remainingSec = prepSec;
      state.session.totalPhaseSec = prepSec;
    } else {
      enterSpeakPhase(false);
    }

    saveState();
    render();
  }

  function prepareModeSpecificState(ch) {
    state.session.lives = 3;
    state.session.currentStyleIndex = Math.floor(Math.random() * DATA.SPEAKING_STYLES.length);
    state.session.nextSwitchAtMs = null;

    if (state.settings.mode === 'random_connection') {
      const count = getWordCountForDifficulty(state.settings.difficulty);
      const sourceWords = Array.isArray(ch.words) && ch.words.length > 0
        ? ch.words
        : String(ch.title).split(',').map((w) => w.trim()).filter(Boolean);
      const shuffled = shuffleArray(sourceWords);
      state.session.randomWordsSlice = shuffled.slice(0, Math.min(count, shuffled.length));
    }

    if (state.settings.mode === 'small_talk') {
      state.session.smallTalkConstraint = ch.constraint ||
        DATA.SMALL_TALK_CONSTRAINTS[Math.floor(Math.random() * DATA.SMALL_TALK_CONSTRAINTS.length)];
    }
  }

  function enterSpeakPhase(autoStart = true, isElaboration = false) {
    stopTimerInternal();
    const mode = state.settings.mode;
    let dur = getSpeakDurationForCurrentMode();

    if (mode === 'short_vs_long') {
      if (isElaboration) {
        state.session.phase = 'speak_elab';
        dur = Number(state.settings.shortElabDuration) || 90;
      } else {
        state.session.phase = 'speak_direct';
        dur = Number(state.settings.shortDirectDuration) || 15;
      }
    } else {
      state.session.phase = 'speak';
    }

    state.session.remainingSec = dur;
    state.session.totalPhaseSec = dur;
    scheduleNextSpeakingSwitch();

    if (autoStart) {
      startTimer();
    } else {
      saveState();
      render();
    }
  }

  function scheduleNextSpeakingSwitch() {
    if (state.settings.mode === 'speaking_switch' && state.settings.switchAuto && state.session.isRunning) {
      const min = Math.max(5, Number(state.settings.switchMinSec) || 10);
      const max = Math.max(min, Number(state.settings.switchMaxSec) || 25);
      const delaySec = min + Math.floor(Math.random() * (max - min + 1));
      state.session.nextSwitchAtMs = Date.now() + delaySec * 1000;
    } else {
      state.session.nextSwitchAtMs = null;
    }
  }

  function triggerSpeakingStyleSwitch(manual = false) {
    if (state.settings.mode !== 'speaking_switch') return;
    let nextIdx = Math.floor(Math.random() * DATA.SPEAKING_STYLES.length);
    if (DATA.SPEAKING_STYLES.length > 1 && nextIdx === state.session.currentStyleIndex) {
      nextIdx = (nextIdx + 1) % DATA.SPEAKING_STYLES.length;
    }
    state.session.currentStyleIndex = nextIdx;
    SOUND.playSwitch();

    if (!manual && state.session.isRunning && state.settings.switchAuto) {
      scheduleNextSpeakingSwitch();
    }

    renderGameplayDynamicParts();

    const styleBox = document.getElementById('speakingStyleBanner');
    if (styleBox) {
      styleBox.classList.remove('scale-105');
      void styleBox.offsetWidth;
      styleBox.classList.add('scale-105');
      setTimeout(() => styleBox.classList.remove('scale-105'), 350);
    }
  }

  function startTimer() {
    if (state.session.phase === 'finished') {
      resetTimer();
      return;
    }
    if (state.session.remainingSec <= 0) {
      enterSpeakPhase(true, state.session.phase === 'speak_direct');
      return;
    }

    state.session.isRunning = true;
    state.session.endTimestamp = Date.now() + state.session.remainingSec * 1000;
    lastTickSec = state.session.remainingSec;

    if (state.settings.mode === 'speaking_switch' && state.settings.switchAuto && !state.session.nextSwitchAtMs) {
      scheduleNextSpeakingSwitch();
    }

    clearInterval(timerInterval);
    timerInterval = setInterval(onTimerTick, 200);
    renderGameplayDynamicParts();
  }

  function pauseTimer() {
    if (!state.session.isRunning) return;
    if (state.session.endTimestamp) {
      state.session.remainingSec = Math.max(0, Math.ceil((state.session.endTimestamp - Date.now()) / 1000));
    }
    state.session.isRunning = false;
    state.session.endTimestamp = null;
    state.session.nextSwitchAtMs = null;
    clearInterval(timerInterval);
    timerInterval = null;
    renderGameplayDynamicParts();
  }

  function stopTimerInternal() {
    state.session.isRunning = false;
    state.session.endTimestamp = null;
    state.session.nextSwitchAtMs = null;
    clearInterval(timerInterval);
    timerInterval = null;
  }

  function toggleTimer() {
    if (!state.session.active) return;
    if (state.session.isRunning) {
      pauseTimer();
    } else {
      startTimer();
    }
  }

  function resetTimer() {
    stopTimerInternal();
    let dur = 60;
    if (state.session.phase === 'prep') {
      dur = Number(state.settings.prepDuration) || 10;
    } else if (state.session.phase === 'speak_direct') {
      dur = Number(state.settings.shortDirectDuration) || 15;
    } else if (state.session.phase === 'speak_elab') {
      dur = Number(state.settings.shortElabDuration) || 90;
    } else {
      dur = getSpeakDurationForCurrentMode();
      if (state.session.phase === 'finished') {
        state.session.phase = state.settings.mode === 'short_vs_long' ? 'speak_direct' : 'speak';
      }
    }
    state.session.remainingSec = dur;
    state.session.totalPhaseSec = dur;
    renderGameplayDynamicParts();
  }

  function adjustTime(deltaSec) {
    const next = Math.max(1, state.session.remainingSec + deltaSec);
    const diff = next - state.session.remainingSec;
    state.session.remainingSec = next;
    state.session.totalPhaseSec = Math.max(state.session.totalPhaseSec, next);
    if (state.session.isRunning && state.session.endTimestamp) {
      state.session.endTimestamp += diff * 1000;
    }
    renderGameplayDynamicParts();
  }

  function onTimerTick() {
    if (!state.session.isRunning || !state.session.endTimestamp) return;

    const now = Date.now();
    const secLeft = Math.max(0, Math.ceil((state.session.endTimestamp - now) / 1000));

    // Check Speaking Switch Auto Timer
    if (
      state.settings.mode === 'speaking_switch' &&
      state.settings.switchAuto &&
      state.session.phase === 'speak' &&
      state.session.nextSwitchAtMs &&
      now >= state.session.nextSwitchAtMs &&
      secLeft > 2
    ) {
      triggerSpeakingStyleSwitch(false);
    }

    if (secLeft !== lastTickSec) {
      lastTickSec = secLeft;
      state.session.remainingSec = secLeft;

      if (secLeft > 0 && secLeft <= 5) {
        SOUND.playLastFive();
      } else if (secLeft > 5 && state.session.phase === 'prep') {
        SOUND.playTick();
      }

      renderGameplayDynamicParts();
    }

    if (secLeft <= 0) {
      handlePhaseComplete();
    }
  }

  function handlePhaseComplete() {
    stopTimerInternal();

    if (state.session.phase === 'prep') {
      SOUND.playChallengeAppear();
      enterSpeakPhase(true, false);
      toast('Waktu Persiapan Habis! Mulai Berbicara!', 'info');
      return;
    }

    if (state.session.phase === 'speak_direct') {
      SOUND.playSwitch();
      state.session.phase = 'speak_elab';
      const elabDur = Number(state.settings.shortElabDuration) || 90;
      state.session.remainingSec = elabDur;
      state.session.totalPhaseSec = elabDur;
      renderGameplayDynamicParts();

      swalBase({
        icon: 'info',
        title: 'Direct Mode Selesai!',
        text: `Lanjut ke Fase 2: Elaboration Mode (${elabDur} detik) dengan topik yang sama!`,
        confirmButtonText: 'Mulai Elaboration! 🚀',
      }).then(() => {
        startTimer();
      });
      return;
    }

    state.session.phase = 'finished';
    state.session.remainingSec = 0;
    SOUND.playTimeUp();
    renderGameplayDynamicParts();
  }

  function deductLife(amount = -1) {
    if (state.settings.mode !== 'forbidden_words') return;
    const next = Math.max(0, Math.min(3, state.session.lives + amount));
    if (next < state.session.lives) {
      SOUND.playLoseLife();
    }
    state.session.lives = next;
    renderGameplayDynamicParts();

    if (next === 0) {
      stopTimerInternal();
      state.session.phase = 'finished';
      toast('Semua nyawa habis! Silakan beri penilaian ronde ini.', 'error');
    }
  }

  function rollRandomPlayerAnimated() {
    const eligible = getPlayersByType(state.settings.playerType);
    if (eligible.length === 0) {
      toast('Belum ada pemain terdaftar!', 'warning');
      return;
    }
    if (eligible.length === 1) {
      state.session.activePlayerId = eligible[0].id;
      SOUND.playPlayerPick();
      renderGameplayDynamicParts();
      return;
    }

    clearInterval(shuffleInterval);
    const targetId = pickNextPlayerId(state.session.activePlayerId);
    let steps = 0;
    const totalSteps = 12;

    shuffleInterval = setInterval(() => {
      steps++;
      const tempPlayer = eligible[Math.floor(Math.random() * eligible.length)];
      const nameEl = document.getElementById('gameplayPlayerName');
      if (nameEl) {
        nameEl.textContent = tempPlayer.name;
        nameEl.classList.add('animate-pulse');
      }
      SOUND.playShuffleStep();

      if (steps >= totalSteps) {
        clearInterval(shuffleInterval);
        state.session.activePlayerId = targetId;
        state.session.partnerPlayerId = needsPartnerPlayer() ? pickPartnerPlayerId(targetId) : null;
        if (nameEl) nameEl.classList.remove('animate-pulse');
        SOUND.playPlayerPick();
        saveState();
        renderGameplayDynamicParts();
      }
    }, 65);
  }

  function nextChallengeAction(skipPrep = false) {
    const ch = pickNextChallenge(state.settings.mode, state.session.currentChallenge?.id);
    if (!ch) {
      toast('Tidak ada tantangan aktif di mode ini.', 'error');
      return;
    }
    stopTimerInternal();
    state.session.currentChallenge = clone(ch);
    prepareModeSpecificState(ch);
    SOUND.playChallengeAppear();

    if (!skipPrep && Number(state.settings.prepDuration) > 0) {
      state.session.phase = 'prep';
      state.session.remainingSec = Number(state.settings.prepDuration);
      state.session.totalPhaseSec = Number(state.settings.prepDuration);
    } else {
      enterSpeakPhase(false, false);
    }
    saveState();
    render();
  }

  function endRoundAction() {
    stopTimerInternal();
    if (!state.session.activePlayerId) {
      toast('Pilih pemain terlebih dahulu!', 'warning');
      return;
    }
    state.scoring = {
      scores: [15, 15, 15, 15, 15],
      notes: '',
    };
    navigate('result');
  }

  function toggleFullscreenMode() {
    state.session.fullscreen = !state.session.fullscreen;
    if (state.session.fullscreen && document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen().catch(() => {});
    } else if (!state.session.fullscreen && document.fullscreenElement && document.exitFullscreen) {
      document.exitFullscreen().catch(() => {});
    }
    render();
  }

  function toggleSound() {
    state.settings.soundEnabled = !state.settings.soundEnabled;
    SOUND.setEnabled(state.settings.soundEnabled);
    saveState();
    toast(state.settings.soundEnabled ? 'Efek Suara AKTIF 🔊' : 'Efek Suara MUTE 🔇', 'info');
    render();
  }

  // =========================================================================
  // SCORING & LEADERBOARD ACTIONS
  // =========================================================================
  function submitRoundScore(goToLeaderboard = false) {
    const activePlayer = state.players.find((p) => p.id === state.session.activePlayerId);
    if (!activePlayer) {
      navigate('leaderboard');
      return;
    }

    const total = state.scoring.scores.reduce((sum, v) => sum + Number(v || 0), 0);
    const modeInfo = DATA.GAME_MODES[state.settings.mode];

    activePlayer.totalScore = (activePlayer.totalScore || 0) + total;
    activePlayer.roundsPlayed = (activePlayer.roundsPlayed || 0) + 1;
    activePlayer.averageScore = Math.round((activePlayer.totalScore / activePlayer.roundsPlayed) * 10) / 10;

    // Jika mode 2 pemain dan partner ada, partner juga bisa mendapatkan catatan
    const partner = state.players.find((p) => p.id === state.session.partnerPlayerId);

    state.history.unshift({
      id: 'hist_' + Date.now(),
      timestamp: new Date().toISOString(),
      round: state.session.round,
      mode: state.settings.mode,
      modeName: modeInfo.name,
      playerId: activePlayer.id,
      playerName: activePlayer.name,
      playerType: activePlayer.type,
      partnerName: partner ? partner.name : null,
      challengeTitle: state.session.currentChallenge?.title || '-',
      criteriaScores: [...state.scoring.scores],
      totalScore: total,
      notes: state.scoring.notes.trim(),
    });

    SOUND.playSuccess();
    if (total >= 80) {
      SOUND.launchConfetti(2000);
    }

    const isLastRound = state.session.round >= Number(state.settings.totalRounds || 10);
    if (goToLeaderboard || isLastRound) {
      state.session.active = false;
      saveState();
      if (isLastRound) {
        SOUND.playWinner();
        SOUND.launchConfetti(3200);
        swalBase({
          icon: 'success',
          title: 'SESI SELESAI! 🏆',
          text: `${activePlayer.name} meraih ${total} poin! Semua ${state.settings.totalRounds} ronde telah selesai. Lihat klasemen akhir sekarang!`,
          confirmButtonText: 'Lihat Leaderboard 🏆',
        }).then(() => navigate('leaderboard'));
      } else {
        navigate('leaderboard');
      }
      return;
    }

    state.session.round += 1;
    prepareNextTurn({ pickPlayer: true, pickChallenge: true, autoSound: true });
    navigate('gameplay');
    toast(`+${total} Poin untuk ${activePlayer.name}! Lanjut Ronde ${state.session.round}`, 'success');
  }

  function skipScoringNextRound() {
    state.session.round += 1;
    prepareNextTurn({ pickPlayer: true, pickChallenge: true, autoSound: true });
    navigate('gameplay');
  }

  function announceWinner() {
    const list = getPlayersByType(state.leaderboardTab).sort((a, b) => (b.totalScore || 0) - (a.totalScore || 0));
    if (list.length === 0 || (list[0].totalScore || 0) === 0) {
      toast('Belum ada poin tercatat untuk mengumumkan pemenang.', 'warning');
      return;
    }
    const winner = list[0];
    SOUND.playWinner();
    SOUND.launchConfetti(3800);
    swalBase({
      icon: 'success',
      title: `🏆 JUARA 1: ${winner.name.toUpperCase()}!`,
      html: `<div class="space-y-2"><p class="text-lg font-extrabold">Total Skor: <span style="color:#0B44D6">${winner.totalScore} Poin</span></p><p class="text-sm">Rata-rata: ${winner.averageScore || 0} poin/ronde • ${winner.roundsPlayed || 0} tantangan selesai</p></div>`,
      confirmButtonText: 'Luar Biasa! 🎉',
    });
  }

  // =========================================================================
  // PLAYER MANAGEMENT
  // =========================================================================
  function addPlayersFromInput(rawText, type = state.settings.playerType) {
    const names = rawText
      .split(/[\n,]+/)
      .map((s) => s.trim())
      .filter(Boolean);

    if (names.length === 0) {
      toast('Masukkan minimal satu nama pemain/kelompok!', 'warning');
      return;
    }

    let added = 0;
    names.forEach((name) => {
      const exists = state.players.some(
        (p) => p.type === type && p.name.toLowerCase() === name.toLowerCase()
      );
      if (!exists) {
        state.players.push({
          id: 'pl_' + Date.now() + '_' + Math.random().toString(36).slice(2, 6),
          name,
          type,
          totalScore: 0,
          roundsPlayed: 0,
          averageScore: 0,
        });
        added++;
      }
    });

    state.playerQueues[type] = [];
    saveState();
    render();
    toast(`${added} ${type === 'group' ? 'kelompok' : 'pemain'} berhasil ditambahkan!`, 'success');
  }

  function seedSamplePlayers() {
    const samples = state.settings.playerType === 'group'
      ? ['Tim Alpha', 'Tim Bravo', 'Tim Charlie', 'Tim Delta', 'Tim Echo']
      : ['Andi', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar'];
    addPlayersFromInput(samples.join('\n'), state.settings.playerType);
  }

  function editPlayerName(playerId) {
    const player = state.players.find((p) => p.id === playerId);
    if (!player) return;

    swalBase({
      title: 'Edit Nama',
      input: 'text',
      inputValue: player.name,
      showCancelButton: true,
      confirmButtonText: 'Simpan',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed && res.value && res.value.trim()) {
        player.name = res.value.trim();
        saveState();
        render();
        toast('Nama diperbarui!', 'success');
      }
    });
  }

  function deletePlayer(playerId) {
    const player = state.players.find((p) => p.id === playerId);
    if (!player) return;

    swalBase({
      icon: 'warning',
      title: 'Hapus Pemain?',
      text: `Hapus "${player.name}" dari daftar peserta?`,
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed) {
        state.players = state.players.filter((p) => p.id !== playerId);
        saveState();
        render();
        toast('Pemain dihapus.', 'info');
      }
    });
  }

  function clearPlayersByType(type) {
    swalBase({
      icon: 'warning',
      title: 'Hapus Semua Peserta?',
      text: `Seluruh daftar ${type === 'group' ? 'kelompok' : 'pemain individu'} akan dihapus.`,
      showCancelButton: true,
      confirmButtonText: 'Ya, Kosongkan',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed) {
        state.players = state.players.filter((p) => p.type !== type);
        state.playerQueues[type] = [];
        saveState();
        render();
        toast('Daftar peserta dikosongkan.', 'info');
      }
    });
  }

  function resetScoresAction() {
    swalBase({
      icon: 'warning',
      title: 'Reset Semua Skor?',
      text: 'Semua poin pemain di leaderboard dan riwayat ronde akan direset ke 0.',
      showCancelButton: true,
      confirmButtonText: 'Ya, Reset Skor',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed) {
        state.players.forEach((p) => {
          p.totalScore = 0;
          p.roundsPlayed = 0;
          p.averageScore = 0;
        });
        state.history = [];
        saveState();
        render();
        toast('Leaderboard dan riwayat berhasil direset.', 'success');
      }
    });
  }

  function factoryResetAll() {
    swalBase({
      icon: 'warning',
      title: 'Reset Semua Data Aplikasi?',
      text: 'Pemain, skor, riwayat, pengaturan, dan tantangan kustom akan dikembalikan ke awal.',
      showCancelButton: true,
      confirmButtonText: 'Ya, Reset Total',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed) {
        localStorage.removeItem(STORAGE_KEY);
        state.settings = { ...DEFAULT_SETTINGS };
        state.players = [];
        state.challenges = clone(DATA.DEFAULT_CHALLENGES);
        state.history = [];
        state.playerQueues = { individual: [], group: [] };
        state.challengeQueues = {};
        state.session.active = false;
        SOUND.setEnabled(true);
        applyTheme();
        render();
        toast('Seluruh data berhasil dikembalikan ke pengaturan awal.', 'success');
      }
    });
  }

  // =========================================================================
  // CHALLENGE LIBRARY CRUD
  // =========================================================================
  function toggleChallengeActive(id) {
    const ch = state.challenges.find((c) => c.id === id);
    if (!ch) return;
    ch.active = ch.active === false ? true : false;
    saveState();
    render();
  }

  function deleteChallenge(id) {
    swalBase({
      icon: 'warning',
      title: 'Hapus Tantangan?',
      text: 'Tantangan ini akan dihapus dari Challenge Library.',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed) {
        state.challenges = state.challenges.filter((c) => c.id !== id);
        saveState();
        render();
        toast('Tantangan dihapus.', 'info');
      }
    });
  }

  function restoreDefaultChallenges() {
    swalBase({
      icon: 'warning',
      title: 'Pulihkan Library Default?',
      text: 'Seluruh 400+ tantangan bawaan akan dipulihkan. Perubahan kustom akan terganti.',
      showCancelButton: true,
      confirmButtonText: 'Ya, Pulihkan',
      cancelButtonText: 'Batal',
    }).then((res) => {
      if (res.isConfirmed) {
        state.challenges = clone(DATA.DEFAULT_CHALLENGES);
        state.challengeQueues = {};
        saveState();
        render();
        toast('Challenge Library dipulihkan ke 400+ data bawaan.', 'success');
      }
    });
  }

  function openChallengeModal(editId = null) {
    const existing = editId ? state.challenges.find((c) => c.id === editId) : null;
    const defaultMode = existing ? existing.mode : (state.libraryFilter.mode !== 'all' ? state.libraryFilter.mode : 'short_vs_long');

    const modeOptions = Object.values(DATA.GAME_MODES)
      .map((m) => `<option value="${m.id}" ${m.id === defaultMode ? 'selected' : ''}>${m.code} — ${m.name}</option>`)
      .join('');

    const catOptions = DATA.CATEGORIES
      .map((c) => `<option value="${c}" ${existing && existing.category === c ? 'selected' : ''}>${c}</option>`)
      .join('');

    const extraValue = existing
      ? (existing.forbidden ? existing.forbidden.join(', ')
        : existing.words ? existing.words.join(', ')
        : existing.audience || existing.constraint || existing.instruction || existing.mission || '')
      : '';

    const contextVal = existing ? (existing.context || existing.hint || '') : '';
    const opponentVal = existing ? (existing.opponent || '') : '';

    Swal.fire({
      title: existing ? 'Edit Tantangan' : 'Tambah Tantangan Baru',
      customClass: {
        popup: 'speakup-swal-popup',
        title: 'speakup-swal-title',
        confirmButton: 'speakup-swal-confirm',
        cancelButton: 'speakup-swal-cancel',
      },
      buttonsStyling: false,
      showCancelButton: true,
      confirmButtonText: 'Simpan Tantangan',
      cancelButtonText: 'Batal',
      html: `
        <div class="text-left space-y-3 mt-2">
          <div>
            <label class="block text-xs font-extrabold uppercase mb-1">Game Mode</label>
            <select id="swalChMode" class="w-full p-2.5 border-2 border-black rounded-lg text-sm font-bold text-black bg-white" ${existing ? 'disabled' : ''}>
              ${modeOptions}
            </select>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase mb-1">Kategori</label>
            <select id="swalChCat" class="w-full p-2.5 border-2 border-black rounded-lg text-sm font-bold text-black bg-white">
              ${catOptions}
            </select>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase mb-1">Topik / Kalimat / Situasi Utama</label>
            <textarea id="swalChTitle" rows="2" class="w-full p-2.5 border-2 border-black rounded-lg text-sm font-bold text-black bg-white" placeholder="Tulis tantangan utama...">${escapeHtml(existing?.title || '')}</textarea>
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase mb-1">Detail Tambahan (Kata Terlarang/Acak pisah koma, Target Audiens, Misi, atau Instruksi)</label>
            <input id="swalChExtra" type="text" class="w-full p-2.5 border-2 border-black rounded-lg text-sm font-bold text-black bg-white" value="${escapeHtml(extraValue)}" placeholder="Contoh: WiFi, Jaringan, Online / Anak 5 Tahun / Buat lebih sopan">
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-xs font-extrabold uppercase mb-1">Konteks / Hint (Opsional)</label>
              <input id="swalChContext" type="text" class="w-full p-2 border-2 border-black rounded-lg text-xs font-bold text-black bg-white" value="${escapeHtml(contextVal)}" placeholder="Konteks/petunjuk">
            </div>
            <div>
              <label class="block text-xs font-extrabold uppercase mb-1">Lawan Bicara (Opsional)</label>
              <input id="swalChOpponent" type="text" class="w-full p-2 border-2 border-black rounded-lg text-xs font-bold text-black bg-white" value="${escapeHtml(opponentVal)}" placeholder="Karakter lawan">
            </div>
          </div>
        </div>
      `,
      preConfirm: () => {
        const mode = document.getElementById('swalChMode').value;
        const category = document.getElementById('swalChCat').value;
        const title = document.getElementById('swalChTitle').value.trim();
        const extra = document.getElementById('swalChExtra').value.trim();
        const context = document.getElementById('swalChContext').value.trim();
        const opponent = document.getElementById('swalChOpponent').value.trim();

        if (!title) {
          Swal.showValidationMessage('Topik/tantangan utama wajib diisi!');
          return false;
        }
        return { mode, category, title, extra, context, opponent };
      },
    }).then((res) => {
      if (!res.isConfirmed || !res.value) return;
      const v = res.value;
      const item = existing || {
        id: 'custom_' + Date.now(),
        mode: v.mode,
        active: true,
      };
      item.category = v.category;
      item.title = v.title;

      if (v.mode === 'forbidden_words') {
        item.forbidden = v.extra ? v.extra.split(',').map((s) => s.trim()).filter(Boolean) : ['Rahasia', 'Terlarang', 'Kata'];
      } else if (v.mode === 'random_connection') {
        item.words = v.extra ? v.extra.split(',').map((s) => s.trim()).filter(Boolean) : v.title.split(',').map((s) => s.trim()).filter(Boolean);
      } else if (v.mode === 'different_audience') {
        item.audience = v.extra || 'Audiens Umum';
        item.hint = v.context || 'Gunakan bahasa yang paling mudah dipahami target audiens.';
      } else if (v.mode === 'awkward_situation') {
        item.mission = v.extra || 'Selesaikan percakapan secara elegan dan tepat sasaran.';
        item.context = v.context || 'Situasi sosial spontan di lapangan.';
        item.opponent = v.opponent || 'Lawan bicara spontan';
      } else if (v.mode === 'small_talk') {
        item.constraint = v.extra || DATA.SMALL_TALK_CONSTRAINTS[0];
      } else if (v.mode === 'say_it_better') {
        item.instruction = v.extra || 'Buat lebih sopan dan jelas.';
      }

      if (!existing) {
        state.challenges.unshift(item);
      }
      saveState();
      render();
      toast(existing ? 'Tantangan diperbarui!' : 'Tantangan baru ditambahkan!', 'success');
    });
  }

  // =========================================================================
  // NAVIGATION, THEME & KEYBOARD SHORTCUTS
  // =========================================================================
  function navigate(view) {
    if (state.view === 'gameplay' && view !== 'gameplay') {
      pauseTimer();
      if (state.session.fullscreen) {
        state.session.fullscreen = false;
        if (document.fullscreenElement && document.exitFullscreen) {
          document.exitFullscreen().catch(() => {});
        }
      }
    }
    state.view = view;
    render();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function applyTheme() {
    const body = document.body;
    if (state.settings.theme === 'light') {
      body.classList.remove('theme-dark');
      body.classList.add('theme-light');
    } else {
      body.classList.remove('theme-light');
      body.classList.add('theme-dark');
    }
  }

  function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
      const tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
      if (['input', 'textarea', 'select'].includes(tag)) return;
      if (typeof Swal !== 'undefined' && Swal.isVisible()) return;
      if (state.view !== 'gameplay') return;

      switch (e.code) {
        case 'Space':
          e.preventDefault();
          toggleTimer();
          break;
        case 'KeyR':
          e.preventDefault();
          resetTimer();
          break;
        case 'KeyN':
          e.preventDefault();
          nextChallengeAction();
          break;
        case 'KeyP':
          e.preventDefault();
          rollRandomPlayerAnimated();
          break;
        case 'KeyF':
          e.preventDefault();
          toggleFullscreenMode();
          break;
        case 'KeyM':
          e.preventDefault();
          toggleSound();
          break;
        case 'KeyE':
          e.preventDefault();
          endRoundAction();
          break;
        case 'Equal':
        case 'NumpadAdd':
          e.preventDefault();
          adjustTime(10);
          break;
        case 'Minus':
        case 'NumpadSubtract':
          e.preventDefault();
          adjustTime(-10);
          break;
        case 'KeyS':
          if (state.settings.mode === 'speaking_switch') {
            e.preventDefault();
            triggerSpeakingStyleSwitch(true);
          }
          break;
      }
    });

    document.addEventListener('visibilitychange', () => {
      if (!document.hidden && state.session.isRunning) {
        onTimerTick();
      }
    });
  }

  // =========================================================================
  // RENDERING VIEWS
  // =========================================================================
  function formatTime(sec) {
    const s = Math.max(0, Number(sec) || 0);
    const m = Math.floor(s / 60);
    const rem = s % 60;
    return `${String(m).padStart(2, '0')}:${String(rem).padStart(2, '0')}`;
  }

  function render() {
    applyTheme();
    const navbar = document.getElementById('speakUpTopNav');
    if (navbar) {
      navbar.classList.toggle('hidden', state.view === 'gameplay' && state.session.fullscreen);
    }

    document.querySelectorAll('[data-nav]').forEach((btn) => {
      const active = btn.getAttribute('data-nav') === state.view;
      btn.classList.toggle('nav-active', active);
    });

    const soundBtnIcon = document.getElementById('navSoundIcon');
    const soundBtnText = document.getElementById('navSoundText');
    if (soundBtnIcon && soundBtnText) {
      soundBtnIcon.textContent = state.settings.soundEnabled ? '🔊' : '🔇';
      soundBtnText.textContent = state.settings.soundEnabled ? 'Sound On' : 'Muted';
    }

    const root = document.getElementById('speakUpRoot');
    if (!root) return;

    switch (state.view) {
      case 'home': root.innerHTML = renderHome(); break;
      case 'selection': root.innerHTML = renderSelection(); break;
      case 'setup': root.innerHTML = renderSetup(); break;
      case 'players': root.innerHTML = renderPlayers(); break;
      case 'gameplay': root.innerHTML = renderGameplay(); break;
      case 'result': root.innerHTML = renderResult(); break;
      case 'leaderboard': root.innerHTML = renderLeaderboard(); break;
      case 'library': root.innerHTML = renderLibrary(); break;
      case 'settings': root.innerHTML = renderSettings(); break;
      default: root.innerHTML = renderHome();
    }

    if (window.lucide) window.lucide.createIcons();
  }

  function renderHome() {
    const totalActiveChallenges = state.challenges.filter((c) => c.active !== false).length;
    const totalPlayers = state.players.length;
    const totalHistory = state.history.length;

    return `
      <div class="max-w-6xl mx-auto py-8 sm:py-12 space-y-10">
        <div class="su-card p-8 sm:p-12 border-4 border-su-border relative overflow-hidden">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#FFD21E] text-black font-black text-xs uppercase tracking-widest mb-5 border-2 border-black">
            <span>🎤</span> INTERACTIVE CLASSROOM GAME SHOW
          </div>

          <h1 class="font-comic text-5xl sm:text-7xl lg:text-8xl leading-none tracking-wide mb-4">
            SPEAK <span class="text-[#FFD21E]">UP!</span>
          </h1>
          <p class="text-xl sm:text-3xl font-extrabold text-[#FFD21E] mb-4">
            "Think Fast. Speak Smart."
          </p>
          <p class="su-muted text-base sm:text-lg max-w-3xl leading-relaxed font-semibold mb-8">
            Platform latihan public speaking, improvisasi, spontanitas, basa-basi, dan komunikasi efektif untuk kelas tatap muka. Dirancang agar Game Master dapat memandu kelas dengan layar proyektor 16:9 secara interaktif dan kompetitif.
          </p>

          <div class="flex flex-wrap gap-4">
            ${state.session.active ? `
              <button onclick="SpeakUpApp.navigate('gameplay')" class="su-btn su-btn-yellow px-7 py-4 text-lg font-black uppercase tracking-wide flex items-center gap-2">
                <span>▶ Lanjutkan Sesi (Ronde ${state.session.round})</span>
              </button>
            ` : ''}
            <button onclick="SpeakUpApp.navigate('selection')" class="su-btn ${state.session.active ? 'su-btn-neutral' : 'su-btn-yellow'} px-7 py-4 text-lg font-black uppercase tracking-wide flex items-center gap-2">
              <span>🎮 Pilih Game Mode</span>
            </button>
            <button onclick="SpeakUpApp.navigate('setup')" class="su-btn su-btn-neutral px-6 py-4 text-base font-extrabold uppercase flex items-center gap-2">
              <span>⚙️ Setup & Mulai Cepat</span>
            </button>
            <button onclick="SpeakUpApp.navigate('players')" class="su-btn su-btn-neutral px-6 py-4 text-base font-extrabold uppercase flex items-center gap-2">
              <span>👥 Kelola Pemain (${totalPlayers})</span>
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div class="su-card p-6 border-2 border-su-border">
            <div class="text-xs font-extrabold uppercase su-muted">Total Game Modes</div>
            <div class="font-comic text-4xl text-[#FFD21E] mt-2">8 MODE</div>
            <p class="text-xs su-muted mt-1 font-semibold">Short vs Long hingga Say It Better</p>
          </div>
          <div class="su-card p-6 border-2 border-su-border">
            <div class="text-xs font-extrabold uppercase su-muted">Challenge Library</div>
            <div class="font-comic text-4xl text-[#FFD21E] mt-2">${totalActiveChallenges}+</div>
            <p class="text-xs su-muted mt-1 font-semibold">Tantangan aktif di 14 kategori</p>
          </div>
          <div class="su-card p-6 border-2 border-su-border">
            <div class="text-xs font-extrabold uppercase su-muted">Ronde Tercatat</div>
            <div class="font-comic text-4xl text-[#FFD21E] mt-2">${totalHistory}</div>
            <p class="text-xs su-muted mt-1 font-semibold">Tersimpan otomatis di browser lokal</p>
          </div>
        </div>

        <div class="su-card p-6 sm:p-8 border-2 border-su-border flex flex-col sm:flex-row items-center justify-between gap-6">
          <div class="space-y-1">
            <span class="text-xs font-black uppercase text-[#FFD21E] tracking-widest">FILOSOFI SPEAK UP!</span>
            <p class="text-lg sm:text-xl font-extrabold italic">
              "Speaking is not about how much you say, but knowing what to say, how to say it, and when to stop."
            </p>
          </div>
          <button onclick="SpeakUpApp.navigate('leaderboard')" class="su-btn su-btn-neutral px-5 py-3 font-extrabold text-sm shrink-0">
            🏆 Lihat Leaderboard
          </button>
        </div>
      </div>
    `;
  }

  function renderSelection() {
    const cards = Object.values(DATA.GAME_MODES).map((m) => {
      const count = state.challenges.filter((c) => c.mode === m.id && c.active !== false).length;
      const isSelected = state.settings.mode === m.id;
      return `
        <div class="su-card p-6 border-2 ${isSelected ? 'border-[#FFD21E]' : 'border-su-border'} flex flex-col justify-between gap-5">
          <div>
            <div class="flex items-center justify-between gap-2 mb-3">
              <span class="px-3 py-1 rounded-md bg-[#FFD21E] text-black font-black text-xs tracking-widest">MODE ${m.code}</span>
              <span class="text-xs font-extrabold su-muted">${m.badge}</span>
            </div>
            <h3 class="font-comic text-2xl sm:text-3xl tracking-wide mb-2">${m.name}</h3>
            <p class="text-sm su-muted font-semibold leading-relaxed">${m.shortDesc}</p>
          </div>
          <div class="pt-4 border-t border-su-border flex items-center justify-between gap-2">
            <span class="text-xs font-extrabold su-muted">${count} Tantangan Aktif</span>
            <button onclick="SpeakUpApp.selectModeAndSetup('${m.id}')" class="su-btn su-btn-yellow px-4 py-2 text-xs font-black uppercase">
              Pilih Mode →
            </button>
          </div>
        </div>
      `;
    }).join('');

    return `
      <div class="max-w-7xl mx-auto py-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h2 class="font-comic text-3xl sm:text-4xl">PILIH <span class="text-[#FFD21E]">GAME MODE</span></h2>
            <p class="text-sm su-muted font-semibold">Pilih salah satu dari 8 mode latihan komunikasi untuk dimainkan bersama kelas.</p>
          </div>
          <button onclick="SpeakUpApp.navigate('setup')" class="su-btn su-btn-neutral px-4 py-2.5 text-xs font-extrabold uppercase self-start">
            Lanjut ke Setup →
          </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          ${cards}
        </div>
      </div>
    `;
  }

  function selectModeAndSetup(modeId) {
    state.settings.mode = modeId;
    const modeInfo = DATA.GAME_MODES[modeId];
    if (modeInfo) {
      state.settings.speakDuration = modeInfo.defaultSpeak;
      state.settings.prepDuration = modeInfo.defaultPrep;
    }
    saveState();
    navigate('setup');
  }

  function renderSetup() {
    const modeInfo = DATA.GAME_MODES[state.settings.mode];
    const eligibleCount = getPlayersByType(state.settings.playerType).length;

    const modeOptions = Object.values(DATA.GAME_MODES)
      .map((m) => `<option value="${m.id}" ${m.id === state.settings.mode ? 'selected' : ''}>${m.code} — ${m.name}</option>`)
      .join('');

    return `
      <div class="max-w-4xl mx-auto py-8 space-y-6">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="font-comic text-3xl sm:text-4xl">GAME <span class="text-[#FFD21E]">SETUP</span></h2>
            <p class="text-sm su-muted font-semibold">Atur mode, durasi persiapan, waktu berbicara, dan jumlah ronde sebelum tampil.</p>
          </div>
          <button onclick="SpeakUpApp.navigate('players')" class="su-btn su-btn-neutral px-4 py-2.5 text-xs font-extrabold uppercase">
            👥 Pemain (${eligibleCount})
          </button>
        </div>

        <div class="su-card p-6 sm:p-8 border-2 border-su-border space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-extrabold uppercase su-muted mb-2">Game Mode</label>
              <select onchange="SpeakUpApp.updateSetupField('mode', this.value)" class="su-input w-full p-3 rounded-xl font-bold text-sm">
                ${modeOptions}
              </select>
              <p class="text-xs su-muted mt-1.5 font-semibold">${modeInfo.shortDesc}</p>
            </div>

            <div>
              <label class="block text-xs font-extrabold uppercase su-muted mb-2">Format Peserta</label>
              <div class="grid grid-cols-2 gap-2">
                <button onclick="SpeakUpApp.updateSetupField('playerType', 'individual')" class="su-btn ${state.settings.playerType === 'individual' ? 'su-btn-yellow' : 'su-btn-neutral'} py-3 font-extrabold text-sm">
                  👤 Individu (${getPlayersByType('individual').length})
                </button>
                <button onclick="SpeakUpApp.updateSetupField('playerType', 'group')" class="su-btn ${state.settings.playerType === 'group' ? 'su-btn-yellow' : 'su-btn-neutral'} py-3 font-extrabold text-sm">
                  👥 Kelompok (${getPlayersByType('group').length})
                </button>
              </div>
            </div>

            <div>
              <label class="block text-xs font-extrabold uppercase su-muted mb-2">Waktu Persiapan (Detik)</label>
              <input type="number" min="0" max="120" value="${state.settings.prepDuration}" onchange="SpeakUpApp.updateSetupField('prepDuration', Number(this.value))" class="su-input w-full p-3 rounded-xl font-bold text-sm">
            </div>

            <div>
              <label class="block text-xs font-extrabold uppercase su-muted mb-2">Jumlah Ronde</label>
              <input type="number" min="1" max="50" value="${state.settings.totalRounds}" onchange="SpeakUpApp.updateSetupField('totalRounds', Number(this.value))" class="su-input w-full p-3 rounded-xl font-bold text-sm">
            </div>

            ${state.settings.mode === 'short_vs_long' ? `
              <div>
                <label class="block text-xs font-extrabold uppercase su-muted mb-2">Durasi Direct Mode (Detik)</label>
                <input type="number" min="5" max="120" value="${state.settings.shortDirectDuration}" onchange="SpeakUpApp.updateSetupField('shortDirectDuration', Number(this.value))" class="su-input w-full p-3 rounded-xl font-bold text-sm">
              </div>
              <div>
                <label class="block text-xs font-extrabold uppercase su-muted mb-2">Durasi Elaboration Mode (Detik)</label>
                <input type="number" min="15" max="300" value="${state.settings.shortElabDuration}" onchange="SpeakUpApp.updateSetupField('shortElabDuration', Number(this.value))" class="su-input w-full p-3 rounded-xl font-bold text-sm">
              </div>
            ` : `
              <div>
                <label class="block text-xs font-extrabold uppercase su-muted mb-2">Durasi Berbicara (Detik)</label>
                <input type="number" min="10" max="300" value="${state.settings.speakDuration}" onchange="SpeakUpApp.updateSetupField('speakDuration', Number(this.value))" class="su-input w-full p-3 rounded-xl font-bold text-sm">
              </div>
            `}

            <div>
              <label class="block text-xs font-extrabold uppercase su-muted mb-2">Tingkat Kesulitan</label>
              <select onchange="SpeakUpApp.updateSetupField('difficulty', this.value)" class="su-input w-full p-3 rounded-xl font-bold text-sm">
                <option value="easy" ${state.settings.difficulty === 'easy' ? 'selected' : ''}>Easy (Random Connection: 2 kata)</option>
                <option value="medium" ${state.settings.difficulty === 'medium' ? 'selected' : ''}>Medium (Random Connection: 3 kata)</option>
                <option value="hard" ${state.settings.difficulty === 'hard' ? 'selected' : ''}>Hard (Random Connection: 5 kata)</option>
                <option value="extreme" ${state.settings.difficulty === 'extreme' ? 'selected' : ''}>Extreme (Random Connection: 7 kata)</option>
              </select>
            </div>
          </div>

          ${state.settings.mode === 'awkward_situation' ? `
            <div class="p-4 rounded-xl border border-su-border flex items-center justify-between gap-4">
              <div>
                <p class="font-extrabold text-sm">Mode Roleplay 2 Pemain</p>
                <p class="text-xs su-muted">Aktifkan untuk memilih 2 pemain secara acak (Pemain 1 sebagai pembicara, Pemain 2 sebagai lawan bicara).</p>
              </div>
              <button onclick="SpeakUpApp.updateSetupField('awkwardTwoPlayer', !SpeakUpApp.getState().settings.awkwardTwoPlayer)" class="su-btn ${state.settings.awkwardTwoPlayer ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black">
                ${state.settings.awkwardTwoPlayer ? 'AKTIF (2 PEMAIN)' : 'NONAKTIF (SOLO)'}
              </button>
            </div>
          ` : ''}

          ${state.settings.mode === 'speaking_switch' ? `
            <div class="p-4 rounded-xl border border-su-border space-y-3">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="font-extrabold text-sm">Perpindahan Gaya Bicara</p>
                  <p class="text-xs su-muted">Otomatis mengganti gaya bicara setiap 10–30 detik atau dikendalikan manual oleh Game Master.</p>
                </div>
                <button onclick="SpeakUpApp.updateSetupField('switchAuto', !SpeakUpApp.getState().settings.switchAuto)" class="su-btn ${state.settings.switchAuto ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black">
                  ${state.settings.switchAuto ? 'OTOMATIS' : 'MANUAL'}
                </button>
              </div>
              ${state.settings.switchAuto ? `
                <div class="grid grid-cols-2 gap-3 pt-2">
                  <div>
                    <label class="block text-xs font-bold su-muted mb-1">Min Interval (Detik)</label>
                    <input type="number" min="5" max="30" value="${state.settings.switchMinSec}" onchange="SpeakUpApp.updateSetupField('switchMinSec', Number(this.value))" class="su-input w-full p-2.5 rounded-lg text-sm font-bold">
                  </div>
                  <div>
                    <label class="block text-xs font-bold su-muted mb-1">Max Interval (Detik)</label>
                    <input type="number" min="10" max="45" value="${state.settings.switchMaxSec}" onchange="SpeakUpApp.updateSetupField('switchMaxSec', Number(this.value))" class="su-input w-full p-2.5 rounded-lg text-sm font-bold">
                  </div>
                </div>
              ` : ''}
            </div>
          ` : ''}

          <div class="pt-4 border-t border-su-border flex flex-wrap items-center justify-between gap-4">
            <div class="text-xs font-bold su-muted">
              ${eligibleCount === 0
                ? '⚠️ Belum ada pemain terdaftar di format ini.'
                : `✅ Siap bermain dengan <b>${eligibleCount}</b> ${state.settings.playerType === 'group' ? 'kelompok' : 'pemain'}.`}
            </div>
            <button onclick="SpeakUpApp.startNewSession()" class="su-btn su-btn-yellow px-8 py-4 text-lg font-black uppercase tracking-wide">
              🚀 Mulai Sesi Permainan
            </button>
          </div>
        </div>
      </div>
    `;
  }

  function updateSetupField(key, val) {
    state.settings[key] = val;
    if (key === 'mode' && DATA.GAME_MODES[val]) {
      state.settings.speakDuration = DATA.GAME_MODES[val].defaultSpeak;
      state.settings.prepDuration = DATA.GAME_MODES[val].defaultPrep;
    }
    saveState();
    render();
  }

  function renderPlayers() {
    const type = state.settings.playerType;
    const list = getPlayersByType(type);
    const remainingQueue = (state.playerQueues[type] || []).filter((id) => list.some((p) => p.id === id));

    const rows = list.length === 0
      ? `<div class="text-center py-12 su-muted font-bold">Belum ada ${type === 'group' ? 'kelompok' : 'pemain'}. Tambahkan melalui form di samping atau klik "Isi Contoh".</div>`
      : list.map((p, idx) => {
          const waitingTurn = remainingQueue.length === 0 || remainingQueue.includes(p.id);
          return `
            <div class="flex items-center justify-between gap-3 p-4 rounded-xl border border-su-border">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-[#FFD21E] text-black font-black text-sm flex items-center justify-center">${idx + 1}</span>
                <div>
                  <div class="font-extrabold text-base">${escapeHtml(p.name)}</div>
                  <div class="text-xs su-muted font-semibold">
                    Skor: <b>${p.totalScore || 0}</b> • Ronde: <b>${p.roundsPlayed || 0}</b> •
                    <span class="${waitingTurn ? 'text-emerald-400' : 'text-amber-400'}">
                      ${waitingTurn ? 'Siap digilir' : 'Sudah tampil di siklus ini'}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button onclick="SpeakUpApp.editPlayerName('${p.id}')" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-bold">Edit</button>
                <button onclick="SpeakUpApp.deletePlayer('${p.id}')" class="su-btn su-btn-danger px-3 py-1.5 text-xs font-bold">Hapus</button>
              </div>
            </div>
          `;
        }).join('');

    return `
      <div class="max-w-6xl mx-auto py-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h2 class="font-comic text-3xl sm:text-4xl">PLAYER <span class="text-[#FFD21E]">MANAGEMENT</span></h2>
            <p class="text-sm su-muted font-semibold">Kelola daftar individu atau kelompok. Sistem memastikan setiap peserta mendapat giliran sebelum diacak ulang.</p>
          </div>
          <div class="flex gap-2">
            <button onclick="SpeakUpApp.updateSetupField('playerType', 'individual')" class="su-btn ${type === 'individual' ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black uppercase">
              👤 Individu (${getPlayersByType('individual').length})
            </button>
            <button onclick="SpeakUpApp.updateSetupField('playerType', 'group')" class="su-btn ${type === 'group' ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black uppercase">
              👥 Kelompok (${getPlayersByType('group').length})
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
          <div class="lg:col-span-5 su-card p-6 border-2 border-su-border space-y-4 self-start">
            <h3 class="font-comic text-xl">TAMBAH ${type === 'group' ? 'KELOMPOK' : 'PEMAIN'}</h3>
            <p class="text-xs su-muted font-semibold">Bisa input satu nama atau banyak sekaligus (pisahkan dengan baris baru atau koma).</p>
            <textarea id="bulkPlayerInput" rows="5" class="su-input w-full p-3 rounded-xl font-bold text-sm" placeholder="Contoh:\nAndi\nBudi\nCitra\nDewi"></textarea>
            <div class="flex flex-wrap gap-2">
              <button onclick="SpeakUpApp.handleAddPlayerForm()" class="su-btn su-btn-yellow flex-1 py-3 font-black text-sm uppercase">
                + Tambahkan
              </button>
              <button onclick="SpeakUpApp.seedSamplePlayers()" class="su-btn su-btn-neutral px-4 py-3 font-bold text-xs uppercase">
                Isi Contoh
              </button>
            </div>
          </div>

          <div class="lg:col-span-7 su-card p-6 border-2 border-su-border space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-su-border">
              <h3 class="font-comic text-xl">DAFTAR AKTIF (${list.length})</h3>
              <div class="flex gap-2">
                <button onclick="SpeakUpApp.resetPlayerCycle('${type}')" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-bold">🔄 Reset Siklus Giliran</button>
                <button onclick="SpeakUpApp.clearPlayersByType('${type}')" class="su-btn su-btn-danger px-3 py-1.5 text-xs font-bold">🗑 Kosongkan</button>
              </div>
            </div>
            <div class="space-y-2.5 max-h-[520px] overflow-y-auto pr-1">
              ${rows}
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function handleAddPlayerForm() {
    const el = document.getElementById('bulkPlayerInput');
    if (!el) return;
    addPlayersFromInput(el.value, state.settings.playerType);
  }

  function resetPlayerCycle(type) {
    state.playerQueues[type] = [];
    saveState();
    render();
    toast('Siklus pengacakan giliran direset!', 'info');
  }

  // =========================================================================
  // GAMEPLAY RENDER (16:9 PROJECTOR OPTIMIZED)
  // =========================================================================
  function renderGameplay() {
    if (!state.session.active || !state.session.currentChallenge) {
      return `
        <div class="max-w-2xl mx-auto py-16 text-center space-y-5 su-card p-10 border-2 border-su-border my-8">
          <h2 class="font-comic text-4xl">BELUM ADA SESI AKTIF</h2>
          <p class="su-muted font-semibold">Mulai sesi baru dari halaman Setup untuk menampilkan gameplay di proyektor.</p>
          <div class="flex justify-center gap-3">
            <button onclick="SpeakUpApp.navigate('setup')" class="su-btn su-btn-yellow px-6 py-3 font-black uppercase">⚙️ Setup Permainan</button>
            <button onclick="SpeakUpApp.startNewSession()" class="su-btn su-btn-neutral px-6 py-3 font-black uppercase">▶ Mulai Langsung</button>
          </div>
        </div>
      `;
    }

    const modeInfo = DATA.GAME_MODES[state.settings.mode];
    const activePlayer = state.players.find((p) => p.id === state.session.activePlayerId);
    const partnerPlayer = state.players.find((p) => p.id === state.session.partnerPlayerId);
    const ch = state.session.currentChallenge;

    const phaseLabel = getPhaseBadgeLabel();
    const isUrgent = state.session.remainingSec > 0 && state.session.remainingSec <= 5 && state.session.isRunning;
    const progressPct = state.session.totalPhaseSec > 0
      ? Math.max(0, Math.min(100, Math.round((state.session.remainingSec / state.session.totalPhaseSec) * 100)))
      : 0;

    return `
      <div class="${state.session.fullscreen ? 'min-h-screen flex flex-col justify-between p-4 sm:p-8' : 'max-w-7xl mx-auto py-4 sm:py-6 space-y-5'}">
        
        <!-- Top Status Bar (Proyektor Friendly) -->
        <div class="su-card px-5 py-3.5 border-2 border-su-border flex flex-wrap items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-3">
            <span class="px-3 py-1 rounded-lg bg-[#FFD21E] text-black font-black text-xs sm:text-sm uppercase tracking-wider">
              MODE ${modeInfo.code}: ${modeInfo.name}
            </span>
            <span class="px-3 py-1 rounded-lg border border-su-border font-extrabold text-xs sm:text-sm">
              RONDE ${state.session.round} / ${state.settings.totalRounds}
            </span>
            <span id="gameplayPhaseBadge" class="px-3 py-1 rounded-lg font-black text-xs sm:text-sm uppercase ${phaseLabel.cls}">
              ${phaseLabel.text}
            </span>
          </div>

          <div class="flex items-center gap-2">
            <select onchange="SpeakUpApp.quickSwitchMode(this.value)" class="su-input px-3 py-1.5 rounded-lg text-xs font-extrabold" title="Ganti Mode Cepat">
              ${Object.values(DATA.GAME_MODES).map((m) => `<option value="${m.id}" ${m.id === state.settings.mode ? 'selected' : ''}>${m.code} — ${m.name}</option>`).join('')}
            </select>
            ${state.settings.mode === 'random_connection' ? `
              <select onchange="SpeakUpApp.quickSwitchDifficulty(this.value)" class="su-input px-3 py-1.5 rounded-lg text-xs font-extrabold" title="Ganti Kesulitan">
                <option value="easy" ${state.settings.difficulty === 'easy' ? 'selected' : ''}>Easy (2 Kata)</option>
                <option value="medium" ${state.settings.difficulty === 'medium' ? 'selected' : ''}>Medium (3 Kata)</option>
                <option value="hard" ${state.settings.difficulty === 'hard' ? 'selected' : ''}>Hard (5 Kata)</option>
                <option value="extreme" ${state.settings.difficulty === 'extreme' ? 'selected' : ''}>Extreme (7 Kata)</option>
              </select>
            ` : ''}
            <button onclick="SpeakUpApp.toggleSound()" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-extrabold" title="Mute/Unmute (M)">
              ${state.settings.soundEnabled ? '🔊' : '🔇'}
            </button>
            <button onclick="SpeakUpApp.toggleFullscreenMode()" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-extrabold" title="Fullscreen Proyektor (F)">
              ${state.session.fullscreen ? '⛶ Keluar Fullscreen' : '⛶ Fullscreen'}
            </button>
          </div>
        </div>

        <!-- Main Projector Stage (16:9 Split) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch my-2">
          
          <!-- Kiri/Tengah: Pemain & Tantangan Utama (8 Cols) -->
          <div class="lg:col-span-8 su-card p-6 sm:p-10 border-4 border-su-border flex flex-col justify-between gap-6">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-su-border">
              <div>
                <div class="text-xs font-extrabold uppercase su-muted tracking-widest">PEMAIN AKTIF</div>
                <div class="flex flex-wrap items-baseline gap-3 mt-1">
                  <span id="gameplayPlayerName" class="font-comic text-3xl sm:text-5xl text-[#FFD21E] tracking-wide">
                    ${activePlayer ? escapeHtml(activePlayer.name) : 'Belum Dipilih'}
                  </span>
                  ${partnerPlayer ? `
                    <span class="text-sm sm:text-xl font-extrabold su-muted">
                      🤝 Partner: <b class="su-text-strong">${escapeHtml(partnerPlayer.name)}</b>
                    </span>
                  ` : ''}
                </div>
              </div>
              <button onclick="SpeakUpApp.rollRandomPlayerAnimated()" class="su-btn su-btn-neutral px-4 py-2.5 text-xs sm:text-sm font-black uppercase" title="Acak Pemain (P)">
                🎲 Acak Pemain
              </button>
            </div>

            <!-- Challenge Display Area -->
            <div class="my-auto space-y-5">
              <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-1 rounded bg-white/10 border border-su-border text-xs font-extrabold uppercase">
                  📂 ${escapeHtml(ch.category)}
                </span>
                <span class="text-xs sm:text-sm su-muted font-bold">
                  ${modeInfo.instruction}
                </span>
              </div>

              ${renderModeSpecificStage(ch)}
            </div>
          </div>

          <!-- Kanan: HUGE Countdown Timer & Progress (4 Cols) -->
          <div class="lg:col-span-4 su-card p-6 sm:p-8 border-4 border-su-border flex flex-col justify-between gap-6 text-center">
            <div>
              <div class="text-xs font-extrabold uppercase su-muted tracking-widest mb-2">COUNTDOWN TIMER</div>
              <div id="gameplayTimerValue" class="font-comic text-7xl sm:text-8xl xl:text-9xl tabular-nums leading-none tracking-tight ${isUrgent ? 'text-[#EB3B24] animate-pulse' : 'text-[#FFD21E]'}">
                ${formatTime(state.session.remainingSec)}
              </div>
              <div class="w-full h-4 bg-black/30 rounded-full overflow-hidden border border-su-border mt-5">
                <div id="gameplayProgressBar" class="h-full transition-all duration-200 ${isUrgent ? 'bg-[#EB3B24]' : 'bg-[#FFD21E]'}" style="width: ${progressPct}%"></div>
              </div>
            </div>

            <!-- Quick Timer Controls -->
            <div class="space-y-3">
              <div class="grid grid-cols-2 gap-2.5">
                <button id="gameplayStartPauseBtn" onclick="SpeakUpApp.toggleTimer()" class="su-btn su-btn-yellow py-3.5 font-black text-base uppercase col-span-2" title="Space">
                  ${state.session.isRunning ? '⏸ Pause (Space)' : (state.session.remainingSec === 0 ? '🔄 Ulangi' : '▶ Start (Space)')}
                </button>
                <button onclick="SpeakUpApp.adjustTime(-10)" class="su-btn su-btn-neutral py-2.5 font-extrabold text-sm" title="-10 Detik (-)">-10 Detik</button>
                <button onclick="SpeakUpApp.adjustTime(10)" class="su-btn su-btn-neutral py-2.5 font-extrabold text-sm" title="+10 Detik (+)">+10 Detik</button>
              </div>
              ${state.settings.mode === 'short_vs_long' ? `
                <div class="grid grid-cols-2 gap-2 pt-1">
                  <button onclick="SpeakUpApp.enterSpeakPhase(false, false)" class="su-btn ${state.session.phase === 'speak_direct' ? 'su-btn-yellow' : 'su-btn-neutral'} py-2 text-xs font-black uppercase">
                    ⚡ Direct (${state.settings.shortDirectDuration}s)
                  </button>
                  <button onclick="SpeakUpApp.enterSpeakPhase(false, true)" class="su-btn ${state.session.phase === 'speak_elab' ? 'su-btn-yellow' : 'su-btn-neutral'} py-2 text-xs font-black uppercase">
                    📖 Elaboration (${state.settings.shortElabDuration}s)
                  </button>
                </div>
              ` : ''}
            </div>
          </div>
        </div>

        <!-- Game Master Control Bar -->
        <div class="su-card px-5 py-3.5 border-2 border-su-border flex flex-wrap items-center justify-between gap-3">
          <div class="flex flex-wrap items-center gap-2">
            <button onclick="SpeakUpApp.resetTimer()" class="su-btn su-btn-neutral px-4 py-2.5 text-xs font-extrabold uppercase" title="R">
              🔄 Reset Timer
            </button>
            <button onclick="SpeakUpApp.nextChallengeAction(false)" class="su-btn su-btn-neutral px-4 py-2.5 text-xs font-extrabold uppercase" title="N">
              ⏭ Next / Skip Challenge
            </button>
            <button onclick="SpeakUpApp.nextChallengeAction(true)" class="su-btn su-btn-neutral px-4 py-2.5 text-xs font-extrabold uppercase">
              ⚡ Skip Persiapan
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <span class="hidden xl:inline text-[11px] su-muted font-bold">
              Shortcuts: <b>Space</b> Start/Pause • <b>R</b> Reset • <b>N</b> Next • <b>P</b> Random Player • <b>F</b> Fullscreen • <b>M</b> Mute • <b>E</b> End Round
            </span>
            <button onclick="SpeakUpApp.endRoundAction()" class="su-btn su-btn-yellow px-6 py-2.5 text-sm font-black uppercase" title="E">
              ✅ End Round & Beri Nilai →
            </button>
          </div>
        </div>

      </div>
    `;
  }

  function getPhaseBadgeLabel() {
    switch (state.session.phase) {
      case 'prep': return { text: '⏳ PERSIAPAN', cls: 'bg-sky-400 text-black' };
      case 'speak_direct': return { text: '⚡ FASE 1: DIRECT MODE', cls: 'bg-amber-400 text-black' };
      case 'speak_elab': return { text: '📖 FASE 2: ELABORATION MODE', cls: 'bg-emerald-400 text-black' };
      case 'finished': return { text: '🔔 WAKTU HABIS', cls: 'bg-[#EB3B24] text-white' };
      case 'speak':
      default: return { text: '🎙️ SEDANG BERBICARA', cls: 'bg-emerald-400 text-black' };
    }
  }

  function renderModeSpecificStage(ch) {
    const mode = state.settings.mode;

    if (mode === 'forbidden_words') {
      const words = (ch.forbidden || []).map((w) =>
        `<span class="px-4 py-2 rounded-xl bg-[#EB3B24] text-white font-black text-lg sm:text-2xl border-2 border-black uppercase tracking-wide">🚫 ${escapeHtml(w)}</span>`
      ).join('');
      const hearts = Array.from({ length: 3 }, (_, i) =>
        `<span class="text-3xl sm:text-4xl ${i < state.session.lives ? '' : 'opacity-20 grayscale'}">❤️</span>`
      ).join('');

      return `
        <div class="space-y-6">
          <h2 class="text-3xl sm:text-5xl font-black leading-tight">${escapeHtml(ch.title)}</h2>
          <div>
            <div class="text-xs font-extrabold uppercase su-muted mb-2">KATA TERLARANG (JANGAN DIUCAPKAN!)</div>
            <div class="flex flex-wrap gap-3">${words}</div>
          </div>
          <div class="p-4 rounded-xl border border-su-border flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <span class="text-xs font-extrabold uppercase su-muted">SISA NYAWA:</span>
              <div id="gameplayHearts" class="flex gap-1.5">${hearts}</div>
            </div>
            <div class="flex gap-2">
              <button onclick="SpeakUpApp.deductLife(-1)" class="su-btn su-btn-danger px-4 py-2 text-xs sm:text-sm font-black uppercase">
                💔 Kurangi Nyawa (-1)
              </button>
              <button onclick="SpeakUpApp.deductLife(1)" class="su-btn su-btn-neutral px-3 py-2 text-xs font-bold">
                +1 Undo
              </button>
            </div>
          </div>
        </div>
      `;
    }

    if (mode === 'awkward_situation') {
      return `
        <div class="space-y-5">
          <h2 class="text-3xl sm:text-4xl font-black leading-tight text-[#FFD21E]">${escapeHtml(ch.title)}</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl border border-su-border">
              <div class="text-xs font-extrabold uppercase su-muted mb-1">📍 KONTEKS SITUASI</div>
              <p class="text-sm sm:text-base font-bold leading-relaxed">${escapeHtml(ch.context)}</p>
            </div>
            <div class="p-4 rounded-xl border border-su-border">
              <div class="text-xs font-extrabold uppercase su-muted mb-1">🎭 KARAKTER LAWAN BICARA</div>
              <p class="text-sm sm:text-base font-bold leading-relaxed">${escapeHtml(ch.opponent)}</p>
            </div>
          </div>
          <div class="p-4 rounded-xl bg-[#FFD21E] text-black border-2 border-black">
            <div class="text-xs font-black uppercase tracking-wider mb-1">🎯 MISI KOMUNIKASI</div>
            <p class="text-base sm:text-lg font-extrabold">${escapeHtml(ch.mission)}</p>
          </div>
        </div>
      `;
    }

    if (mode === 'random_connection') {
      const words = state.session.randomWordsSlice || [];
      const cards = words.map((w, idx) => `
        <div class="px-5 py-4 rounded-2xl bg-[#FFD21E] text-black border-2 border-black text-center">
          <div class="text-[11px] font-black uppercase opacity-70">KATA #${idx + 1}</div>
          <div class="font-comic text-2xl sm:text-4xl mt-1">${escapeHtml(w)}</div>
        </div>
      `).join('');
      return `
        <div class="space-y-5">
          <div class="text-xs font-extrabold uppercase text-[#FFD21E]">
            TINGKAT KESULITAN: ${state.settings.difficulty.toUpperCase()} (${words.length} KATA)
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">${cards}</div>
          <p class="text-sm sm:text-base font-bold su-muted">
            Rangkai seluruh kata di atas menjadi satu cerita yang mengalir logis dan menarik!
          </p>
        </div>
      `;
    }

    if (mode === 'speaking_switch') {
      const style = DATA.SPEAKING_STYLES[state.session.currentStyleIndex] || DATA.SPEAKING_STYLES[0];
      return `
        <div class="space-y-6">
          <h2 class="text-3xl sm:text-4xl font-black leading-tight">${escapeHtml(ch.title)}</h2>
          <div id="speakingStyleBanner" class="p-6 rounded-2xl border-4 border-black text-black transition-transform duration-200" style="background-color: ${style.color};">
            <div class="flex flex-wrap items-center justify-between gap-3">
              <div>
                <div class="text-xs font-black uppercase tracking-widest">GAYA BERBICARA SAAT INI:</div>
                <div class="font-comic text-4xl sm:text-6xl mt-1">${escapeHtml(style.name.toUpperCase())}</div>
                <p class="text-sm sm:text-base font-extrabold mt-1">${escapeHtml(style.desc)}</p>
              </div>
              <button onclick="SpeakUpApp.triggerSpeakingStyleSwitch(true)" class="su-btn bg-black text-white px-5 py-3 text-xs sm:text-sm font-black uppercase" title="Shortcut: S">
                ⚡ Ganti Gaya Sekarang (S)
              </button>
            </div>
          </div>
          <div class="text-xs font-bold su-muted">
            Mode Perpindahan: <b>${state.settings.switchAuto ? `Otomatis (${state.settings.switchMinSec}–${state.settings.switchMaxSec} dtk)` : 'Manual oleh Game Master'}</b>
          </div>
        </div>
      `;
    }

    if (mode === 'different_audience') {
      return `
        <div class="space-y-5">
          <div>
            <div class="text-xs font-extrabold uppercase su-muted mb-1">TOPIK YANG HARUS DIJELASKAN:</div>
            <h2 class="text-3xl sm:text-5xl font-black leading-tight">${escapeHtml(ch.title)}</h2>
          </div>
          <div class="p-6 rounded-2xl bg-[#FFD21E] text-black border-2 border-black">
            <div class="text-xs font-black uppercase tracking-widest mb-1">👥 TARGET AUDIENS KAMU:</div>
            <div class="font-comic text-3xl sm:text-5xl">${escapeHtml(ch.audience)}</div>
          </div>
          <div class="p-4 rounded-xl border border-su-border">
            <div class="text-xs font-extrabold uppercase su-muted mb-1">💡 PETUNJUK PENYAMPAIAN</div>
            <p class="text-sm sm:text-base font-bold">${escapeHtml(ch.hint || 'Sesuaikan diksi dan analogi agar audiens mudah paham.')}</p>
          </div>
        </div>
      `;
    }

    if (mode === 'small_talk') {
      return `
        <div class="space-y-5">
          <div>
            <div class="text-xs font-extrabold uppercase su-muted mb-1">SITUASI PERCAKAPAN 2 PEMAIN:</div>
            <h2 class="text-2xl sm:text-4xl font-black leading-tight">${escapeHtml(ch.title)}</h2>
          </div>
          <div class="p-5 rounded-2xl bg-[#FFD21E] text-black border-2 border-black flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="text-xs font-black uppercase tracking-widest mb-1">⚡ TANTANGAN WAJIB:</div>
              <div class="text-lg sm:text-2xl font-black">${escapeHtml(state.session.smallTalkConstraint)}</div>
            </div>
            <button onclick="SpeakUpApp.rerollSmallTalkConstraint()" class="su-btn bg-black text-white px-4 py-2 text-xs font-black uppercase">
              🎲 Ganti Tantangan
            </button>
          </div>
        </div>
      `;
    }

    if (mode === 'say_it_better') {
      return `
        <div class="space-y-5">
          <div class="p-5 rounded-2xl border-2 border-[#EB3B24] bg-[#EB3B24]/10">
            <div class="text-xs font-black uppercase text-[#EB3B24] mb-2">❌ KALIMAT AWAL (KURANG EFEKTIF):</div>
            <p class="text-xl sm:text-3xl font-extrabold leading-snug">${escapeHtml(ch.title)}</p>
          </div>
          <div class="p-5 rounded-2xl bg-[#FFD21E] text-black border-2 border-black">
            <div class="text-xs font-black uppercase tracking-widest mb-1">✅ INSTRUKSI PERBAIKAN:</div>
            <p class="text-xl sm:text-3xl font-black">${escapeHtml(ch.instruction)}</p>
          </div>
        </div>
      `;
    }

    // Default / 01 — Short vs Long
    return `
      <div class="space-y-5">
        <h2 class="text-3xl sm:text-5xl font-black leading-tight">${escapeHtml(ch.title)}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-4 rounded-xl border-2 ${state.session.phase === 'speak_direct' ? 'border-[#FFD21E] bg-[#FFD21E]/10' : 'border-su-border opacity-70'}">
            <div class="text-xs font-black uppercase text-[#FFD21E]">FASE 1 • DIRECT MODE (${state.settings.shortDirectDuration} DETIK)</div>
            <p class="text-xs sm:text-sm font-semibold su-muted mt-1">Langsung ke inti jawaban tanpa bertele-tele.</p>
          </div>
          <div class="p-4 rounded-xl border-2 ${state.session.phase === 'speak_elab' ? 'border-emerald-400 bg-emerald-400/10' : 'border-su-border opacity-70'}">
            <div class="text-xs font-black uppercase text-emerald-400">FASE 2 • ELABORATION (${state.settings.shortElabDuration} DETIK)</div>
            <p class="text-xs sm:text-sm font-semibold su-muted mt-1">Kembangkan topik yang sama dengan alasan, contoh & cerita.</p>
          </div>
        </div>
      </div>
    `;
  }

  function rerollSmallTalkConstraint() {
    const others = DATA.SMALL_TALK_CONSTRAINTS.filter((c) => c !== state.session.smallTalkConstraint);
    state.session.smallTalkConstraint = others[Math.floor(Math.random() * others.length)] || DATA.SMALL_TALK_CONSTRAINTS[0];
    SOUND.playChallengeAppear();
    render();
  }

  function quickSwitchMode(modeId) {
    state.settings.mode = modeId;
    const info = DATA.GAME_MODES[modeId];
    if (info) {
      state.settings.speakDuration = info.defaultSpeak;
    }
    prepareNextTurn({ pickPlayer: false, pickChallenge: true, autoSound: true });
    toast(`Mode diganti ke ${info.name}`, 'info');
  }

  function quickSwitchDifficulty(diff) {
    state.settings.difficulty = diff;
    if (state.session.currentChallenge) {
      prepareModeSpecificState(state.session.currentChallenge);
    }
    saveState();
    render();
  }

  function renderGameplayDynamicParts() {
    const timerEl = document.getElementById('gameplayTimerValue');
    const barEl = document.getElementById('gameplayProgressBar');
    const btnEl = document.getElementById('gameplayStartPauseBtn');
    const phaseEl = document.getElementById('gameplayPhaseBadge');
    const heartsEl = document.getElementById('gameplayHearts');
    const styleEl = document.getElementById('speakingStyleBanner');

    const isUrgent = state.session.remainingSec > 0 && state.session.remainingSec <= 5 && state.session.isRunning;

    if (timerEl) {
      timerEl.textContent = formatTime(state.session.remainingSec);
      timerEl.className = `font-comic text-7xl sm:text-8xl xl:text-9xl tabular-nums leading-none tracking-tight ${isUrgent ? 'text-[#EB3B24] animate-pulse' : 'text-[#FFD21E]'}`;
    }

    if (barEl) {
      const pct = state.session.totalPhaseSec > 0
        ? Math.max(0, Math.min(100, Math.round((state.session.remainingSec / state.session.totalPhaseSec) * 100)))
        : 0;
      barEl.style.width = `${pct}%`;
      barEl.className = `h-full transition-all duration-200 ${isUrgent ? 'bg-[#EB3B24]' : 'bg-[#FFD21E]'}`;
    }

    if (btnEl) {
      btnEl.textContent = state.session.isRunning
        ? '⏸ Pause (Space)'
        : (state.session.remainingSec === 0 ? '🔄 Ulangi' : '▶ Start (Space)');
    }

    if (phaseEl) {
      const lbl = getPhaseBadgeLabel();
      phaseEl.textContent = lbl.text;
      phaseEl.className = `px-3 py-1 rounded-lg font-black text-xs sm:text-sm uppercase ${lbl.cls}`;
    }

    if (heartsEl && state.settings.mode === 'forbidden_words') {
      heartsEl.innerHTML = Array.from({ length: 3 }, (_, i) =>
        `<span class="text-3xl sm:text-4xl ${i < state.session.lives ? '' : 'opacity-20 grayscale'}">❤️</span>`
      ).join('');
    }

    if (styleEl && state.settings.mode === 'speaking_switch') {
      const style = DATA.SPEAKING_STYLES[state.session.currentStyleIndex] || DATA.SPEAKING_STYLES[0];
      styleEl.style.backgroundColor = style.color;
      styleEl.innerHTML = `
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <div class="text-xs font-black uppercase tracking-widest">GAYA BERBICARA SAAT INI:</div>
            <div class="font-comic text-4xl sm:text-6xl mt-1">${escapeHtml(style.name.toUpperCase())}</div>
            <p class="text-sm sm:text-base font-extrabold mt-1">${escapeHtml(style.desc)}</p>
          </div>
          <button onclick="SpeakUpApp.triggerSpeakingStyleSwitch(true)" class="su-btn bg-black text-white px-5 py-3 text-xs sm:text-sm font-black uppercase" title="Shortcut: S">
            ⚡ Ganti Gaya Sekarang (S)
          </button>
        </div>
      `;
    }
  }

  // =========================================================================
  // ROUND RESULT (PENILAIAN MANUAL GAME MASTER)
  // =========================================================================
  function renderResult() {
    const modeInfo = DATA.GAME_MODES[state.settings.mode];
    const activePlayer = state.players.find((p) => p.id === state.session.activePlayerId);
    const total = state.scoring.scores.reduce((a, b) => a + Number(b || 0), 0);

    const criteriaRows = modeInfo.rubric.map((label, idx) => {
      const val = state.scoring.scores[idx] ?? 15;
      return `
        <div class="p-4 rounded-xl border border-su-border space-y-2">
          <div class="flex items-center justify-between gap-2">
            <span class="font-extrabold text-sm sm:text-base">${escapeHtml(label)}</span>
            <span id="scoreVal_${idx}" class="font-comic text-2xl text-[#FFD21E]">${val} / 20</span>
          </div>
          <input type="range" min="0" max="20" value="${val}" oninput="SpeakUpApp.updateCriteriaScore(${idx}, this.value)" class="w-full accent-[#FFD21E] cursor-pointer">
          <div class="flex gap-1.5 justify-end">
            ${[5, 10, 15, 18, 20].map((preset) => `
              <button onclick="SpeakUpApp.setCriteriaPreset(${idx}, ${preset})" class="su-btn su-btn-neutral px-2.5 py-1 text-[11px] font-black">${preset}</button>
            `).join('')}
          </div>
        </div>
      `;
    }).join('');

    return `
      <div class="max-w-4xl mx-auto py-8 space-y-6">
        <div class="su-card p-6 sm:p-8 border-2 border-su-border flex flex-wrap items-center justify-between gap-4">
          <div>
            <span class="px-3 py-1 rounded bg-[#FFD21E] text-black font-black text-xs uppercase">PENILAIAN RONDE ${state.session.round}</span>
            <h2 class="font-comic text-3xl sm:text-5xl mt-2">${activePlayer ? escapeHtml(activePlayer.name) : 'Pemain'}</h2>
            <p class="text-xs sm:text-sm su-muted font-bold mt-1">
              ${modeInfo.name} • "${escapeHtml(state.session.currentChallenge?.title || '')}"
            </p>
          </div>
          <div class="text-center p-4 rounded-2xl bg-[#FFD21E] text-black border-2 border-black min-w-[140px]">
            <div class="text-[11px] font-black uppercase">TOTAL SKOR</div>
            <div id="resultTotalScore" class="font-comic text-5xl leading-none mt-1">${total}</div>
            <div class="text-xs font-extrabold">/ 100 POIN</div>
          </div>
        </div>

        <div class="su-card p-6 sm:p-8 border-2 border-su-border space-y-4">
          <h3 class="font-comic text-xl">INDIKATOR PENILAIAN (${modeInfo.name.toUpperCase()})</h3>
          <div class="space-y-3">${criteriaRows}</div>

          <div class="pt-2">
            <label class="block text-xs font-extrabold uppercase su-muted mb-1.5">Catatan / Feedback Mentor (Opsional)</label>
            <textarea rows="2" oninput="SpeakUpApp.setScoringNotes(this.value)" class="su-input w-full p-3 rounded-xl font-bold text-sm" placeholder="Contoh: Intonasi mantap, kurangi kata 'ee...' di awal.">${escapeHtml(state.scoring.notes)}</textarea>
          </div>

          <div class="pt-4 border-t border-su-border flex flex-wrap items-center justify-between gap-3">
            <button onclick="SpeakUpApp.skipScoringNextRound()" class="su-btn su-btn-neutral px-4 py-3 text-xs font-extrabold uppercase">
              Lewati Nilai & Lanjut
            </button>
            <div class="flex flex-wrap gap-3">
              <button onclick="SpeakUpApp.submitRoundScore(true)" class="su-btn su-btn-neutral px-5 py-3 text-sm font-black uppercase">
                Simpan & Lihat Leaderboard
              </button>
              <button onclick="SpeakUpApp.submitRoundScore(false)" class="su-btn su-btn-yellow px-6 py-3 text-sm font-black uppercase">
                💾 Simpan & Ronde Berikutnya →
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function updateCriteriaScore(idx, val) {
    state.scoring.scores[idx] = Number(val);
    const valEl = document.getElementById(`scoreVal_${idx}`);
    if (valEl) valEl.textContent = `${val} / 20`;
    const totalEl = document.getElementById('resultTotalScore');
    if (totalEl) totalEl.textContent = state.scoring.scores.reduce((a, b) => a + Number(b || 0), 0);
  }

  function setCriteriaPreset(idx, val) {
    state.scoring.scores[idx] = val;
    render();
  }

  function setScoringNotes(val) {
    state.scoring.notes = val;
  }

  // =========================================================================
  // LEADERBOARD & RIWAYAT
  // =========================================================================
  function renderLeaderboard() {
    const tab = state.leaderboardTab;
    const players = getPlayersByType(tab === 'history' ? state.settings.playerType : tab)
      .slice()
      .sort((a, b) => (b.totalScore || 0) - (a.totalScore || 0));

    let contentHtml = '';
    if (tab === 'history') {
      contentHtml = state.history.length === 0
        ? `<div class="text-center py-12 su-muted font-bold">Belum ada riwayat ronde tercatat.</div>`
        : `<div class="space-y-3">
            ${state.history.map((h) => `
              <div class="p-4 rounded-xl border border-su-border flex flex-wrap items-center justify-between gap-3">
                <div>
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-[#FFD21E] text-black font-black text-[11px]">Ronde ${h.round}</span>
                    <span class="font-extrabold text-sm">${escapeHtml(h.playerName)}${h.partnerName ? ` & ${escapeHtml(h.partnerName)}` : ''}</span>
                    <span class="text-xs su-muted font-bold">• ${escapeHtml(h.modeName)}</span>
                  </div>
                  <p class="text-xs su-muted mt-1 font-semibold">${escapeHtml(h.challengeTitle)}</p>
                  ${h.notes ? `<p class="text-xs text-[#FFD21E] mt-1 font-bold">💬 "${escapeHtml(h.notes)}"</p>` : ''}
                </div>
                <div class="font-comic text-3xl text-[#FFD21E]">${h.totalScore} <span class="text-xs su-muted font-sans">PTS</span></div>
              </div>
            `).join('')}
          </div>`;
    } else {
      const podium = players.slice(0, 3).map((p, i) => {
        const medals = ['🥇 JUARA 1', '🥈 JUARA 2', '🥉 JUARA 3'];
        return `
          <div class="su-card p-5 border-2 ${i === 0 ? 'border-[#FFD21E]' : 'border-su-border'} text-center">
            <div class="text-xs font-black uppercase ${i === 0 ? 'text-[#FFD21E]' : 'su-muted'}">${medals[i]}</div>
            <div class="font-comic text-2xl sm:text-3xl mt-1">${escapeHtml(p.name)}</div>
            <div class="font-comic text-4xl text-[#FFD21E] mt-2">${p.totalScore || 0}</div>
            <div class="text-xs su-muted font-bold">Rata-rata: ${p.averageScore || 0} • ${p.roundsPlayed || 0} Tantangan</div>
          </div>
        `;
      }).join('');

      const tableRows = players.length === 0
        ? `<tr><td colspan="5" class="py-10 text-center su-muted font-bold">Belum ada data peserta di kategori ini.</td></tr>`
        : players.map((p, idx) => `
            <tr class="border-t border-su-border">
              <td class="py-3.5 px-4 font-black">#${idx + 1}</td>
              <td class="py-3.5 px-4 font-extrabold text-base">${escapeHtml(p.name)}</td>
              <td class="py-3.5 px-4 font-black text-[#FFD21E] text-lg">${p.totalScore || 0}</td>
              <td class="py-3.5 px-4 font-bold">${p.averageScore || 0}</td>
              <td class="py-3.5 px-4 font-bold">${p.roundsPlayed || 0}</td>
            </tr>
          `).join('');

      contentHtml = `
        ${players.length > 0 ? `<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">${podium}</div>` : ''}
        <div class="overflow-x-auto rounded-xl border border-su-border">
          <table class="w-full text-left text-sm">
            <thead class="bg-black/30 text-xs uppercase su-muted">
              <tr>
                <th class="py-3 px-4">Rank</th>
                <th class="py-3 px-4">Nama</th>
                <th class="py-3 px-4">Total Poin</th>
                <th class="py-3 px-4">Rata-rata</th>
                <th class="py-3 px-4">Tantangan Selesai</th>
              </tr>
            </thead>
            <tbody>${tableRows}</tbody>
          </table>
        </div>
      `;
    }

    return `
      <div class="max-w-6xl mx-auto py-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h2 class="font-comic text-3xl sm:text-4xl">LEADERBOARD <span class="text-[#FFD21E]">KLASEMEN</span></h2>
            <p class="text-sm su-muted font-semibold">Rekapitulasi total poin, rata-rata nilai, dan riwayat tantangan kelas.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <button onclick="SpeakUpApp.announceWinner()" class="su-btn su-btn-yellow px-4 py-2.5 text-xs font-black uppercase">
              🏆 Umumkan Pemenang
            </button>
            <button onclick="SpeakUpApp.resetScoresAction()" class="su-btn su-btn-danger px-4 py-2.5 text-xs font-black uppercase">
              🔄 Reset Skor
            </button>
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          <button onclick="SpeakUpApp.setLeaderboardTab('individual')" class="su-btn ${tab === 'individual' ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black uppercase">
            👤 Individu
          </button>
          <button onclick="SpeakUpApp.setLeaderboardTab('group')" class="su-btn ${tab === 'group' ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black uppercase">
            👥 Kelompok
          </button>
          <button onclick="SpeakUpApp.setLeaderboardTab('history')" class="su-btn ${tab === 'history' ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black uppercase">
            📜 Riwayat (${state.history.length})
          </button>
        </div>

        <div class="su-card p-6 border-2 border-su-border">
          ${contentHtml}
        </div>
      </div>
    `;
  }

  function setLeaderboardTab(tab) {
    state.leaderboardTab = tab;
    render();
  }

  // =========================================================================
  // CHALLENGE LIBRARY (CRUD + FILTERING 400+ DATA)
  // =========================================================================
  function getFilteredLibraryRowsHtml() {
    const f = state.libraryFilter;
    const filtered = state.challenges.filter((c) => {
      if (f.mode !== 'all' && c.mode !== f.mode) return false;
      if (f.category !== 'all' && c.category !== f.category) return false;
      if (f.status === 'active' && c.active === false) return false;
      if (f.status === 'inactive' && c.active !== false) return false;
      if (f.search) {
        const hay = `${c.title} ${c.category} ${c.audience || ''} ${c.instruction || ''} ${(c.forbidden || []).join(' ')}`.toLowerCase();
        if (!hay.includes(f.search.toLowerCase())) return false;
      }
      return true;
    });

    const previewItems = filtered.slice(0, 60);

    const rows = previewItems.length === 0
      ? `<div class="text-center py-12 su-muted font-bold">Tidak ada tantangan yang cocok dengan filter ini.</div>`
      : previewItems.map((c) => {
          const modeName = DATA.GAME_MODES[c.mode]?.name || c.mode;
          const isActive = c.active !== false;
          const detail = c.forbidden ? `Terlarang: ${c.forbidden.join(', ')}`
            : c.audience ? `Audiens: ${c.audience}`
            : c.instruction ? `Instruksi: ${c.instruction}`
            : c.constraint ? `Tantangan: ${c.constraint}`
            : c.mission ? `Misi: ${c.mission}`
            : '';

          return `
            <div class="p-4 rounded-xl border border-su-border flex flex-col sm:flex-row sm:items-center justify-between gap-3 ${isActive ? '' : 'opacity-50'}">
              <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="px-2 py-0.5 rounded bg-[#FFD21E] text-black font-black text-[10px] uppercase">${escapeHtml(modeName)}</span>
                  <span class="px-2 py-0.5 rounded border border-su-border text-[10px] font-extrabold">${escapeHtml(c.category)}</span>
                  <span class="text-[10px] font-extrabold ${isActive ? 'text-emerald-400' : 'text-rose-400'}">${isActive ? '● Aktif' : '○ Nonaktif'}</span>
                </div>
                <div class="font-extrabold text-sm sm:text-base">${escapeHtml(c.title)}</div>
                ${detail ? `<div class="text-xs su-muted font-semibold">${escapeHtml(detail)}</div>` : ''}
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <button onclick="SpeakUpApp.toggleChallengeActive('${c.id}')" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-bold">
                  ${isActive ? 'Nonaktifkan' : 'Aktifkan'}
                </button>
                <button onclick="SpeakUpApp.openChallengeModal('${c.id}')" class="su-btn su-btn-neutral px-3 py-1.5 text-xs font-bold">Edit</button>
                <button onclick="SpeakUpApp.deleteChallenge('${c.id}')" class="su-btn su-btn-danger px-3 py-1.5 text-xs font-bold">Hapus</button>
              </div>
            </div>
          `;
        }).join('');

    return {
      countText: `Menampilkan ${previewItems.length} dari ${filtered.length} hasil cocok`,
      rows,
    };
  }

  function renderLibrary() {
    const f = state.libraryFilter;
    const { countText, rows } = getFilteredLibraryRowsHtml();

    return `
      <div class="max-w-7xl mx-auto py-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h2 class="font-comic text-3xl sm:text-4xl">CHALLENGE <span class="text-[#FFD21E]">LIBRARY</span></h2>
            <p class="text-sm su-muted font-semibold">Total <b>${state.challenges.length}</b> tantangan tersedia. Tambah, edit, aktifkan/nonaktifkan sesuai kebutuhan kelas.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <button onclick="SpeakUpApp.openChallengeModal()" class="su-btn su-btn-yellow px-5 py-2.5 text-xs font-black uppercase">
              + Tambah Tantangan Baru
            </button>
            <button onclick="SpeakUpApp.restoreDefaultChallenges()" class="su-btn su-btn-neutral px-4 py-2.5 text-xs font-bold uppercase">
              🔄 Reset Library Default
            </button>
          </div>
        </div>

        <div class="su-card p-5 border-2 border-su-border grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <select onchange="SpeakUpApp.setLibraryFilter('mode', this.value)" class="su-input p-2.5 rounded-xl font-bold text-xs">
            <option value="all" ${f.mode === 'all' ? 'selected' : ''}>Semua Mode (${state.challenges.length})</option>
            ${Object.values(DATA.GAME_MODES).map((m) => `<option value="${m.id}" ${f.mode === m.id ? 'selected' : ''}>${m.code} — ${m.name}</option>`).join('')}
          </select>

          <select onchange="SpeakUpApp.setLibraryFilter('category', this.value)" class="su-input p-2.5 rounded-xl font-bold text-xs">
            <option value="all" ${f.category === 'all' ? 'selected' : ''}>Semua Kategori (14)</option>
            ${DATA.CATEGORIES.map((cat) => `<option value="${cat}" ${f.category === cat ? 'selected' : ''}>${cat}</option>`).join('')}
          </select>

          <select onchange="SpeakUpApp.setLibraryFilter('status', this.value)" class="su-input p-2.5 rounded-xl font-bold text-xs">
            <option value="all" ${f.status === 'all' ? 'selected' : ''}>Semua Status</option>
            <option value="active" ${f.status === 'active' ? 'selected' : ''}>Aktif Saja</option>
            <option value="inactive" ${f.status === 'inactive' ? 'selected' : ''}>Nonaktif Saja</option>
          </select>

          <input type="text" value="${escapeHtml(f.search)}" oninput="SpeakUpApp.setLibraryFilter('search', this.value)" placeholder="Cari topik atau kata..." class="su-input p-2.5 rounded-xl font-bold text-xs">
        </div>

        <div class="su-card p-6 border-2 border-su-border space-y-3">
          <div id="libraryCountText" class="text-xs font-extrabold su-muted pb-2 border-b border-su-border">
            ${countText}
          </div>
          <div id="libraryRowsContainer" class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
            ${rows}
          </div>
        </div>
      </div>
    `;
  }

  function setLibraryFilter(key, val) {
    state.libraryFilter[key] = val;
    if (key === 'search') {
      const { countText, rows } = getFilteredLibraryRowsHtml();
      const cEl = document.getElementById('libraryCountText');
      const rEl = document.getElementById('libraryRowsContainer');
      if (cEl) cEl.textContent = countText;
      if (rEl) rEl.innerHTML = rows;
      return;
    }
    render();
  }

  // =========================================================================
  // SETTINGS PAGE
  // =========================================================================
  function renderSettings() {
    return `
      <div class="max-w-4xl mx-auto py-8 space-y-6">
        <div>
          <h2 class="font-comic text-3xl sm:text-4xl">SETTINGS & <span class="text-[#FFD21E]">PREFERENSI</span></h2>
          <p class="text-sm su-muted font-semibold">Konfigurasi tampilan proyektor, efek suara, durasi standar, dan manajemen data lokal.</p>
        </div>

        <div class="su-card p-6 sm:p-8 border-2 border-su-border space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="p-4 rounded-xl border border-su-border flex items-center justify-between">
              <div>
                <div class="font-extrabold text-sm">Tema Layar Proyektor</div>
                <div class="text-xs su-muted">Pilih kontras tinggi Hitam atau Off-White</div>
              </div>
              <button onclick="SpeakUpApp.updateSetupField('theme', SpeakUpApp.getState().settings.theme === 'dark' ? 'light' : 'dark')" class="su-btn su-btn-yellow px-4 py-2 text-xs font-black uppercase">
                ${state.settings.theme === 'dark' ? '🌙 Hitam (Game Show)' : '☀️ Off-White (Terang)'}
              </button>
            </div>

            <div class="p-4 rounded-xl border border-su-border flex items-center justify-between">
              <div>
                <div class="font-extrabold text-sm">Efek Suara Game Show</div>
                <div class="text-xs su-muted">Audio timer, shuffle, buzzer & fanfare</div>
              </div>
              <button onclick="SpeakUpApp.toggleSound()" class="su-btn ${state.settings.soundEnabled ? 'su-btn-yellow' : 'su-btn-neutral'} px-4 py-2 text-xs font-black uppercase">
                ${state.settings.soundEnabled ? '🔊 AKTIF' : '🔇 NONAKTIF'}
              </button>
            </div>
          </div>

          <div class="p-5 rounded-xl border border-[#EB3B24]/50 bg-[#EB3B24]/10 flex flex-wrap items-center justify-between gap-4">
            <div>
              <div class="font-extrabold text-sm text-[#EB3B24]">Zona Berbahaya — Reset Data</div>
              <p class="text-xs su-muted mt-0.5">Mengembalikan seluruh daftar pemain, skor, dan pengaturan ke kondisi bersih.</p>
            </div>
            <div class="flex gap-2">
              <button onclick="SpeakUpApp.resetScoresAction()" class="su-btn su-btn-neutral px-4 py-2 text-xs font-bold">Reset Skor Saja</button>
              <button onclick="SpeakUpApp.factoryResetAll()" class="su-btn su-btn-danger px-4 py-2 text-xs font-black">🗑 Factory Reset</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // =========================================================================
  // BOOTSTRAP
  // =========================================================================
  function init() {
    loadState();
    SOUND.setEnabled(state.settings.soundEnabled);
    setupKeyboardShortcuts();
    render();
  }

  return {
    init,
    getState: () => state,
    navigate,
    selectModeAndSetup,
    updateSetupField,
    startNewSession,
    toggleTimer,
    resetTimer,
    adjustTime,
    enterSpeakPhase,
    triggerSpeakingStyleSwitch,
    deductLife,
    rollRandomPlayerAnimated,
    nextChallengeAction,
    endRoundAction,
    toggleFullscreenMode,
    toggleSound,
    rerollSmallTalkConstraint,
    quickSwitchMode,
    quickSwitchDifficulty,
    updateCriteriaScore,
    setCriteriaPreset,
    setScoringNotes,
    submitRoundScore,
    skipScoringNextRound,
    announceWinner,
    handleAddPlayerForm,
    seedSamplePlayers,
    editPlayerName,
    deletePlayer,
    clearPlayersByType,
    resetPlayerCycle,
    resetScoresAction,
    factoryResetAll,
    setLeaderboardTab,
    setLibraryFilter,
    toggleChallengeActive,
    deleteChallenge,
    restoreDefaultChallenges,
    openChallengeModal,
  };
})();

window.addEventListener('DOMContentLoaded', () => {
  window.SpeakUpApp.init();
});

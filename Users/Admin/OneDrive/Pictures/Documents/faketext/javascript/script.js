// Fake Text Interactivity and Real-time Live Binding

document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const inputPhone = document.getElementById('input-phone');
  const selectCarrier = document.getElementById('select-carrier');
  const inputCarrier = document.getElementById('input-carrier');
  const inputStatusTime = document.getElementById('input-status-time');
  const inputBattery = document.getElementById('input-battery');

  const headerPhoneVal = document.getElementById('header-phone-val');
  const infoPhoneVal = document.getElementById('info-phone-val');
  const infoCarrierVal = document.getElementById('info-carrier-val');
  const msgCarrierVal = document.getElementById('msg-carrier-val');
  const inputCarrierPrefix = document.getElementById('input-carrier-prefix');
  const statusTimeVal = document.getElementById('status-time-val');
  const batteryVal = document.getElementById('battery-val');

  // Shared Call Status Bar & Timestamp elements (declared early to prevent TDZ ReferenceError)
  const inputCallStatusTime = document.getElementById('input-call-status-time');
  const callStatusTimeVal = document.getElementById('call-status-time-val');
  const inputCallBattery = document.getElementById('input-call-battery');
  const callBatteryVal = document.getElementById('call-battery-val');
  const inputTimestampTime = document.getElementById('input-timestamp-time');
  const selectTimestampDivider = document.getElementById('select-timestamp-divider');
  const selectTimestampHour = document.getElementById('select-timestamp-hour');
  const selectTimestampMinute = document.getElementById('select-timestamp-minute');
  const selectTimestampAmpm = document.getElementById('select-timestamp-ampm');
  const btnTimestampWheel = document.getElementById('btn-timestamp-wheel');
  const btnTimestampNow = document.getElementById('btn-timestamp-now');

  const chatMessages = document.getElementById('chat-messages');
  const chatInputField = document.getElementById('chat-input-field');
  const micSendBtn = document.getElementById('mic-send-btn');

  const btnReset = document.getElementById('btn-reset');
  const btnToggleEdit = document.getElementById('btn-toggle-edit');
  const newMsgText = document.getElementById('new-msg-text');
  const btnAddReceived = document.getElementById('btn-add-received');
  const btnAddSent = document.getElementById('btn-add-sent');

  // Initial reference state HTML
  const initialChatHTML = chatMessages.innerHTML;

  // 1. Phone number sync
  inputPhone.addEventListener('input', (e) => {
    const val = e.target.value;
    headerPhoneVal.textContent = val;
    if (infoPhoneVal) infoPhoneVal.textContent = val;
  });

  headerPhoneVal.addEventListener('input', (e) => {
    inputPhone.value = e.target.textContent;
    if (infoPhoneVal) infoPhoneVal.textContent = e.target.textContent;
  });

  // 2. Carrier sync (shared across Message Generator & Call Logs Editor)
  function syncCarrier(val, isCustom) {
    const knownCarriers = ['DITO', 'GLOBE', 'TM', 'SMART', 'TNT'];
    const carrierName = String(val).trim();

    // Update Message Generator select & input
    if (selectCarrier) {
      if (knownCarriers.includes(carrierName) && !isCustom) {
        selectCarrier.value = carrierName;
        if (inputCarrier) inputCarrier.style.display = 'none';
      } else {
        selectCarrier.value = 'CUSTOM';
        if (inputCarrier) {
          inputCarrier.style.display = 'block';
          inputCarrier.value = carrierName;
        }
      }
    }

    // Update Call Logs Editor select & input
    const selectCallCarrier = document.getElementById('select-call-carrier');
    const inputCallCarrier = document.getElementById('input-call-carrier');
    if (selectCallCarrier) {
      if (knownCarriers.includes(carrierName) && !isCustom) {
        selectCallCarrier.value = carrierName;
        if (inputCallCarrier) inputCallCarrier.style.display = 'none';
      } else {
        selectCallCarrier.value = 'CUSTOM';
        if (inputCallCarrier) {
          inputCallCarrier.style.display = 'block';
          inputCallCarrier.value = carrierName;
        }
      }
    }

    // Update Mockups
    if (infoCarrierVal) infoCarrierVal.textContent = carrierName;
    if (msgCarrierVal) msgCarrierVal.textContent = carrierName;
    if (inputCarrierPrefix) inputCarrierPrefix.textContent = carrierName;
    if (typeof updateCallLogsCarrier === 'function') {
      updateCallLogsCarrier(carrierName);
    }
  }

  if (selectCarrier) {
    selectCarrier.addEventListener('change', (e) => {
      const choice = e.target.value;
      if (choice === 'CUSTOM') {
        if (inputCarrier) {
          inputCarrier.style.display = 'block';
          inputCarrier.value = '';
          inputCarrier.focus();
          syncCarrier('', true);
        }
      } else {
        if (inputCarrier) inputCarrier.style.display = 'none';
        syncCarrier(choice, false);
      }
    });
  }

  if (inputCarrier) {
    inputCarrier.addEventListener('input', (e) => {
      syncCarrier(e.target.value, true);
    });
  }

  // 3. Status bar time sync, 1-12 Hours options & Android Drum Wheel Picker
  const selectStatusHour = document.getElementById('select-status-hour');
  const selectStatusMinute = document.getElementById('select-status-minute');
  const selectCallStatusHour = document.getElementById('select-call-status-hour');
  const selectCallStatusMinute = document.getElementById('select-call-status-minute');

  // Populate minutes dropdowns (00 to 59)
  function populateMinuteSelect(sel, defaultVal = '12') {
    if (!sel) return;
    sel.innerHTML = '';
    for (let i = 0; i < 60; i++) {
      const opt = document.createElement('option');
      const val = String(i).padStart(2, '0');
      opt.value = val;
      opt.textContent = val;
      if (val === defaultVal) opt.selected = true;
      sel.appendChild(opt);
    }
  }
  populateMinuteSelect(selectStatusMinute, '12');
  populateMinuteSelect(selectCallStatusMinute, '12');
  if (selectTimestampMinute) populateMinuteSelect(selectTimestampMinute, '38');

  let realTimeClockInterval = null;

  function getDeviceCurrentTime() {
    const d = new Date();
    let hours = d.getHours();
    const minutes = String(d.getMinutes()).padStart(2, '0');
    hours = hours % 12;
    hours = hours ? hours : 12;
    return `${hours}:${minutes}`;
  }

  function parseHourMinute(timeStr) {
    const parts = String(timeStr || '11:12').trim().split(':');
    let h = parseInt(parts[0], 10) || 11;
    if (h < 1) h = 12;
    if (h > 12) h = ((h - 1) % 12) + 1;
    const m = parts[1] !== undefined ? String(parseInt(parts[1], 10) || 0).padStart(2, '0') : '00';
    return { hour: String(h), minute: m };
  }

  function setStatusBarTimeGlobal(timeStr) {
    const { hour, minute } = parseHourMinute(timeStr);
    const formatted = `${hour}:${minute}`;

    if (statusTimeVal) statusTimeVal.textContent = formatted;
    if (callStatusTimeVal) callStatusTimeVal.textContent = formatted;
    if (inputStatusTime) inputStatusTime.value = formatted;
    if (inputCallStatusTime) inputCallStatusTime.value = formatted;

    if (selectStatusHour) selectStatusHour.value = hour;
    if (selectStatusMinute) selectStatusMinute.value = minute;
    if (selectCallStatusHour) selectCallStatusHour.value = hour;
    if (selectCallStatusMinute) selectCallStatusMinute.value = minute;
  }

  function onTimeSelectChange() {
    toggleRealTimeClockSync(false);
    const h = selectStatusHour ? selectStatusHour.value : '11';
    const m = selectStatusMinute ? selectStatusMinute.value : '12';
    setStatusBarTimeGlobal(`${h}:${m}`);
  }

  function onCallTimeSelectChange() {
    toggleRealTimeClockSync(false);
    const h = selectCallStatusHour ? selectCallStatusHour.value : '11';
    const m = selectCallStatusMinute ? selectCallStatusMinute.value : '12';
    setStatusBarTimeGlobal(`${h}:${m}`);
  }

  if (selectStatusHour) selectStatusHour.addEventListener('change', onTimeSelectChange);
  if (selectStatusMinute) selectStatusMinute.addEventListener('change', onTimeSelectChange);
  if (selectCallStatusHour) selectCallStatusHour.addEventListener('change', onCallTimeSelectChange);
  if (selectCallStatusMinute) selectCallStatusMinute.addEventListener('change', onCallTimeSelectChange);

  function startLiveRealTimeClock() {
    stopLiveRealTimeClock();
    setStatusBarTimeGlobal(getDeviceCurrentTime());
    realTimeClockInterval = setInterval(() => {
      setStatusBarTimeGlobal(getDeviceCurrentTime());
    }, 1000);
  }

  function stopLiveRealTimeClock() {
    if (realTimeClockInterval) {
      clearInterval(realTimeClockInterval);
      realTimeClockInterval = null;
    }
  }

  const checkRealtimeClock = document.getElementById('check-realtime-clock');
  const checkCallRealtimeClock = document.getElementById('check-call-realtime-clock');
  const btnSmsNow = document.getElementById('btn-sms-now');
  const btnCallNow = document.getElementById('btn-call-now');

  function toggleRealTimeClockSync(enabled) {
    if (checkRealtimeClock) checkRealtimeClock.checked = enabled;
    if (checkCallRealtimeClock) checkCallRealtimeClock.checked = enabled;

    if (enabled) {
      startLiveRealTimeClock();
    } else {
      stopLiveRealTimeClock();
    }
  }

  if (checkRealtimeClock) {
    checkRealtimeClock.addEventListener('change', (e) => {
      toggleRealTimeClockSync(e.target.checked);
    });
  }

  if (checkCallRealtimeClock) {
    checkCallRealtimeClock.addEventListener('change', (e) => {
      toggleRealTimeClockSync(e.target.checked);
    });
  }

  if (btnSmsNow) {
    btnSmsNow.addEventListener('click', () => {
      setStatusBarTimeGlobal(getDeviceCurrentTime());
    });
  }

  if (btnCallNow) {
    btnCallNow.addEventListener('click', () => {
      setStatusBarTimeGlobal(getDeviceCurrentTime());
    });
  }

  // Set real-time clock as default on page load
  toggleRealTimeClockSync(true);

  // =========================================================================
  // Android Drum Wheel Time Picker (Supports 1-12 Hours, 00-59 Minutes & AM/PM)
  // =========================================================================
  const wheelModal = document.getElementById('wheel-time-modal');
  const wheelTimeTitle = document.getElementById('wheel-time-title');
  const wheelHoursCol = document.getElementById('wheel-hours-col');
  const wheelMinutesCol = document.getElementById('wheel-minutes-col');
  const wheelAmpmCol = document.getElementById('wheel-ampm-col');
  const btnWheelCancel = document.getElementById('btn-wheel-cancel');
  const btnWheelSet = document.getElementById('btn-wheel-set');
  const btnOpenTimePicker = document.getElementById('btn-open-time-picker');
  const btnCallOpenTimePicker = document.getElementById('btn-call-open-time-picker');
  const btnTimestampWheelPicker = document.getElementById('btn-timestamp-wheel-picker');

  let activeWheelHour = 11;
  let activeWheelMinute = 12;
  let activeWheelAmpm = 'AM';
  let wheelPickerMode = 'status'; // 'status' (2 cols) or 'timestamp' (3 cols with AM/PM)

  if (wheelHoursCol && wheelMinutesCol) {
    // Populate Hours (1-12)
    wheelHoursCol.innerHTML = '';
    for (let h = 1; h <= 12; h++) {
      const el = document.createElement('div');
      el.className = 'wheel-item';
      el.dataset.val = String(h);
      el.textContent = String(h);
      el.addEventListener('click', () => {
        scrollToWheelItem(wheelHoursCol, el);
        activeWheelHour = parseInt(el.dataset.val, 10);
        updateWheelHighlight(wheelHoursCol);
      });
      wheelHoursCol.appendChild(el);
    }

    // Populate Minutes (00-59)
    wheelMinutesCol.innerHTML = '';
    for (let m = 0; m < 60; m++) {
      const val = String(m).padStart(2, '0');
      const el = document.createElement('div');
      el.className = 'wheel-item';
      el.dataset.val = val;
      el.textContent = val;
      el.addEventListener('click', () => {
        scrollToWheelItem(wheelMinutesCol, el);
        activeWheelMinute = parseInt(el.dataset.val, 10);
        updateWheelHighlight(wheelMinutesCol);
      });
      wheelMinutesCol.appendChild(el);
    }

    // Populate AM/PM Column
    if (wheelAmpmCol) {
      wheelAmpmCol.innerHTML = '';
      ['AM', 'PM'].forEach(period => {
        const el = document.createElement('div');
        el.className = 'wheel-item';
        el.dataset.val = period;
        el.textContent = period;
        el.addEventListener('click', () => {
          scrollToWheelItem(wheelAmpmCol, el);
          activeWheelAmpm = el.dataset.val;
          updateWheelHighlight(wheelAmpmCol);
        });
        wheelAmpmCol.appendChild(el);
      });
    }

    function updateWheelHighlight(col) {
      const items = col.querySelectorAll('.wheel-item');
      const colRect = col.getBoundingClientRect();
      const centerY = colRect.top + colRect.height / 2;

      let closestItem = null;
      let minDiff = Infinity;

      items.forEach(item => {
        const itemRect = item.getBoundingClientRect();
        const itemCenter = itemRect.top + itemRect.height / 2;
        const diff = Math.abs(centerY - itemCenter);
        item.classList.remove('selected', 'adjacent');
        if (diff < minDiff) {
          minDiff = diff;
          closestItem = item;
        }
      });

      if (closestItem) {
        closestItem.classList.add('selected');
        const prev = closestItem.previousElementSibling;
        const next = closestItem.nextElementSibling;
        if (prev) prev.classList.add('adjacent');
        if (next) next.classList.add('adjacent');
        return closestItem.dataset.val;
      }
      return null;
    }

    function scrollToWheelItem(col, item) {
      if (!item) return;
      const colHeight = col.clientHeight;
      const itemHeight = item.offsetHeight;
      const targetTop = item.offsetTop - (colHeight / 2) + (itemHeight / 2);
      col.scrollTo({ top: targetTop, behavior: 'smooth' });
    }

    wheelHoursCol.addEventListener('scroll', () => {
      const h = updateWheelHighlight(wheelHoursCol);
      if (h) activeWheelHour = parseInt(h, 10);
    });

    wheelMinutesCol.addEventListener('scroll', () => {
      const m = updateWheelHighlight(wheelMinutesCol);
      if (m) activeWheelMinute = parseInt(m, 10);
    });

    if (wheelAmpmCol) {
      wheelAmpmCol.addEventListener('scroll', () => {
        const p = updateWheelHighlight(wheelAmpmCol);
        if (p) activeWheelAmpm = p;
      });
    }

    function openWheelModalCore() {
      if (wheelModal) wheelModal.style.display = 'flex';

      setTimeout(() => {
        const hItem = Array.from(wheelHoursCol.querySelectorAll('.wheel-item')).find(el => el.dataset.val === String(activeWheelHour));
        const mItem = Array.from(wheelMinutesCol.querySelectorAll('.wheel-item')).find(el => el.dataset.val === String(activeWheelMinute).padStart(2, '0'));
        if (hItem) scrollToWheelItem(wheelHoursCol, hItem);
        if (mItem) scrollToWheelItem(wheelMinutesCol, mItem);

        if (wheelPickerMode === 'timestamp' && wheelAmpmCol) {
          const ampmItem = Array.from(wheelAmpmCol.querySelectorAll('.wheel-item')).find(el => el.dataset.val === activeWheelAmpm);
          if (ampmItem) scrollToWheelItem(wheelAmpmCol, ampmItem);
          updateWheelHighlight(wheelAmpmCol);
        }

        updateWheelHighlight(wheelHoursCol);
        updateWheelHighlight(wheelMinutesCol);
      }, 50);
    }

    function openWheelTimePickerForStatus() {
      wheelPickerMode = 'status';
      if (wheelAmpmCol) wheelAmpmCol.style.display = 'none';
      if (wheelTimeTitle) wheelTimeTitle.textContent = 'Select Status Bar Time (1-12 Hours)';

      const cur = parseHourMinute(statusTimeVal?.textContent || '11:12');
      activeWheelHour = parseInt(cur.hour, 10);
      activeWheelMinute = parseInt(cur.minute, 10);
      openWheelModalCore();
    }

    function parseTimestampTimeString(str) {
      const text = (str || '9:38 AM').trim();
      const match = text.match(/(\d{1,2}):(\d{2})(?:\s*([AaPp][Mm]))?/);
      if (match) {
        let h = parseInt(match[1], 10) || 12;
        if (h < 1) h = 12;
        if (h > 12) h = ((h - 1) % 12) + 1;
        const m = parseInt(match[2], 10) || 0;
        const ampm = (match[3] || 'AM').toUpperCase();
        return { hour: h, minute: m, ampm };
      }
      return { hour: 9, minute: 38, ampm: 'AM' };
    }

    function openWheelTimePickerForTimestamp() {
      wheelPickerMode = 'timestamp';
      if (wheelAmpmCol) wheelAmpmCol.style.display = 'flex';
      if (wheelTimeTitle) wheelTimeTitle.textContent = 'Select Timestamp Time (1-12 Hours AM/PM)';

      const rawVal = inputTimestampTime ? inputTimestampTime.value : '9:38 AM';
      const parsed = parseTimestampTimeString(rawVal);
      activeWheelHour = parsed.hour;
      activeWheelMinute = parsed.minute;
      activeWheelAmpm = parsed.ampm;
      openWheelModalCore();
    }

    function closeWheelTimePicker() {
      if (wheelModal) wheelModal.style.display = 'none';
    }

    // Bind Status Bar triggers
    if (btnOpenTimePicker) btnOpenTimePicker.addEventListener('click', openWheelTimePickerForStatus);
    if (btnCallOpenTimePicker) btnCallOpenTimePicker.addEventListener('click', openWheelTimePickerForStatus);
    if (statusTimeVal) {
      statusTimeVal.style.cursor = 'pointer';
      statusTimeVal.title = 'Click to open Time Picker';
      statusTimeVal.addEventListener('click', openWheelTimePickerForStatus);
    }
    if (callStatusTimeVal) {
      callStatusTimeVal.style.cursor = 'pointer';
      callStatusTimeVal.title = 'Click to open Time Picker';
      callStatusTimeVal.addEventListener('click', openWheelTimePickerForStatus);
    }

    // Bind Timestamp triggers (with AM/PM) - clicking the field automatically opens wheel
    if (btnTimestampWheelPicker) btnTimestampWheelPicker.addEventListener('click', openWheelTimePickerForTimestamp);
    if (inputTimestampTime) {
      inputTimestampTime.style.cursor = 'pointer';
      inputTimestampTime.addEventListener('click', openWheelTimePickerForTimestamp);
    }

    if (btnWheelCancel) btnWheelCancel.addEventListener('click', closeWheelTimePicker);
    if (btnWheelSet) {
      btnWheelSet.addEventListener('click', () => {
        const mStr = String(activeWheelMinute).padStart(2, '0');
        if (wheelPickerMode === 'status') {
          toggleRealTimeClockSync(false);
          setStatusBarTimeGlobal(`${activeWheelHour}:${mStr}`);
        } else {
          // Timestamp mode with AM/PM
          const formatted = `${activeWheelHour}:${mStr} ${activeWheelAmpm}`;
          if (typeof setTimestampDropdowns === 'function') {
            setTimestampDropdowns(formatted);
          }
          if (inputTimestampTime) {
            inputTimestampTime.value = formatted;
            inputTimestampTime.dispatchEvent(new Event('input', { bubbles: true }));
          }
        }
        closeWheelTimePicker();
      });
    }

    if (wheelModal) {
      wheelModal.addEventListener('click', (e) => {
        if (e.target === wheelModal) closeWheelTimePicker();
      });
    }
  }

  // 4. Battery sync (shared across SMS & Call Logs)
  inputBattery.addEventListener('input', (e) => {
    const val = e.target.value;
    batteryVal.textContent = val;
    if (callBatteryVal) callBatteryVal.textContent = val;
    if (inputCallBattery) inputCallBattery.value = val;
  });

  batteryVal.addEventListener('input', (e) => {
    const val = e.target.textContent;
    inputBattery.value = val;
    if (callBatteryVal) callBatteryVal.textContent = val;
    if (inputCallBattery) inputCallBattery.value = val;
  });

  // 4b. Avatar Profile Customization & Randomization
  const avatarCircle = document.getElementById('avatar-circle');
  const avatarIconSvg = document.getElementById('avatar-icon-svg');
  const avatarSwatchesContainer = document.getElementById('avatar-swatches');
  const inputAvatarColor = document.getElementById('input-avatar-color');
  const avatarHexBadge = document.getElementById('avatar-hex-badge');
  const btnRandomAvatar = document.getElementById('btn-random-avatar');
  const btnIconWhite = document.getElementById('btn-icon-white');
  const btnIconDark = document.getElementById('btn-icon-dark');

  // Curated Google Messages / Android contacts palette (exact matches to reference images)
  const AVATAR_PALETTE = [
    '#fa508f', // Hot Pink
    '#9d4edd', // Vibrant Purple
    '#fb923c', // Warm Orange
    '#38bdf8', // Sky Blue / Cyan
    '#4ade80', // Emerald / Android Green
    '#ef4444', // Coral Red
    '#00b4d8', // Deep Aqua / Teal
    '#a855f7', // Vivid Violet
    '#f43f5e', // Rose Pink
    '#e11d48', // Crimson Red
    '#3b82f6', // Google Blue
    '#f59e0b', // Amber
  ];

  function normalizeHex(hex) {
    if (!hex) return '';
    hex = hex.trim();
    if (!hex.startsWith('#')) hex = '#' + hex;
    return hex.toLowerCase();
  }

  function getRandomAvatarColor(excludeColor = null) {
    const normExclude = excludeColor ? normalizeHex(excludeColor) : null;
    const candidates = normExclude 
      ? AVATAR_PALETTE.filter(c => normalizeHex(c) !== normExclude)
      : AVATAR_PALETTE;
    const list = candidates.length > 0 ? candidates : AVATAR_PALETTE;
    return list[Math.floor(Math.random() * list.length)];
  }

  function setAvatarColor(color) {
    if (!color) return;
    const hex = normalizeHex(color);
    document.documentElement.style.setProperty('--avatar-bg', hex);
    if (avatarCircle) {
      avatarCircle.style.backgroundColor = hex;
    }
    if (inputAvatarColor) {
      inputAvatarColor.value = hex;
    }
    if (avatarHexBadge) {
      avatarHexBadge.textContent = hex.toUpperCase();
    }

    if (avatarSwatchesContainer) {
      const swatches = avatarSwatchesContainer.querySelectorAll('.avatar-swatch');
      swatches.forEach(swatch => {
        if (normalizeHex(swatch.dataset.color) === hex) {
          swatch.classList.add('active');
        } else {
          swatch.classList.remove('active');
        }
      });
    }

    try {
      sessionStorage.setItem('last_avatar_color', hex);
    } catch (e) {}
  }

  function setAvatarIconColor(iconColor) {
    document.documentElement.style.setProperty('--avatar-icon', iconColor);
    if (avatarIconSvg) {
      avatarIconSvg.style.fill = iconColor;
    }
    if (iconColor.toLowerCase() === '#ffffff') {
      btnIconWhite?.classList.add('active');
      btnIconDark?.classList.remove('active');
    } else {
      btnIconDark?.classList.add('active');
      btnIconWhite?.classList.remove('active');
    }
  }

  // Populate swatches in the control panel
  if (avatarSwatchesContainer) {
    avatarSwatchesContainer.innerHTML = '';
    AVATAR_PALETTE.forEach(color => {
      const swatch = document.createElement('button');
      swatch.type = 'button';
      swatch.className = 'avatar-swatch';
      swatch.dataset.color = color;
      swatch.style.backgroundColor = color;
      swatch.title = `Color: ${color}`;
      swatch.setAttribute('aria-label', `Select avatar color ${color}`);
      swatch.addEventListener('click', () => {
        setAvatarColor(color);
      });
      avatarSwatchesContainer.appendChild(swatch);
    });
  }

  // Automatically randomize avatar color every time the link is refreshed or reopened
  let lastAvatarColor = null;
  try {
    lastAvatarColor = sessionStorage.getItem('last_avatar_color');
  } catch (e) {}
  const initialRandomColor = getRandomAvatarColor(lastAvatarColor);
  setAvatarColor(initialRandomColor);
  setAvatarIconColor('#ffffff');

  // Manual Color Picker
  if (inputAvatarColor) {
    inputAvatarColor.addEventListener('input', (e) => {
      setAvatarColor(e.target.value);
    });
    inputAvatarColor.addEventListener('change', (e) => {
      setAvatarColor(e.target.value);
    });
  }

  // Randomize Button in panel
  if (btnRandomAvatar) {
    btnRandomAvatar.addEventListener('click', () => {
      const current = inputAvatarColor ? inputAvatarColor.value : null;
      setAvatarColor(getRandomAvatarColor(current));
    });
  }

  // Clicking avatar circle directly on phone screen randomizes color
  if (avatarCircle) {
    avatarCircle.addEventListener('click', () => {
      const current = inputAvatarColor ? inputAvatarColor.value : null;
      setAvatarColor(getRandomAvatarColor(current));
    });
  }

  // Icon silhouette toggle
  btnIconWhite?.addEventListener('click', () => setAvatarIconColor('#ffffff'));
  btnIconDark?.addEventListener('click', () => setAvatarIconColor('#201f24'));

  // 5. Day / Date & Dynamic Timestamps / Dividers Controls
  const selectDayPreset = document.getElementById('select-day-preset');
  const wrapperCustomDay = document.getElementById('wrapper-custom-day');
  const inputCustomDay = document.getElementById('input-custom-day');
  const btnAddTimestamp = document.getElementById('btn-add-timestamp');
  const btnRemoveTimestamp = document.getElementById('btn-remove-timestamp');

  function getActiveDayString() {
    if (selectDayPreset && selectDayPreset.value === 'custom') {
      return (inputCustomDay && inputCustomDay.value.trim()) || 'Today';
    }
    return (selectDayPreset && selectDayPreset.value) || 'Yesterday';
  }

  function getOrdinal(n) {
    const s = ['th', 'st', 'nd', 'rd'];
    const v = n % 100;
    return n + (s[(v - 20) % 10] || s[v] || s[0]);
  }

  function parseDividerTime(text) {
    if (!text) return '12:00 PM';
    const parts = text.split('•');
    if (parts.length > 1) {
      return parts[1].trim();
    }
    return text.trim();
  }

  function getAllDividers() {
    if (!chatMessages) return [];
    return Array.from(chatMessages.querySelectorAll('.timestamp-center'));
  }

  function refreshDividerDropdown(targetIdx = null) {
    if (!selectTimestampDivider) return;
    const dividers = getAllDividers();
    const prevVal = targetIdx !== null ? targetIdx : parseInt(selectTimestampDivider.value, 10) || 0;

    selectTimestampDivider.innerHTML = '';

    if (dividers.length === 0) {
      const opt = document.createElement('option');
      opt.value = '-1';
      opt.textContent = 'No Dividers in Chat';
      selectTimestampDivider.appendChild(opt);
      if (inputTimestampTime) {
        inputTimestampTime.value = '';
        inputTimestampTime.disabled = true;
      }
      if (btnRemoveTimestamp) {
        btnRemoveTimestamp.disabled = true;
        btnRemoveTimestamp.style.opacity = '0.5';
      }
      return;
    }

    if (inputTimestampTime) inputTimestampTime.disabled = false;
    if (btnRemoveTimestamp) {
      btnRemoveTimestamp.disabled = false;
      btnRemoveTimestamp.style.opacity = '1';
    }

    dividers.forEach((div, i) => {
      const time = parseDividerTime(div.textContent);
      const opt = document.createElement('option');
      opt.value = String(i);
      opt.textContent = `${getOrdinal(i + 1)} Timestamp (${time})`;
      selectTimestampDivider.appendChild(opt);
    });

    const activeIdx = Math.max(0, Math.min(prevVal, dividers.length - 1));
    selectTimestampDivider.value = String(activeIdx);
    if (dividers[activeIdx]) {
      const curTime = parseDividerTime(dividers[activeIdx].textContent);
      if (inputTimestampTime) inputTimestampTime.value = curTime;
      setTimestampDropdowns(curTime);
    }
  }

  function setTimestampDropdowns(timeStr) {
    const parsed = parseTimestampTimeString(timeStr);
    if (selectTimestampHour) selectTimestampHour.value = String(parsed.hour);
    if (selectTimestampMinute) selectTimestampMinute.value = String(parsed.minute).padStart(2, '0');
    if (selectTimestampAmpm) selectTimestampAmpm.value = parsed.ampm;
    if (inputTimestampTime) inputTimestampTime.value = `${parsed.hour}:${String(parsed.minute).padStart(2, '0')} ${parsed.ampm}`;
  }

  function applyTimestampFromDropdowns() {
    const h = selectTimestampHour ? selectTimestampHour.value : '9';
    const m = selectTimestampMinute ? selectTimestampMinute.value : '38';
    const ampm = selectTimestampAmpm ? selectTimestampAmpm.value : 'AM';
    const formatted = `${h}:${m} ${ampm}`;
    if (inputTimestampTime) inputTimestampTime.value = formatted;

    const idx = parseInt(selectTimestampDivider?.value, 10);
    const dividers = getAllDividers();
    if (dividers[idx]) {
      const day = getActiveDayString();
      dividers[idx].textContent = `${day} • ${formatted}`;
      const opt = selectTimestampDivider.options[selectTimestampDivider.selectedIndex];
      if (opt) {
        opt.textContent = `${getOrdinal(idx + 1)} Timestamp (${formatted})`;
      }
    }
  }

  if (selectTimestampHour) selectTimestampHour.addEventListener('change', applyTimestampFromDropdowns);
  if (selectTimestampMinute) selectTimestampMinute.addEventListener('change', applyTimestampFromDropdowns);
  if (selectTimestampAmpm) selectTimestampAmpm.addEventListener('change', applyTimestampFromDropdowns);

  if (btnTimestampNow) {
    btnTimestampNow.addEventListener('click', () => {
      const d = new Date();
      let hours = d.getHours();
      const minutes = String(d.getMinutes()).padStart(2, '0');
      const ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12 || 12;
      setTimestampDropdowns(`${hours}:${minutes} ${ampm}`);
      applyTimestampFromDropdowns();
    });
  }

  if (btnTimestampWheel) {
    btnTimestampWheel.addEventListener('click', openWheelTimePickerForTimestamp);
  }

  // When user selects a different divider from dropdown
  if (selectTimestampDivider) {
    selectTimestampDivider.addEventListener('change', () => {
      const idx = parseInt(selectTimestampDivider.value, 10);
      const dividers = getAllDividers();
      if (dividers[idx]) {
        const curTime = parseDividerTime(dividers[idx].textContent);
        if (inputTimestampTime) inputTimestampTime.value = curTime;
        setTimestampDropdowns(curTime);
      }
    });
  }

  // When user edits timestamp time in input field
  if (inputTimestampTime) {
    inputTimestampTime.addEventListener('input', () => {
      const idx = parseInt(selectTimestampDivider?.value, 10);
      const dividers = getAllDividers();
      if (dividers[idx]) {
        const timeVal = inputTimestampTime.value.trim();
        const day = getActiveDayString();
        dividers[idx].textContent = `${day} • ${timeVal}`;

        // Update the option text in the dropdown in real time
        const opt = selectTimestampDivider.options[selectTimestampDivider.selectedIndex];
        if (opt) {
          opt.textContent = `${getOrdinal(idx + 1)} Timestamp (${timeVal})`;
        }
      }
    });
  }

  // Day preset changes
  function updateAllDividerDays() {
    const day = getActiveDayString();
    const dividers = getAllDividers();
    dividers.forEach((div) => {
      const time = parseDividerTime(div.textContent);
      div.textContent = `${day} • ${time}`;
    });
    refreshDividerDropdown();
  }

  if (selectDayPreset) {
    selectDayPreset.addEventListener('change', () => {
      if (selectDayPreset.value === 'custom') {
        if (wrapperCustomDay) wrapperCustomDay.style.display = 'flex';
      } else {
        if (wrapperCustomDay) wrapperCustomDay.style.display = 'none';
      }
      updateAllDividerDays();
    });
  }

  if (inputCustomDay) {
    inputCustomDay.addEventListener('input', updateAllDividerDays);
  }

  // Insert New Divider Button - automatically lists new timestamp on the dropdown!
  if (btnAddTimestamp) {
    btnAddTimestamp.addEventListener('click', () => {
      const day = getActiveDayString();
      const d = new Date();
      let hours = d.getHours();
      const minutes = String(d.getMinutes()).padStart(2, '0');
      const ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12 || 12;
      const defaultTime = `${hours}:${minutes} ${ampm}`;

      const ts = document.createElement('div');
      ts.className = 'timestamp-center';
      ts.setAttribute('contenteditable', 'true');
      ts.textContent = `${day} • ${defaultTime}`;
      chatMessages.appendChild(ts);
      chatMessages.scrollTop = chatMessages.scrollHeight;

      // Refresh dropdown and select the newly inserted divider
      const newDividers = getAllDividers();
      const newIdx = newDividers.length - 1;
      refreshDividerDropdown(newIdx);
      openWheelTimePickerForTimestamp();
    });
  }

  // Remove Divider Button
  if (btnRemoveTimestamp) {
    btnRemoveTimestamp.addEventListener('click', () => {
      const idx = parseInt(selectTimestampDivider?.value, 10);
      const dividers = getAllDividers();
      if (dividers[idx]) {
        dividers[idx].remove();
      } else if (dividers.length > 0) {
        dividers[dividers.length - 1].remove();
      }
      refreshDividerDropdown(Math.max(0, idx - 1));
    });
  }

  // Keep dropdown updated when editing inline on phone screen
  if (chatMessages) {
    chatMessages.addEventListener('input', (e) => {
      if (e.target.classList.contains('timestamp-center')) {
        const idx = parseInt(selectTimestampDivider?.value, 10);
        const dividers = getAllDividers();
        if (dividers[idx] === e.target && inputTimestampTime) {
          inputTimestampTime.value = parseDividerTime(e.target.textContent);
        }
        refreshDividerDropdown();
      }
    });

    // Clicking a timestamp divider on the phone screen selects it and automatically opens the AM/PM Wheel Picker
    chatMessages.addEventListener('click', (e) => {
      const ts = e.target.closest('.timestamp-center');
      if (ts && typeof openWheelTimePickerForTimestamp === 'function') {
        const dividers = getAllDividers();
        const idx = dividers.indexOf(ts);
        if (idx !== -1 && selectTimestampDivider) {
          selectTimestampDivider.value = String(idx);
          if (inputTimestampTime) {
            inputTimestampTime.value = parseDividerTime(ts.textContent);
          }
        }
        openWheelTimePickerForTimestamp();
      }
    });
  }

  // Default Timestamp Time to realtime on initial load
  function getDeviceCurrentTimestampTime() {
    const d = new Date();
    let hours = d.getHours();
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `${hours}:${minutes} ${ampm}`;
  }

  const initialRealtime = getDeviceCurrentTimestampTime();
  const firstDivider = document.getElementById('timestamp-1');
  if (firstDivider) {
    const day = getActiveDayString();
    firstDivider.textContent = `${day} • ${initialRealtime}`;
  }
  setTimestampDropdowns(initialRealtime);

  // Initial populate of divider dropdown
  refreshDividerDropdown(0);

  // 6. Typography live controls
  const selectFontFamily = document.getElementById('select-font-family');
  const inputFontSize = document.getElementById('input-font-size');
  const selectFontWeight = document.getElementById('select-font-weight');
  const inputLetterSpacing = document.getElementById('input-letter-spacing');

  if (selectFontFamily) {
    selectFontFamily.addEventListener('change', (e) => {
      document.documentElement.style.setProperty('--font-family', e.target.value);
    });
  }

  if (inputFontSize) {
    inputFontSize.addEventListener('input', (e) => {
      document.documentElement.style.setProperty('--bubble-font-size', `${e.target.value}px`);
    });
  }

  if (selectFontWeight) {
    selectFontWeight.addEventListener('change', (e) => {
      document.documentElement.style.setProperty('--bubble-font-weight', e.target.value);
    });
  }

  if (inputLetterSpacing) {
    inputLetterSpacing.addEventListener('input', (e) => {
      document.documentElement.style.setProperty('--bubble-letter-spacing', `${e.target.value}px`);
    });
  }

  // 6. Add message function with Hover Action Toolbar
  const actionToolbarHTML = `
    <div class="msg-action-toolbar" data-html2canvas-ignore="true">
      <button class="msg-action-btn edit-msg-btn" title="Edit message" aria-label="Edit">
        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 20h9"></path>
          <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
        </svg>
      </button>
      <button class="msg-action-btn delete-msg-btn" title="Delete message" aria-label="Delete">
        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"></polyline>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        </svg>
      </button>
    </div>
  `;

  function appendMessage(text, isSent = true, isAddress = false) {
    if (!text.trim()) return;

    const row = document.createElement('div');
    row.className = `message-row ${isSent ? 'sent-row' : 'received-row'}`;

    if (isSent) {
      row.innerHTML = `
        ${actionToolbarHTML}
        <div class="bubble sent-bubble" contenteditable="true">${escapeHTML(text)}</div>
      `;
    } else {
      row.innerHTML = `
        ${actionToolbarHTML}
        <div class="bubble received-bubble" contenteditable="true">${escapeHTML(text)}</div>
      `;
    }

    chatMessages.appendChild(row);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Handle Edit and Delete on hover action toolbar
  chatMessages.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.edit-msg-btn');
    const deleteBtn = e.target.closest('.delete-msg-btn');

    if (editBtn) {
      e.stopPropagation();
      const row = editBtn.closest('.message-row');
      if (row) {
        const bubble = row.querySelector('.bubble');
        if (bubble) {
          bubble.focus();
          // Move cursor to end of text
          const range = document.createRange();
          const sel = window.getSelection();
          range.selectNodeContents(bubble);
          range.collapse(false);
          sel.removeAllRanges();
          sel.addRange(range);
        }
      }
    }

    if (deleteBtn) {
      e.stopPropagation();
      const row = deleteBtn.closest('.message-row');
      if (row) {
        row.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
        row.style.opacity = '0';
        row.style.transform = 'scale(0.95)';
        setTimeout(() => {
          row.remove();
        }, 200);
      }
    }
  });

  function escapeHTML(str) {
    return str.replace(/[&<>'"]/g, 
      tag => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
      }[tag] || tag)
    );
  }

  // Add message buttons
  btnAddSent.addEventListener('click', () => {
    if (newMsgText.value.trim()) {
      appendMessage(newMsgText.value.trim(), true);
      newMsgText.value = '';
    }
  });

  btnAddReceived.addEventListener('click', () => {
    if (newMsgText.value.trim()) {
      appendMessage(newMsgText.value.trim(), false);
      newMsgText.value = '';
    }
  });

  // Send from chat input field directly
  chatInputField.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && chatInputField.value.trim()) {
      appendMessage(chatInputField.value.trim(), true);
      chatInputField.value = '';
    }
  });

  micSendBtn.addEventListener('click', () => {
    if (chatInputField.value.trim()) {
      appendMessage(chatInputField.value.trim(), true);
      chatInputField.value = '';
    }
  });

  // Suggestion chips click action
  const chips = document.querySelectorAll('.chip');
  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      appendMessage(chip.textContent.trim(), true);
    });
  });

  // Reset to original image replica
  btnReset.addEventListener('click', () => {
    chatMessages.innerHTML = initialChatHTML;
    inputPhone.value = '0921 358 2262';
    inputCarrier.value = 'DITO';
    inputStatusTime.value = '11:12';
    inputBattery.value = '54';
    
    headerPhoneVal.textContent = '0921 358 2262';
    statusTimeVal.textContent = '11:12';
    batteryVal.textContent = '54';
    if (inputCarrierPrefix) inputCarrierPrefix.textContent = 'DITO';

    if (selectDayPreset) selectDayPreset.value = 'Yesterday';
    if (wrapperCustomDay) wrapperCustomDay.style.display = 'none';
    if (inputCustomDay) inputCustomDay.value = '';
    const resetRealtime = getDeviceCurrentTimestampTime();
    const rDiv = document.getElementById('timestamp-1');
    if (rDiv) rDiv.textContent = `Yesterday • ${resetRealtime}`;
    setTimestampDropdowns(resetRealtime);
    refreshDividerDropdown(0);

    if (selectFontFamily) selectFontFamily.value = "'Roboto', sans-serif";
    if (inputFontSize) inputFontSize.value = 15;
    if (selectFontWeight) selectFontWeight.value = '400';
    if (inputLetterSpacing) inputLetterSpacing.value = 0;

    document.documentElement.style.setProperty('--font-family', "'Roboto', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif");
    document.documentElement.style.setProperty('--bubble-font-size', '15px');
    document.documentElement.style.setProperty('--bubble-font-weight', '400');
    document.documentElement.style.setProperty('--bubble-letter-spacing', '0px');

    setAvatarColor(getRandomAvatarColor());
    setAvatarIconColor('#ffffff');

    if (checkSmsBottomNav) {
      checkSmsBottomNav.checked = true;
      if (smsAndroidNavBar) smsAndroidNavBar.style.display = 'flex';
      if (smsPhoneScreen) smsPhoneScreen.classList.remove('hide-nav');
    }
  });

  // SMS Android 3-Button Nav Bar toggle
  const checkSmsBottomNav = document.getElementById('check-sms-bottom-nav');
  const smsAndroidNavBar = document.getElementById('sms-android-nav-bar');
  const smsPhoneScreen = document.querySelector('.phone-screen:not(.call-phone-screen)');

  if (checkSmsBottomNav && smsAndroidNavBar) {
    checkSmsBottomNav.addEventListener('change', (e) => {
      smsAndroidNavBar.style.display = e.target.checked ? 'flex' : 'none';
      if (smsPhoneScreen) {
        if (e.target.checked) smsPhoneScreen.classList.remove('hide-nav');
        else smsPhoneScreen.classList.add('hide-nav');
      }
    });
  }

  // 7. Download Screenshot (1080x2340/2400 Android Standard Size)
  const btnDownload = document.getElementById('btn-download');
  if (btnDownload) {
    btnDownload.addEventListener('click', async () => {
      if (typeof html2canvas === 'undefined') {
        alert('Screenshot generator is still loading. Please try again in a second.');
        return;
      }

      // 1. Blur any active focus so edit borders don't appear in screenshot
      if (document.activeElement && document.activeElement.blur) {
        document.activeElement.blur();
      }

      const phoneScreen = document.getElementById('phone-screen');
      const originalText = btnDownload.innerHTML;
      btnDownload.innerHTML = `⏳ Generating Screenshot...`;
      btnDownload.style.opacity = '0.7';
      btnDownload.style.pointerEvents = 'none';

      try {
        // Standard Android high-res scale (1080px width: 1080 / 412 = 2.621)
        const canvas = await html2canvas(phoneScreen, {
          scale: 2.62136,
          useCORS: true,
          allowTaint: true,
          logging: false,
          backgroundColor: '#131314',
          windowWidth: document.documentElement.offsetWidth,
          windowHeight: document.documentElement.offsetHeight,
          onclone: (clonedDoc) => {
            // Remove hover action buttons from screenshot
            clonedDoc.querySelectorAll('.msg-action-toolbar').forEach(el => el.remove());

            // Make downloaded screenshot a true rectangular device screen without frame border radius
            const clonedPhone = clonedDoc.getElementById('phone-screen');
            if (clonedPhone) {
              clonedPhone.style.borderRadius = '0px';
              clonedPhone.style.boxShadow = 'none';
            }

            // 1. Info line: uniform flex gap for perfectly equal spacing between all words and numbers
            const clonedInfoLine = clonedDoc.querySelector('.info-line');
            if (clonedInfoLine) {
              const phoneVal = (clonedDoc.getElementById('info-phone-val')?.textContent || '0921 358 2262').trim();
              const allTokens = `Texting with ${phoneVal} (SMS/MMS)`.split(/\s+/);
              
              clonedInfoLine.style.display = 'flex';
              clonedInfoLine.style.justifyContent = 'center';
              clonedInfoLine.style.alignItems = 'center';
              clonedInfoLine.style.gap = '5px';
              clonedInfoLine.style.whiteSpace = 'nowrap';
              clonedInfoLine.style.width = '100%';
              clonedInfoLine.innerHTML = allTokens.map(tok => `<span>${tok}</span>`).join('');
            }

            // 2. Header phone number: uniform flex gap for equal spacing between number chunks
            const clonedHeaderPhone = clonedDoc.getElementById('header-phone-val');
            if (clonedHeaderPhone) {
              const hTokens = (clonedHeaderPhone.textContent || '0921 358 2262').trim().split(/\s+/);
              clonedHeaderPhone.style.display = 'flex';
              clonedHeaderPhone.style.alignItems = 'center';
              clonedHeaderPhone.style.gap = '6px';
              clonedHeaderPhone.innerHTML = hTokens.map(t => `<span>${t}</span>`).join('');
            }

            // 3. Info sub line: uniform flex gap
            const clonedInfoSub = clonedDoc.querySelector('.info-line-sub');
            if (clonedInfoSub) {
              const carrierVal = (clonedDoc.getElementById('info-carrier-val')?.textContent || 'DITO').trim();
              clonedInfoSub.style.display = 'flex';
              clonedInfoSub.style.justifyContent = 'center';
              clonedInfoSub.style.alignItems = 'center';
              clonedInfoSub.style.gap = '5px';
              clonedInfoSub.style.whiteSpace = 'nowrap';
              clonedInfoSub.style.width = '100%';
              clonedInfoSub.innerHTML = `<span>Sending</span><span>with</span><u style="text-decoration:underline; text-underline-offset:2px;">${carrierVal}</u>`;
            }

            // 4. Input placeholder: uniform flex gap
            const clonedInput = clonedDoc.getElementById('chat-input-field');
            if (clonedInput) {
              const wrapper = clonedDoc.createElement('div');
              wrapper.style.display = 'flex';
              wrapper.style.alignItems = 'center';
              wrapper.style.gap = '5px';
              wrapper.style.color = clonedInput.value ? '#ffffff' : '#9ea1a7';
              wrapper.style.fontSize = '15.5px';
              wrapper.style.fontFamily = 'inherit';
              wrapper.style.whiteSpace = 'nowrap';
              
              const val = (clonedInput.value || clonedInput.placeholder || 'Text message').trim();
              const words = val.split(/\s+/);
              wrapper.innerHTML = words.map(w => `<span>${w}</span>`).join('');

              clonedInput.parentNode.replaceChild(wrapper, clonedInput);
            }
          }
        });

        // Format Android screenshot filename: Screenshot_YYYYMMDD-HHMMSS_Messages.png
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        const filename = `Screenshot_${year}${month}${day}-${hours}${mins}${secs}_Messages.png`;

        // Trigger download
        const imageURL = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = filename;
        link.href = imageURL;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        btnDownload.innerHTML = `✅ Saved as PNG!`;
        setTimeout(() => {
          btnDownload.innerHTML = originalText;
          btnDownload.style.opacity = '1';
          btnDownload.style.pointerEvents = 'auto';
        }, 2000);
      } catch (err) {
        console.error('Screenshot capture failed:', err);
        alert('Failed to generate screenshot. Please check console for details.');
        btnDownload.innerHTML = originalText;
        btnDownload.style.opacity = '1';
        btnDownload.style.pointerEvents = 'auto';
      }
    });
  }

  // ========================================================
  // 8. CALL LOGS EDITOR (Google Phone Call History Replica)
  // ========================================================
  const inputCallPhone = document.getElementById('input-call-phone');
  const inputCallLocation = document.getElementById('input-call-location');
  const inputCallCarrier = document.getElementById('input-call-carrier');
  const inputCallSectionTitle = document.getElementById('input-call-section-title');

  const inputCallNetSpeed = document.getElementById('input-call-net-speed');
  const callNetSpeedVal = document.getElementById('call-net-speed-val');
  const checkCallChatIcon = document.getElementById('check-call-chat-icon');
  const callStatusChatIcon = document.getElementById('call-status-chat-icon');

  const callPhoneVal = document.getElementById('call-phone-val');
  const callLocationVal = document.getElementById('call-location-val');
  const selectCallSectionTitle = document.getElementById('select-call-section-title');
  const selectNewCallSection = document.getElementById('select-new-call-section');
  const callBody = document.getElementById('call-body');

  const selectCallType = document.getElementById('select-call-type');
  const inputNewCallTime = document.getElementById('input-new-call-time');
  const inputNewCallDuration = document.getElementById('input-new-call-duration');
  const checkNewCallHd = document.getElementById('check-new-call-hd');
  const btnAddLayer = document.getElementById('btn-add-layer') || document.getElementById('btn-add-call-log');
  const btnRemoveLayer = document.getElementById('btn-remove-layer');
  const callLayerBadge = document.getElementById('call-layer-badge');
  const durationPreviewBadge = document.getElementById('duration-preview-badge');

  const btnCallPresetLatest = document.getElementById('btn-call-preset-image-latest');
  const btnCallPresetImage4 = document.getElementById('btn-call-preset-image4');
  const btnCallPresetImage1 = document.getElementById('btn-call-preset-image1');
  const btnCallPresetImage2 = document.getElementById('btn-call-preset-image2');
  const btnCallPresetImage3 = document.getElementById('btn-call-preset-image3');
  const btnCallResetSingle = document.getElementById('btn-call-reset-single');
  const btnCallDownload = document.getElementById('btn-call-download');

  const checkCallBottomNav = document.getElementById('check-call-bottom-nav');
  const callAndroidNavBar = document.getElementById('call-android-nav-bar');
  const callBottomBar = document.querySelector('.call-bottom-bar');
  const checkShowVideoCall = document.getElementById('check-show-video-call');
  const callVideoActionItem = document.getElementById('call-video-action-item');

  // Call logs initial data state (Default: 1 layer)
  let callLogs = [
    { id: 1, section: 'today', type: 'outgoing', title: 'Outgoing call', time: '12:20 PM', carrier: 'DITO', duration: '', hd: false }
  ];

  function getCallDirectionSVG(type) {
    if (type === 'incoming') {
      return `
        <svg class="call-dir-icon incoming" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="17" y1="7" x2="7" y2="17"></line>
          <polyline points="17 17 7 17 7 7"></polyline>
        </svg>
      `;
    } else if (type === 'missed') {
      return `
        <svg class="call-dir-icon missed" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="17" y1="7" x2="7" y2="17"></line>
          <polyline points="17 17 7 17 7 7"></polyline>
        </svg>
      `;
    } else if (type === 'cancelled') {
      return `
        <svg class="call-dir-icon cancelled" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      `;
    }
    // Outgoing (default)
    return `
      <svg class="call-dir-icon outgoing" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
        <line x1="7" y1="17" x2="17" y2="7"></line>
        <polyline points="7 7 17 7 17 17"></polyline>
      </svg>
    `;
  }

  // Format duration helper (e.g. 60 -> 1m, 61 -> 1m 1s, 32 -> 32s)
  function formatCallDuration(raw) {
    if (raw === null || raw === undefined) return '';
    const str = String(raw).trim();
    if (!str) return '';

    // Match raw seconds like "60", "61", "32", or "60s"
    const numMatch = str.match(/^(\d+)\s*s?$/i);
    if (numMatch) {
      const totalSec = parseInt(numMatch[1], 10);
      if (totalSec <= 0) return '';
      const hours = Math.floor(totalSec / 3600);
      const mins = Math.floor((totalSec % 3600) / 60);
      const secs = totalSec % 60;

      const parts = [];
      if (hours > 0) parts.push(`${hours}h`);
      if (mins > 0) parts.push(`${mins}m`);
      // If duration is 1 hour or more, omit the seconds indicator
      if (hours === 0 && secs > 0) parts.push(`${secs}s`);

      return parts.join(' ');
    }

    // Match mm:ss or hh:mm:ss format
    if (/^\d+(?::\d+)+$/.test(str)) {
      const segments = str.split(':').map(Number);
      let totalSec = 0;
      if (segments.length === 2) {
        totalSec = (segments[0] * 60) + segments[1];
      } else if (segments.length === 3) {
        totalSec = (segments[0] * 3600) + (segments[1] * 60) + segments[2];
      }
      return formatCallDuration(totalSec);
    }

    // Match and standardize existing formats like "1m 1s", "1m", "32s", "1m1s", "2h 46m 40s"
    const mMatch = str.match(/^(?:(\d+)\s*h)?\s*(?:(\d+)\s*m)?\s*(?:(\d+)\s*s)?$/i);
    if (mMatch && (mMatch[1] || mMatch[2] || mMatch[3])) {
      const h = mMatch[1] ? parseInt(mMatch[1], 10) : 0;
      const m = mMatch[2] ? parseInt(mMatch[2], 10) : 0;
      const s = mMatch[3] ? parseInt(mMatch[3], 10) : 0;
      const parts = [];
      if (h > 0) parts.push(`${h}h`);
      if (m > 0) parts.push(`${m}m`);
      // If duration is 1 hour or more, omit the seconds indicator
      if (h === 0 && s > 0) parts.push(`${s}s`);
      return parts.join(' ');
    }

    return str;
  }

  // Validate duration input (rejects letters and special characters)
  function validateDurationInput(val) {
    if (!val) return { valid: true };
    const str = String(val).trim();
    if (!str) return { valid: true };

    // Check for disallowed characters (letters other than h, m, s, or special characters other than colon and space)
    if (/[^0-9\s:hmsHMS]/.test(str)) {
      return {
        valid: false,
        message: 'Letters and special characters are not allowed. Please enter numbers (e.g. <code>32</code> or <code>60</code>) or valid format (e.g. <code>1m 1s</code>).'
      };
    }

    // If it contains letters h, m, s, verify it matches valid duration pattern
    if (/[hmsHMS]/.test(str)) {
      const validUnitPattern = /^(?:(\d+)\s*h)?\s*(?:(\d+)\s*m)?\s*(?:(\d+)\s*s)?$/i;
      const match = str.match(validUnitPattern);
      if (!match || (!match[1] && !match[2] && !match[3])) {
        return {
          valid: false,
          message: 'Invalid duration unit format. Use numbers (e.g. <code>60</code>) or valid format like <code>1m 20s</code>.'
        };
      }
      return { valid: true };
    }

    // If it contains colon (:), verify it's mm:ss or hh:mm:ss
    if (/:/.test(str)) {
      if (!/^\d+(?::\d+)+$/.test(str)) {
        return {
          valid: false,
          message: 'Invalid time format. Use <code>mm:ss</code> (e.g. <code>1:30</code>) or raw seconds.'
        };
      }
      return { valid: true };
    }

    // Otherwise it must be pure digits
    if (!/^\d+$/.test(str)) {
      return {
        valid: false,
        message: 'Invalid input. Please enter numbers only (e.g. <code>60</code> for 1 minute).'
      };
    }

    return { valid: true };
  }

  // Floating Error Pop-up Helpers
  let durationPopupTimeout = null;
  function showDurationErrorPopup(msg) {
    const popup = document.getElementById('duration-error-popup');
    if (!popup) return;
    const desc = popup.querySelector('.popup-error-desc');
    if (desc && msg) desc.innerHTML = msg;
    popup.style.display = 'flex';
    requestAnimationFrame(() => {
      popup.classList.add('show');
    });
    clearTimeout(durationPopupTimeout);
    durationPopupTimeout = setTimeout(() => {
      hideDurationErrorPopup();
    }, 4500);
  }

  function hideDurationErrorPopup() {
    const popup = document.getElementById('duration-error-popup');
    if (!popup) return;
    popup.classList.remove('show');
    setTimeout(() => {
      if (!popup.classList.contains('show')) {
        popup.style.display = 'none';
      }
    }, 250);
  }

  const btnCloseDurationPopup = document.getElementById('btn-close-duration-popup');
  if (btnCloseDurationPopup) {
    btnCloseDurationPopup.addEventListener('click', hideDurationErrorPopup);
  }

  function createCallLogElement(item) {
    const el = document.createElement('div');
    el.className = 'call-log-item' + (item.type === 'missed' ? ' missed' : '');
    el.dataset.id = item.id;

    el.innerHTML = `
      <div class="call-action-toolbar" data-html2canvas-ignore="true">
        <button class="call-action-btn edit-call-btn" title="Edit call log" aria-label="Edit">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
          </svg>
        </button>
        <button class="call-action-btn delete-call-btn" title="Delete call log" aria-label="Delete">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          </svg>
        </button>
      </div>
      <div class="call-icon-col">
        ${getCallDirectionSVG(item.type)}
      </div>
      <div class="call-details-col">
        <div class="call-title-row">
          <span class="call-type-text" contenteditable="true">${escapeHTML(item.title)}</span>
          ${item.hd ? '<span class="hd-badge">HD</span>' : ''}
        </div>
        <div class="call-time-row" contenteditable="true">${escapeHTML(item.time)}</div>
        <div class="call-carrier-row" contenteditable="true">${escapeHTML(item.carrier)}</div>
      </div>
      <div class="call-meta-col">
        <span class="call-duration" contenteditable="true">${escapeHTML(item.duration || '')}</span>
      </div>
    `;

    // Auto-format inline edited duration on blur or Enter with validation
    const durEl = el.querySelector('.call-duration');
    if (durEl) {
      durEl.addEventListener('blur', () => {
        const raw = durEl.textContent.trim();
        const validation = validateDurationInput(raw);
        if (!validation.valid) {
          showDurationErrorPopup(validation.message);
          durEl.style.color = '#ef4444';
          return;
        }
        durEl.style.color = '';
        const formatted = formatCallDuration(raw);
        if (formatted !== durEl.textContent) {
          durEl.textContent = formatted;
        }
        const log = callLogs.find(c => c.id === item.id);
        if (log) log.duration = formatted;
      });
      durEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          durEl.blur();
        }
      });
    }

    // Hide delete button if only 1 layer remaining total
    if (callLogs.length <= 1) {
      const del = el.querySelector('.delete-call-btn');
      if (del) del.style.display = 'none';
    }

    return el;
  }

  function renderCallLogs() {
    if (!callBody) return;
    callBody.innerHTML = '';

    const mode = selectCallSectionTitle ? selectCallSectionTitle.value : 'Today';

    if (mode === 'Today') {
      // Split into Today and Older sections (Layout on the second reference image)
      const todayLogs = callLogs.filter(item => (item.section || 'today') === 'today');
      const olderLogs = callLogs.filter(item => item.section === 'older');

      // 1. Today section
      if (todayLogs.length > 0 || olderLogs.length === 0) {
        const todayTitle = document.createElement('div');
        todayTitle.className = 'call-section-title';
        todayTitle.id = 'call-section-today-title';
        todayTitle.contentEditable = 'true';
        todayTitle.textContent = 'Today';
        callBody.appendChild(todayTitle);

        const todayCard = document.createElement('div');
        todayCard.className = 'call-card';
        todayCard.dataset.section = 'today';
        todayLogs.forEach(item => {
          todayCard.appendChild(createCallLogElement(item));
        });
        callBody.appendChild(todayCard);
      }

      // 2. Older section
      if (olderLogs.length > 0) {
        const olderTitle = document.createElement('div');
        olderTitle.className = 'call-section-title';
        olderTitle.id = 'call-section-older-title';
        olderTitle.contentEditable = 'true';
        olderTitle.textContent = 'Older';
        callBody.appendChild(olderTitle);

        const olderCard = document.createElement('div');
        olderCard.className = 'call-card';
        olderCard.dataset.section = 'older';
        olderLogs.forEach(item => {
          olderCard.appendChild(createCallLogElement(item));
        });
        callBody.appendChild(olderCard);
      }
    } else {
      // Older mode only
      const olderTitle = document.createElement('div');
      olderTitle.className = 'call-section-title';
      olderTitle.id = 'call-section-older-title';
      olderTitle.contentEditable = 'true';
      olderTitle.textContent = 'Older';
      callBody.appendChild(olderTitle);

      const olderCard = document.createElement('div');
      olderCard.className = 'call-card';
      olderCard.dataset.section = 'older';
      callLogs.forEach(item => {
        olderCard.appendChild(createCallLogElement(item));
      });
      callBody.appendChild(olderCard);
    }

    // Update active layer count badge
    if (callLayerBadge) {
      callLayerBadge.textContent = `${callLogs.length} ${callLogs.length === 1 ? 'Layer' : 'Layers'}`;
    }

    // Update Remove Layer button state (must have at least 1 layer)
    if (btnRemoveLayer) {
      if (callLogs.length <= 1) {
        btnRemoveLayer.style.opacity = '0.4';
        btnRemoveLayer.style.cursor = 'not-allowed';
        btnRemoveLayer.title = 'At least 1 layer is required';
      } else {
        btnRemoveLayer.style.opacity = '1';
        btnRemoveLayer.style.cursor = 'pointer';
        btnRemoveLayer.title = 'Remove layer';
      }
    }
  }

  function updateCallLogsCarrier(carrierName) {
    callLogs.forEach(c => { c.carrier = carrierName; });
    renderCallLogs();
  }

  // Initial Call Logs Render
  renderCallLogs();

  // Call Logs Contact Details sync
  if (inputCallPhone && callPhoneVal) {
    inputCallPhone.addEventListener('input', (e) => {
      callPhoneVal.textContent = e.target.value;
    });
    callPhoneVal.addEventListener('input', (e) => {
      inputCallPhone.value = e.target.textContent;
    });
  }

  if (inputCallLocation && callLocationVal) {
    inputCallLocation.addEventListener('input', (e) => {
      callLocationVal.textContent = e.target.value;
    });
    callLocationVal.addEventListener('input', (e) => {
      inputCallLocation.value = e.target.textContent;
    });
  }

  const selectCallCarrier = document.getElementById('select-call-carrier');
  if (selectCallCarrier) {
    selectCallCarrier.addEventListener('change', (e) => {
      const choice = e.target.value;
      if (choice === 'CUSTOM') {
        if (inputCallCarrier) {
          inputCallCarrier.style.display = 'block';
          inputCallCarrier.value = '';
          inputCallCarrier.focus();
          syncCarrier('', true);
        }
      } else {
        if (inputCallCarrier) inputCallCarrier.style.display = 'none';
        syncCarrier(choice, false);
      }
    });
  }

  if (inputCallCarrier) {
    inputCallCarrier.addEventListener('input', (e) => {
      syncCarrier(e.target.value, true);
    });
  }

  if (selectCallSectionTitle) {
    selectCallSectionTitle.addEventListener('change', (e) => {
      const val = e.target.value;
      if (selectNewCallSection) {
        selectNewCallSection.value = val === 'Older' ? 'older' : 'today';
      }
      renderCallLogs();
    });
  }

  // Call Status Bar sync listeners
  if (inputCallStatusTime && callStatusTimeVal) {
    inputCallStatusTime.addEventListener('input', (e) => {
      toggleRealTimeClockSync(false);
      const val = e.target.value;
      callStatusTimeVal.textContent = val;
      if (statusTimeVal) statusTimeVal.textContent = val;
      if (inputStatusTime) inputStatusTime.value = val;
    });
    callStatusTimeVal.addEventListener('input', (e) => {
      toggleRealTimeClockSync(false);
      const val = e.target.textContent;
      inputCallStatusTime.value = val;
      if (statusTimeVal) statusTimeVal.textContent = val;
      if (inputStatusTime) inputStatusTime.value = val;
    });
  }

  if (inputCallBattery && callBatteryVal) {
    inputCallBattery.addEventListener('input', (e) => {
      const val = e.target.value;
      callBatteryVal.textContent = val;
      if (batteryVal) batteryVal.textContent = val;
      if (inputBattery) inputBattery.value = val;
    });
    callBatteryVal.addEventListener('input', (e) => {
      const val = e.target.textContent;
      inputCallBattery.value = val;
      if (batteryVal) batteryVal.textContent = val;
      if (inputBattery) inputBattery.value = val;
    });
  }

  if (inputCallNetSpeed && callNetSpeedVal) {
    inputCallNetSpeed.addEventListener('input', (e) => {
      callNetSpeedVal.textContent = e.target.value;
    });
  }

  if (checkCallChatIcon && callStatusChatIcon) {
    checkCallChatIcon.addEventListener('change', (e) => {
      callStatusChatIcon.style.display = e.target.checked ? 'inline-flex' : 'none';
    });
  }

  // Add Layer button
  if (btnAddLayer) {
    btnAddLayer.addEventListener('click', () => {
      const type = selectCallType ? selectCallType.value : 'outgoing';
      let title = 'Outgoing call';
      if (type === 'incoming') title = 'Incoming call';
      else if (type === 'missed') title = 'Missed call';
      else if (type === 'cancelled') title = 'Cancelled call';

      let time = inputNewCallTime ? inputNewCallTime.value.trim() : '';
      if (!time) {
        const d = new Date();
        time = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      }

      const rawDuration = inputNewCallDuration ? inputNewCallDuration.value.trim() : '';
      const validation = validateDurationInput(rawDuration);
      if (!validation.valid) {
        if (inputNewCallDuration) {
          inputNewCallDuration.classList.add('input-error');
          inputNewCallDuration.focus();
        }
        const fieldErr = document.getElementById('duration-field-error');
        if (fieldErr) fieldErr.style.display = 'block';
        showDurationErrorPopup(validation.message);
        return; // Prevent adding layer with invalid duration!
      }

      const duration = formatCallDuration(rawDuration);
      const hd = checkNewCallHd ? checkNewCallHd.checked : false;
      const carrier = inputCallCarrier ? inputCallCarrier.value.trim() : 'DITO';
      const sectionChoice = selectNewCallSection ? selectNewCallSection.value : (selectCallSectionTitle && selectCallSectionTitle.value === 'Older' ? 'older' : 'today');

      callLogs.push({
        id: Date.now(),
        section: sectionChoice,
        type,
        title,
        time,
        carrier,
        duration,
        hd
      });

      renderCallLogs();
    });
  }

  // Live duration preview badge & error pop-up validation
  const durationFieldError = document.getElementById('duration-field-error');
  if (inputNewCallDuration) {
    inputNewCallDuration.addEventListener('input', (e) => {
      const val = e.target.value;
      const validation = validateDurationInput(val);

      if (!validation.valid) {
        inputNewCallDuration.classList.add('input-error');
        if (durationFieldError) durationFieldError.style.display = 'block';
        if (durationPreviewBadge) {
          durationPreviewBadge.textContent = '❌ Invalid Input';
          durationPreviewBadge.style.color = '#ef4444';
        }
        showDurationErrorPopup(validation.message);
      } else {
        inputNewCallDuration.classList.remove('input-error');
        if (durationFieldError) durationFieldError.style.display = 'none';
        hideDurationErrorPopup();
        const formatted = formatCallDuration(val.trim());
        if (formatted && formatted !== val.trim()) {
          if (durationPreviewBadge) {
            durationPreviewBadge.textContent = `→ ${formatted}`;
            durationPreviewBadge.style.color = '#4ade80';
          }
        } else {
          if (durationPreviewBadge) durationPreviewBadge.textContent = '';
        }
      }
    });
  }

  // Remove Layer button (at least 1 layer must remain)
  if (btnRemoveLayer) {
    btnRemoveLayer.addEventListener('click', () => {
      if (callLogs.length <= 1) {
        alert('Cannot remove layer: At least 1 layer is required.');
        return;
      }
      callLogs.pop();
      renderCallLogs();
    });
  }

  // Call log card delegation: hover edit / delete
  if (callBody) {
    callBody.addEventListener('click', (e) => {
      const editBtn = e.target.closest('.edit-call-btn');
      const deleteBtn = e.target.closest('.delete-call-btn');

      if (editBtn) {
        e.stopPropagation();
        const row = editBtn.closest('.call-log-item');
        if (row) {
          const id = Number(row.dataset.id);
          const item = callLogs.find(c => c.id === id);
          if (item) {
            openCallEditModal(item);
          }
        }
      }

      if (deleteBtn) {
        e.stopPropagation();
        if (callLogs.length <= 1) {
          alert('Cannot remove layer: At least 1 layer is required.');
          return;
        }
        const row = deleteBtn.closest('.call-log-item');
        if (row) {
          const id = Number(row.dataset.id);
          row.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
          row.style.opacity = '0';
          row.style.transform = 'scale(0.95)';
          setTimeout(() => {
            callLogs = callLogs.filter(c => c.id !== id);
            renderCallLogs();
          }, 200);
        }
      }
    });
  }

  // ==========================================
  // Call Layer Edit Modal Logic (Duration, Time, Type)
  // ==========================================
  const callEditModal = document.getElementById('call-edit-modal');
  const modalEditCallDuration = document.getElementById('modal-edit-call-duration');
  const modalEditCallTime = document.getElementById('modal-edit-call-time');
  const modalEditCallType = document.getElementById('modal-edit-call-type');
  const btnModalCancelCall = document.getElementById('btn-modal-cancel-call');
  const btnModalSaveCall = document.getElementById('btn-modal-save-call');

  let editingCallId = null;

  function openCallEditModal(item) {
    if (!item) return;
    editingCallId = item.id;
    if (modalEditCallDuration) {
      modalEditCallDuration.value = item.duration || '';
      modalEditCallDuration.classList.remove('input-error');
    }
    if (modalEditCallTime) modalEditCallTime.value = item.time || '';
    if (modalEditCallType) modalEditCallType.value = item.type || 'outgoing';

    if (callEditModal) {
      callEditModal.style.display = 'flex';
      setTimeout(() => {
        if (modalEditCallDuration) {
          modalEditCallDuration.focus();
          modalEditCallDuration.select();
        }
      }, 50);
    }
  }

  function closeCallEditModal() {
    if (callEditModal) callEditModal.style.display = 'none';
    editingCallId = null;
  }

  if (btnModalCancelCall) btnModalCancelCall.addEventListener('click', closeCallEditModal);

  if (callEditModal) {
    callEditModal.addEventListener('click', (e) => {
      if (e.target === callEditModal) closeCallEditModal();
    });
  }

  if (btnModalSaveCall) {
    btnModalSaveCall.addEventListener('click', () => {
      if (!editingCallId) return closeCallEditModal();
      const item = callLogs.find(c => c.id === editingCallId);
      if (!item) return closeCallEditModal();

      const rawDuration = modalEditCallDuration ? modalEditCallDuration.value.trim() : '';
      if (rawDuration) {
        const validation = validateDurationInput(rawDuration);
        if (!validation.valid) {
          showDurationErrorPopup(validation.message);
          if (modalEditCallDuration) modalEditCallDuration.classList.add('input-error');
          return;
        }
        item.duration = formatCallDuration(rawDuration);
      } else {
        item.duration = '';
      }

      if (modalEditCallTime && modalEditCallTime.value.trim()) {
        item.time = modalEditCallTime.value.trim();
      }

      if (modalEditCallType) {
        item.type = modalEditCallType.value;
        if (item.type === 'incoming') item.title = 'Incoming call';
        else if (item.type === 'missed') item.title = 'Missed call';
        else if (item.type === 'cancelled') item.title = 'Cancelled call';
        else item.title = 'Outgoing call';
      }

      renderCallLogs();
      closeCallEditModal();
    });
  }

  // Quick duration chips in modal
  document.querySelectorAll('.quick-duration-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      if (modalEditCallDuration) {
        modalEditCallDuration.value = chip.dataset.val;
        modalEditCallDuration.classList.remove('input-error');
        modalEditCallDuration.focus();
      }
    });
  });

  // Enter key in modal submits
  [modalEditCallDuration, modalEditCallTime].forEach(input => {
    if (input) {
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          btnModalSaveCall?.click();
        }
      });
    }
  });

  // Video call button display toggle
  if (checkShowVideoCall && callVideoActionItem) {
    checkShowVideoCall.addEventListener('change', (e) => {
      callVideoActionItem.style.display = e.target.checked ? 'flex' : 'none';
    });
  }

  // Android 3-Button Nav Bar toggle
  if (checkCallBottomNav && callAndroidNavBar) {
    checkCallBottomNav.addEventListener('change', (e) => {
      callAndroidNavBar.style.display = e.target.checked ? 'flex' : 'none';
      if (callBottomBar) {
        if (e.target.checked) callBottomBar.classList.add('has-nav');
        else callBottomBar.classList.remove('has-nav');
      }
    });
  }

  // Presets
  // Preset Latest Reference (8:46 screenshot)
  if (btnCallPresetLatest) {
    btnCallPresetLatest.addEventListener('click', () => {
      if (inputCallPhone) inputCallPhone.value = '+63 956 825 5857';
      if (callPhoneVal) callPhoneVal.textContent = '+63 956 825 5857';
      if (inputCallLocation) inputCallLocation.value = 'Philippines';
      if (callLocationVal) callLocationVal.textContent = 'Philippines';
      syncCarrier('DITO', false);
      if (selectCallSectionTitle) selectCallSectionTitle.value = 'Today';
      if (selectNewCallSection) selectNewCallSection.value = 'today';
      callLogs = [
        { id: 1, section: 'today', type: 'outgoing', title: 'Outgoing call', time: '12:20 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 2, section: 'today', type: 'outgoing', title: 'Outgoing call', time: '12:19 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 3, section: 'today', type: 'outgoing', title: 'Outgoing call', time: '12:18 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 4, section: 'older', type: 'outgoing', title: 'Outgoing call', time: 'Tue 11:46 AM', carrier: 'DITO', duration: '3m 44s', hd: false },
        { id: 5, section: 'older', type: 'missed', title: 'Missed call', time: 'Tue 11:44 AM', carrier: 'DITO', duration: '', hd: false }
      ];
      renderCallLogs();

      // Synced status bar
      if (inputCallStatusTime) {
        inputCallStatusTime.value = '8:46';
        inputCallStatusTime.dispatchEvent(new Event('input'));
      }
      if (inputCallBattery) {
        inputCallBattery.value = '21';
        inputCallBattery.dispatchEvent(new Event('input'));
      }
      if (inputCallNetSpeed) {
        inputCallNetSpeed.value = '9.14';
        inputCallNetSpeed.dispatchEvent(new Event('input'));
      }

      // Action bar
      if (checkShowVideoCall) {
        checkShowVideoCall.checked = true;
        if (callVideoActionItem) callVideoActionItem.style.display = 'flex';
      }
      if (checkCallBottomNav) {
        checkCallBottomNav.checked = false;
        if (callAndroidNavBar) callAndroidNavBar.style.display = 'none';
        if (callBottomBar) callBottomBar.classList.remove('has-nav');
      }

      callLogs = [
        { id: 1, type: 'outgoing', title: 'Outgoing call', time: '12:20 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 2, type: 'outgoing', title: 'Outgoing call', time: '12:19 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 3, type: 'outgoing', title: 'Outgoing call', time: '12:18 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 4, type: 'outgoing', title: 'Outgoing call', time: 'Tue 11:46 AM', carrier: 'DITO', duration: '3m 44s', hd: false },
        { id: 5, type: 'missed', title: 'Missed call', time: 'Tue 11:44 AM', carrier: 'DITO', duration: '', hd: false }
      ];
      renderCallLogs();
    });
  }
  if (btnCallPresetImage1) {
    btnCallPresetImage1.addEventListener('click', () => {
      if (inputCallPhone) inputCallPhone.value = '0924 062 5937';
      if (callPhoneVal) callPhoneVal.textContent = '0924 062 5937';
      if (inputCallLocation) inputCallLocation.value = 'Philippines';
      if (callLocationVal) callLocationVal.textContent = 'Philippines';
      if (inputCallSectionTitle) inputCallSectionTitle.value = 'Older';
      if (callSectionTitleVal) callSectionTitleVal.textContent = 'Older';
      if (checkShowVideoCall) {
        checkShowVideoCall.checked = false;
        if (callVideoActionItem) callVideoActionItem.style.display = 'none';
      }

      callLogs = [
        { id: 1, type: 'outgoing', title: 'Outgoing call', time: 'Sat 11:55 AM', carrier: 'DITO', duration: '1m 21s', hd: true },
        { id: 2, type: 'outgoing', title: 'Outgoing call', time: 'Sat 11:54 AM', carrier: 'DITO', duration: '33s', hd: true }
      ];
      renderCallLogs();
    });
  }

  if (btnCallPresetImage2) {
    btnCallPresetImage2.addEventListener('click', () => {
      if (inputCallPhone) inputCallPhone.value = '0939 499 2619';
      if (callPhoneVal) callPhoneVal.textContent = '0939 499 2619';
      if (inputCallLocation) inputCallLocation.value = 'Philippines';
      if (callLocationVal) callLocationVal.textContent = 'Philippines';
      if (inputCallSectionTitle) inputCallSectionTitle.value = 'Older';
      if (callSectionTitleVal) callSectionTitleVal.textContent = 'Older';
      if (checkShowVideoCall) {
        checkShowVideoCall.checked = false;
        if (callVideoActionItem) callVideoActionItem.style.display = 'none';
      }

      callLogs = [
        { id: 1, type: 'outgoing', title: 'Outgoing call', time: 'Sat 12:55 PM', carrier: 'DITO', duration: '', hd: false }
      ];
      renderCallLogs();
    });
  }

  if (btnCallPresetImage3) {
    btnCallPresetImage3.addEventListener('click', () => {
      if (inputCallPhone) inputCallPhone.value = '0970 899 7307';
      if (callPhoneVal) callPhoneVal.textContent = '0970 899 7307';
      if (inputCallLocation) inputCallLocation.value = 'Philippines';
      if (callLocationVal) callLocationVal.textContent = 'Philippines';
      if (inputCallSectionTitle) inputCallSectionTitle.value = 'Older';
      if (callSectionTitleVal) callSectionTitleVal.textContent = 'Older';
      if (checkShowVideoCall) {
        checkShowVideoCall.checked = true;
        if (callVideoActionItem) callVideoActionItem.style.display = 'flex';
      }

      callLogs = [
        { id: 1, type: 'outgoing', title: 'Outgoing call', time: 'Fri 11:28 AM', carrier: 'DITO', duration: '', hd: false },
        { id: 2, type: 'outgoing', title: 'Outgoing call', time: 'Fri 10:21 AM', carrier: 'DITO', duration: '', hd: false },
        { id: 3, type: 'outgoing', title: 'Outgoing call', time: 'Fri 9:15 AM', carrier: 'DITO', duration: '', hd: false }
      ];
      renderCallLogs();
    });
  }

  // Preset 4: 3 Outgoing calls with gaps (12:20, 12:19, 12:18 PM)
  if (btnCallPresetImage4) {
    btnCallPresetImage4.addEventListener('click', () => {
      if (inputCallPhone) inputCallPhone.value = '0924 062 5937';
      if (callPhoneVal) callPhoneVal.textContent = '0924 062 5937';
      if (inputCallLocation) inputCallLocation.value = 'Philippines';
      if (callLocationVal) callLocationVal.textContent = 'Philippines';
      if (inputCallSectionTitle) inputCallSectionTitle.value = 'Older';
      if (callSectionTitleVal) callSectionTitleVal.textContent = 'Older';
      if (checkShowVideoCall) {
        checkShowVideoCall.checked = false;
        if (callVideoActionItem) callVideoActionItem.style.display = 'none';
      }

      callLogs = [
        { id: 1, type: 'outgoing', title: 'Outgoing call', time: '12:20 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 2, type: 'outgoing', title: 'Outgoing call', time: '12:19 PM', carrier: 'DITO', duration: '', hd: false },
        { id: 3, type: 'outgoing', title: 'Outgoing call', time: '12:18 PM', carrier: 'DITO', duration: '', hd: false }
      ];
      renderCallLogs();
    });
  }

  // Reset to 1 layer (minimum)
  if (btnCallResetSingle) {
    btnCallResetSingle.addEventListener('click', () => {
      callLogs = [
        { id: 1, type: 'outgoing', title: 'Outgoing call', time: '12:20 PM', carrier: 'DITO', duration: '', hd: false }
      ];
      renderCallLogs();
    });
  }

  // Call Logs Screenshot Download
  if (btnCallDownload) {
    btnCallDownload.addEventListener('click', async () => {
      if (typeof html2canvas === 'undefined') {
        alert('Screenshot generator is still loading. Please try again in a second.');
        return;
      }

      if (document.activeElement && document.activeElement.blur) {
        document.activeElement.blur();
      }

      const callPhoneScreen = document.getElementById('call-phone-screen');
      const origText = btnCallDownload.innerHTML;
      btnCallDownload.innerHTML = `⏳ Generating Screenshot...`;
      btnCallDownload.style.opacity = '0.7';
      btnCallDownload.style.pointerEvents = 'none';

      try {
        const canvas = await html2canvas(callPhoneScreen, {
          scale: 2.62136,
          useCORS: true,
          allowTaint: true,
          logging: false,
          backgroundColor: '#131314',
          windowWidth: document.documentElement.offsetWidth,
          windowHeight: document.documentElement.offsetHeight,
          onclone: (clonedDoc) => {
            clonedDoc.querySelectorAll('.call-action-toolbar').forEach(el => el.remove());
            clonedDoc.querySelectorAll('.call-meta-col').forEach(el => el.style.marginRight = '0px');

            // Remove all phone frame border-radius and shadows so downloaded screenshot is a true rectangular device screen
            const clonedScreen = clonedDoc.getElementById('call-phone-screen');
            if (clonedScreen) {
              clonedScreen.style.borderRadius = '0px';
              clonedScreen.style.borderTopLeftRadius = '0px';
              clonedScreen.style.borderTopRightRadius = '0px';
              clonedScreen.style.borderBottomLeftRadius = '0px';
              clonedScreen.style.borderBottomRightRadius = '0px';
              clonedScreen.style.boxShadow = 'none';
            }

            // Ensure Call Bottom Bar extends completely flush to the bottom edge with 0 bottom border radius
            const clonedBottomBar = clonedDoc.querySelector('.call-bottom-bar');
            if (clonedBottomBar) {
              clonedBottomBar.style.borderBottomLeftRadius = '0px';
              clonedBottomBar.style.borderBottomRightRadius = '0px';
            }

            // Ensure Android 3-Button Nav Bar has 0 border radius if present
            const clonedNavBar = clonedDoc.getElementById('call-android-nav-bar');
            if (clonedNavBar) {
              clonedNavBar.style.borderRadius = '0px';
            }
          }
        });

        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        const filename = `Screenshot_${year}${month}${day}-${hours}${mins}${secs}_CallHistory.png`;

        const imageURL = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = filename;
        link.href = imageURL;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        btnCallDownload.innerHTML = `✅ Saved as PNG!`;
        setTimeout(() => {
          btnCallDownload.innerHTML = origText;
          btnCallDownload.style.opacity = '1';
          btnCallDownload.style.pointerEvents = 'auto';
        }, 2000);
      } catch (err) {
        console.error('Call history screenshot capture failed:', err);
        alert('Failed to generate screenshot.');
        btnCallDownload.innerHTML = origText;
        btnCallDownload.style.opacity = '1';
        btnCallDownload.style.pointerEvents = 'auto';
      }
    });
  }

  // Top Navigation link active state, tool tab switching, and fixed viewport centering
  const navLinks = document.querySelectorAll('.nav-link');
  const smsDivision = document.getElementById('sms-division');
  const callLogsDivision = document.getElementById('call-logs-division');

  function setActiveTool(targetId) {
    navLinks.forEach(link => {
      if (link.getAttribute('href') === targetId) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });

    if (window.innerWidth >= 1024) {
      if (targetId === '#call-logs-division') {
        if (smsDivision) smsDivision.classList.remove('active-division');
        if (callLogsDivision) callLogsDivision.classList.add('active-division');
      } else {
        if (callLogsDivision) callLogsDivision.classList.remove('active-division');
        if (smsDivision) smsDivision.classList.add('active-division');
      }
    } else {
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  }

  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = link.getAttribute('href');
      setActiveTool(targetId);
    });
  });

  // Dynamic Viewport Scale for Large Screen Devices
  function updateViewportScale() {
    if (window.innerWidth >= 1024) {
      const navEl = document.querySelector('.top-nav-bar');
      const navHeight = navEl ? navEl.offsetHeight : 48;
      const verticalPadding = 28;
      const availableHeight = window.innerHeight - navHeight - verticalPadding;
      const targetHeight = 890;
      const scale = Math.min(1, Math.max(0.55, availableHeight / targetHeight));
      document.documentElement.style.setProperty('--viewport-scale', scale);
    } else {
      document.documentElement.style.setProperty('--viewport-scale', '1');
    }
  }

  window.addEventListener('resize', () => {
    updateViewportScale();
    const activeLink = document.querySelector('.nav-link.active');
    if (activeLink) {
      setActiveTool(activeLink.getAttribute('href'));
    }
  });

  // Initial call
  updateViewportScale();
  setActiveTool('#sms-division');

  // Intercept paste events globally on contenteditable elements to paste plain text only
  document.addEventListener('paste', (e) => {
    const editable = e.target.closest('[contenteditable="true"]');
    if (editable) {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text/plain');
      
      // Modern Selection API for inserting plain text
      const selection = window.getSelection();
      if (!selection.rangeCount) return;
      selection.deleteFromDocument();
      const textNode = document.createTextNode(text);
      selection.getRangeAt(0).insertNode(textNode);
      
      // Move caret after inserted node
      const range = document.createRange();
      range.setStartAfter(textNode);
      range.setEndAfter(textNode);
      selection.removeAllRanges();
      selection.addRange(range);
      
      // Dispatch input event so any listeners update
      editable.dispatchEvent(new Event('input', { bubbles: true }));
    }
  });

  // ==========================================
  // Custom Sleek Dropdown (Zero Scrollbar Popup)
  // ==========================================
  let activeDropdownPopup = null;

  function closeCustomDropdown() {
    if (activeDropdownPopup) {
      activeDropdownPopup.remove();
      activeDropdownPopup = null;
    }
  }

  function openCustomDropdown(selectEl) {
    closeCustomDropdown();
    if (!selectEl) return;

    const rect = selectEl.getBoundingClientRect();
    const popup = document.createElement('div');
    popup.className = 'custom-dropdown-popup';
    popup.style.top = `${rect.bottom + 4}px`;
    popup.style.left = `${rect.left}px`;
    popup.style.minWidth = `${Math.max(rect.width, 68)}px`;

    // Flip upward if close to bottom of screen
    const windowHeight = window.innerHeight;
    if (rect.bottom + 230 > windowHeight && rect.top > 230) {
      popup.style.top = `${rect.top - 224}px`;
    }

    let activeItem = null;
    Array.from(selectEl.options).forEach(opt => {
      const item = document.createElement('div');
      item.className = 'custom-dropdown-item' + (opt.value === selectEl.value ? ' active' : '');
      item.textContent = opt.textContent;
      item.dataset.value = opt.value;

      if (opt.value === selectEl.value) {
        activeItem = item;
      }

      item.addEventListener('click', (e) => {
        e.stopPropagation();
        selectEl.value = opt.value;
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        selectEl.dispatchEvent(new Event('input', { bubbles: true }));
        closeCustomDropdown();
      });

      popup.appendChild(item);
    });

    popup.addEventListener('mousedown', (e) => e.stopPropagation());
    popup.addEventListener('click', (e) => e.stopPropagation());

    document.body.appendChild(popup);
    activeDropdownPopup = popup;

    if (activeItem) {
      setTimeout(() => {
        activeItem.scrollIntoView({ block: 'center', behavior: 'auto' });
      }, 0);
    }
  }

  const zeroScrollSelects = [
    selectStatusHour,
    selectStatusMinute,
    selectTimestampHour,
    selectTimestampMinute,
    selectTimestampAmpm,
    selectCallStatusHour,
    selectCallStatusMinute
  ].filter(Boolean);

  zeroScrollSelects.forEach(sel => {
    sel.addEventListener('mousedown', (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (activeDropdownPopup && activeDropdownPopup.dataset.targetId === sel.id) {
        closeCustomDropdown();
      } else {
        openCustomDropdown(sel);
        if (activeDropdownPopup) activeDropdownPopup.dataset.targetId = sel.id;
      }
    });

    sel.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
    });

    sel.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openCustomDropdown(sel);
      } else if (e.key === 'Escape') {
        closeCustomDropdown();
      }
    });
  });

  window.addEventListener('click', (e) => {
    if (activeDropdownPopup && !e.target.closest('.custom-dropdown-popup') && !zeroScrollSelects.includes(e.target)) {
      closeCustomDropdown();
    }
  });

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCustomDropdown();
  });

  window.addEventListener('scroll', (e) => {
    if (activeDropdownPopup && e.target !== activeDropdownPopup && !activeDropdownPopup.contains(e.target)) {
      closeCustomDropdown();
    }
  }, true);

  window.addEventListener('resize', closeCustomDropdown);
});

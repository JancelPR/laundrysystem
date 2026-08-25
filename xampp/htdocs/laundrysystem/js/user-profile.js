/**
 * LaundryEase User Profile & Security Settings Module
 * Handles profile information updating and secure password change with verification.
 */

const UserProfileManager = {
  currentUser: null,

  init(user) {
    this.currentUser = user;
  },

  openProfileModal() {
    const modalId = "userProfileModal";
    let modal = document.getElementById(modalId);

    if (!modal) {
      modal = document.createElement("div");
      modal.id = modalId;
      modal.className = "profile-modal-backdrop";
      document.body.appendChild(modal);
    }

    const user = this.currentUser || {
      id: 0,
      full_name: "User",
      phone: "",
      address: "",
      role: "admin"
    };

    modal.innerHTML = `
      <div class="profile-dialog-container">
        <div class="profile-dialog-header">
          <div class="profile-title-box">
            <span class="profile-icon">⚙️</span>
            <div>
              <h3>Account Settings & Profile</h3>
              <p>${user.full_name} &bull; ${user.role ? user.role.toUpperCase() : 'USER'}</p>
            </div>
          </div>
          <button type="button" class="profile-close-btn" onclick="UserProfileManager.closeModal()">&times;</button>
        </div>

        <div class="profile-dialog-body">
          <!-- Profile Tabs -->
          <div style="display: flex; gap: 8px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px;">
            <button type="button" id="tabGeneralBtn" class="btn-receipt-secondary" style="color: #2dd4bf; border-color: #2dd4bf; background: rgba(45,212,191,0.15);" onclick="UserProfileManager.switchTab('general')">General Info</button>
            <button type="button" id="tabSecurityBtn" class="btn-receipt-secondary" onclick="UserProfileManager.switchTab('security')">Security & Password</button>
          </div>

          <!-- General Info Tab -->
          <form id="generalProfileForm" onsubmit="UserProfileManager.saveGeneral(event)">
            <input type="hidden" name="user_id" value="${user.id}">
            <div class="notify-field-group">
              <label>Full Name</label>
              <input type="text" name="full_name" class="clean-input" value="${user.full_name || ''}" required>
            </div>
            <div class="notify-field-group">
              <label>Mobile Contact Number</label>
              <input type="text" name="phone" class="clean-input" value="${user.phone || ''}" required>
            </div>
            <div class="notify-field-group">
              <label>Address / Street</label>
              <input type="text" name="address" class="clean-input" value="${user.address || ''}">
            </div>
            <div id="generalFeedback" style="font-size: 0.82rem; margin-top: 8px;"></div>
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
              <button type="submit" class="btn-receipt-primary" id="saveGeneralBtn">Save Changes</button>
            </div>
          </form>

          <!-- Security Tab -->
          <form id="securityProfileForm" style="display: none;" onsubmit="UserProfileManager.changePassword(event)">
            <input type="hidden" name="user_id" value="${user.id}">
            <div class="notify-field-group">
              <label>Current Password</label>
              <div style="position: relative;">
                <input type="password" id="profCurrPass" name="current_password" class="clean-input has-toggle" required>
                <button type="button" class="password-toggle-btn" onclick="UserProfileManager.togglePass('profCurrPass', this)">👁️</button>
              </div>
            </div>
            <div class="notify-field-group">
              <label>New Password (min 6 characters)</label>
              <div style="position: relative;">
                <input type="password" id="profNewPass" name="new_password" class="clean-input has-toggle" minlength="6" required>
                <button type="button" class="password-toggle-btn" onclick="UserProfileManager.togglePass('profNewPass', this)">👁️</button>
              </div>
            </div>
            <div class="notify-field-group">
              <label>Confirm New Password</label>
              <div style="position: relative;">
                <input type="password" id="profConfirmPass" name="confirm_password" class="clean-input has-toggle" minlength="6" required>
                <button type="button" class="password-toggle-btn" onclick="UserProfileManager.togglePass('profConfirmPass', this)">👁️</button>
              </div>
            </div>
            <div id="securityFeedback" style="font-size: 0.82rem; margin-top: 8px;"></div>
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
              <button type="submit" class="btn-receipt-primary" id="saveSecurityBtn">Update Password</button>
            </div>
          </form>
        </div>
      </div>
    `;

    modal.classList.add("active");
  },

  closeModal() {
    const modal = document.getElementById("userProfileModal");
    if (modal) {
      modal.classList.remove("active");
    }
  },

  switchTab(tab) {
    const generalForm = document.getElementById("generalProfileForm");
    const securityForm = document.getElementById("securityProfileForm");
    const gBtn = document.getElementById("tabGeneralBtn");
    const sBtn = document.getElementById("tabSecurityBtn");

    if (tab === 'general') {
      generalForm.style.display = "block";
      securityForm.style.display = "none";
      gBtn.style.color = "#2dd4bf";
      gBtn.style.borderColor = "#2dd4bf";
      gBtn.style.background = "rgba(45,212,191,0.15)";
      sBtn.style.color = "#94a3b8";
      sBtn.style.borderColor = "rgba(255,255,255,0.15)";
      sBtn.style.background = "rgba(255,255,255,0.08)";
    } else {
      generalForm.style.display = "none";
      securityForm.style.display = "block";
      sBtn.style.color = "#2dd4bf";
      sBtn.style.borderColor = "#2dd4bf";
      sBtn.style.background = "rgba(45,212,191,0.15)";
      gBtn.style.color = "#94a3b8";
      gBtn.style.borderColor = "rgba(255,255,255,0.15)";
      gBtn.style.background = "rgba(255,255,255,0.08)";
    }
  },

  togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
      input.type = 'text';
      btn.textContent = '🙈';
    } else {
      input.type = 'password';
      btn.textContent = '👁️';
    }
  },

  async saveGeneral(e) {
    e.preventDefault();
    const form = e.target;
    const feedback = document.getElementById("generalFeedback");
    const saveBtn = document.getElementById("saveGeneralBtn");

    saveBtn.disabled = true;
    saveBtn.textContent = "Saving...";
    feedback.textContent = "";

    const payload = {
      user_id: form.user_id.value,
      full_name: form.full_name.value.trim(),
      phone: form.phone.value.trim(),
      address: form.address.value.trim()
    };

    try {
      const res = await fetch("api/update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.status === "success") {
        feedback.style.color = "#34d399";
        feedback.textContent = data.message;
        if (data.user) {
          this.currentUser = data.user;
          // Update display names on page
          const dAdmin = document.getElementById("displayAdminName");
          if (dAdmin) dAdmin.textContent = data.user.full_name;
          const dStaff = document.getElementById("displayStaffName");
          if (dStaff) dStaff.textContent = data.user.full_name;
          const dCust = document.getElementById("displayUserName");
          if (dCust) dCust.textContent = data.user.full_name;
        }
        setTimeout(() => this.closeModal(), 1800);
      } else {
        feedback.style.color = "#f87171";
        feedback.textContent = data.message;
      }
    } catch (err) {
      feedback.style.color = "#f87171";
      feedback.textContent = "Connection error. Please try again.";
    } finally {
      saveBtn.disabled = false;
      saveBtn.textContent = "Save Changes";
    }
  },

  async changePassword(e) {
    e.preventDefault();
    const form = e.target;
    const feedback = document.getElementById("securityFeedback");
    const saveBtn = document.getElementById("saveSecurityBtn");

    if (form.new_password.value !== form.confirm_password.value) {
      feedback.style.color = "#f87171";
      feedback.textContent = "New password and confirmation do not match.";
      return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = "Updating...";
    feedback.textContent = "";

    const payload = {
      user_id: form.user_id.value,
      current_password: form.current_password.value,
      new_password: form.new_password.value
    };

    try {
      const res = await fetch("api/change_password.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.status === "success") {
        feedback.style.color = "#34d399";
        feedback.textContent = data.message;
        form.reset();
        setTimeout(() => this.closeModal(), 2000);
      } else {
        feedback.style.color = "#f87171";
        feedback.textContent = data.message;
      }
    } catch (err) {
      feedback.style.color = "#f87171";
      feedback.textContent = "Connection error. Please try again.";
    } finally {
      saveBtn.disabled = false;
      saveBtn.textContent = "Update Password";
    }
  }
};

window.UserProfileManager = UserProfileManager;

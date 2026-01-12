import React, { useState } from "react";
import { useStickyState } from "../../hooks/useStickyState";
import { Product } from "../../types";
import {
  User as UserIcon,
  AlertCircle,
  Mail,
  ChevronRight,
  Lock,
  Shield,
  KeyRound,
  CheckCircle2,
  ArrowLeft,
  LayoutGrid,
  Plus,
  Trash2,
  Edit2,
  Check,
  X,
} from "lucide-react";

interface SettingsProps {
  categories: string[];
  setCategories: React.Dispatch<React.SetStateAction<string[]>>;
  products: Product[];
  setProducts: React.Dispatch<React.SetStateAction<Product[]>>;
}

const Settings: React.FC<SettingsProps> = ({
  categories,
  setCategories,
  products,
  setProducts,
}) => {
  const [activeSetting, setActiveSetting] = useState<
    "profile-list" | "email-form" | "password-form" | "category-list"
  >("profile-list");
 
  // Auth States
  const [currentEmail, setCurrentEmail] = useStickyState(
    "admin@store.com",
    "admin_email"
  );
  const [newEmail, setNewEmail] = useState("");
  const [emailChangePassword, setEmailChangePassword] = useState("");
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [settingsError, setSettingsError] = useState("");
  const [settingsSuccess, setSettingsSuccess] = useState("");

  // Category States
  const [newCategoryName, setNewCategoryName] = useState("");
  const [editingCategory, setEditingCategory] = useState<string | null>(null);
  const [editCategoryValue, setEditCategoryValue] = useState("");

  const handleChangeEmail = (e: React.FormEvent) => {
    e.preventDefault();
    setSettingsError("");
    setSettingsSuccess("");

    if (!newEmail || !emailChangePassword) {
      setSettingsError("Please fill in all required fields");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(newEmail)) {
      setSettingsError("Please enter a valid email address");
      return;
    }

    setSettingsSuccess("Email updated successfully!");
    setCurrentEmail(newEmail);
    setNewEmail("");
    setEmailChangePassword("");

    setTimeout(() => {
      setSettingsSuccess("");
      setActiveSetting("profile-list");
    }, 2000);
  };

  const handleChangePassword = (e: React.FormEvent) => {
    e.preventDefault();
    setSettingsError("");
    setSettingsSuccess("");

    if (!currentPassword || !newPassword || !confirmPassword) {
      setSettingsError("Please fill in all required fields");
      return;
    }

    if (newPassword !== confirmPassword) {
      setSettingsError("New passwords do not match");
      return;
    }

    if (newPassword.length < 6) {
      setSettingsError("Password must be at least 6 characters long");
      return;
    }

    setSettingsSuccess("Password security updated!");
    setCurrentPassword("");
    setNewPassword("");
    setConfirmPassword("");

    setTimeout(() => {
      setSettingsSuccess("");
      setActiveSetting("profile-list");
    }, 2000);
  };

  // Category Handlers
  const handleAddCategory = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newCategoryName.trim()) return;
    if (categories.includes(newCategoryName.trim())) {
      setSettingsError("Category already exists");
      return;
    }
    setCategories([...categories, newCategoryName.trim()]);
    setNewCategoryName("");
    setSettingsSuccess("Category added successfully");
    setTimeout(() => setSettingsSuccess(""), 2000);
  };

  const handleDeleteCategory = (catToDelete: string) => {
    if (catToDelete === "All") return;
    
    // Update products to 'All' category
    setProducts(products.map(p => 
      p.category === catToDelete ? { ...p, category: "All" } : p
    ));

    setCategories(categories.filter((c) => c !== catToDelete));
    setSettingsSuccess("Category deleted and products moved to 'All'");
    setTimeout(() => setSettingsSuccess(""), 2000);
  };

  const startEditing = (cat: string) => {
    setEditingCategory(cat);
    setEditCategoryValue(cat);
  };

  const handleSaveEdit = () => {
    const newVal = editCategoryValue.trim();
    if (!newVal || !editingCategory) return;
    if (
      categories.includes(newVal) &&
      newVal !== editingCategory
    ) {
      setSettingsError("Category name already exists");
      return;
    }

    // Update products use the renamed category
    setProducts(products.map(p => 
      p.category === editingCategory ? { ...p, category: newVal } : p
    ));

    setCategories(
      categories.map((c) =>
        c === editingCategory ? newVal : c
      )
    );
    setEditingCategory(null);
    setSettingsSuccess("Category updated");
    setTimeout(() => setSettingsSuccess(""), 2000);
  };

  return (
    <div className="flex-1 h-full overflow-hidden flex flex-col">
      <div className="flex-1 bg-white flex flex-col md:flex-row overflow-hidden animate-in fade-in duration-500">
        {/* Left Sidebar */}
        <div className="relative w-full md:w-52 lg:w-64 overflow-hidden flex flex-col border-r border-gray-50 bg-gray-50/25">
          <div className="relative z-10 p-4 lg:p-6 flex-1 flex flex-col">
            <div className="flex items-center gap-2.5 lg:gap-3 mb-5 lg:mb-8">
              <div className="p-2 lg:p-2.5 bg-[#4285F4] rounded-lg lg:rounded-xl shadow-sm">
                <Shield size={18} className="text-white lg:w-5 lg:h-5" />
              </div>
              <div>
                <h2 className="font-extrabold text-base lg:text-lg tracking-tight text-gray-900 leading-none">
                  Settings
                </h2>
              </div>
            </div>

            <nav className="space-y-0.5 lg:space-y-1">
              <button
                onClick={() => setActiveSetting("profile-list")}
                className={`w-full flex items-center gap-2 lg:gap-3 px-3.5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl transition-all duration-300 font-bold text-[11px] lg:text-sm ${
                  activeSetting === "profile-list" ||
                  activeSetting === "email-form" ||
                  activeSetting === "password-form"
                    ? "bg-white text-[#4285F4] shadow-sm border border-gray-100"
                    : "text-gray-500 hover:bg-white/60"
                }`}
              >
                <UserIcon size={14} className="lg:w-4 lg:h-4" />
                <span>Profile</span>
              </button>
              <button
                onClick={() => setActiveSetting("category-list")}
                className={`w-full flex items-center gap-2 lg:gap-3 px-3.5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl transition-all duration-300 font-bold text-[11px] lg:text-sm ${
                  activeSetting === "category-list"
                    ? "bg-white text-[#4285F4] shadow-sm border border-gray-100"
                    : "text-gray-500 hover:bg-white/60"
                }`}
              >
                <LayoutGrid size={14} className="lg:w-4 lg:h-4" />
                <span>Categories</span>
              </button>
            </nav>
          </div>
        </div>

        {/* Right Content */}
        <div className="flex-1 p-4 md:p-5 lg:p-6 bg-white flex flex-col overflow-y-auto no-scrollbar">
          {/* Messages */}
          {settingsSuccess && (
            <div className="mb-4 p-2.5 lg:p-3 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-700 text-[10px] lg:text-xs flex items-center gap-2 animate-in slide-in-from-top-4">
              <CheckCircle2 size={14} className="lg:w-4 lg:h-4" />
              <span className="font-bold">{settingsSuccess}</span>
            </div>
          )}
          {settingsError && (
            <div className="mb-4 p-2.5 lg:p-3 bg-rose-50 border border-rose-100 rounded-lg text-rose-700 text-[10px] lg:text-xs flex items-center gap-2 animate-in slide-in-from-top-4">
              <AlertCircle size={14} className="lg:w-4 lg:h-4" />
              <span className="font-bold">{settingsError}</span>
            </div>
          )}

          {activeSetting === "profile-list" && (
            <div className="animate-in fade-in slide-in-from-right-4 duration-500">
              <div className="mb-4 lg:mb-6">
                <h3 className="text-xl lg:text-2xl font-extrabold text-gray-900 mb-0.5">
                  Profile
                </h3>
                <p className="text-gray-500 text-xs lg:text-sm font-medium">
                  Account identity and security settings.
                </p>
              </div>

              <div className="space-y-1 max-w-lg lg:max-w-xl">
                <button
                  onClick={() => setActiveSetting("email-form")}
                  className="w-full group flex items-center justify-between py-2 px-3 lg:py-2 lg:px-4 bg-gray-50 hover:bg-white border border-gray-100 hover:border-blue-100 hover:shadow-sm rounded-xl transition-all duration-300"
                >
                  <div className="flex items-center gap-3 lg:gap-4">
                    <div className="w-7 h-7 lg:w-9 lg:h-9 bg-white group-hover:bg-blue-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:text-[#4285F4] transition-all border border-gray-50 lg:border-gray-100 group-hover:border-blue-100">
                      <Mail size={14} className="lg:w-4 lg:h-4" />
                    </div>
                    <p className="text-gray-900 font-bold text-xs lg:text-sm">
                      Update Email
                    </p>
                  </div>
                  <ChevronRight
                    size={13}
                    className="text-gray-300 lg:w-4 lg:h-4 group-hover:text-[#4285F4] group-hover:translate-x-1 transition-all"
                  />
                </button>

                <button
                  onClick={() => setActiveSetting("password-form")}
                  className="w-full group flex items-center justify-between py-2 px-3 lg:py-2 lg:px-4 bg-gray-50 hover:bg-white border border-gray-100 hover:border-blue-100 hover:shadow-sm rounded-xl transition-all duration-300"
                >
                  <div className="flex items-center gap-3 lg:gap-4">
                    <div className="w-7 h-7 lg:w-9 lg:h-9 bg-white group-hover:bg-blue-50 rounded-lg flex items-center justify-center text-gray-400 group-hover:text-[#4285F4] transition-all border border-gray-50 lg:border-gray-100 group-hover:border-blue-100">
                      <KeyRound size={14} className="lg:w-4 lg:h-4" />
                    </div>
                    <p className="text-gray-900 font-bold text-xs lg:text-sm">
                      Update Password
                    </p>
                  </div>
                  <ChevronRight
                    size={13}
                    className="text-gray-300 lg:w-4 lg:h-4 group-hover:text-[#4285F4] group-hover:translate-x-1 transition-all"
                  />
                </button>
              </div>
            </div>
          )}

          {activeSetting === "category-list" && (
            <div className="animate-in fade-in slide-in-from-right-4 duration-500 flex flex-col h-full overflow-hidden">
              <div className="mb-4 lg:mb-6">
                <h3 className="text-xl lg:text-2xl font-extrabold text-gray-900 mb-0.5">
                  Categories
                </h3>
                <p className="text-gray-500 text-xs lg:text-sm font-medium">
                  Manage store classification.
                </p>
              </div>

              {/* Add category form */}
              <form
                onSubmit={handleAddCategory}
                className="flex gap-2 lg:gap-2 mb-4 lg:mb-6"
              >
                <div className="relative flex-1 max-w-xs lg:max-w-sm">
                  <div className="absolute inset-y-0 left-0 pl-2.5 lg:pl-3 flex items-center pointer-events-none text-gray-400">
                    <LayoutGrid size={14} className="lg:w-4 lg:h-4" />
                  </div>
                  <input
                    type="text"
                    value={newCategoryName}
                    onChange={(e) => setNewCategoryName(e.target.value)}
                    placeholder="Add category..."
                    className="w-full pl-8 lg:pl-10 pr-3 lg:pr-4 py-1.5 lg:py-2 rounded-lg lg:rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/5 focus:border-blue-500 text-gray-900 font-bold text-[11px] lg:text-sm transition-all"
                  />
                </div>
                <button
                  type="submit"
                  disabled={!newCategoryName.trim()}
                  className="px-4 lg:px-6 py-1.5 lg:py-2 bg-[#4285F4] text-white rounded-lg lg:rounded-xl font-bold flex items-center gap-1.5 lg:gap-2 hover:shadow-md hover:shadow-blue-500/10 disabled:opacity-50 transition-all text-[11px] lg:text-sm"
                >
                  <Plus size={14} className="lg:w-4 lg:h-4" />
                  <span>Add</span>
                </button>
              </form>

              {/* Category List */}
              <div className="flex-1 overflow-y-auto no-scrollbar pr-1 space-y-1">
                {categories.map((cat) => (
                  <div
                    key={cat}
                    className="group flex items-center justify-between py-1.5 px-3 lg:py-1.5 lg:px-4 bg-gray-50/50 rounded-lg lg:rounded-xl border border-gray-100 hover:bg-white hover:border-blue-50 hover:shadow-xs transition-all duration-200"
                  >
                    {editingCategory === cat ? (
                      <div className="flex-1 flex items-center gap-2 lg:gap-2">
                        <input
                          autoFocus
                          type="text"
                          value={editCategoryValue}
                          onChange={(e) => setEditCategoryValue(e.target.value)}
                          onKeyDown={(e) =>
                            e.key === "Enter" && handleSaveEdit()
                          }
                          className="flex-1 px-2 py-1 lg:px-2.5 lg:py-1 rounded-md lg:rounded-lg border border-blue-200 outline-none focus:ring-2 focus:ring-blue-500/5 text-gray-900 font-bold bg-white text-[11px] lg:text-sm"
                        />
                        <button
                          onClick={handleSaveEdit}
                          className="p-1 lg:p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-md lg:rounded-lg transition-colors"
                        >
                          <Check size={14} className="lg:w-4 lg:h-4" />
                        </button>
                        <button
                          onClick={() => setEditingCategory(null)}
                          className="p-1 lg:p-1.5 text-rose-600 hover:bg-rose-50 rounded-md lg:rounded-lg transition-colors"
                        >
                          <X size={14} className="lg:w-4 lg:h-4" />
                        </button>
                      </div>
                    ) : (
                      <>
                        <div className="flex items-center gap-2.5 lg:gap-3">
                          <div className="w-7 h-7 lg:w-8 lg:h-8 bg-white rounded-md lg:rounded-lg flex items-center justify-center text-gray-400 group-hover:text-[#4285F4] transition-colors border border-gray-50 lg:border-gray-100">
                            <LayoutGrid size={12} className="lg:w-4 lg:h-4" />
                          </div>
                          <span className="font-bold text-gray-800 text-[11px] lg:text-sm">
                            {cat}
                          </span>
                        </div>
                        <div className="flex items-center gap-0.5 lg:gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                          {cat !== "All" && (
                            <>
                              <button
                                onClick={() => startEditing(cat)}
                                className="p-1 lg:p-1 text-gray-400 hover:text-[#4285F4] hover:bg-blue-50 rounded-md lg:rounded-lg transition-all"
                                title="Rename"
                              >
                                <Edit2
                                  size={12}
                                  className="lg:w-3.5 lg:h-3.5"
                                />
                              </button>
                              <button
                                onClick={() => handleDeleteCategory(cat)}
                                className="p-1 lg:p-1 text-gray-400 hover:text-[#EA4335] hover:bg-red-50 rounded-md lg:rounded-lg transition-all"
                                title="Delete"
                              >
                                <Trash2
                                  size={12}
                                  className="lg:w-3.5 lg:h-3.5"
                                />
                              </button>
                            </>
                          )}
                        </div>
                      </>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeSetting === "email-form" && (
            <div className="animate-in fade-in slide-in-from-right-4 duration-500 max-w-xs lg:max-w-sm">
              <button
                onClick={() => setActiveSetting("profile-list")}
                className="mb-6 lg:mb-8 flex items-center gap-1.5 lg:gap-2 text-gray-500 hover:text-[#4285F4] font-bold text-[10px] lg:text-xs uppercase tracking-widest transition-colors group"
              >
                <ArrowLeft
                  size={12}
                  className="lg:w-4 lg:h-4 group-hover:-translate-x-0.5 lg:group-hover:-translate-x-1 transition-transform"
                />
                Back
              </button>

              <h3 className="text-lg lg:text-xl font-extrabold text-gray-900 mb-0.5">
                Update Email
              </h3>
              <p className="text-gray-500 text-[11px] lg:text-xs font-medium mb-6 lg:mb-8">
                Change admin contact address.
              </p>

              <div className="space-y-3 lg:space-y-4">
                <div className="space-y-1">
                  <label className="text-[10px] lg:text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    Current Email
                  </label>
                  <div className="px-1 py-2 text-gray-600 font-bold text-xs lg:text-sm">
                    {currentEmail}
                  </div>
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] lg:text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    New Email
                  </label>
                  <div className="relative">
                    <Mail
                      size={14}
                      className="absolute left-2.5 lg:left-3.5 top-1/2 -translate-y-1/2 text-gray-300 lg:w-4 lg:h-4"
                    />
                    <input
                      type="email"
                      value={newEmail}
                      onChange={(e) => setNewEmail(e.target.value)}
                      placeholder="admin@example.com"
                      className="w-full pl-8 lg:pl-10 pr-3 lg:pr-4 py-1.5 lg:py-2.5 rounded-lg lg:rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/5 focus:border-blue-500 text-gray-900 font-bold text-[11px] lg:text-sm transition-all"
                    />
                  </div>
                </div>
                <div className="space-y-1">
                  <label className="text-[10px] lg:text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    Password
                  </label>
                  <div className="relative">
                    <Lock
                      size={14}
                      className="absolute left-2.5 lg:left-3.5 top-1/2 -translate-y-1/2 text-gray-300 lg:w-4 lg:h-4"
                    />
                    <input
                      type="password"
                      value={emailChangePassword}
                      onChange={(e) => setEmailChangePassword(e.target.value)}
                      placeholder="Verify identity"
                      className="w-full pl-8 lg:pl-10 pr-3 lg:pr-4 py-1.5 lg:py-2.5 rounded-lg lg:rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/5 focus:border-blue-500 text-gray-900 font-bold text-[11px] lg:text-sm transition-all"
                    />
                  </div>
                </div>
                <button
                  onClick={handleChangeEmail}
                  className="w-full bg-[#4285F4] text-white py-2 lg:py-2.5 rounded-lg lg:rounded-xl font-bold hover:shadow-md lg:hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] transition-all text-[11px] lg:text-sm mt-2 lg:mt-3"
                >
                  Save Changes
                </button>
              </div>
            </div>
          )}

          {activeSetting === "password-form" && (
            <div className="animate-in fade-in slide-in-from-right-4 duration-500 max-w-xs lg:max-w-sm">
              <button
                onClick={() => setActiveSetting("profile-list")}
                className="mb-6 lg:mb-8 flex items-center gap-1.5 lg:gap-2 text-gray-500 hover:text-[#4285F4] font-bold text-[10px] lg:text-xs uppercase tracking-widest transition-colors group"
              >
                <ArrowLeft
                  size={12}
                  className="lg:w-4 lg:h-4 group-hover:-translate-x-0.5 lg:group-hover:-translate-x-1 transition-transform"
                />
                Back
              </button>

              <h3 className="text-lg lg:text-xl font-extrabold text-gray-900 mb-0.5">
                Security
              </h3>
              <p className="text-gray-500 text-[11px] lg:text-xs font-medium mb-6 lg:mb-8">
                Update admin password.
              </p>

              <div className="space-y-2 lg:space-y-3">
                <div className="space-y-1">
                  <label className="text-[10px] lg:text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    Current
                  </label>
                  <div className="relative">
                    <Lock
                      size={14}
                      className="absolute left-2.5 lg:left-3.5 top-1/2 -translate-y-1/2 text-gray-300 lg:w-4 lg:h-4"
                    />
                    <input
                      type="password"
                      value={currentPassword}
                      onChange={(e) => setCurrentPassword(e.target.value)}
                      placeholder="••••••••"
                      className="w-full pl-8 lg:pl-10 pr-3 lg:pr-4 py-1.5 lg:py-2.5 rounded-lg lg:rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/5 focus:border-blue-500 text-gray-900 font-bold text-[11px] lg:text-sm transition-all"
                    />
                  </div>
                </div>
                <div className="space-y-1">
                  <label className="text-[10px] lg:text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    New
                  </label>
                  <div className="relative">
                    <KeyRound
                      size={14}
                      className="absolute left-2.5 lg:left-3.5 top-1/2 -translate-y-1/2 text-gray-300 lg:w-4 lg:h-4"
                    />
                    <input
                      type="password"
                      value={newPassword}
                      onChange={(e) => setNewPassword(e.target.value)}
                      placeholder="Min 6 characters"
                      className="w-full pl-8 lg:pl-10 pr-3 lg:pr-4 py-1.5 lg:py-2.5 rounded-lg lg:rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/5 focus:border-blue-500 text-gray-900 font-bold text-[11px] lg:text-sm transition-all"
                    />
                  </div>
                </div>
                <div className="space-y-1">
                  <label className="text-[10px] lg:text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    Confirm
                  </label>
                  <div className="relative">
                    <KeyRound
                      size={14}
                      className="absolute left-2.5 lg:left-3.5 top-1/2 -translate-y-1/2 text-gray-300 lg:w-4 lg:h-4"
                    />
                    <input
                      type="password"
                      value={confirmPassword}
                      onChange={(e) => setConfirmPassword(e.target.value)}
                      placeholder="Repeat password"
                      className="w-full pl-8 lg:pl-10 pr-3 lg:pr-4 py-1.5 lg:py-2.5 rounded-lg lg:rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/5 focus:border-blue-500 text-gray-900 font-bold text-[11px] lg:text-sm transition-all"
                    />
                  </div>
                </div>
                <button
                  onClick={handleChangePassword}
                  className="w-full bg-[#4285F4] text-white py-2 lg:py-2.5 rounded-lg lg:rounded-xl font-bold hover:shadow-md lg:hover:shadow-lg hover:shadow-blue-500/10 active:scale-[0.98] transition-all text-[11px] lg:text-sm mt-2 lg:mt-3"
                >
                  Update Password
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Settings;

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin Dashboard - Sistem Penilaian Digital</title>
    <link rel="stylesheet" href="{{ asset('css/superadmin.css') }}">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎯 Dashboard Superadmin</h1>
            <p>Sistem Manajemen Arena, User, dan Pertandingan</p>
        </div>

        <!-- Tab Navigation -->
        <div class="tab-nav">
            <button class="tab-btn active" onclick="switchTab('arena')">
                📍 Kelola Arena
            </button>
            <button class="tab-btn" onclick="switchTab('users')">
                👥 Kelola User
            </button>
            <button class="tab-btn" onclick="switchTab('assignment')">
                🔗 Penempatan User
            </button>
            <button class="tab-btn" onclick="switchTab('matches')">
                🏆 Kelola Pertandingan
            </button>
        </div>

        <!-- Arena Management Tab -->
        <div id="arena-tab" class="tab-content active">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Kelola Arena</h2>
                    <button class="btn btn-primary" onclick="openArenaModal('add')">
                        ➕ Tambah Arena
                    </button>
                </div>

                <div class="controls">
                    <input type="text" class="search-box" id="arenaSearch" placeholder="🔍 Cari arena..." onkeyup="searchTable('arenaTable', 'arenaSearch')">
                </div>

                <table class="data-table" id="arenaTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Arena</th>
                            <th>Dibuat Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="arenaTableBody">
                        <tr><td colspan="4" class="loading">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Management Tab -->
        <div id="users-tab" class="tab-content">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Kelola User</h2>
                    <button class="btn btn-primary" onclick="openUserModal('add')">
                        ➕ Tambah User
                    </button>
                </div>

                <div class="controls">
                    <input type="text" class="search-box" id="userSearch" placeholder="🔍 Cari user..." onkeyup="searchTable('userTable', 'userSearch')">
                    <select class="filter-select" id="roleFilter" onchange="filterByRole()">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="juri_1">Juri 1</option>
                        <option value="juri_2">Juri 2</option>
                        <option value="juri_3">Juri 3</option>
                        <option value="juri_4">Juri 4</option>
                        <option value="juri_5">Juri 5</option>
                        <option value="juri_6">Juri 6</option>
                        <option value="juri_7">Juri 7</option>
                        <option value="juri_8">Juri 8</option>
                        <option value="juri_9">Juri 9</option>
                        <option value="juri_10">Juri 10</option>
                        <option value="dewan">Dewan</option>
                        <option value="operator">Operator</option>
                    </select>
                </div>

                <table class="data-table" id="userTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="5" class="loading">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User-Arena Assignment Tab -->
        <div id="assignment-tab" class="tab-content">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Penempatan User ke Arena</h2>
                    <button class="btn btn-primary" onclick="openAssignmentModal()">
                        ➕ Tambah Penempatan
                    </button>
                </div>

                <div class="assignment-grid" id="assignmentGrid">
                    <div class="loading">Memuat data...</div>
                </div>
            </div>
        </div>

        <!-- Match Management Tab -->
        <div id="matches-tab" class="tab-content">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Kelola Pertandingan</h2>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-primary" onclick="openMatchModal()">
                            ➕ Tambah Pertandingan
                        </button>
                        <button class="btn btn-success" onclick="openImportCsvModal()">
                            📁 Import CSV
                        </button>
                    </div>
                </div>

                <div class="controls">
                    <input type="text" class="search-box" id="matchSearch" placeholder="🔍 Cari pertandingan..." onkeyup="searchTable('matchTable', 'matchSearch')">
                    <select class="filter-select" id="statusFilter" onchange="filterByStatus()">
                        <option value="">Semua Status</option>
                        <option value="belum_dimulai">Belum Dimulai</option>
                        <option value="berlangsung">Berlangsung</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <table class="data-table" id="matchTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kelas</th>
                            <th>Players & Contingent</th>
                            <th>Arena</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="matchTableBody">
                        <tr><td colspan="6" class="loading">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Arena Modal -->
    <div id="arenaModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="arenaModalTitle">Tambah Arena</h3>
                <button class="close-btn" onclick="closeModal('arenaModal')">×</button>
            </div>
            <form id="arenaForm" onsubmit="submitArena(event)">
                <input type="hidden" id="arenaId">
                <div class="form-group">
                    <label class="form-label">Nama Arena</label>
                    <input type="text" class="form-control" id="arenaName" placeholder="Masukkan nama arena" required>
                </div>
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
            </form>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="userModalTitle">Tambah User</h3>
                <button class="close-btn" onclick="closeModal('userModal')">×</button>
            </div>
            <form id="userForm" onsubmit="submitUser(event)">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="fullName" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span id="passwordHint" style="font-size:0.85em; color:#718096;">(optional untuk edit)</span></label>
                    <input type="password" class="form-control" id="password" placeholder="Masukkan password">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select class="form-control" id="role" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="juri_1">Juri 1</option>
                        <option value="juri_2">Juri 2</option>
                        <option value="juri_3">Juri 3</option>
                        <option value="juri_4">Juri 4</option>
                        <option value="juri_5">Juri 5</option>
                        <option value="juri_6">Juri 6</option>
                        <option value="juri_7">Juri 7</option>
                        <option value="juri_8">Juri 8</option>
                        <option value="juri_9">Juri 9</option>
                        <option value="juri_10">Juri 10</option>
                        <option value="dewan">Dewan</option>
                        <option value="operator">Operator</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
            </form>
        </div>
    </div>

    <!-- Assignment Modal -->
    <div id="assignmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Penempatan User</h3>
                <button class="close-btn" onclick="closeModal('assignmentModal')">×</button>
            </div>
            <form id="assignmentForm" onsubmit="submitAssignment(event)">
                <div class="form-group">
                    <label class="form-label">Pilih User</label>
                    <select class="form-control" id="assignUser" required>
                        <option value="">Pilih User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Pilih Arena</label>
                    <select class="form-control" id="assignArena" required>
                        <option value="">Pilih Arena</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">💾 Simpan Penempatan</button>
            </form>
        </div>
    </div>

    <!-- Match Detail Modal -->
    <div id="matchDetailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Pertandingan</h3>
                <button class="close-btn" onclick="closeModal('matchDetailModal')">×</button>
            </div>
            <div id="matchDetailContent">
                <div class="loading">Memuat detail...</div>
            </div>
        </div>
    </div>

    <!-- Create Match Modal -->
    <div id="matchModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Pertandingan Baru</h3>
                <button class="close-btn" onclick="closeModal('matchModal')">×</button>
            </div>
            <form id="matchForm" onsubmit="submitMatch(event)">
                <div class="form-group">
                    <label class="form-label">Kelas Pertandingan</label>
                    <select class="form-control" id="matchKelas" required>
                        <option value="">Pilih Kelas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Arena</label>
                    <select class="form-control" id="matchArenaSelect" required>
                        <option value="">Pilih Arena</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" id="matchStatusSelect" required>
                        <option value="belum_dimulai">Belum Dimulai</option>
                        <option value="berlangsung">Berlangsung</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <hr style="margin: 2rem 0; border-color: var(--border-color);">

                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Players Tim 1 (Biru 🔵)</h4>
                <div id="team1Players">
                    <div class="player-row" style="margin-bottom: 1rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: end;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Nama Player</label>
                                <input type="text" class="form-control player-name" placeholder="Nama player" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Contingent</label>
                                <input type="text" class="form-control player-contingent" placeholder="Contingent" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="addPlayer(1)" style="margin-bottom: 1.5rem;">
                    ➕ Tambah Player Tim 1
                </button>

                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Players Tim 2 (Merah 🔴)</h4>
                <div id="team2Players">
                    <div class="player-row" style="margin-bottom: 1rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: end;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Nama Player</label>
                                <input type="text" class="form-control player-name" placeholder="Nama player" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Contingent</label>
                                <input type="text" class="form-control player-contingent" placeholder="Contingent" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="addPlayer(2)" style="margin-bottom: 1.5rem;">
                    ➕ Tambah Player Tim 2
                </button>

                <button type="submit" class="btn btn-primary">💾 Simpan Pertandingan</button>
            </form>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div id="importCsvModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 class="modal-title">Import Pertandingan dari CSV</h3>
                <button class="close-btn" onclick="closeModal('importCsvModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">File CSV</label>
                    <input type="file" id="csvFileInput" accept=".csv" class="form-control" style="padding: 10px;">
                    <small class="form-text">Format: Partai, Kategori, Jenis Pertandingan, Kelas, Unit 1, Kontingen 1, Unit 2, Kontingen 2, Arena, next match</small>
                </div>
                <div id="importProgress" style="margin-top: 15px; display: none;">
                    <div class="loading">Mengupload dan memproses...</div>
                </div>
                <div id="importResults" style="margin-top: 15px; display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('importCsvModal')">Batal</button>
                <button type="button" class="btn btn-success" onclick="uploadCsvFile()">Upload & Import</button>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let currentEditArenaId = null;
        let currentEditUserId = null;
        let allMatches = [];
        let allUsers = [];
        let allArenas = [];

        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Helper function for API calls
        async function apiCall(url, method = 'GET', data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };
            if (data && method !== 'GET') {
                options.body = JSON.stringify(data);
            }
            
            const response = await fetch(url, options);
            return response.json();
        }

        // Tab Switching
        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(btn => btn.classList.remove('active'));

            document.getElementById(`${tabName}-tab`).classList.add('active');
            event.target.classList.add('active');

            // Load data when switch to tab
            if (tabName === 'arena') loadArenas();
            else if (tabName === 'users') loadUsers();
            else if (tabName === 'assignment') loadAssignments();
            else if (tabName === 'matches') loadMatches();
        }

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });

        // Search & Filter Functions
        function searchTable(tableId, searchId) {
            const input = document.getElementById(searchId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent || row.innerText;
                row.style.display = text.toLowerCase().includes(filter) ? '' : 'none';
            });
        }

        function filterByRole() {
            const filter = document.getElementById('roleFilter').value;
            const rows = document.querySelectorAll('#userTableBody tr');
            
            rows.forEach(row => {
                if (filter === '' || row.dataset.role === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function filterByStatus() {
            const filter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#matchTableBody tr');
            
            rows.forEach(row => {
                if (filter === '' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                   row.style.display = 'none';
                }
            });
        }

        // ========== ARENA CRUD Functions ==========
        async function loadArenas() {
            const tbody = document.getElementById('arenaTableBody');
            tbody.innerHTML = '<tr><td colspan="4" class="loading">Memuat data...</td></tr>';
            
            try {
                const arenas = await apiCall('/api/superadmin/arenas');
                allArenas = arenas;
                
                if (arenas.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#718096;">Belum ada data arena</td></tr>';
                    return;
                }
                
                tbody.innerHTML = arenas.map(arena => `
                    <tr>
                        <td data-label="ID">${arena.id}</td>
                        <td data-label="Nama Arena">${arena.arena_name}</td>
                        <td data-label="Dibuat Pada">${new Date(arena.created_at).toLocaleDateString('id-ID')}</td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <button class="btn btn-success btn-sm" onclick="editArena(${arena.id})">✏️ Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteArena(${arena.id})">🗑️ Hapus</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#ef4444;">Error loading data</td></tr>';
                console.error('Error loading arenas:', error);
            }
        }

        function openArenaModal(mode, id = null) {
            currentEditArenaId = id;
            const title = document.getElementById('arenaModalTitle');
            const form = document.getElementById('arenaForm');
            
            form.reset();
            document.getElementById('arenaId').value = id || '';
            
            if (mode === 'add') {
                title.textContent = 'Tambah Arena';
            } else {
                title.textContent = 'Edit Arena';
                const arena = allArenas.find(a => a.id == id);
                if (arena) {
                    document.getElementById('arenaName').value = arena.arena_name;
                }
            }
            
            openModal('arenaModal');
        }

        async function submitArena(event) {
            event.preventDefault();
            const id = document.getElementById('arenaId').value;
            const name = document.getElementById('arenaName').value;
            
            try {
                const url = id ? `/api/superadmin/arenas/${id}` : '/api/superadmin/arenas';
                const method = id ? 'PUT' : 'POST';
                
                const result = await apiCall(url, method, { arena_name: name });
                
                if (result.success) {
                    alert(result.message);
                    closeModal('arenaModal');
                    loadArenas();
                } else {
                    alert('Error: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function editArena(id) {
            openArenaModal('edit', id);
        }

        async function deleteArena(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus arena ini?')) return;
            
            try {
                const result = await apiCall(`/api/superadmin/arenas/${id}`, 'DELETE');
                if (result.success) {
                    alert(result.message);
                    loadArenas();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // ========== USER CRUD Functions ==========
        async function loadUsers() {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '<tr><td colspan="5" class="loading">Memuat data...</td></tr>';
            
            try {
                const users = await apiCall('/api/superadmin/users');
                allUsers = users;
                
                if (users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#718096;">Belum ada data user</td></tr>';
                    return;
                }
                
                const roleBadges = {
                    'admin': 'badge-danger',
                    'juri_1': 'badge-info',
                    'juri_2': 'badge-info',
                    'juri_3': 'badge-info',
                    'juri_4': 'badge-info',
                    'juri_5': 'badge-info',
                    'juri_6': 'badge-info',
                    'juri_7': 'badge-info',
                    'juri_8': 'badge-info',
                    'juri_9': 'badge-info',
                    'juri_10': 'badge-info',
                    'dewan': 'badge-warning',
                    'operator': 'badge-success'
                };
                
                tbody.innerHTML = users.map(user => `
                    <tr data-role="${user.role}">
                        <td data-label="ID">${user.id}</td>
                        <td data-label="Username">${user.username}</td>
                        <td data-label="Nama Lengkap">${user.name}</td>
                        <td data-label="Role"><span class="badge ${roleBadges[user.role] || 'badge-info'}">${user.role}</span></td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <button class="btn btn-success btn-sm" onclick="editUser(${user.id})">✏️ Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">🗑️ Hapus</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#ef4444;">Error loading data</td></tr>';
                console.error('Error loading users:', error);
            }
        }

        function openUserModal(mode, id = null) {
            currentEditUserId = id;
            const title = document.getElementById('userModalTitle');
            const form = document.getElementById('userForm');
            const passwordField = document.getElementById('password');
            const passwordHint = document.getElementById('passwordHint');
            
            form.reset();
            document.getElementById('userId').value = id || '';
            
            if (mode === 'add') {
                title.textContent = 'Tambah User';
                passwordField.required = true;
                passwordHint.style.display = 'none';
            } else {
                title.textContent = 'Edit User';
                passwordField.required = false;
                passwordHint.style.display = 'inline';
                
                const user = allUsers.find(u => u.id == id);
                if (user) {
                    document.getElementById('username').value = user.username;
                    document.getElementById('fullName').value = user.name;
                    document.getElementById('role').value = user.role;
                }
            }
            
            openModal('userModal');
        }

        async function submitUser(event) {
            event.preventDefault();
            const id = document.getElementById('userId').value;
            const data = {
                username: document.getElementById('username').value,
                name: document.getElementById('fullName').value,
                role: document.getElementById('role').value
            };
            
            const password = document.getElementById('password').value;
            if (password) {
                data.password = password;
            }
            
            try {
                const url = id ? `/api/superadmin/users/${id}` : '/api/superadmin/users';
                const method = id ? 'PUT' : 'POST';
                
                const result = await apiCall(url, method, data);
                
                if (result.success) {
                    alert(result.message);
                    closeModal('userModal');
                    loadUsers();
                } else {
                    alert('Error: ' + (result.message || JSON.stringify(result.errors)));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function editUser(id) {
            openUserModal('edit', id);
        }

        async function deleteUser(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return;
            
            try {
                const result = await apiCall(`/api/superadmin/users/${id}`, 'DELETE');
                if (result.success) {
                    alert(result.message);
                    loadUsers();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // ========== ASSIGNMENT Functions ==========
        async function loadAssignments() {
            const grid = document.getElementById('assignmentGrid');
            grid.innerHTML = '<div class="loading">Memuat data...</div>';
            
            try {
                const arenas = await apiCall('/api/superadmin/assignments');
                
                if (arenas.length === 0) {
                    grid.innerHTML = '<div style="text-align:center; color:#718096;">Belum ada data arena</div>';
                    return;
                }
                
                grid.innerHTML = arenas.map(arena => `
                    <div class="assignment-card">
                        <h4>📍 ${arena.arena_name}</h4>
                        <div class="user-tags">
                            ${arena.users && arena.users.length > 0 ? arena.users.map(user => `
                                <span class="user-tag">
                                    ${user.name} (${user.role})
                                    <span class="remove-tag" onclick="removeAssignment(${arena.id}, ${user.id})">✕</span>
                                </span>
                            `).join('') : '<span style="color:#718096; font-size:0.9em;">Belum ada user</span>'}
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                grid.innerHTML = '<div style="text-align:center; color:#ef4444;">Error loading data</div>';
                console.error('Error loading assignments:', error);
            }
        }

        async function openAssignmentModal() {
            // Load users and arenas for dropdowns
            try {
                const [users, arenas] = await Promise.all([
                    apiCall('/api/superadmin/users'),
                    apiCall('/api/superadmin/arenas')
                ]);
                
                const userSelect = document.getElementById('assignUser');
                const arenaSelect = document.getElementById('assignArena');
                
                userSelect.innerHTML = '<option value="">Pilih User</option>' + 
                    users.map(u => `<option value="${u.id}">${u.name} (${u.username})</option>`).join('');
                
                arenaSelect.innerHTML = '<option value="">Pilih Arena</option>' + 
                    arenas.map(a => `<option value="${a.id}">${a.arena_name}</option>`).join('');
                
                openModal('assignmentModal');
            } catch (error) {
                alert('Error loading data: ' + error.message);
            }
        }

        async function submitAssignment(event) {
            event.preventDefault();
            const data = {
                user_id: document.getElementById('assignUser').value,
                arena_id: document.getElementById('assignArena').value
            };
            
            try {
                const result = await apiCall('/api/superadmin/assignments', 'POST', data);
                
                if (result.success) {
                    alert(result.message);
                    closeModal('assignmentModal');
                    loadAssignments();
                } else {
                    alert('Error: ' + (result.message || JSON.stringify(result.errors)));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function removeAssignment(arenaId, userId) {
            if (!confirm('Apakah Anda yakin ingin menghapus penempatan ini?')) return;
            
            try {
                const result = await apiCall(`/api/superadmin/assignments/${arenaId}/${userId}`, 'DELETE');
                if (result.success) {
                    alert(result.message);
                    loadAssignments();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // ========== MATCH Functions ==========
        async function loadMatches() {
            const tbody = document.getElementById('matchTableBody');
            tbody.innerHTML = '<tr><td colspan="6" class="loading">Memuat data...</td></tr>';
            
            try {
                const matches = await apiCall('/api/superadmin/matches');
                allMatches = matches;
                
                if (matches.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#718096;">Belum ada data pertandingan</td></tr>';
                    return;
                }
                
                const statusBadges = {
                    'belum_dimulai': 'badge-info',
                    'berlangsung': 'badge-warning',
                    'selesai': 'badge-success'
                };
                
                tbody.innerHTML = matches.map(match => {
                    const players = match.players || [];
                    
                    // Group players by side_number
                    const team1 = players.filter(p => p.side_number === 1);
                    const team2 = players.filter(p => p.side_number === 2);
                    
                    // Format team players
                    const team1Text = team1.length > 0 
                        ? team1.map(p => `${p.player_name} (${p.player_contingent})`).join(', ')
                        : '-';
                    const team2Text = team2.length > 0 
                        ? team2.map(p => `${p.player_name} (${p.player_contingent})`).join(', ')
                        : '-';
                    
                    const playerText = team1Text + ' <strong>vs</strong> ' + team2Text;
                    
                    return `
                        <tr data-status="${match.status}" data-arena="${match.arena_id}">
                            <td data-label="ID">${match.id}</td>
                            <td data-label="Kelas">${match.kelas ? match.kelas.nama_kelas : '-'}</td>
                            <td data-label="Players & Contingent">${playerText}</td>
                            <td data-label="Arena">${match.arena ? match.arena.arena_name : '-'}</td>
                            <td data-label="Status"><span class="badge ${statusBadges[match.status] || 'badge-info'}">${match.status.replace('_', ' ')}</span></td>
                            <td data-label="Aksi">
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-sm" onclick="viewMatchDetail(${match.id})">👁️ Detail</button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#ef4444;">Error loading data</td></tr>';
                console.error('Error loading matches:', error);
            }
        }

        async function viewMatchDetail(matchId) {
            openModal('matchDetailModal');
            const content = document.getElementById('matchDetailContent');
            content.innerHTML = '<div class="loading">Memuat detail...</div>';
            
            try {
                const result = await apiCall(`/api/superadmin/matches/${matchId}`);
                const match = result.data;
                
                const statusBadges = {
                    'belum_dimulai': 'badge-info',
                    'berlangsung': 'badge-warning',
                    'selesai': 'badge-success'
                };
                
                const players = match.players || [];
                
                content.innerHTML = `
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-label">ID Pertandingan:</span>
                            <span class="detail-value">${match.id}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Kelas:</span>
                            <span class="detail-value">${match.kelas ? match.kelas.nama_kelas : '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Arena:</span>
                            <span class="detail-value">${match.arena ? match.arena.arena_name : '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status Saat Ini:</span>
                            <span class="detail-value"><span class="badge ${statusBadges[match.status]}">${match.status.replace('_', ' ')}</span></span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4 style="margin-bottom:1rem; color:var(--text-primary);">Players:</h4>
                        ${players.length > 0 ? `
                            <ul class="player-list">
                                ${players.map(player => `
                                    <li class="player-item">
                                        <strong>${player.player_name}</strong> - ${player.player_contingent}
                                        ${player.side_number === 1 ? '🔵' : '🔴'}
                                    </li>
                                `).join('')}
                            </ul>
                        ` : '<p style="color:#718096;">Belum ada player</p>'}
                    </div>
                    
                    <div class="detail-section">
                        <h4 style="margin-bottom:1rem; color:var(--text-primary);">Ubah Status:</h4>
                        <div class="form-group">
                            <select class="form-control" id="matchStatusDropdown">
                                <option value="belum_dimulai" ${match.status === 'belum_dimulai' ? 'selected' : ''}>Belum Dimulai</option>
                                <option value="berlangsung" ${match.status === 'berlangsung' ? 'selected' : ''}>Berlangsung</option>
                                <option value="selesai" ${match.status === 'selesai' ? 'selected' : ''}>Selesai</option>
                            </select>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="saveMatchStatus(${match.id})">💾 Simpan Status</button>
                    </div>
                    
                    <div class="detail-section">
                        <h4 style="margin-bottom:1rem; color:var(--text-primary);">Pindah Arena:</h4>
                        <div class="form-group">
                            <select class="form-control" id="matchArenaDropdown">
                                ${allArenas.map(arena => `
                                    <option value="${arena.id}" ${match.arena_id == arena.id ? 'selected' : ''}>${arena.arena_name}</option>
                                `).join('')}
                            </select>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="saveMatchArena(${match.id})">💾 Simpan Arena</button>
                    </div>
                `;
            } catch (error) {
                content.innerHTML = '<div style="color:#ef4444;">Error loading detail</div>';
                console.error('Error loading match detail:', error);
            }
        }

        async function saveMatchStatus(matchId) {
            const dropdown = document.getElementById('matchStatusDropdown');
            const status = dropdown.value;
            
            if (!confirm(`Ubah status pertandingan menjadi "${status.replace('_', ' ')}"?`)) {
                return;
            }
            
            try {
                const result = await apiCall(`/api/superadmin/matches/${matchId}/status`, 'PUT', { status });
                if (result.success) {
                    alert(result.message);
                    loadMatches();
                    viewMatchDetail(matchId); // Refresh detail
                } else {
                    alert('Error: ' + (result.message || 'Gagal update status'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function saveMatchArena(matchId) {
            const dropdown = document.getElementById('matchArenaDropdown');
            const arenaId = dropdown.value;
            
            if (!confirm('Pindahkan pertandingan ke arena ini?')) {
                return;
            }
            
            try {
                const result = await apiCall(`/api/superadmin/matches/${matchId}/arena`, 'PUT', { arena_id: arenaId });
                if (result.success) {
                    alert(result.message);
                    loadMatches();
                    viewMatchDetail(matchId); // Refresh detail
                } else {
                    alert('Error: ' + (result.message || 'Gagal pindah arena'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // ========== CREATE MATCH Functions ==========
        async function openMatchModal() {
            try {
                // Load kelas and arenas for dropdowns
                const [kelas, arenas] = await Promise.all([
                    apiCall('/api/superadmin/kelas'),
                    apiCall('/api/superadmin/arenas')
                ]);
                
                const kelasSelect = document.getElementById('matchKelas');
                const arenaSelect = document.getElementById('matchArenaSelect');
                
                kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>' + 
                    kelas.map(k => `<option value="${k.id}">${k.nama_kelas}</option>`).join('');
                
                arenaSelect.innerHTML = '<option value="">Pilih Arena</option>' + 
                    arenas.map(a => `<option value="${a.id}">${a.arena_name}</option>`).join('');
                
                // Reset form and player fields
                document.getElementById('matchForm').reset();
                resetPlayerFields();
                
                openModal('matchModal');
            } catch (error) {
                alert('Error loading data: ' + error.message);
            }
        }

        function resetPlayerFields() {
            // Reset to 1 player per team
            document.getElementById('team1Players').innerHTML = `
                <div class="player-row" style="margin-bottom: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Nama Player</label>
                            <input type="text" class="form-control player-name" placeholder="Nama player" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Contingent</label>
                            <input type="text" class="form-control player-contingent" placeholder="Contingent" required>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('team2Players').innerHTML = `
                <div class="player-row" style="margin-bottom: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Nama Player</label>
                            <input type="text" class="form-control player-name" placeholder="Nama player" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Contingent</label>
                            <input type="text" class="form-control player-contingent" placeholder="Contingent" required>
                        </div>
                    </div>
                </div>
            `;
        }

        function addPlayer(teamNumber) {
            const container = document.getElementById(`team${teamNumber}Players`);
            const playerRow = document.createElement('div');
            playerRow.className = 'player-row';
            playerRow.style.marginBottom = '1rem';
            
            playerRow.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.5rem; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nama Player</label>
                        <input type="text" class="form-control player-name" placeholder="Nama player" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Contingent</label>
                        <input type="text" class="form-control player-contingent" placeholder="Contingent" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removePlayer(this)" style="height: 48px;">
                        🗑️
                    </button>
                </div>
            `;
            
            container.appendChild(playerRow);
        }

        function removePlayer(button) {
            const playerRow = button.closest('.player-row');
            playerRow.remove();
        }

        async function submitMatch(event) {
            event.preventDefault();
            
            const kelasId = document.getElementById('matchKelas').value;
            const arenaId = document.getElementById('matchArenaSelect').value;
            const status = document.getElementById('matchStatusSelect').value;
            
            // Collect all players
            const players = [];
            
            // Team 1 players (side_number = 1)
            const team1Container = document.getElementById('team1Players');
            const team1Rows = team1Container.querySelectorAll('.player-row');
            team1Rows.forEach(row => {
                const name = row.querySelector('.player-name').value;
                const contingent = row.querySelector('.player-contingent').value;
                players.push({
                    player_name: name,
                    player_contingent: contingent,
                    side_number: 1
                });
            });
            
            // Team 2 players (side_number = 2)
            const team2Container = document.getElementById('team2Players');
            const team2Rows = team2Container.querySelectorAll('.player-row');
            team2Rows.forEach(row => {
                const name = row.querySelector('.player-name').value;
                const contingent = row.querySelector('.player-contingent').value;
                players.push({
                    player_name: name,
                    player_contingent: contingent,
                    side_number: 2
                });
            });
            
            const data = {
                kelas_id: kelasId,
                arena_id: arenaId,
                status: status,
                players: players
            };
            
            try {
                const result = await apiCall('/api/superadmin/matches', 'POST', data);
                
                if (result.success) {
                    alert(result.message);
                    closeModal('matchModal');
                    loadMatches();
                } else {
                    alert('Error: ' + (result.message || JSON.stringify(result.errors)));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // ========== CSV IMPORT FUNCTIONS ==========
        
        // Open import CSV modal
        function openImportCsvModal() {
            // Reset form
           document.getElementById('csvFileInput').value = '';
            document.getElementById('importProgress').style.display = 'none';
            document.getElementById('importResults').style.display = 'none';
            document.getElementById('importResults').innerHTML = '';
            
            openModal('importCsvModal');
        }
        
        // Upload and import CSV file
        async function uploadCsvFile() {
            const fileInput = document.getElementById('csvFileInput');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Silakan pilih file CSV terlebih dahulu');
                return;
            }
            
            if (!file.name.endsWith('.csv')) {
                alert('File harus berformat CSV');
                return;
            }
            
            // Show progress
            document.getElementById('importProgress').style.display = 'block';
            document.getElementById('importResults').style.display = 'none';
            
            const formData = new FormData();
            formData.append('csv_file', file);
            
            try {
                const response = await fetch('/api/superadmin/import-matches', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                // Hide progress
                document.getElementById('importProgress').style.display = 'none';
                
                // Show results
                displayImportResults(result);
                
                // Refresh match list if success
                if (result.success) {
                    loadMatches();
                }
                
            } catch (error) {
                document.getElementById('importProgress').style.display = 'none';
                document.getElementById('importResults').style.display = 'block';
                document.getElementById('importResults').innerHTML = `
                    <div style="padding: 15px; background: #fee; border-left: 4px solid #c33; border-radius: 4px;">
                        <strong>❌ Error:</strong> ${error.message}
                    </div>
                `;
            }
        }
        
        // Display import results
        function displayImportResults(result) {
            const resultsDiv = document.getElementById('importResults');
            resultsDiv.style.display = 'block';
            
            if (result.success) {
                let html = `
                    <div style="padding: 15px; background: #d4edda; border-left: 4px solid #28a745; border-radius: 4px; margin-bottom: 10px;">
                        <strong>✅ ${result.message}</strong>
                    </div>
                `;
                
                if (result.data && result.data.errors && result.data.errors.length > 0) {
                    html += `
                        <div style="padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <strong>⚠️ Peringatan:</strong>
                            <ul style="margin-top: 10px; padding-left: 20px;">
                    `;
                    result.data.errors.forEach(error => {
                        html += `<li>${error}</li>`;
                    });
                    html += '</ul></div>';
                }
                
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = `
                    <div style="padding: 15px; background: #f8d7da; border-left: 4px solid #dc3545; border-radius: 4px;">
                        <strong>❌ Gagal:</strong> ${result.message || 'Terjadi kesalahan'}
                    </div>
                `;
            }
        }

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadArenas();
        });
    </script>
</body>
</html>

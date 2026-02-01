// --- CONFIGURATION & NAVIGATION ---
const state = {
    files: [],
    currentTab: 'files'
};

function router(view) {
    const sections = document.querySelectorAll('.view-section');
    sections.forEach(el => el.classList.add('hidden'));
    
    const target = document.getElementById('view-' + view);
    if (target) {
        target.classList.remove('hidden');
    }
}

// Attendre que le DOM soit chargé pour attacher les événements
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. GESTION DES BOUTONS DE NAVIGATION (INDEX)
    if (document.getElementById('btn-register')) {
        document.getElementById('btn-register').onclick = () => router('register');
        document.getElementById('btn-login').onclick = () => router('login');
        document.getElementById('back-register').onclick = () => router('landing');
        document.getElementById('back-login').onclick = () => router('landing');
    }

    // 2. FORMULAIRE D'INSCRIPTION
    const regForm = document.getElementById('form-register');
    if (regForm) {
        regForm.onsubmit = (e) => {
            e.preventDefault();
            const name = document.getElementById('reg-name').value;
            const email = document.getElementById('reg-email').value;
            const pass = document.getElementById('reg-pass').value;

            fetch('/php/register.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(pass)}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success'){
                    alert('Inscription réussie !');
                    router('login');
                } else { alert(data.message); }
            });
        };
    }

    // 3. FORMULAIRE DE CONNEXION
    const loginForm = document.getElementById('form-login');
    if (loginForm) {
        loginForm.onsubmit = (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const pass = document.getElementById('login-pass').value;

            fetch('/php/login.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(pass)}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success'){
                    window.location.href = 'dashboard.html';
                } else { alert(data.message); }
            });
        };
    }

    // 4. LOGIQUE DU DASHBOARD (si on est sur dashboard.html)
    if (document.getElementById('file-container')) {
        loadFiles();
    }
});

// --- FONCTIONS DU CLOUD ---

function loadFiles() {
    fetch('/php/list_files.php')
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                state.files = data.files;
                renderFiles(); // Affiche les fichiers
                
                // --- MISE À JOUR DU STOCKAGE ---
                if (data.storage) {
                    const bar = document.getElementById('storage-bar');
                    const text = document.getElementById('storage-text');
                    
                    // 1. Calcul du pourcentage (max 100%)
                    const percent = Math.min(data.storage.percent, 100);
                    
                    // 2. Mise à jour de la largeur de la barre
                    bar.style.width = percent + '%';
                    
                    // 3. Changement de couleur si c'est presque plein (ex: > 80%)
                    if (percent > 80) {
                        bar.style.background = '#ea4335'; // Rouge
                    } else {
                        bar.style.background = '#4285f4'; // Bleu
                    }
                    
                    // 4. Mise à jour du texte (ex: "12.5 MB / 100 MB")
                    text.innerText = `${data.storage.readableUsed} / ${data.storage.readableLimit}`;
                }
            }
        })
        .catch(err => console.error("Erreur chargement stockage:", err));
}

function updateStorageBar(storage) {
    const bar = document.querySelector('.progress-bar-fill');
    const text = document.querySelector('.storage-info span:last-child');
    
    if (bar && text) {
        // On limite à 100% pour ne pas que la barre dépasse
        const percent = Math.min(storage.percent, 100);
        bar.style.width = percent + '%';
        text.innerText = `${storage.readableUsed} / ${storage.readableLimit}`;
        
        // Changer la couleur si c'est presque plein
        if (percent > 80) bar.style.backgroundColor = '#ea4335'; // Rouge
        else bar.style.backgroundColor = '#4285f4'; // Bleu
    }
}

function renderFiles() {
    const container = document.getElementById('file-container');
    if (!container) return;
    container.innerHTML = '';

    state.files.forEach(file => {
        let icon = 'fa-file';
        let color = '#5f6368';

        if (file.isDir) { 
            icon = 'fa-folder'; color = '#fcc934'; 
        } else if (file.type === 'image') { 
            icon = 'fa-file-image'; color = '#4285f4'; 
        } else if (file.type === 'archive') { 
            icon = 'fa-file-zipper'; color = '#f4b400'; // Orange/Jaune pour les ZIP
        } else if (file.type === 'package') { 
            icon = 'fa-box-open'; color = '#34a853'; // Vert pour les .deb
        } else if (file.type === 'text' || file.extension === 'yml') { 
            icon = 'fa-file-code'; color = '#ea4335';
        }

        const card = document.createElement('div');
        card.className = 'file-card';
        card.innerHTML = `
            <div onclick="openFile('${file.path}')">
                <i class="fa-solid ${icon}" style="color: ${color}; font-size: 3.5rem;"></i>
                <div class="file-name" title="${file.name}">${file.name}</div>
                <div class="file-meta">${file.size}</div>
            </div>
            <div class="file-actions">
                <button onclick="downloadFile('${file.path}')"><i class="fa-solid fa-download"></i></button>
            </div>
        `;
        container.appendChild(card);
    });
}
// Déclenche l'explorateur pour FICHIERS
function triggerFileUpload() {
    const input = document.getElementById('file-input');
    input.webkitdirectory = false;
    input.click();
}

// Déclenche l'explorateur pour DOSSIERS
function triggerFolderUpload() {
    const input = document.getElementById('folder-input');
    input.click();
}

function processUpload(input) {
    const files = input.files;
    if (files.length === 0) return;

    const overlay = document.getElementById('upload-overlay');
    overlay.classList.add('show');

    const formData = new FormData();
    // Pour tester, on envoie le premier fichier
    formData.append('file', files[0]);
    formData.append('fullPath', files[0].name);

    fetch('/php/upload.php', { 
        method: 'POST', 
        body: formData 
    })
    .then(response => response.json()) // On attend la réponse JSON
    .then(data => {
        console.log("Réponse serveur:", data);
        overlay.classList.remove('show');
        if(data.status === 'success') {
            loadFiles(); // ON RECHARGE LA LISTE ICI
        } else {
            alert("Erreur: " + data.message);
        }
    })
    .catch(err => {
        overlay.classList.remove('show');
        console.error("Erreur Fetch:", err);
    });
}

function openFile(path) {
    // Mode 'view' pour regarder la photo
    window.open(`/php/download.php?mode=view&file=${encodeURIComponent(path)}`, '_blank');
}

function downloadFile(path) {
    // Mode 'download' par défaut pour reprendre le fichier
    window.location.href = `/php/download.php?file=${encodeURIComponent(path)}`;
}
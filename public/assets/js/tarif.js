
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    }

    function loadTarifs(typeId, containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.warn('Container introuvable :', containerId);
            return;
        }

        container.innerHTML = '<p class="text-muted-soft">Chargement...</p>';

        const url = window.baseUrl + '/backoffice/tarif/getTarifs?id_type=' + typeId;
        console.log('Chargement des tarifs depuis :', url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    container.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                    return;
                }
                if (!data || !data.length) {
                    container.innerHTML = '<p class="text-muted-soft">Aucun tarif défini pour ce type.</p>';
                    return;
                }
                let html = '<table class="table table-chic">';
                html += '<thead><tr><th>Min (Ar)</th><th>Max (Ar)</th><th>Prix (Ar)</th><th>Action</th></tr></thead>';
                html += '<tbody>';
                data.forEach(tarif => {
                    html += '<tr>';
                    html += '<td>' + Number(tarif.min).toLocaleString() + '</td>';
                    html += '<td>' + Number(tarif.max).toLocaleString() + '</td>';
                    html += '<td><input type="number" class="form-control form-control-sm tarif-prix" data-id="' + tarif.id + '" value="' + tarif.prix + '" step="10"></td>';
                    html += '<td><button type="button" class="btn btn-accent btn-sm btn-update-tarif" data-id="' + tarif.id + '">Mettre à jour</button></td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                container.innerHTML = html;

                container.querySelectorAll('.btn-update-tarif').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const row = this.closest('tr');
                        const input = row.querySelector('.tarif-prix');
                        const prix = input.value.trim();

                        if (!prix || isNaN(prix)) {
                            alert('Veuillez saisir un prix valide.');
                            return;
                        }

                        this.disabled = true;
                        this.textContent = 'En cours...';

                        const updateUrl = window.baseUrl + '/backoffice/tarif/update';
                        const formData = new FormData();
                        formData.append('id_tarif', id);
                        formData.append('prix', prix);

                        fetch(updateUrl, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur HTTP ' + response.status);
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (result.success) {
                                alert('Tarif mis à jour avec succès !');
                                loadTarifs(typeId, containerId);
                            } else {
                                alert('Erreur : ' + (result.error || 'Erreur inconnue'));
                                this.disabled = false;
                                this.textContent = 'Mettre à jour';
                            }
                        })
                        .catch(err => {
                            alert('Erreur réseau : ' + err.message);
                            this.disabled = false;
                            this.textContent = 'Mettre à jour';
                        });
                    });
                });
            })
            .catch(err => {
                container.innerHTML = '<div class="alert alert-danger">Erreur de chargement : ' + err.message + '</div>';
            });
    }

    function loadFirstTab() {
        const activeTab = document.querySelector('.nav-link.active');
        if (activeTab) {
            const typeId = activeTab.dataset.typeId;
            if (typeId) {
                loadTarifs(typeId, 'tarif-list-' + typeId);
            }
        } else {
            const firstTab = document.querySelector('.nav-link');
            if (firstTab) {
                const typeId = firstTab.dataset.typeId;
                if (typeId) {
                    loadTarifs(typeId, 'tarif-list-' + typeId);
                }
            }
        }
    }

    loadFirstTab();

    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const typeId = this.dataset.typeId;
            if (typeId) {
                loadTarifs(typeId, 'tarif-list-' + typeId);
            }
        });
    });
});
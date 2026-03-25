document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('form-connexion');
    if (!form) return;

    function afficherErreur(id, message) {
        const champ = document.getElementById(id);
        let err = document.getElementById('erreur-' + id);
        if (!err) {
            err = document.createElement('span');
            err.id = 'erreur-' + id;
            err.className = 'erreur-champ';
            champ.parentNode.insertBefore(err, champ.nextSibling);
        }
        err.textContent = message;
        champ.style.borderColor = '#c0392b';
    }

    function supprimerErreur(id) {
        const champ = document.getElementById(id);
        const err = document.getElementById('erreur-' + id);
        if (err) err.textContent = '';
        champ.style.borderColor = '';
    }

    // Validation en temps réel
    document.getElementById('login').addEventListener('blur', function () {
        if (this.value.trim() === '')
            afficherErreur('login', 'Le login est obligatoire.');
        else
            supprimerErreur('login');
    });

    document.getElementById('mot_de_passe').addEventListener('blur', function () {
        if (this.value.trim() === '')
            afficherErreur('mot_de_passe', 'Le mot de passe est obligatoire.');
        else if (this.value.length < 6)
            afficherErreur('mot_de_passe', 'Minimum 6 caractères.');
        else
            supprimerErreur('mot_de_passe');
    });

    // Validation à la soumission
    form.addEventListener('submit', function (e) {
        let ok = true;

        const login = document.getElementById('login').value.trim();
        if (login === '') {
            afficherErreur('login', 'Le login est obligatoire.');
            ok = false;
        } else {
            supprimerErreur('login');
        }

        const mdp = document.getElementById('mot_de_passe').value;
        if (mdp.trim() === '') {
            afficherErreur('mot_de_passe', 'Le mot de passe est obligatoire.');
            ok = false;
        } else if (mdp.length < 6) {
            afficherErreur('mot_de_passe', 'Minimum 6 caractères.');
            ok = false;
        } else {
            supprimerErreur('mot_de_passe');
        }

        if (!ok) e.preventDefault();
    });
});
document.addEventListener('DOMContentLoaded', function () {

    // ── Utilitaires ──────────────────────────────────
    function afficherErreur(champId, message) {
        const champ = document.getElementById(champId);
        let erreur = document.getElementById('erreur-' + champId);

        if (!erreur) {
            erreur = document.createElement('span');
            erreur.id = 'erreur-' + champId;
            erreur.className = 'erreur-champ';
            champ.parentNode.insertBefore(erreur, champ.nextSibling);
        }

        erreur.textContent = message;
        champ.style.borderColor = '#c0392b';
    }

    function supprimerErreur(champId) {
        const champ = document.getElementById(champId);
        const erreur = document.getElementById('erreur-' + champId);

        if (erreur) erreur.textContent = '';
        champ.style.borderColor = '';
    }

    function validerChamp(champId, regle) {
        const champ = document.getElementById(champId);
        if (!champ) return true;

        const valeur = champ.value.trim();
        const resultat = regle(valeur);

        if (resultat !== true) {
            afficherErreur(champId, resultat);
            return false;
        }

        supprimerErreur(champId);
        return true;
    }

    // ── Règles de validation ─────────────────────────
    const regles = {
        titre: (v) => {
            if (v === '')         return 'Le titre est obligatoire.';
            if (v.length < 5)     return 'Le titre doit contenir au moins 5 caractères.';
            if (v.length > 200)   return 'Le titre ne peut pas dépasser 200 caractères.';
            return true;
        },
        contenu: (v) => {
            if (v === '')         return 'Le contenu est obligatoire.';
            if (v.length < 20)    return 'Le contenu doit contenir au moins 20 caractères.';
            return true;
        },
        categorie: (v) => {
            if (v === '' || v === '0') return 'Veuillez sélectionner une catégorie.';
            return true;
        },
        description: (v) => {
            if (v.length > 300)   return 'La description ne peut pas dépasser 300 caractères.';
            return true;
        }
    };

    // ── Validation en temps réel (au blur) ──────────
    ['titre', 'contenu', 'categorie', 'description'].forEach(id => {
        const champ = document.getElementById(id);
        if (!champ) return;
        champ.addEventListener('blur', () => validerChamp(id, regles[id]));
        champ.addEventListener('input', () => supprimerErreur(id));
    });

    // ── Compteur de caractères pour le titre ─────────
    const champTitre = document.getElementById('titre');
    if (champTitre) {
        const compteur = document.createElement('span');
        compteur.className = 'compteur-caracteres';
        compteur.style.cssText = 'font-size:12px;color:#888;float:right;';
        champTitre.parentNode.insertBefore(compteur, champTitre);

        champTitre.addEventListener('input', () => {
            const nb = champTitre.value.length;
            compteur.textContent = nb + ' / 200';
            compteur.style.color = nb > 200 ? '#c0392b' : '#888';
        });
    }

    // ── Soumission formulaire ajout article ──────────
    const formAjout = document.getElementById('form-ajouter-article');
    if (formAjout) {
        formAjout.addEventListener('submit', function (e) {
            const ok = [
                validerChamp('titre',       regles.titre),
                validerChamp('contenu',     regles.contenu),
                validerChamp('categorie',   regles.categorie),
                validerChamp('description', regles.description),
            ].every(Boolean);

            if (!ok) {
                e.preventDefault();
                // Scroll vers la première erreur
                const premiere = formAjout.querySelector('.erreur-champ:not(:empty)');
                if (premiere) premiere.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    // ── Soumission formulaire modification article ───
    const formModif = document.getElementById('form-modifier-article');
    if (formModif) {
        formModif.addEventListener('submit', function (e) {
            const ok = [
                validerChamp('titre',       regles.titre),
                validerChamp('contenu',     regles.contenu),
                validerChamp('categorie',   regles.categorie),
                validerChamp('description', regles.description),
            ].every(Boolean);

            if (!ok) e.preventDefault();
        });
    }

    // ── Confirmation suppression article ─────────────
    const boutonsSuppression = document.querySelectorAll('.btn-supprimer-article');
    boutonsSuppression.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const titre = this.dataset.titre || 'cet article';
            if (!confirm('Supprimer "' + titre + '" ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

});
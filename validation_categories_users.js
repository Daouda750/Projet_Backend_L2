document.addEventListener('DOMContentLoaded', function () {

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

    // ════════════════════════════════════════════════
    // CATÉGORIES
    // ════════════════════════════════════════════════

    const reglesCategorie = {
        nom_categorie: (v) => {
            if (v === '')       return 'Le nom de la catégorie est obligatoire.';
            if (v.length < 2)   return 'Le nom doit contenir au moins 2 caractères.';
            if (v.length > 100) return 'Le nom ne peut pas dépasser 100 caractères.';
            return true;
        },
        description_categorie: (v) => {
            if (v.length > 255) return 'La description ne peut pas dépasser 255 caractères.';
            return true;
        }
    };

    ['nom_categorie', 'description_categorie'].forEach(id => {
        const champ = document.getElementById(id);
        if (!champ) return;
        champ.addEventListener('blur',  () => validerChamp(id, reglesCategorie[id]));
        champ.addEventListener('input', () => supprimerErreur(id));
    });

    const formAjoutCat = document.getElementById('form-ajouter-categorie');
    if (formAjoutCat) {
        formAjoutCat.addEventListener('submit', function (e) {
            const ok = [
                validerChamp('nom_categorie',         reglesCategorie.nom_categorie),
                validerChamp('description_categorie', reglesCategorie.description_categorie),
            ].every(Boolean);
            if (!ok) e.preventDefault();
        });
    }

    const formModifCat = document.getElementById('form-modifier-categorie');
    if (formModifCat) {
        formModifCat.addEventListener('submit', function (e) {
            const ok = [
                validerChamp('nom_categorie',         reglesCategorie.nom_categorie),
                validerChamp('description_categorie', reglesCategorie.description_categorie),
            ].every(Boolean);
            if (!ok) e.preventDefault();
        });
    }

    const boutonsSuppCat = document.querySelectorAll('.btn-supprimer-categorie');
    boutonsSuppCat.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.dataset.nom || 'cette catégorie';
            if (!confirm('Supprimer "' + nom + '" ? Les articles liés ne seront pas supprimés.')) {
                e.preventDefault();
            }
        });
    });

    // ════════════════════════════════════════════════
    // UTILISATEURS
    // ════════════════════════════════════════════════

    const reglesUser = {
        nom: (v) => {
            if (v === '')       return 'Le nom est obligatoire.';
            if (v.length < 2)   return 'Le nom doit contenir au moins 2 caractères.';
            if (v.length > 100) return 'Le nom ne peut pas dépasser 100 caractères.';
            return true;
        },
        prenom: (v) => {
            if (v === '')     return 'Le prénom est obligatoire.';
            if (v.length < 2) return 'Le prénom doit contenir au moins 2 caractères.';
            return true;
        },
        login: (v) => {
            if (v === '')       return 'Le login est obligatoire.';
            if (v.length < 3)   return 'Le login doit contenir au moins 3 caractères.';
            if (v.length > 50)  return 'Le login ne peut pas dépasser 50 caractères.';
            if (!/^[a-zA-Z0-9_]+$/.test(v))
                return 'Le login ne peut contenir que des lettres, chiffres et _.';
            return true;
        },
        mot_de_passe: (v) => {
            if (v === '')     return 'Le mot de passe est obligatoire.';
            if (v.length < 6) return 'Le mot de passe doit contenir au moins 6 caractères.';
            return true;
        },
        role: (v) => {
            // ✅ Corrigé : 'administrateur' au lieu de 'admin'
            const rolesValides = ['visiteur', 'editeur', 'administrateur'];
            if (!rolesValides.includes(v))
                return 'Veuillez sélectionner un rôle valide.';
            return true;
        }
    };

    ['nom', 'prenom', 'login', 'mot_de_passe', 'role'].forEach(id => {
        const champ = document.getElementById(id);
        if (!champ) return;
        champ.addEventListener('blur',  () => validerChamp(id, reglesUser[id]));
        champ.addEventListener('input', () => supprimerErreur(id));
    });

    const formAjoutUser = document.getElementById('form-ajouter-utilisateur');
    if (formAjoutUser) {
        formAjoutUser.addEventListener('submit', function (e) {
            const ok = [
                validerChamp('nom',          reglesUser.nom),
                validerChamp('prenom',       reglesUser.prenom),
                validerChamp('login',        reglesUser.login),
                validerChamp('mot_de_passe', reglesUser.mot_de_passe),
                validerChamp('role',         reglesUser.role),
            ].every(Boolean);
            if (!ok) e.preventDefault();
        });
    }

    const formModifUser = document.getElementById('form-modifier-utilisateur');
    if (formModifUser) {
        formModifUser.addEventListener('submit', function (e) {
            const champMdp = document.getElementById('mot_de_passe');
            const mdpObligatoire = champMdp && champMdp.value.trim() !== '';

            const validations = [
                validerChamp('nom',    reglesUser.nom),
                validerChamp('prenom', reglesUser.prenom),
                validerChamp('login',  reglesUser.login),
                validerChamp('role',   reglesUser.role),
            ];

            if (mdpObligatoire) {
                validations.push(validerChamp('mot_de_passe', reglesUser.mot_de_passe));
            }

            const ok = validations.every(Boolean);
            if (!ok) e.preventDefault();
        });
    }

    const boutonsSuppUser = document.querySelectorAll('.btn-supprimer-utilisateur');
    boutonsSuppUser.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const login = this.dataset.login || 'cet utilisateur';
            if (!confirm('Supprimer le compte "' + login + '" ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

});
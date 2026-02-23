/**
 * FermeEngine.js
 * Moteur visuel pour Ferme Manager.
 * Responsabilité :
 * - Afficher l'état du jeu (Bâtiments)
 * - Gérer les événements de clic (Récolte, Amélioration)
 * - Exécuter le cycle de vie du jeu (Load -> Action -> Load)
 * - NE GÈRE PAS la logique métier ni les appels réseau (c'est le rôle de votre code dans main.js).
 */
class FermeEngine {
    constructor() {
        this.loadConfig = async () => {
            try {
                const response = await fetch('Public/JS/game_config.json');
                this.config = await response.json();
            } catch (e) {
                console.error("Fichier de configuration game_config.json non trouvé", e);
            }
        };
        this.loadConfig();

        this.callbacks = {
            loadStateCallback: null,
            harvestCallback: null,
            upgradeCallback: null
        };

        // Délai avant d'attacher les événements pour s'assurer que le DOM est prêt
        document.addEventListener("DOMContentLoaded", () => {
            this.attachEvents();
        });
    }

    /**
     * Enregistre la fonction de rappel chargée de récupérer les données (API)
     * et de les afficher.
     * @param {function} callback
     */
    onLoadState(callback) {
        this.callbacks.loadStateCallback = callback;
    }

    /**
     * Met à jour l'affichage de tous les bâtiments
     * @param {Object} buildings - Objet contenant les bâtiments { "mine": {...}, "champ": {...} }
     * @param {Object|null} playerInventory - (Optionnel) pour calculer si l'amélioration est possible
     */
    renderBuildings(buildings, playerInventory = null) {
        Object.entries(buildings).forEach(([buildingId, buildingState]) => {
            const container = document.getElementById(`building-${buildingId}`);
            if (!container) {
                console.error(`Bâtiment ${buildingId} non trouvé dans le DOM`);
                return;
            }

            const buildingConfig = this.config.buildings[buildingId];
            if (!buildingConfig) return; // Bâtiment inconnu dans la config

            // Mise à jour niveau
            const levelEl = container.querySelector('.level');
            if (levelEl) levelEl.textContent = buildingState.level;

            // Calcul et affichage du coût d'amélioration
            const nextCost = (buildingState.level + 1) * (buildingConfig.upgrade_cost_multiplier || 10);
            const costEl = container.querySelector('.cost');
            if (costEl) costEl.textContent = nextCost;

            // Gestion bouton "Améliorer" (activé/désactivé selon le stock)
            const upgradeBtn = container.querySelector('.upgrade');
            if (upgradeBtn) {
                if (playerInventory) {
                    const costResource = buildingConfig.cost;
                    const playerStock = playerInventory[costResource] || 0;
                    // On désactive si pas assez de ressources
                    upgradeBtn.disabled = playerStock < nextCost;
                } else {
                    upgradeBtn.disabled = false; // Par défaut, toujours actif si pas d'inventaire fourni
                }
            }
        });
    }

    /**
     * Enregistre une fonction de rappel à exécuter lors d'un clic sur "Récolter".
     * La fonction recevra l'ID du bâtiment en paramètre et DOIT retourner une promesse (fetch).
     * @param {function} callback
     */
    onHarvest(callback) {
        this.callbacks.harvestCallback = callback;
    }

    /**
     * Enregistre une fonction de rappel à exécuter lors d'un clic sur "Améliorer".
     * La fonction recevra l'ID du bâtiment en paramètre et DOIT retourner une promesse (fetch).
     * @param {function} callback
     */
    onUpgrade(callback) {
        this.callbacks.upgradeCallback = callback;
    }

    /**
     * Démarre le moteur, déclenchant le premier chargement de l'état.
     */
    init() {
        if (this.callbacks.loadStateCallback) {
            this.callbacks.loadStateCallback();
        } else {
            console.error("FermeEngine: Aucune callback onLoadState n'a été définie.");
        }
    }

    attachEvents() {
        const batiments = document.querySelectorAll('#buildings article');
        batiments.forEach(batiment => {
            const buildingId = batiment.id.replace('building-', '');

            const harvestBtn = batiment.querySelector('.harvest');
            if (harvestBtn) {
                harvestBtn.addEventListener('click', async () => {
                    if (this.callbacks.harvestCallback) {
                        try {
                            harvestBtn.disabled = true; // Empêche le multi-clic
                            await this.callbacks.harvestCallback(buildingId);
                        } catch (e) {
                            console.error(`Erreur lors de la récolte sur ${buildingId}:`, e);
                        } finally {
                            harvestBtn.disabled = false;
                            // Recharge l'état après l'action
                            if (this.callbacks.loadStateCallback) this.callbacks.loadStateCallback();
                        }
                    }
                });
            } else {
                console.error(`Bâtiment ${buildingId} : bouton de récolte introuvable`);
            }

            const upgradeBtn = batiment.querySelector('.upgrade');
            if (upgradeBtn) {
                upgradeBtn.addEventListener('click', async () => {
                    if (this.callbacks.upgradeCallback) {
                        try {
                            upgradeBtn.disabled = true;
                            await this.callbacks.upgradeCallback(buildingId);
                        } catch (e) {
                            console.error(`Erreur lors de l'amélioration sur ${buildingId}:`, e);
                        } finally {
                            // On ne réactive pas toujours manuellement, loadStateCallback pourrait le faire via renderBuildings.
                            // Mais c'est plus safe en fallback en cas d'erreur
                            upgradeBtn.disabled = false;
                            // Recharge l'état après l'action
                            if (this.callbacks.loadStateCallback) this.callbacks.loadStateCallback();
                        }
                    }
                });
            } else {
                console.error(`Bâtiment ${buildingId} : bouton d'amélioration introuvable`);
            }
        });
    }
}

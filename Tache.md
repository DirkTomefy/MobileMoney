# Table (Tomefy)

-t_operateur
    -id
    -libelle

-t_prefix
    -id
    -id_operateur
    -libelle ("samy hafa pour chaque opperateur ny +261 33 sy ny 033")

-t_type_operation
    -id
    -code (All Majuscule)

-t_tarif_operation
    -id
    -id_operateur
    -id_type_operation
    -min
    -max
    -prix

-t_historique_tarif
    -id
    -date_changement (curent null)
    -id_tarif_operation
    -prix

-t_client
    -id
    -nom
    -prenom
    -operateurs
    -numero (validation cote php)
    -date_creation

-t_transaction
    -id
    -id_client_source
    -id_client_cible( nulable)
    -id_type_operation
    -date
    -montant
    -frais
    

# Tache

## Front-ofice

-[ok][Tsimbina] Login.php
    -[ok] page html+ css
    -[ok] fonction
        -[ok] isSigned
        -[ok] isValid
        -[ok] signUp
    -[ok] integration (avec session)
-[x][Tsimbina] Login.php
    -[x] page html+ css
    -[x]fonction
        -[x] isSigned
        -[x] isValid
        -[x] signUp
    -[x] integration (avec session)

-[x][Tomefy] Solde.php
    -[x] page html+ css
        -bouton action( retrait, depot ,transfert)
    -[x] fonction
        -[x] getSolde($id_client ,$date=today)
        -[x] getAllTransaction($id_client, $paginer ,$type_transactions, date_min,date_max)

    -[x] integration 
        -[x]list
        -[x]solde
        -[x] bouton avec redirection (id_type_transaction)

-[x][Tsimbina] Transaction.php
    -[ok] page html+ css
        -[ok] miovaova par rapport type_transaction
    -[x] fonction
        -[x] retirer($id_client,$montant,$date=today) (message erreur si solde tsy ampy ou depasse le seuil du montant)
        -[x] deposer($id_client,$montant,$date=today)
        -[x] transferer($id_client_source,$id_client_cible,$montant,$date=today) (message erreur si solde tsy ampy ou depasse le seuil du montant)
    -[x] integration
        -[x] bouton + action

## Back-office

-[x][Tomefy] DashBoard.php
    -[x] page html +css
        -[x] formulaire (date debut et fin)
        -[x] situation global
        -[x] liste gain (+ graphe si possible)

    -[x] fonction
        -[x] getSituationGlobal($date_actuel)
        -[x] getSituationDetail ($date_debut, $date_fin)
    -[x] integration
        -[x] situation global
        -[x] liste gain (+ graphe si possible)

-[x][Tsimbina] Portefeuille.php
    -[x] page html +css
        -[x] input date
        -[x] liste portefeuille
    -[x] fonction
        -[x] getAllPortefeuille($date)

-[x][Tomefy] Tarif.php
    -[x] page html+css
        -[x] onglet type_transaction(retrait , transfert) (il y a encore un erreur js)
        -[x] liste tarif
            -[x] input prix modifiable
        -[x] bouton valider
    -[x] fonction
        -[x] getAllTarif($id_type_operation )
        -[x] updateTarif($id_tarif,$prix,$date=today)
            -[x] uptable t_tarif_operation
            -[x] insert t_historique_operation + update date null before
    -[x] integration
        -[x] list
        -[x] input

- Finition
    -[x] [Tsimbina]refacto css + navbar
    -[][Tomefy] Déconnexion

# Fonctionnalite 2

# Back office

-[][Tomefy]  suffix.php (15)
    -[x] page html CRUD
    -[x] function
        -[x] CRUD
    -[x] integration

## table + donne reel (10)
[][Tsimbina]
-t_commission
    -id
    -id_operateur_envoi
    -id_operateur_receveur
    -pourcentage
    -valable(bool=true)

-t_historique_commision
    -id
    -date_modif
    -id_commission
    -pourcentage

-v_frais_opperateur
    -id_operateur_envoi
    -id_operateur_receveur
    -frais
    -date
    -id_type_operation
    -type_operation_libelle
    -commission

(Alter table)
-t_transaction
    -commision
# Page

-[][Tomefy] commision.php (15)
    -[] page html CRUD
    -[] function
        -[] CRUD
    -[] integration

-[][Tomefy] dashboard.php (20)
    -[] html
        -[] checkbox pour les opperateurs
        -[] montant a envoyer autre operateur
    -[] fonction
        -[] getSituationGlobal($date_actuel)
        -[] getSituationDetail ($date_debut, $date_fin)
        -[] getSituationEnvoyeeByAutreOperateur ($date_debut, $date_fin) (montant +commission)
        -[] getSituationRecueByAutreOperateur ($date_debut, $date_fin)

-[][Tsimbina] transaction.php (10)
    -[] function
        -[] transferer


# Front-Office
-[][Tsimbina] transaction.php (30)
    -[] page
        -[] clean
        -[] option avec ou sans frais de retrait
        -[] envois multiple
    -[] function
        -[] getFraisRetrait
        -[] transfererMultiple

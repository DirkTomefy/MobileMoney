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

# Fonctionnalite

## Front-ofice

-[][Tsimbina] Login.php 
    -[ok] page html+ css
    -[] fonction 
        -[] isSigned
        -[] isValid
        -[] signUp
    -[] integration (avec session)

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

-[][Tsimbina] Transaction.php
    -[] page html+ css
        -[] miovaova par rapport type_transaction
    -[] fonction
        -[] retirer($id_client,$montant,$date=today) (message erreur si solde tsy ampy ou depasse le seuil du montant) 
        -[] deposer($id_client,$montant,$date=today)
        -[] transferer($id_client_source,$id_client_cible,$montant,$date=today) (message erreur si solde tsy ampy ou depasse le seuil du montant) 
    -[] integration
        -[] bouton + action


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

-[][Tsimbina] Portefeuille.php
    -[x] page html +css
        -[x] input date
        -[x] liste portefeuille
    -[x] fonction
        -[x] getAllPortefeuille($date)

-[][Tomefy] Tarif.php
    -[x] page html+css
        -[] onglet type_transaction(retrait , transfert) (il y a encore un erreur js)
        -[x] liste tarif
            -[x] input prix modifiable
        -[x] bouton valider
    -[] fonction
        -[x] getAllTarif($id_type_operation )
        -[x] updateTarif($id_tarif,$prix,$date=today)
            -[x] uptable t_tarif_operation
            -[x] insert t_historique_operation + update date null before
    -[] integration
        -[x] list
        -[x] input

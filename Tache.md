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

-[ok][Tsimbina] Login.php 
    -[ok] page html+ css
    -[ok] fonction 
        -[ok] isSigned
        -[ok] isValid
        -[ok] signUp
    -[ok] integration (avec session)

-[][Tomefy] Solde.php
    -[] page html+ css
        -bouton action( retrait, depot ,transfert)
    -[] fonction
        -[] getSolde($id_client ,$date=today)
        -[] getAllTransaction($id_client, $paginer ,$type_transactions, date_min,date_max)

    -[] integration 
        -[]list
        -[]solde
        -[] bouton avec redirection (id_type_transaction)

-[][Tsimbina] Transaction.php
    -[ok] page html+ css
        -[ok] miovaova par rapport type_transaction
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
    -[] page html +css
        -[] input date
        -[] liste portefeuille
    -[] fonction
        -[] getAllPortefeuille($date)

-[][Tomefy] Tarif.php
    -[] page html+css
        -[] onglet type_transaction(retrait , transfert)
        -[] liste tarif
            -[] input prix modifiable
        -[] bouton valider
    -[] fonction
        -[] getAllTarif($id_type_operation )
        -[] updateTarif($id_tarif,$prix,$date=today)
            -[] uptable t_tarif_operation
            -[] insert t_historique_operation + update date null before
    -[] integration
        -[] list
        -[] input
        
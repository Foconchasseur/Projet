#!/usr/bin/env bash

CSV_FOLDER="./csv"

cat << EOF
                                                                            ▓▓
                                                                    ▓▓▓▓▓▓▓▓  ▓▓████▒▒
                                                                ▓▓▓▓▓▓██▓▓▓▓▓▓████░░
                                                            ██▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓██▓▓▓▓
                  ░░                                        ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓
                ░░░░                                      ▒▒▓▓▓▓▓▓▓▓▓▓▓▓▓▓██▓▓
              ░░░░░░          ▒▒                          ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▒▒
            ▓▓▓▓▓▓▒▒▒▒░░░░░░▒▒▒▒                          ▓▓▓▓▓▓▓▓▓▓
          ░░░░▒▒▓▓▓▓░░▒▒▓▓██▒▒                ▒▒    ░░  ██▓▓▓▓██
        ░░░░░░░░░░░░▒▒██▓▓▒▒░░          ▓▓▒▒▓▓████████████▓▓▓▓██
      ░░░░░░▒▒▓▓▓▓░░▒▒██▓▓              ▓▓▓▓▓▓▓▓▓▓▓▓▓▓██▓▓██░░
  ▒▒▒▒░░▒▒▒▒▓▓▓▓░░░░░░▓▓▓▓▓▓    ░░    ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓██░░
  ░░░░░░░░░░░░▒▒▓▓░░░░██▓▓▓▓▓▓▓▓░░  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓██
    ▒▒▒▒▒▒░░▓▓░░▒▒░░░░▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓██▓▓▓▓▓▓████████
        ░░░░▒▒▒▒░░░░▒▒▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓██▓▓░░░░▒▒▓▓██▓▓██▓▓
          ▒▒░░▓▓░░░░░░▓▓████▓▓▓▓▓▓▓▓▓▓▓▓▓▓████▓▓░░░░░░░░▒▒▓▓░░██
        ░░▒▒░░░░▒▒▒▒░░██▓▓██▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░▒▒▒▒▒▒
        ░░░░▒▒▓▓██▒▒░░░░░░░░██████▓▓██████░░░░▒▒▒▒░░░░░░▒▒▒▒
                ██▓▓░░░░░░░░░░██▓▓██▓▓▒▒░░░░▒▒▒▒▒▒░░░░░░░░▒▒░░
                  ░░░░░░░░░░░░▓▓░░▓▓▒▒░░░░▒▒▒▒░░  ░░░░░░░░▒▒▒▒▒▒
                    ▒▒░░░░░░▒▒░░░░░░░░▒▒▒▒▒▒        ░░░░░░▓▓▓▓▓▓▓▓
                    ▒▒░░░░░░░░░░░░░░░░▒▒▒▒░░            ▓▓▓▓▓▓▓▓▓▓▒▒
                    ░░▒▒▒▒░░░░▒▒░░░░░░▒▒▒▒                  ▓▓▓▓▓▓▓▓
                    ░░░░▒▒▒▒░░░░▒▒░░░░░░▓▓▒▒                  ▓▓▓▓██
                    ░░░░▒▒▒▒▒▒▒▒▒▒░░░░░░▓▓                    ▓▓▓▓▓▓
                  ░░░░▒▒▒▒  ░░    ▒▒▓▓▓▓██                    ██▓▓▓▓
                  ░░░░▓▓░░          ▓▓▓▓▓▓                  ▒▒▓▓▓▓▓▓
                ▓▓▓▓▓▓▓▓        ░░▓▓██▓▓▓▓                ██░░▓▓▓▓▓▓
                ▓▓▓▓██            ░░░░▓▓▓▓                      ░░
              ▒▒▓▓▓▓░░                ▓▓▓▓▓▓
              ▓▓▓▓▒▒                  ▓▓▓▓▓▓
        ▒▒▓▓▓▓▓▓██                  ▓▓▓▓██▓▓
      ██▒▒▓▓▓▓▓▓                  ░░▒▒▓▓  ▓▓
        __  __  ___   ___ _  __
       |  \/  |/ _ \ / __| |/ /
       | |\/| | (_) | (__| ' <
       |_|  |_|\___/ \___|_|\_\

EOF

# Définir les variables de connexion à la base de données PostgreSQL
DB_NAME="netyparneo"
DB_USER="postgres"
DB_PASSWORD="OwOUWUFurry"
DB_HOST="localhost"
DB_PORT="5432"

# Définir les variables relatives au fichier CSV à importer
DELIMITER=";"

CSV_FILE="${CSV_FOLDER}/site.csv"
TABLE_NAME="site"
COLUMN_NAMES="pays;ville;adresse;ouvert"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/etudiant.csv"
TABLE_NAME="etudiant"
COLUMN_NAMES="no_etu;nationalite"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/laboratoire.csv"
TABLE_NAME="laboratoire"
COLUMN_NAMES="nom"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/employe.csv"
TABLE_NAME="employe"
COLUMN_NAMES="nom,prenom"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/entreprise.csv"
TABLE_NAME="entreprise"
COLUMN_NAMES="nom,pays_siege,ville_siege,adresse_siege,ouvert,effectif"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/entreprise_site.csv"
TABLE_NAME="entreprise_site"
COLUMN_NAMES="pays_site,adresse_site,ville_site,nom_entreprise"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/donation.csv"
TABLE_NAME="donation"
COLUMN_NAMES="montant,date_debut,date_fin,entreprise"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/contrat_vacataire.csv"
TABLE_NAME="contrat_vacataire"
COLUMN_NAMES="nom_vacataire,prenom_vacataire,date_debut,date_fin,entreprise,pays_site,ville_site,adresse_site"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/contrat_laboratoire.csv"
TABLE_NAME="contrat_labo"
COLUMN_NAMES="laboratoire,date_debut,date_fin,entreprise,pays_site,ville_site,adresse_site"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

CSV_FILE="${CSV_FOLDER}/contrat_etudiant.csv"
TABLE_NAME="contrat_etudiant"
COLUMN_NAMES="type,date_debut,date_fin,date_fin_effective,note_maitre,note_entreprise,entreprise,pays_site,ville_site,adresse_site,nom_maitre,prenom_maitre,etudiant"
psql "dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD host=$DB_HOST port=$DB_PORT" -c "\copy $TABLE_NAME($COLUMN_NAMES) FROM '$CSV_FILE' DELIMITER '$DELIMITER' CSV HEADER;" --quiet

echo "Done !"

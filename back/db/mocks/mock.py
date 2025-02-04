from uuid import uuid4
from random import randint
from datetime import date, timedelta
from faker import Faker
from faker.providers import address, misc, person, company
from faker_education import SchoolProvider

laboratoire_file = "./csv/laboratoire.csv"
site_file = "./csv/site.csv"
etudiant_file = "./csv/etudiant.csv"
employe_file = "./csv/employe.csv"
entreprise_file = "./csv/entreprise.csv"
entreprise_site_file = "./csv/entreprise_site.csv"
donation_file = "./csv/donation.csv"
contrat_vacataire_file = "./csv/contrat_vacataire.csv"
contrat_laboratoire_file = "./csv/contrat_laboratoire.csv"
contrat_etudiant_file = "./csv/contrat_etudiant.csv"

fake = Faker()
fake.add_provider(person)
fake.add_provider(SchoolProvider)
fake.add_provider(address)
fake.add_provider(misc)
fake.add_provider(company)

DELTA = 1

NB_LABS = 30 * DELTA
NB_SITES = 200 * DELTA
NB_ETUDIANTS = 100000 * DELTA
NB_EMPLOYES = 2000 * DELTA
NB_ENTREPRISES = 1000 * DELTA
NB_ENTREPRISES_SITE = 800 * DELTA
NB_DONATIONS = 10000 * DELTA
NB_CONTRATS_VACATAIRE = 5000 * DELTA
NB_CONTRATS_LABORATOIRE = 5000 * DELTA
NB_CONTRATS_ETUDIANT = 500000 * DELTA

DATE_DEBUT_CONTRAT = date(day=1, month=1, year=2010)
DATE_FIN_CONTRAT = date(day=25, month=12, year=2025)

list_laboratoires = []
list_sites = []
list_etudiants = []
list_employes = []
list_entreprises = []
list_entreprises_site = []
list_donations = []
list_contrats_vacataire = []
list_contrats_laboratoire = []
list_contrats_etudiant = []

def pick_random(l: list):
    return l[randint(0, len(l)-1)]

def random_date(date_debut: date, date_fin: date):
    delta = date_fin - date_debut
    rnd = randint(0, delta.days)
    return date_debut + timedelta(days=rnd)

def html_special_chars(data):
    symbols = {'<': '', '>': '', '"': '', '&': '', ';': ''}
    return "".join(symbols.get(x, x) for x in data)

with open(laboratoire_file, "w") as f:
    f.write("nom")
    for _ in range(NB_LABS):
        nom = fake.school_name()
        list_laboratoires.append(nom)
        f.write(f"\n{nom}")

with open(site_file, "w") as f:
    f.write("pays;ville;adresse;ouvert")
    for _ in range(NB_SITES):
        pays = html_special_chars(fake.country())
        ville = html_special_chars(fake.city())
        adresse = html_special_chars(fake.street_address())
        ouvert = fake.boolean(chance_of_getting_true=90)
        list_sites.append([pays, ville, adresse, ouvert])
        f.write(f"\n{pays};{ville};{adresse};{'true' if ouvert else 'false'}")

with open(etudiant_file, "w") as f:
    f.write("no_etu;nationalite")
    for _ in range(NB_ETUDIANTS):
        no_etu = str(uuid4().hex)
        nat = html_special_chars(fake.country())
        list_etudiants.append([no_etu, nat])
        f.write(f"\n{no_etu};{nat}")

with open(employe_file, "w") as f:
    f.write("nom;prenom")
    for _ in range(NB_EMPLOYES):
        prenom = fake.first_name()
        nom = fake.last_name()
        list_employes.append([nom, prenom])
        f.write(f"\n{nom};{prenom}")

with open(entreprise_file, "w") as f:
    f.write("nom;pays_siege;ville_siege;adresse_siege;ouvert;effectif")
    for _ in range(NB_EMPLOYES):
        nom = fake.company()
        siege = pick_random(list_sites)
        ouvert = fake.boolean(chance_of_getting_true=90)
        effectif = fake.random_int(1, 5000)
        list_entreprises.append([nom, siege[0], siege[1], siege[2], ouvert, effectif])
        f.write(f"\n{nom};{siege[0]};{siege[1]};{siege[2]};{'true' if ouvert else 'false'};{effectif}")

with open(entreprise_site_file, "w") as f:
    f.write("nom_entreprise;pays_site;ville_site;adresse_site")
    for _ in range(NB_ENTREPRISES_SITE):
        nom = pick_random(list_entreprises)[0]
        site = pick_random(list_sites)
        list_entreprises_site.append([nom, site[0], site[1], site[2]])
        f.write(f"\n{nom};{site[0]};{site[1]};{site[2]}")

with open(donation_file, "w") as f:
    f.write("montant;date_debut;date_fin;entreprise")
    for _ in range(NB_DONATIONS):
        nom = pick_random(list_entreprises)[0]
        montant = randint(1000, 50000000)
        date_debut = random_date(DATE_DEBUT_CONTRAT, DATE_FIN_CONTRAT)
        date_fin = random_date(date_debut, DATE_FIN_CONTRAT)
        list_donations.append([montant, date_debut, date_fin, nom])
        f.write(f"\n{montant};{date_debut.isoformat()};{date_fin.isoformat()};{nom}")

with open(contrat_vacataire_file, "w") as f:
    f.write("nom_vacataire;prenom_vacataire;date_debut;date_fin;entreprise;pays_site;ville_site;adresse_site")
    for _ in range(NB_CONTRATS_VACATAIRE):
        nom_entreprise = pick_random(list_entreprises)[0]
        nom, prenom = pick_random(list_employes)
        date_debut = random_date(DATE_DEBUT_CONTRAT, DATE_FIN_CONTRAT)
        date_fin = random_date(date_debut, DATE_FIN_CONTRAT)
        site = pick_random(list_sites)
        list_contrats_vacataire.append([nom, prenom, date_debut, date_fin, nom_entreprise, site[0], site[1], site[2]])
        f.write(f"\n{nom};{prenom};{date_debut.isoformat()};{date_fin.isoformat()};{nom_entreprise};{site[0]};{site[1]};{site[2]}")

with open(contrat_laboratoire_file, "w") as f:
    f.write("laboratoire;date_debut;date_fin;entreprise;pays_site;ville_site;adresse_site")
    for _ in range(NB_CONTRATS_LABORATOIRE):
        nom_entreprise = pick_random(list_entreprises)[0]
        laboratoire = pick_random(list_laboratoires)
        date_debut = random_date(DATE_DEBUT_CONTRAT, DATE_FIN_CONTRAT)
        date_fin = random_date(date_debut, DATE_FIN_CONTRAT)
        site = pick_random(list_sites)
        list_contrats_laboratoire.append([laboratoire, date_debut, date_fin, nom_entreprise, site[0], site[1], site[2]])
        f.write(f"\n{laboratoire};{date_debut.isoformat()};{date_fin.isoformat()};{nom_entreprise};{site[0]};{site[1]};{site[2]}")

with open(contrat_etudiant_file, "w") as f:
    f.write("type;date_debut;date_fin;date_fin_anticipe;note_maitre;note_entreprise;entreprise;pays_site;ville_site;adresse_site;nom_maitre;prenom_maitre;etudiant")
    for _ in range(NB_CONTRATS_ETUDIANT):
        type = pick_random(["stage", "alternance"])

        date_debut = random_date(DATE_DEBUT_CONTRAT, DATE_FIN_CONTRAT)
        date_fin = random_date(date_debut, DATE_FIN_CONTRAT)
        date_fin_anticipe = random_date(date_debut, date_fin) if randint(0, 100) > 90 else None

        note_maitre = randint(0, 10) if randint(0, 100) <= 90 else None
        note_entreprise = randint(0, 10) if randint(0, 100) <= 90 else None

        nom_entreprise = pick_random(list_entreprises)[0]
        site = pick_random(list_sites)

        vacataire = pick_random(list_contrats_vacataire)
        etudiant = pick_random(list_etudiants)

        list_contrats_etudiant.append([type, date_debut, date_fin, date_fin_anticipe, note_maitre, note_entreprise, nom_entreprise, site[0], site[1], site[2], vacataire[0], vacataire[1], etudiant[0]])
        f.write(f"\n{type};{date_debut.isoformat()};{date_fin.isoformat()};{date_fin_anticipe.isoformat() if date_fin_anticipe else ''};{note_maitre if note_maitre else ''};{note_entreprise if note_entreprise else ''};{nom_entreprise};{site[0]};{site[1]};{site[2]};{vacataire[0]};{vacataire[1]};{etudiant[0]}")

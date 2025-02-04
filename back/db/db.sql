CREATE TYPE type_contrat AS ENUM ('stage', 'alternance');

DROP FUNCTION IF EXISTS get_current_contracts_by_company(nom text);
CREATE OR REPLACE FUNCTION get_current_contracts_by_company(nom text) RETURNS TABLE(type type_contrat, date_debut date, date_fin date, date_fin_anticipe date, nom_maitre varchar, prenom_maitre varchar, etudiant varchar) AS $$
BEGIN
    RETURN QUERY SELECT c.type, c.date_debut, c.date_fin, c.date_fin_anticipe, c.nom_maitre, c.prenom_maitre, c.etudiant FROM contrat_etudiant AS c
                 WHERE current_date >= c.date_debut AND
                     ((c.date_fin_anticipe IS NOT NULL AND current_date <= c.date_fin_anticipe)
                         OR current_date <= c.date_fin) AND c.entreprise=nom;
end;
$$ language plpgsql;

DROP FUNCTION IF EXISTS can_have_another_student_contract(nom_entreprise text);
CREATE OR REPLACE FUNCTION can_have_another_student_contract(nom_entreprise text) RETURNS BOOLEAN AS $$
DECLARE
    nb_employees int;
    nb_max_students int;
    nb_students int;
BEGIN
    SELECT effectif INTO nb_employees FROM entreprise WHERE nom=nom_entreprise;
    SELECT COUNT(*) INTO nb_students FROM get_current_contracts_by_company(nom_entreprise);
    IF nb_employees > 20 THEN
        nb_max_students := nb_employees * 0.15;
        IF nb_students < nb_max_students THEN
            RETURN TRUE;
        end if;
    end if;
    IF nb_students < 2 THEN
        RETURN TRUE;
    end if;
    RETURN FALSE;
end;
$$ language plpgsql;

DROP FUNCTION IF EXISTS check_contracts_number_students();
CREATE OR REPLACE FUNCTION check_contracts_number_students()
    RETURNS TRIGGER AS $$
BEGIN
    IF can_have_another_student_contract(NEW.entreprise) = FALSE THEN
        RAISE EXCEPTION 'entreprise % ne peut pas avoir de stagiaire ou alternant supplémentaire', NEW.entreprise;
    end if;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

create table employe
(
    nom    varchar(50),
    prenom varchar(50),
    primary key (nom, prenom)
);


create table laboratoire
(
    nom varchar(100) primary key
);


create table etudiant
(
    no_etu      varchar(20) primary key,
    nationalite varchar(80) not null
);


create table site
(
    pays    varchar(50),
    ville   varchar(30),
    adresse varchar(30),
    ouvert  bool default true not null,
    primary key (pays, ville, adresse)
);


create table entreprise
(
    nom           varchar(30) primary key,
    pays_siege    varchar(50)       not null,
    ville_siege   varchar(30)       not null,
    adresse_siege varchar(30)       not null,
    effectif      int               not null check ( effectif > 0 ),
    ouvert        bool default true not null,
    foreign key (pays_siege, ville_siege, adresse_siege) references site (pays, ville, adresse) on UPDATE cascade
);


create table entreprise_site
(
    pays_site      varchar(50),
    ville_site     varchar(30),
    adresse_site   varchar(30),
    nom_entreprise varchar(30) references entreprise (nom) on update cascade,
    foreign key (pays_site, ville_site, adresse_site) references site (pays, ville, adresse) on UPDATE cascade,
    primary key (nom_entreprise, ville_site, pays_site, adresse_site)
);

create table contrat_etudiant
(
    type              type_contrat,
    date_debut        date        not null,
    date_fin          date        not null check ( date_fin >= date_debut ),
    date_fin_anticipe date default null check ( date_fin_anticipe >= date_debut and date_fin_anticipe < date_fin),
    note_maitre       int check (note_maitre <= 10),
    note_entreprise   int check ( note_entreprise <= 10 ),
    nom_maitre        varchar(50) not null,
    prenom_maitre     varchar(50) not null,
    foreign key (nom_maitre, prenom_maitre) references employe (nom, prenom),
    pays_site         varchar(30) not null,
    ville_site        varchar(30) not null,
    adresse_site      varchar(30) not null,
    foreign key (pays_site, ville_site, adresse_site) references site (pays, ville, adresse),
    etudiant          varchar(20) not null references etudiant (no_etu) on UPDATE cascade,
    entreprise        varchar(30) references entreprise (nom) on update cascade,
    primary key (date_debut, etudiant)
);
CREATE OR REPLACE TRIGGER check_contracts_number_students_trigger
    BEFORE INSERT OR UPDATE ON contrat_etudiant
    FOR EACH ROW
EXECUTE FUNCTION check_contracts_number_students();

create table contrat_vacataire
(
    nom_vacataire     varchar(50) not null,
    prenom_vacataire  varchar(50) not null,
    foreign key (nom_vacataire, prenom_vacataire) references employe (nom, prenom),
    date_debut        date        not null,
    date_fin          date        not null,
    date_fin_anticipe date default null check ( date_fin_anticipe >= date_debut and date_fin_anticipe < date_fin),
    entreprise        varchar(30) references entreprise (nom) on update cascade,
    pays_site         varchar(30),
    ville_site        varchar(30),
    adresse_site      varchar(30),
    note              int check (note <= 10),
    foreign key (pays_site, ville_site, adresse_site) references site (pays, ville, adresse) on UPDATE cascade ,
    primary key (date_debut, nom_vacataire, prenom_vacataire)
);


create table contrat_labo
(
    laboratoire       varchar(100) not null references laboratoire (nom) on update cascade,
    date_debut        date        not null,
    date_fin          date        not null,
    date_fin_anticipe date default null check ( date_fin_anticipe >= date_debut and date_fin_anticipe < date_fin),
    entreprise        varchar(30) references entreprise (nom) on update cascade,
    pays_site         varchar(30),
    ville_site        varchar(30),
    adresse_site      varchar(30),
    foreign key (pays_site, ville_site, adresse_site) references site (pays, ville, adresse) on UPDATE cascade ,
    primary key (date_debut, laboratoire)
);


create table donation
(
    id         serial primary key,
    montant    int  not null check ( montant > 0 ),
    date_debut date not null,
    date_fin   date not null,
    entreprise varchar(30) references entreprise (nom) on update cascade
);


create table utilisateur
(
    nom      varchar(50)  not null,
    prenom   varchar(50)  not null,
    login    varchar(50) primary key,
    password varchar(255) not null
);


create view VueEntpCourrier as
select nom, pays_siege, ville_siege, adresse_siege
from entreprise;


create view VueEntpBlocked as
select entreprise.*
from entreprise
         left join contrat_etudiant ce on entreprise.nom = ce.entreprise
group by entreprise.nom
having avg(note_entreprise) <= 3;


create view VueVacMds as
select *
from employe
where (nom, prenom) in (select nom_maitre, prenom_maitre from contrat_etudiant)
  and (nom, prenom) in (select nom_vacataire, prenom_vacataire from contrat_vacataire);


-- could have used joins, but seems to be as efficient/fast...
create view VueVacMdsCurrent as
select *
from employe
where (nom, prenom) in
      (select nom_maitre, prenom_maitre
       from contrat_etudiant
       where date_debut < now()
         and (case when date_fin_anticipe is null then date_fin else date_fin_anticipe end) >
             now()) --sorry but ifnull() does not exist in postgre
  and (nom, prenom) in
      (select nom_vacataire, prenom_vacataire
       from contrat_vacataire
       where date_debut < now()
         and (case when date_fin_anticipe is null then date_fin else date_fin_anticipe end) > now());


create view VueEntpNbByContrat as
select entreprise.nom,
       count(entreprise)               nb_total, --entreprise not * to avoid 1 when 0
       sum((type = 'stage')::int)      nb_stage, --it works !
       sum((type = 'alternance')::int) nb_alternance,
       sum((type = 'vacataire')::int)  nb_vacataire,
       sum((type = 'labo')::int)       nb_labo,
       sum((type = 'donation')::int)   nb_donation
from entreprise
         left join(select entreprise, date_debut, date_fin, type::varchar(20)
                   from contrat_etudiant
                   union
                   select entreprise, date_debut, date_fin, 'vacataire' as type
                   from contrat_vacataire
                   union
                   select entreprise, date_debut, date_fin, 'labo' as type
                   from contrat_labo
                   union
                   select entreprise, date_debut, date_fin, 'donation' as type
                   from donation) contrats on contrats.entreprise = entreprise.nom
group by entreprise.nom;


create view VueLaboContrat as
select *
from contrat_labo;


create view VueMds as
select distinct nom_maitre, prenom_maitre
from contrat_etudiant
where type = 'stage';


create view VueVacataire as
select distinct nom_vacataire, prenom_vacataire
from contrat_vacataire;


create view VueAlternant as
select distinct etudiant.*
from contrat_etudiant
         join etudiant on contrat_etudiant.etudiant = etudiant.no_etu
where type = 'alternance';


create view VueStagiaire as
select distinct etudiant.*
from contrat_etudiant
         join etudiant on contrat_etudiant.etudiant = etudiant.no_etu
where type = 'stage';


create view VueEtudiant as
select *
from etudiant
where no_etu in (select etudiant from contrat_etudiant);


create view VueConflict as
select *
from (select extract(year from date_fin_anticipe) as annee,
             type::varchar(20)                    as type,
             entreprise --cast type else it throw type error
      from contrat_etudiant
      where date_fin_anticipe is not null
      union
      select extract(year from date_fin_anticipe) as annee, 'laboratoire' as type, entreprise
      from contrat_labo
      where date_fin_anticipe is not null
      union
      select extract(year from date_fin_anticipe) as annee, 'vacataire' as type, entreprise
      from contrat_vacataire
      where date_fin_anticipe is not null) contrats
order by entreprise, annee;


create view VueYearStatEntps as
select entreprise,
       extract(YEAR from date_debut)                    as annee,
       min(note_entreprise)                             as min,
       max(note_entreprise)                             as max,
       round(avg(note_entreprise), 2)::double precision as moyenne --round to keep only to decimals and double cast to remove 0s
from contrat_etudiant
where note_entreprise is not null
group by extract(YEAR from date_debut), entreprise;


create view VueConflitEtudiant as
select etudiant.*, extract(year from date_fin_anticipe) as annee, entreprise
from etudiant
         join contrat_etudiant on etudiant.no_etu = contrat_etudiant.etudiant
where contrat_etudiant.date_fin_anticipe is not null;


create view VueConflitEtudiant2 as
select no_etu, anneeConflit1, nomEntp1, anneeConflit2, nomEntp2
from (select etudiant.no_etu,
             extract(year from ce1.date_fin_anticipe) as                               anneeConflit1,
             ce1.entreprise                           as                               nomEntp1,
             extract(year from ce2.date_fin_anticipe) as                               anneeConflit2,
             ce2.entreprise                           as                               nomEntp2,
             row_number() over (partition by etudiant.no_etu order by etudiant.no_etu) RNum
      from etudiant
               join contrat_etudiant ce1 on etudiant.no_etu = ce1.etudiant
               join contrat_etudiant ce2
                    on etudiant.no_etu = ce2.etudiant and ce2.date_debut > ce1.date_debut -- > make sure it's not the same row and we don't have doubles
      where ce1.date_fin_anticipe is not null
        and ce2.date_fin_anticipe is not null) data
where data.RNum = 1;


create view VueMdsNote as
select employe.nom,
       employe.prenom,
       extract(year from date_debut)                          as annee,
       concat(sum(note_maitre), '/', count(note_maitre) * 10) as note_cumule
from employe
         join contrat_etudiant
              on employe.nom = contrat_etudiant.nom_maitre
                  and employe.prenom = contrat_etudiant.prenom_maitre
group by employe.nom, employe.prenom, extract(year from date_debut)
order by annee desc, nom desc
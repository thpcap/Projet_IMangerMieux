/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de cr�ation :  11/10/2024 17:01:14                      */
/*==============================================================*/

DROP TABLE if exists `aliment`, `a_besoins`, `compose`, `contient`, `niveau_de_pratique`, `nutriments`, `repas`, `sexe`, `tranches_d_age`, `type_aliment`, `utilisateur` cascade;

/*==============================================================*/
/* Table : ALIMENT                                              */
/*==============================================================*/
create table ALIMENT
(
   ID_ALIMENT           bigint not null AUTO_INCREMENT,
   ID_TYPE              bigint not null,
   LABEL_ALIMENT        varchar(256) not null,
   primary key (ID_ALIMENT)
);

/*==============================================================*/
/* Table : A_BESOINS                                            */
/*==============================================================*/
create table A_BESOINS
(
   ID_NUTRIMENT         bigint not null,
   LOGIN                varchar(50) not null,
   BESOINS              double not null,
   primary key (ID_NUTRIMENT, LOGIN)
);

/*==============================================================*/
/* Table : COMPOSE                                              */
/*==============================================================*/
create table COMPOSE
(
   ALI_ID_ALIMENT       bigint not null,
   ID_ALIMENT           bigint not null,
   POURCENTAGE          float not null,
   primary key (ALI_ID_ALIMENT, ID_ALIMENT)
);

/*==============================================================*/
/* Table : CONTIENT                                             */
/*==============================================================*/
create table CONTIENT
(
   ID_ALIMENT           bigint not null,
   ID_NUTRIMENT         bigint not null,
   RATIOS               double not null,
   primary key (ID_ALIMENT, ID_NUTRIMENT)
);

/*==============================================================*/
/* Table : NIVEAU_DE_PRATIQUE                                   */
/*==============================================================*/
create table NIVEAU_DE_PRATIQUE
(
   ID_PRATIQUE          bigint not null AUTO_INCREMENT,
   LIBELE_PRATIQUE      varchar(50) not null,
   primary key (ID_PRATIQUE)
);

/*==============================================================*/
/* Table : NUTRIMENTS                                           */
/*==============================================================*/
create table NUTRIMENTS
(
   ID_NUTRIMENT         bigint not null AUTO_INCREMENT,
   LIBELE_NUTRIMENT     varchar(255) not null,
   primary key (ID_NUTRIMENT)
);

/*==============================================================*/
/* Table : REPAS                                                */
/*==============================================================*/
create table REPAS
(
   ID_REPAS             bigint not null AUTO_INCREMENT,
   ID_ALIMENT           bigint not null,
   LOGIN                varchar(50) not null,
   QUANTITE             double not null,
   DATE                 datetime not null,
   primary key (ID_REPAS)
);

/*==============================================================*/
/* Table : SEXE                                                 */
/*==============================================================*/
create table SEXE
(
   ID_SEXE              int not null AUTO_INCREMENT,
   LIBELE_SEXE          varchar(50) not null,
   LIBELE_SEXE_COURT    varchar(5) not null,
   primary key (ID_SEXE)
);

/*==============================================================*/
/* Table : TRANCHES_D_AGE                                       */
/*==============================================================*/
create table TRANCHES_D_AGE
(
   ID_AGE               int not null AUTO_INCREMENT,
   MIN_AGE              int,
   MAX_AGE              int,
   LIBELE_AGE           varchar(50) not null,
   primary key (ID_AGE)
);

/*==============================================================*/
/* Table : TYPE_ALIMENT                                         */
/*==============================================================*/
create table TYPE_ALIMENT
(
   ID_TYPE              bigint not null AUTO_INCREMENT,
   LIBELE_TYPE          varchar(50) not null,
   primary key (ID_TYPE)
);

/*==============================================================*/
/* Table : UTILISATEUR                                          */
/*==============================================================*/
create table UTILISATEUR
(
   LOGIN                varchar(50) not null,
   ID_SEXE              int not null,
   ID_AGE               int not null,
   ID_PRATIQUE          bigint,
   MDP                  varchar(255) not null,
   NOM                  varchar(50) not null,
   PRENOM               varchar(50) not null,
   DATE_DE_NAISSANCE    date not null,
   MAIL                 varchar(50) UNIQUE not null,
   primary key (LOGIN)
);

alter table ALIMENT add constraint FK_EST foreign key (ID_TYPE)
      references TYPE_ALIMENT (ID_TYPE) on delete restrict on update cascade;

alter table A_BESOINS add constraint FK_A_BESOINS foreign key (ID_NUTRIMENT)
      references NUTRIMENTS (ID_NUTRIMENT) on delete cascade on update cascade;

alter table A_BESOINS add constraint FK_A_BESOINS2 foreign key (LOGIN)
      references UTILISATEUR (LOGIN) on delete cascade on update cascade;

alter table COMPOSE add constraint FK_COMPOSE foreign key (ALI_ID_ALIMENT)
      references ALIMENT (ID_ALIMENT) on delete cascade on update cascade;

alter table COMPOSE add constraint FK_COMPOSE2 foreign key (ID_ALIMENT)
      references ALIMENT (ID_ALIMENT) on delete cascade on update cascade;

alter table CONTIENT add constraint FK_CONTIENT foreign key (ID_ALIMENT)
      references ALIMENT (ID_ALIMENT) on delete cascade on update cascade;

alter table CONTIENT add constraint FK_CONTIENT2 foreign key (ID_NUTRIMENT)
      references NUTRIMENTS (ID_NUTRIMENT) on delete cascade on update cascade;

alter table REPAS add constraint FK_EST_COMPSE foreign key (ID_ALIMENT)
      references ALIMENT (ID_ALIMENT) on delete cascade on update cascade;

alter table REPAS add constraint FK_MANGE foreign key (LOGIN)
      references UTILISATEUR (LOGIN) on delete cascade on update cascade;

alter table UTILISATEUR add constraint FK_AGE foreign key (ID_AGE)
      references TRANCHES_D_AGE (ID_AGE) on delete restrict on update cascade;

alter table UTILISATEUR add constraint FK_APPARTIENT foreign key (ID_SEXE)
      references SEXE (ID_SEXE) on delete restrict on update cascade;

alter table UTILISATEUR add constraint FK_PRATIQUE foreign key (ID_PRATIQUE)
      references NIVEAU_DE_PRATIQUE (ID_PRATIQUE) on delete restrict on update cascade;

INSERT INTO SEXE(LIBELE_SEXE,LIBELE_SEXE_COURT) VALUES ('Homme','H'),('Femme','F'),('Droid','D'),('Autre','A');
INSERT INTO NIVEAU_DE_PRATIQUE(LIBELE_PRATIQUE) VALUES ('Nulle'),('Basse'),('Moyenne'),('Haute'),('Extreme');
INSERT INTO TRANCHES_D_AGE(MAX_AGE,LIBELE_AGE) VALUES(5,'Moins de 5 ans');
INSERT INTO TRANCHES_D_AGE(MIN_AGE,MAX_AGE,LIBELE_AGE) VALUES(5,10,'entre 5 et 10 ans'),(10,20,'entre 10 et 20 ans'),(20,30,'entre 20 et 30 ans'),(30,40,'entre 30 et 40 ans'),(40,50,'entre 40 et 50 ans');
INSERT INTO TRANCHES_D_AGE(MIN_AGE,LIBELE_AGE) VALUES(50,'Plus de 50ans');
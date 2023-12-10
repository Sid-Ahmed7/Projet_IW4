


-- Table "User" : Stocke les informations des utilisateurs
CREATE TABLE "User" (
  "userID" SERIAL PRIMARY KEY, -- Clé primaire
  "lastName" VARCHAR(50) NOT NULL, -- Nom de famille de l'utilisateur
  "firstName" VARCHAR(50) NOT NULL, -- Prénom de l'utilisateur
  "username" VARCHAR(50),
  "picture" VARCHAR(255) NOT NULL, -- Photo de profile de l'utilisateur
  "email" VARCHAR(50) UNIQUE NOT NULL, -- Adresse email de l'utilisateur, doit être unique
  "password" VARCHAR(255) NOT NULL, -- Mot de passe de l'utilisateur, stocké de manière sécurisée
  "signupDate" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de l'inscription
  "state" VARCHAR(20), -- Etat de l'objet dans le systeme
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP ,-- Date et heure de la suppression (si supprimé)
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);


-- Table "Companie" : Stocke les informations des entreprises
CREATE TABLE "Companie" (
  "companyID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT, -- Clee etrangere de la table user
  "name" VARCHAR(50) NOT NULL, -- Nom de l'entreprise
  "address" varchar(255), -- Adresse de l'entreprise
  "logo" VARCHAR(255), -- logo de l'entreprise
  "banner" VARCHAR(255), -- banniere de l'entreprise
  "email" VARCHAR(255), -- Adresse email de l'entreprise
  "phoneNumber" INT, -- Numéro de téléphone de l'entreprise
  "taxNumber" VARCHAR(255), -- Numéro de TVA
  "siretNumber" VARCHAR(255), -- Numéro SIRET
  "likes" INT DEFAULT 0, -- Nombre de "likes" ou appréciations de l'entreprise
  "createdAt" TIMESTAMP, -- 
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP, -- Date et heure de la suppression (si supprimé)
  "state" VARCHAR(20),  -- Etat de l'objet dans le systeme
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-- Table "Plans" : Stocke les différents plans d'abonnement disponibles
CREATE TABLE "Plan" (
  "planID" SERIAL PRIMARY KEY, -- Clé primaire
  "authorID" INT,
  "name" VARCHAR(255) NOT NULL, -- Nom du plan
  "price" DECIMAL(10, 2) NOT NULL, -- Prix du plan
  "features" TEXT, -- Description des fonctionnalités offertes par le plan
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP, -- Date et heure de la suppression (si supprimé)
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "uniqueRef" UUID DEFAULT uuid_generate_v4(), --
  "slug" VARCHAR(255) UNIQUE

);


-- Table "UserPlans" : Lie les utilisateurs à leurs plans d'abonnement
CREATE TABLE "UserPlan" (
  "userPlanID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "planID" INT REFERENCES "Plans"("planID") ON DELETE CASCADE, -- Clé étrangère vers la table "Plans"
  "startDate" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date de début de l'abonnement
  "endDate" TIMESTAMP, -- Date de fin de l'abonnement
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
);

-- Table "Request" : Les requetes sont les demandes que font les utilisateurs afin d'avoir un Devis auprès des companies
CREATE TABLE "Request"(
  "requestID" SERIAL PRIMARY KEY, -- Clé primaire de la table Request
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "companyID" INT REFERENCES "Company"("companyID") ON DELETE CASCADE, -- Clé étrangère vers la table "Company"
  "category" VARCHAR, -- Les catégories de l'événement
  "description" TEXT NOT NULL, -- L'utilisateur décrit l'événement pour lequel il veut un devis
  "status" VARCHAR(50) DEFAULT 'PENDING', -- Statut de la demande
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création de la demande
  -- "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour de la demande
  "eventDate" TIMESTAMP, -- Date limite pour répondre à la demande
  "response" TEXT, -- Réponse de l'organisation à la demande
  "devisAmount" DECIMAL(10, 2), -- Montant du devis proposé par l'organisation
  "eventDate" DATE NOT NULL, -- Date de l'événement
  "eventLocation" VARCHAR NOT NULL, -- Lieu de l'événement
  "maxBudget" DECIMAL(10, 2), -- Budget maximum pour l'événement
  "slug" VARCHAR(255)-- UNIQUE


);

  
-- Table "Devis" : Stocke les devis envoyés aux entreprises
CREATE TABLE "Devis" (
  "devisID" SERIAL PRIMARY KEY, -- Clé primaire
  "companieID" INT REFERENCES "Companie"("companieID") ON DELETE CASCADE, -- Clé étrangère vers la table "Companie"
  "userID" INT REFERENCES "User"("userID") ON DELETE SET NULL, -- Clé étrangère vers la table "User"
  "content" TEXT NOT NULL, -- Contenu détaillé du devis
  "status" VARCHAR(255), -- Statut du devis (ex : envoyé, en attente, approuvé, rejeté)
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "isNegotiable" BOOLEAN NOT NULL DEFAULT TRUE, -- Indique si le devis est négociable
  -- "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
  "createdAt" TIMESTAMPTZ NOT NULL DEFAULT (NOW()),
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-----------------------------------------------------------------------------------------------------------------------------------

-- Table "Negotiation" : Stocke les négociations sur les devis
CREATE TABLE "Negotiation" (
  "negotiationID" SERIAL PRIMARY KEY, -- Clé primaire
  "devisID" INT REFERENCES "Devis"("devisID") ON DELETE CASCADE, -- Clé étrangère vers la table "Quotes"
  "CompanieID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "initialPrice" DECIMAL(10, 2) NOT NULL, -- Prix de départ de la négociation
  "previewPrice" DECIMAL(10, 2), -- Prix de prévisualisation
  "finalPrice" DECIMAL(10, 2), -- Prix final après négociation
  "status" VARCHAR(255) NOT NULL DEFAULT 'pending', -- Statut de la négociation (pending, accepted, rejected)
  "expirationTimeLeft" INTERVAL, -- Intervalle durant lequel le devis modifiable, Permet de limiter la duré du process et limité les ressources y étant alloué
  "message" TEXT NOT NULL, -- Message de négociation
  "createdAt" TIMESTAMPTZ NOT NULL DEFAULT (NOW()),
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
);


-- Table "Invoice" : Stocke les factures associées aux devis
CREATE TABLE "Invoice" (
  "invoiceID" SERIAL PRIMARY KEY, -- Clé primaire
  "DevisID" INT REFERENCES "Devis"("devisID") ON DELETE CASCADE, -- Clé étrangère vers la table "Quotes"
  "stripePaymentID" VARCHAR(255), -- ID de paiement Stripe
  "paymentType" VARCHAR(255), -- Type de paiement (carte, espèce, chèque, etc.)
  "VAT" DECIMAL(10, 2), -- TVA
  "paymentMethod" "PaymentMethod", -- Méthode de paiement utilisée
  "paymentDetails" TEXT, -- Détails de la solution de paiement
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "createdAt" TIMESTAMPTZ NOT NULL DEFAULT (NOW()),
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-- Table "Messages" : Stocke les messages échangés dans les conversations
CREATE TABLE "Message" (
  "messageID" SERIAL PRIMARY KEY, -- Clé primaire
  "conversationID" INT REFERENCES "Conversations"("conversationID") ON DELETE CASCADE, -- Clé étrangère vers la table "Conversations"
  "senderID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User" pour l'expéditeur
  "messageType" VARCHAR(50) NOT NULL, -- Type du message (ex. "text", "image", "document")
  "imageID" INT REFERENCES "Image"("imageID") ON DELETE SET NULL,
  "documentID" INT REFERENCES "Document"("documentID") ON DELETE SET NULL,
  "contentText" TEXT, -- Pour les messages textuels
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création du message
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour du message
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE' -- Etat de l'objet dans le système
);

-- Table "Images" : Stocke les informations des images envoyées dans les conversations
CREATE TABLE "Image" (
  "imageID" SERIAL PRIMARY KEY, -- Clé primaire
  "url" VARCHAR(255) NOT NULL, -- URL de l'image
  "caption" TEXT -- Légende ou description de l'image (optionnel)
);

-- Table "Documents" : Stocke les informations des documents envoyés dans les conversations
CREATE TABLE "Document" (
  "documentID" SERIAL PRIMARY KEY, -- Clé primaire
  "url" VARCHAR(255) NOT NULL, -- URL du document
  "filename" VARCHAR(255) NOT NULL, -- Nom du fichier
  "fileType" VARCHAR(50) NOT NULL -- Type du fichier (ex. "pdf", "docx")
);

-- Table "UserPreferences" : Stocke les préférences des utilisateurs
CREATE TABLE "UserPreferences" (
  "preferenceID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "language" VARCHAR(10) NOT NULL DEFAULT 'EN', -- Langue de l'utilisateur (FR, EN, ES)
  "theme" VARCHAR(10) NOT NULL DEFAULT 'LIGHT', -- Thème de l'interface utilisateur (LIGHT, DARK)
  "twoFactorAuth" BOOLEAN NOT NULL DEFAULT FALSE, -- Activation de l'authentification à deux facteurs
  "notifications" BOOLEAN NOT NULL DEFAULT TRUE, -- Activation des notifications
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
);

-- Table "Media" : Stocke les médias associés aux entreprises ou utilisateurs
CREATE TABLE "Media" (
  "mediaID" SERIAL PRIMARY KEY, -- Clé primaire
  "companieID" INT REFERENCES "Companie"("companieID") ON DELETE SET NULL, -- Clé étrangère vers la table "Companie"
  "userID" INT REFERENCES "User"("userID") ON DELETE SET NULL, -- Clé étrangère vers la table "User"
  "type" VARCHAR(255), -- Type de média (image, vidéo, document)
  "url" TEXT NOT NULL, -- URL du média
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création du média
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-- Table "Notifications" : Stocke les notifications destinées aux utilisateurs
CREATE TABLE "Notification" (
  "notificationID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "type" VARCHAR(255) NOT NULL, -- Type de notification (ex. : 'NEW_MESSAGE', 'INVOICE_PAID', 'QUOTE_NEGOTIATION')
  "title" VARCHAR(255) NOT NULL, -- Titre de la notification
  "message" TEXT NOT NULL, -- Message de la notification
  "isRead" BOOLEAN NOT NULL DEFAULT FALSE, -- Indique si la notification a été lue
  "link" VARCHAR(255), -- Lien optionnel pour rediriger l'utilisateur
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création de la notification
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
);

-- Table "Conversations" : Stocke les métadonnées des conversations
CREATE TABLE "Conversation" (
  "conversationID" SERIAL PRIMARY KEY, -- Clé primaire
  "title" VARCHAR(100), -- Titre ou sujet de la conversation (optionnel)
  "lastMessageTimestamp" TIMESTAMP, -- Horodatage du dernier message (pour faciliter le tri et l'affichage)
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le système
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création de la conversation
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour de la conversation
);

-- Table "ConversationUser" : Associe les utilisateurs aux conversations
CREATE TABLE "ConversationUser" (
  "conversationID" INT REFERENCES "Conversation"("conversationID") ON DELETE CASCADE,
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE,
  "isArchived" BOOLEAN NOT NULL DEFAULT FALSE, -- Pour savoir si la conversation est archivée pour l'utilisateur
  "unreadCount" INT NOT NULL DEFAULT 0, -- Nombre de messages non lus par l'utilisateur dans la conversation
  "muteNotifications" BOOLEAN NOT NULL DEFAULT FALSE, -- Pour savoir si les notifications sont désactivées pour l'utilisateur
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le système
  PRIMARY KEY ("conversationID", "userID")
);
 


-- Table "User" : Stocke les informations des utilisateurs
CREATE TABLE "User" (
  "userID" SERIAL PRIMARY KEY, -- Clé primaire
  "lastName" VARCHAR(50) NOT NULL, -- Nom de famille de l'utilisateur
  "firstName" VARCHAR(50) NOT NULL, -- Prénom de l'utilisateur
  "username" VARCHAR,
  "picture" VARCHAR(255) NOT NULL, -- Photo de profile de l'utilisateur
  "email" VARCHAR(50) UNIQUE NOT NULL, -- Adresse email de l'utilisateur, doit être unique
  "password" VARCHAR(255) NOT NULL, -- Mot de passe de l'utilisateur, stocké de manière sécurisée
  "signupDate" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de l'inscription
  "state" VARCHAR(20), -- Etat de l'objet dans le systeme
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP, -- Date et heure de la suppression (si supprimé)
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);


-- Table "Companie" : Stocke les informations des entreprises
CREATE TABLE "Companie" (
  "companyID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT -- Clee etrangere de la table user
  "name" VARCHAR(50) NOT NULL, -- Nom de l'entreprise
  "address" varchar(255), -- Adresse de l'entreprise
  "logo" VARCHAR(255), -- logo de l'entreprise
  "banner" VARCHAR(255), -- banniere de l'entreprise
  "email" VARCHAR(255), -- Adresse email de l'entreprise
  "phoneNumber" INT, -- Numéro de téléphone de l'entreprise
  "taxNumber" VARCHAR(255), -- Numéro de TVA
  "siretNumber" VARCHAR(255), -- Numéro SIRET
  "likes" INT DEFAULT 0, -- Nombre de "likes" ou appréciations de l'entreprise
  "createdAt" TIMESTAMP, -- 
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
  "state"  -- Etat de l'objet dans le systeme
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-- Table "Plans" : Stocke les différents plans d'abonnement disponibles
CREATE TABLE "Plan" (
  "planID" SERIAL PRIMARY KEY, -- Clé primaire
  "authorID" INT
  "name" VARCHAR(255) NOT NULL, -- Nom du plan
  "price" DECIMAL(10, 2) NOT NULL, -- Prix du plan
  "features" TEXT, -- Description des fonctionnalités offertes par le plan
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE

);


-- Table "UserPlans" : Lie les utilisateurs à leurs plans d'abonnement
CREATE TABLE "UserPlan" (
  "userPlanID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "planID" INT REFERENCES "Plans"("planID") ON DELETE CASCADE, -- Clé étrangère vers la table "Plans"
  "startDate" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date de début de l'abonnement
  "endDate" TIMESTAMP, -- Date de fin de l'abonnement
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
);

-- Table "Request" : Les requetes sont les demandes que font les utilisateurs afin d'avoir un Devis auprès des companies
CREATE TABLE "Request"(
  "requestID" SERIAL PRIMARY KEY, -- Clé primaire de la table Request
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "companyID" INT REFERENCES "Company"("companyID") ON DELETE CASCADE, -- Clé étrangère vers la table "Company"
  "category" VARCHAR, -- Les catégories de l'événement
  "description" TEXT NOT NULL, -- L'utilisateur décrit l'événement pour lequel il veut un devis
  "status" VARCHAR(50) DEFAULT 'PENDING', -- Statut de la demande
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création de la demande
  -- "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour de la demande
  "eventDate" TIMESTAMP, -- Date limite pour répondre à la demande
  "response" TEXT, -- Réponse de l'organisation à la demande
  "devisAmount" DECIMAL(10, 2), -- Montant du devis proposé par l'organisation
  "eventDate" DATE NOT NULL, -- Date de l'événement
  "eventLocation" VARCHAR NOT NULL, -- Lieu de l'événement
  "maxBudget" DECIMAL(10, 2) -- Budget maximum pour l'événement
  "slug" VARCHAR(255) UNIQUE


);

  
-- Table "Devis" : Stocke les devis envoyés aux entreprises
CREATE TABLE "Devis" (
  "devisID" SERIAL PRIMARY KEY, -- Clé primaire
  "companieID" INT REFERENCES "Companie"("companieID") ON DELETE CASCADE, -- Clé étrangère vers la table "Companie"
  "userID" INT REFERENCES "User"("userID") ON DELETE SET NULL, -- Clé étrangère vers la table "User"
  "content" TEXT NOT NULL, -- Contenu détaillé du devis
  "status" VARCHAR(255), -- Statut du devis (ex : envoyé, en attente, approuvé, rejeté)
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "isNegotiable" BOOLEAN NOT NULL DEFAULT TRUE, -- Indique si le devis est négociable
  -- "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
  "createdAt" TIMESTAMPTZ NOT NULL DEFAULT (NOW()),
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-----------------------------------------------------------------------------------------------------------------------------------

-- Table "Negotiation" : Stocke les négociations sur les devis
CREATE TABLE "Negotiation" (
  "negotiationID" SERIAL PRIMARY KEY, -- Clé primaire
  "devisID" INT REFERENCES "Devis"("devisID") ON DELETE CASCADE, -- Clé étrangère vers la table "Quotes"
  "CompanieID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "initialPrice" DECIMAL(10, 2) NOT NULL, -- Prix de départ de la négociation
  "previewPrice" DECIMAL(10, 2), -- Prix de prévisualisation
  "finalPrice" DECIMAL(10, 2), -- Prix final après négociation
  "status" VARCHAR(255) NOT NULL DEFAULT 'pending', -- Statut de la négociation (pending, accepted, rejected)
  "expirationTimeLeft" INTERVAL, -- Intervalle durant lequel le devis modifiable, Permet de limiter la duré du process et limité les ressources y étant alloué
  "message" TEXT NOT NULL, -- Message de négociation
  "createdAt" TIMESTAMPTZ NOT NULL DEFAULT (NOW()),
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
);


-- Table "Invoice" : Stocke les factures associées aux devis
CREATE TABLE "Invoice" (
  "invoiceID" SERIAL PRIMARY KEY, -- Clé primaire
  "DevisID" INT REFERENCES "Devis"("devisID") ON DELETE CASCADE, -- Clé étrangère vers la table "Quotes"
  "stripePaymentID" VARCHAR(255), -- ID de paiement Stripe
  "paymentType" VARCHAR(255), -- Type de paiement (carte, espèce, chèque, etc.)
  "VAT" DECIMAL(10, 2), -- TVA
  "paymentMethod" "PaymentMethod", -- Méthode de paiement utilisée
  "paymentDetails" TEXT, -- Détails de la solution de paiement
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour
  "createdAt" TIMESTAMPTZ NOT NULL DEFAULT (NOW()),
  "deletedAt" TIMESTAMP -- Date et heure de la suppression (si supprimé)
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-- Table "Messages" : Stocke les messages échangés dans les conversations
CREATE TABLE "Message" (
  "messageID" SERIAL PRIMARY KEY, -- Clé primaire
  "conversationID" INT REFERENCES "Conversations"("conversationID") ON DELETE CASCADE, -- Clé étrangère vers la table "Conversations"
  "senderID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User" pour l'expéditeur
  "messageType" VARCHAR(50) NOT NULL, -- Type du message (ex. "text", "image", "document")
  "imageID" INT REFERENCES "Image"("imageID") ON DELETE SET NULL,
  "documentID" INT REFERENCES "Document"("documentID") ON DELETE SET NULL,
  "contentText" TEXT, -- Pour les messages textuels
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création du message
  "updatedAt" TIMESTAMP, -- Date et heure de la dernière mise à jour du message
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE' -- Etat de l'objet dans le système
);

-- Table "Images" : Stocke les informations des images envoyées dans les conversations
CREATE TABLE "Image" (
  "imageID" SERIAL PRIMARY KEY, -- Clé primaire
  "url" VARCHAR(255) NOT NULL, -- URL de l'image
  "caption" TEXT -- Légende ou description de l'image (optionnel)
);

-- Table "Documents" : Stocke les informations des documents envoyés dans les conversations
CREATE TABLE "Document" (
  "documentID" SERIAL PRIMARY KEY, -- Clé primaire
  "url" VARCHAR(255) NOT NULL, -- URL du document
  "filename" VARCHAR(255) NOT NULL, -- Nom du fichier
  "fileType" VARCHAR(50) NOT NULL -- Type du fichier (ex. "pdf", "docx")
);

-- Table "UserPreferences" : Stocke les préférences des utilisateurs
CREATE TABLE "UserPreferences" (
  "preferenceID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "language" VARCHAR(10) NOT NULL DEFAULT 'EN', -- Langue de l'utilisateur (FR, EN, ES)
  "theme" VARCHAR(10) NOT NULL DEFAULT 'LIGHT', -- Thème de l'interface utilisateur (LIGHT, DARK)
  "twoFactorAuth" BOOLEAN NOT NULL DEFAULT FALSE, -- Activation de l'authentification à deux facteurs
  "notifications" BOOLEAN NOT NULL DEFAULT TRUE, -- Activation des notifications
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
);

-- Table "Media" : Stocke les médias associés aux entreprises ou utilisateurs
CREATE TABLE "Media" (
  "mediaID" SERIAL PRIMARY KEY, -- Clé primaire
  "companieID" INT REFERENCES "Companie"("companieID") ON DELETE SET NULL, -- Clé étrangère vers la table "Companie"
  "userID" INT REFERENCES "User"("userID") ON DELETE SET NULL, -- Clé étrangère vers la table "User"
  "type" VARCHAR(255), -- Type de média (image, vidéo, document)
  "url" TEXT NOT NULL, -- URL du média
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le systeme
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création du média
  "updatedAt" TIMESTAMP ,-- Date et heure de la dernière mise à jour
  "uniqueRef" UUID DEFAULT uuid_generate_v4(),
  "slug" VARCHAR(255) UNIQUE
);

-- Table "Notifications" : Stocke les notifications destinées aux utilisateurs
CREATE TABLE "Notification" (
  "notificationID" SERIAL PRIMARY KEY, -- Clé primaire
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE, -- Clé étrangère vers la table "User"
  "type" VARCHAR(255) NOT NULL, -- Type de notification (ex. : 'NEW_MESSAGE', 'INVOICE_PAID', 'QUOTE_NEGOTIATION')
  "title" VARCHAR(255) NOT NULL, -- Titre de la notification
  "message" TEXT NOT NULL, -- Message de la notification
  "isRead" BOOLEAN NOT NULL DEFAULT FALSE, -- Indique si la notification a été lue
  "link" VARCHAR(255), -- Lien optionnel pour rediriger l'utilisateur
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création de la notification
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour
);

-- Table "Conversations" : Stocke les métadonnées des conversations
CREATE TABLE "Conversation" (
  "conversationID" SERIAL PRIMARY KEY, -- Clé primaire
  "title" VARCHAR(100), -- Titre ou sujet de la conversation (optionnel)
  "lastMessageTimestamp" TIMESTAMP, -- Horodatage du dernier message (pour faciliter le tri et l'affichage)
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le système
  "createdAt" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date et heure de création de la conversation
  "updatedAt" TIMESTAMP -- Date et heure de la dernière mise à jour de la conversation
);

-- Table "ConversationUser" : Associe les utilisateurs aux conversations
CREATE TABLE "ConversationUser" (
  "conversationID" INT REFERENCES "Conversation"("conversationID") ON DELETE CASCADE,
  "userID" INT REFERENCES "User"("userID") ON DELETE CASCADE,
  "isArchived" BOOLEAN NOT NULL DEFAULT FALSE, -- Pour savoir si la conversation est archivée pour l'utilisateur
  "unreadCount" INT NOT NULL DEFAULT 0, -- Nombre de messages non lus par l'utilisateur dans la conversation
  "muteNotifications" BOOLEAN NOT NULL DEFAULT FALSE, -- Pour savoir si les notifications sont désactivées pour l'utilisateur
  "state" "ObjectState" NOT NULL DEFAULT 'VISIBLE', -- Etat de l'objet dans le système
  PRIMARY KEY ("conversationID", "userID")
);
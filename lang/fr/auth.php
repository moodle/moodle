<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.6.4 (2002112400)


$string['auth_dbdescription'] = "Cette méthode utilise une base de données externe afin de vérifier qu'un nom d'utilisateur et son mot de passe sont valides. Si le compte concerné est nouveau, il est possible de copier des données provenant de certains champs vers Moodle.";
$string['auth_dbextrafields'] = "Ces zones sont optionnelles. Il vous est possible de remplir certains champs de Moodle avec des données provenant des <b>champs de la base de données externe</b>.<p>Si vous laissez ces zones vides, les valeurs par défaut seront utilisées.<p>Dans tous les cas, l'utilisateur a la possibilité de modifier tous ces champs une fois connecté.";
$string['auth_dbfieldpass'] = "Nom du champ contenant les mots de passe";
$string['auth_dbfielduser'] = "Nom du champ contenant les noms des utilisateurs";
$string['auth_dbhost'] = "La machine contenant la base de données";
$string['auth_dbname'] = "Nom de la base de données";
$string['auth_dbpass'] = "Mot de passe pour ce compte";
$string['auth_dbtable'] = "Nom de la table dans la base de données";
$string['auth_dbtitle'] = "Utiliser une base de données externe";
$string['auth_dbtype'] = "Type de la base de données (voir la <a href=../lib/adodb/readme.htm#drivers>documentation de ADOdb</a> pour plus d'information)";
$string['auth_dbuser'] = "Compte avec accès en lecture à la base de données";
$string['auth_emaildescription'] = "La confirmation par émail est la méthode d'authentification par défaut. Lorsqu'un utilisateur s'enregistre en choisissant ses nom d'utilisateur et mot de passe, un message de confirmation est envoyé à son adresse émail. Ce message contient un lien sécurisé vers une page Web où il peut confirmer son inscription. Les connexions suivantes ne vérifient que les nom d'utilisateur et mot de passe précédemment enregistrés dans la base de données de Moodle.";
$string['auth_emailtitle'] = "Authentification par émail";
$string['auth_imapdescription'] = "Cette méthode utilise un serveur IMAP pour vérifier qu'un nom d'utilisateur et son mot de passe sont valides.";
$string['auth_imaphost'] = "L'adresse du serveur IMAP. Utiliser l'adresse IP et non le nom de la machine.";
$string['auth_imapport'] = "Numéro de port du serveur IMAP. Il s'agit généralement de 143 ou 993.";
$string['auth_imaptitle'] = "Utiliser un serveur IMAP";
$string['auth_imaptype'] = "Le type de serveur IMAP. Les serveurs IMAP peuvent avoir différentes méthodes d'authentification et de négociation.";
$string['auth_ldap_bind_dn'] = "Si vous souhaitez utiliser une connexion authentifiée au serveur LDAP pour chercher les utilisateurs, indiquez ici son nom de connexion. Quelque chose comme : « cn=ldapuser, o=Organisation, c=FR ».";
$string['auth_ldap_bind_pw'] = "Mot de passe pour cette connexion";
$string['auth_ldap_contexts'] = "Liste des noeuds de l'annuaire LDAP, séparés par « ; », où les enregistrements des utilisateurs sont situés. Par exemple : « ou=Étudiants, o=Organisation, c=FR; ou=Professeurs, o=Organisation, c=FR ».";
$string['auth_ldap_host_url'] = "Indiquer le serveur LDAP sous form d'URL comme ceci :<br>« ldap://ldap.organisation.fr/ »<br>ou :<br>« ldaps://ldap.organisation.fr/ »";
$string['auth_ldap_search_sub'] = "Mettre une valeur différente de 0 pour rechercher les enregistrements dans les sous-noeuds.";
$string['auth_ldap_update_userinfo'] = "Mettre-à-jour les données des utilisateurs (prénom, nom, addresse, etc.) de Moodle depuis l'annuaire LDAP. Lire « /auth/ldap/attr_mappings.php » pour avoir des informations sur la correspondance.";
$string['auth_ldap_user_attribute'] = "L'attribut utilisé pour nommer et rechercher les utilisateurs. Habituellement « cn ».";
$string['auth_ldapdescription'] = "Cette méthode permet l'authentification auprès d'un annuaire LDAP externe. Si les nom d'utilisateur et mot de passe sont corrects, Moodle créera un nouvel enregistrement pour cet utilisateur dans sa base de données. Ce module peut récupérer les attributs de l'enregistrement LDAP de l'utilisateur afin de remplir certains champs dans Moodle. Lors des connexions suivantes, seuls les nom d'utilisateur et mot de passe sont vérifiés.";
$string['auth_ldapextrafields'] = "Ces zones sont optionnelles. Il vous est possible de remplir certains champs de Moodle avec des données provenant des <b>attributs de l'annuaire LDAP</b>.<p>Si vous laissez ces zones vides, aucune donnée ne sera récupérée de l'annuaire LDAP et les valeurs par défaut de Moodle seront utilisées. <p>Dans tous les cas, l'utilisateur a la possibilité de modifier tous ces champs une fois connecté.";
$string['auth_ldaptitle'] = "Utiliser un serveur LDAP";
$string['auth_nntpdescription'] = "Cette méthode utilise un serveur NNTP pour vérifier qu'un nom d'utilisateur et son mot de passe sont valides.";
$string['auth_nntphost'] = "L'adresse du serveur NNTP. Utiliser l'adresse IP et non le nom de la machine.";
$string['auth_nntpport'] = "Numéro de port du serveur NNTP. Il s'agit généralement de 119.";
$string['auth_nntptitle'] = "Utiliser un serveur NNTP";
$string['auth_nonedescription'] = "Les utilisateurs peuvent s'enregistrer et créer des comptes valides immédiatement sans aucune authentification tiers ni confirmation par émail. Attention lors de l'utilisation de cette méthode, bien penser à toutes les implications (problèmes d'administration et sécurité).";
$string['auth_nonetitle'] = "Pas d'authentification";
$string['auth_pop3description'] = "Cette méthode utilise un serveur POP3 pour vérifier qu'un nom d'utilisateur et son mot de passe sont valides.";
$string['auth_pop3host'] = "L'adresse du serveur POP3. Utiliser l'adresse IP et non le nom de la machine.";
$string['auth_pop3port'] = "Numéro de port du serveur NNTP. Il s'agit généralement de 110.";
$string['auth_pop3title'] = "Utiliser un serveur POP3";
$string['auth_pop3type'] = "Type de serveur. Si le serveur POP3 utilise « certificate security », choisir « pop3cert ».";
$string['authenticationoptions'] = "Options d'authentification";
$string['authinstructions'] = "Dans cette zone il vous est possible de fournir des instructions à vos utilisateurs afin qu'ils sachent quels nom d'utilisateur et mot de passe utiliser. Ce texte apparaîtra sur la page de connexion. Si vous laissez cette zone vide, aucune instruction ne sera affichée.";
$string['changepassword'] = "URL changer de mot de passe";
$string['changepasswordhelp'] = "Vous pouvez indiquer dans cette zone l'URL d'une page sur laquelle vos utilisateurs pourront récupérer ou changer leurs nom d'utilisateur et mot de passe s'ils les ont oubliés. Cette URL sera disponible sous forme d'un bouton sur la page de connexion. Si cette zone est vide, ce bouton ne sera pas affiché.";
$string['chooseauthmethod'] = "Choisir une méthode d'authentification";
$string['guestloginbutton'] = "Bouton de connexion anonyme";
$string['instructions'] = "Instructions";
$string['showguestlogin'] = "Vous pouvez choisir de montrer ou non le bouton de connexion en tant qu'utilisateur anonyme sur la page de connexion.";

?>

<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2 Beta (2004022400)


$string['auth_dbdescription'] = 'Cette méthode utilise une base de données externe afin de vérifier qu\'un nom d\'utilisateur et son mot de passe sont valides. Si le compte est nouveau, il est possible de copier des données provenant de certains champs vers Moodle.';
$string['auth_dbextrafields'] = 'Ces champs sont optionnels. Il vous est possible de remplir certains champs de Moodle avec des données provenant des <b>champs de la base de données externe</b>.<p>Si vous laissez ces zones vides, les valeurs par défaut seront utilisées.<p>Dans tous les cas, l\'utilisateur a la possibilité de modifier tous ces champs une fois connecté.';
$string['auth_dbfieldpass'] = 'Nom du champ contenant les mots de passe';
$string['auth_dbfielduser'] = 'Nom du champ contenant les noms des utilisateurs';
$string['auth_dbhost'] = 'L\'ordinateur contenant la base de données.';
$string['auth_dbname'] = 'Nom de la base de données';
$string['auth_dbpass'] = 'Mot de passe pour ce compte';
$string['auth_dbpasstype'] = 'Indiquez la méthode avec laquelle est crypté le champ qui contient le mot de passe. L\'algorithme MD5 est utile pour une utilisation conjointe avec d\'autres applications Web telles que PostNuke.';
$string['auth_dbtable'] = 'Nom de la table dans la base de données';
$string['auth_dbtitle'] = 'Utiliser une base de données externe';
$string['auth_dbtype'] = 'Type de la base de données (voir la <a href=../lib/adodb/readme.htm#drivers>documentation de ADOdb</a> pour plus d\'information)';
$string['auth_dbuser'] = 'Compte avec accès en lecture à la base de données';
$string['auth_emaildescription'] = 'La confirmation par courriel est la méthode d\'authentification par défaut. Lorsqu\'un utilisateur s\'enregistre en choisissant ses nom d\'utilisateur et mot de passe, un message de confirmation est envoyé à son adresse de courriel. Ce message contient un lien sécurisé vers une page Web où il peut confirmer son inscription. Lors des connexions suivantes, le nom d\'utilisateur et son mot de passe sont vérifiés à partir de ces informations qui sont enregistrées dans la base de données de Moodle.';
$string['auth_emailtitle'] = 'Authentification par courriel';
$string['auth_imapdescription'] = 'Cette méthode utilise un serveur IMAP pour vérifier qu\'un nom d\'utilisateur et son mot de passe sont valides.';
$string['auth_imaphost'] = 'L\'adresse du serveur IMAP. Utiliser l\'adresse IP et non le nom de l\'ordinateur.';
$string['auth_imapport'] = 'Numéro de port du serveur IMAP. Il s\'agit généralement de 143 ou 993.';
$string['auth_imaptitle'] = 'Utiliser un serveur IMAP';
$string['auth_imaptype'] = 'Le type de serveur IMAP. Les serveurs IMAP peuvent avoir différentes méthodes d\'authentification et de négociation.';
$string['auth_ldap_bind_dn'] = 'Si vous souhaitez utiliser une connexion authentifiée au serveur LDAP pour chercher les utilisateurs, indiquez ici son nom de connexion. Par exemple : « cn=ldapuser, o=Organisation, c=FR ».';
$string['auth_ldap_bind_pw'] = 'Mot de passe pour cette connexion';
$string['auth_ldap_contexts'] = 'Liste des noeuds (contextes) de l\'annuaire LDAP, séparés par « ; », où les enregistrements des utilisateurs sont situés. Par exemple : « ou=Étudiants, o=Organisation, c=FR; ou=Professeurs, o=Organisation, c=FR ».';
$string['auth_ldap_create_context'] = 'Si vous avez activé la création d\'utilisateur avec confirmation par courriel, vous devez spécifiez le contexte dans lequel ces utilisateurs seront créés.   Ces contextes doivent être différents des autres utilisateurs pour éviter des failles de sécurité. Vous n\'avez pas à ajouter ce contexte à ldap_context-variable, Moodle cherchera automatiquement les utilisateurs dans ce contexte.';
$string['auth_ldap_creators'] = 'Liste des groupes dont les membres peuvent créer des cours. Il faut séparer les groupes par «,». Par exemple, «cn=professeurs,ou=personnel,o=college».';
$string['auth_ldap_host_url'] = 'Indiquer le serveur LDAP sous form d\'URL comme ceci :<br>« ldap://ldap.organisation.fr/ »<br>ou :<br>« ldaps://ldap.organisation.fr/ »';
$string['auth_ldap_memberattribute'] = 'Caractériser les membres du groupe lorsque les utilisateurs font parti d\'un groupe. Par exemple : «membre».';
$string['auth_ldap_search_sub'] = 'Mettre une valeur différente de 0 pour rechercher les enregistrements dans les sous-noeuds (sous-contextes).';
$string['auth_ldap_update_userinfo'] = 'Mettre-à-jour les données des utilisateurs (prénom, nom, addresse, etc.) de Moodle depuis l\'annuaire LDAP. Lire « /auth/ldap/attr_mappings.php » pour avoir des informations sur la correspondance.';
$string['auth_ldap_user_attribute'] = 'L\'attribut utilisé pour nommer et rechercher les utilisateurs. Habituellement « cn ».';
$string['auth_ldapdescription'] = 'Cette méthode permet l\'authentification auprès d\'un annuaire LDAP externe. Si les nom d\'utilisateur et mot de passe sont corrects, Moodle créera un nouvel enregistrement pour cet utilisateur dans sa base de données. Ce module peut récupérer les attributs de l\'enregistrement LDAP de l\'utilisateur afin de remplir certains champs dans Moodle. Lors des connexions suivantes, seuls les nom d\'utilisateur et mot de passe sont vérifiés.';
$string['auth_ldapextrafields'] = 'Ces champs sont optionnels. Il vous est possible de remplir certains champs de Moodle avec des données provenant des <b>attributs de l\'annuaire LDAP</b>.<p>Si vous laissez ces champs vides, aucune donnée ne sera récupérée de l\'annuaire LDAP et les valeurs par défaut de Moodle seront utilisées. <p>Dans tous les cas, l\'utilisateur a la possibilité de modifier tous ces champs une fois connecté.';
$string['auth_ldaptitle'] = 'Utiliser un serveur LDAP';
$string['auth_manualdescription'] = 'Cette méthode empêche les utilisateurs de créer leur propre compte. Tous les comptes devront être créés manuellement par l\'administeur du serveur.';
$string['auth_manualtitle'] = 'Comptes créés manuellement seulement';
$string['auth_multiplehosts'] = 'Vous pouvez indiquer ici plusieurs hôtes SMTP (par exemple host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Cette méthode utilise un serveur NNTP pour vérifier qu\'un nom d\'utilisateur et son mot de passe sont valides.';
$string['auth_nntphost'] = 'L\'adresse du serveur NNTP. Utiliser l\'adresse IP et non le nom de la machine.';
$string['auth_nntpport'] = 'Numéro de port du serveur NNTP. Il s\'agit généralement de 119.';
$string['auth_nntptitle'] = 'Utiliser un serveur NNTP';
$string['auth_nonedescription'] = 'Les utilisateurs peuvent s\'enregistrer et créer des comptes valides immédiatement sans aucune validation externe ni confirmation par courriel. Attention lors de l\'utilisation de cette méthode : réfléchissez à toutes les implications sur la sécurité et la gestion des utilisateurs.';
$string['auth_nonetitle'] = 'Pas d\'authentification';
$string['auth_pop3description'] = 'Cette méthode utilise un serveur POP3 pour vérifier qu\'un nom d\'utilisateur et son mot de passe sont valides.';
$string['auth_pop3host'] = 'L\'adresse du serveur POP3. Utiliser l\'adresse IP et non le nom de la machine.';
$string['auth_pop3port'] = 'Numéro de port du serveur NNTP. Il s\'agit généralement de 110.';
$string['auth_pop3title'] = 'Utiliser un serveur POP3';
$string['auth_pop3type'] = 'Type de serveur. Si le serveur POP3 utilise « certificate security », choisir « pop3cert ».';
$string['auth_user_create'] = 'Permettre la création d\'utilisateur';
$string['auth_user_creation'] = 'Les nouveux utilisateurs (anonymes) peuvent créer un compte avec l\'authentification de source extérieure et confirmation par courriel. N\'oubliez pas de configurer également les options des autres modules qui traitent de la création de comptes.';
$string['auth_usernameexists'] = 'Désolé, ce nom existe déjà! Veuillez en choisir un autre.';
$string['authenticationoptions'] = 'Options d\'authentification';
$string['authinstructions'] = 'Dans cette zone il vous est possible de fournir des instructions à vos utilisateurs afin qu\'ils sachent quels nom d\'utilisateur et mot de passe utiliser. Ce texte apparaîtra sur la page de connexion. Si vous laissez cette zone vide, aucune instruction ne sera affichée.';
$string['changepassword'] = 'URL changer de mot de passe';
$string['changepasswordhelp'] = 'Vous pouvez indiquer dans cette zone l\'URL d\'une page sur laquelle vos utilisateurs pourront récupérer ou changer leurs nom d\'utilisateur et mot de passe s\'ils les ont oubliés. Cette URL sera disponible sous forme d\'un bouton sur la page de connexion. Si cette zone est vide, ce bouton ne sera pas affiché.';
$string['chooseauthmethod'] = 'Choisir une méthode d\'authentification';
$string['guestloginbutton'] = 'Bouton pour visiteur anonyme';
$string['instructions'] = 'Instructions';
$string['md5'] = 'Cryptage MD5';
$string['plaintext'] = 'Texte en clair';
$string['showguestlogin'] = 'Vous pouvez choisir de montrer ou non le bouton de connexion en tant que visiteur anonyme sur la page de connexion.';

?>
